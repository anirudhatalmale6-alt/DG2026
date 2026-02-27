<?php

namespace Modules\CIMSDocumentGenerator\Services;

// Ensure TCPDF and FPDI are loaded (packages added outside Composer workflow)
if (!class_exists('TCPDF')) {
    require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');
}
if (!class_exists('setasign\Fpdi\Tcpdf\Fpdi')) {
    require_once base_path('vendor/setasign/fpdi/src/autoload.php');
}

use Modules\CIMSDocumentGenerator\Models\DocgenTemplate;
use Modules\CIMSDocumentGenerator\Models\DocgenSetting;

class PdfGeneratorService
{
    protected $fpdi;
    protected $defaultFont;
    protected $defaultFontSize;
    protected $defaultFontColor;
    protected $defaultTextAlign;

    public function __construct()
    {
        $this->defaultFont = DocgenSetting::getVal('default_font_family', 'Helvetica');
        $this->defaultFontSize = (float) DocgenSetting::getVal('default_font_size', 10);
        $this->defaultFontColor = DocgenSetting::getVal('default_font_color', '#000000');
        $this->defaultTextAlign = DocgenSetting::getVal('default_text_align', 'L');
    }

    /**
     * Generate a PDF document by overlaying client data on template pages.
     *
     * @param DocgenTemplate $template
     * @param array $clientData  Associative array of field_name => value
     * @param string $outputPath Full path for the generated PDF
     * @return string The output file path
     */
    public function generate(DocgenTemplate $template, array $clientData, string $outputPath): string
    {
        // Use FPDI to import PDF pages and overlay text
        $pdf = new \setasign\Fpdi\Tcpdf\Fpdi();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetAutoPageBreak(false, 0);

        $pages = $template->activePages()->with('activeFieldMappings')->orderBy('sort_order')->get();

        foreach ($pages as $page) {
            $pdfPath = storage_path('app/' . $page->pdf_path);

            if (!file_exists($pdfPath)) {
                continue;
            }

            // Import the background PDF page
            $pageCount = $pdf->setSourceFile($pdfPath);
            $tplId = $pdf->importPage(1);
            $size = $pdf->getTemplateSize($tplId);

            // Determine orientation
            if ($page->orientation === 'landscape') {
                $pdf->AddPage('L', [$size['width'], $size['height']]);
            } else {
                $pdf->AddPage('P', [$size['width'], $size['height']]);
            }

            $pdf->useTemplate($tplId, 0, 0, $size['width'], $size['height']);

            // Overlay fields
            foreach ($page->activeFieldMappings as $field) {
                $value = $this->resolveFieldValue($field, $clientData);

                if ($value === null || $value === '') {
                    continue;
                }

                $this->renderField($pdf, $field, $value);
            }
        }

        // Ensure output directory exists
        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $pdf->Output($outputPath, 'F');

        return $outputPath;
    }

    /**
     * Resolve the value for a field from the client data array.
     */
    protected function resolveFieldValue($field, array $clientData): ?string
    {
        $fieldName = $field->field_name;

        // Check if value exists in client data
        if (isset($clientData[$fieldName])) {
            $value = $clientData[$fieldName];
        } elseif ($field->default_value) {
            $value = $field->default_value;
        } else {
            return null;
        }

        // Format based on field type
        switch ($field->field_type) {
            case 'date':
                $format = $field->date_format ?: 'd/m/Y';
                try {
                    $date = new \DateTime($value);
                    return $date->format($format);
                } catch (\Exception $e) {
                    return $value;
                }

            case 'checkbox':
                return $value ? 'X' : '';

            default:
                return (string) $value;
        }
    }

    /**
     * Render a field value onto the PDF at the specified position.
     */
    protected function renderField($pdf, $field, string $value): void
    {
        // Handle image/signature fields
        if (in_array($field->field_type, ['image', 'signature'])) {
            $this->renderImage($pdf, $field, $value);
            return;
        }

        // Set font
        $fontFamily = $field->font_family ?: $this->defaultFont;
        $fontSize = $field->font_size ?: $this->defaultFontSize;
        $fontStyle = $this->parseFontStyle($field->font_style);

        $pdf->SetFont($fontFamily, $fontStyle, $fontSize);

        // Set color
        $color = $field->font_color ?: $this->defaultFontColor;
        $rgb = $this->hexToRgb($color);
        $pdf->SetTextColor($rgb[0], $rgb[1], $rgb[2]);

        // Alignment
        $align = $this->parseAlignment($field->text_align ?: $this->defaultTextAlign);

        // Position and render
        $x = (float) $field->pos_x;
        $y = (float) $field->pos_y;
        $width = $field->width ? (float) $field->width : 0;
        $height = $field->height ? (float) $field->height : 0;

        if ($width > 0 && $height > 0) {
            // Use MultiCell for bounded area
            $pdf->SetXY($x, $y);
            $pdf->MultiCell($width, $height, $value, 0, $align, false, 1, $x, $y, true, 0, false, true, $height, 'T');
        } else {
            // Simple text at position
            $pdf->SetXY($x, $y);
            $pdf->Cell($width ?: 100, $fontSize * 0.4, $value, 0, 0, $align);
        }
    }

    /**
     * Render an image or signature field.
     */
    protected function renderImage($pdf, $field, string $value): void
    {
        $x = (float) $field->pos_x;
        $y = (float) $field->pos_y;
        $width = $field->width ? (float) $field->width : 30;
        $height = $field->height ? (float) $field->height : 15;

        // Check if it's a base64 image
        if (strpos($value, 'data:image') === 0 || strpos($value, 'base64,') !== false) {
            $imgData = $value;
            if (strpos($imgData, 'base64,') !== false) {
                $imgData = explode('base64,', $imgData)[1];
            }
            $imgData = base64_decode($imgData);

            // Save to temp file
            $tmpFile = tempnam(sys_get_temp_dir(), 'docgen_img_');
            file_put_contents($tmpFile, $imgData);

            try {
                $pdf->Image($tmpFile, $x, $y, $width, $height, '', '', '', false, 300, '', false, false, 0);
            } catch (\Exception $e) {
                // Skip if image fails
            }
            @unlink($tmpFile);
        } elseif (file_exists($value)) {
            // It's a file path
            try {
                $pdf->Image($value, $x, $y, $width, $height, '', '', '', false, 300, '', false, false, 0);
            } catch (\Exception $e) {
                // Skip if image fails
            }
        }
    }

    /**
     * Parse font style string to TCPDF style codes.
     */
    protected function parseFontStyle(?string $style): string
    {
        if (!$style) return '';

        $result = '';
        $style = strtolower($style);

        if (strpos($style, 'bold') !== false) $result .= 'B';
        if (strpos($style, 'italic') !== false) $result .= 'I';
        if (strpos($style, 'underline') !== false) $result .= 'U';

        return $result;
    }

    /**
     * Parse alignment string to TCPDF alignment code.
     */
    protected function parseAlignment(?string $align): string
    {
        switch (strtolower($align ?? 'left')) {
            case 'center': return 'C';
            case 'right': return 'R';
            case 'justify': return 'J';
            default: return 'L';
        }
    }

    /**
     * Convert hex color to RGB array.
     */
    protected function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }
}
