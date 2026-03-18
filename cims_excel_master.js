/* =============================================================================
   CIMS EXCEL MASTER — Reusable Excel Export Component
   =============================================================================
   Location: public/modules/cimscore/js/cims_excel_master.js
   Requires: ExcelJS (cdn), FileSaver.js (cdn)

   USAGE:
   cimsExcelExport({
       title:      'CUSTOMER AGED ANALYSIS',          // Report title
       subtitle:   'Outstanding Balances by Aging Bucket', // Optional subtitle
       company:    'My Company (Pty) Ltd',             // Company name
       date:       'Thursday, 12 March 2026',          // Date string to display
       filename:   'Customer_Aged_Analysis_2026-03-12', // Filename (no .xlsx)
       headers:    ['Client Code','Client Name','Current','30 Days','60 Days','90+ Days','Total'],
       headerAlign:['left','left','right','right','right','right','right'],
       colWidths:  [16, 32, 16, 16, 16, 16, 18],      // Optional column widths
       rows: [
           { cells: ['ACC100','Acme Corp',1500.00,0,0,0,1500.00], styles: { 0:{font:'teal'}, 6:{font:'bold'} } },
           ...
       ],
       totals:     { label: 'GRAND TOTAL (5 clients)', values: [15000.00,5000.00,2000.00,1000.00,23000.00] },
       agingCols:  { amber: 3, red: 4, darkRed: 5 }   // Optional: column indices for aging color
   });
   ============================================================================= */

var cimsExcelExport = (function() {
    'use strict';

    // SmartWeigh brand colours (ARGB format for ExcelJS)
    var COLORS = {
        dark:       'FF0D3D56',
        teal:       'FF17A2B8',
        lightBg:    'FFF0FAFB',
        stripe:     'FFFAFBFC',
        white:      'FFFFFFFF',
        text:       'FF333333',
        border:     'FFDDE4EC',
        borderDark: 'FF0A2E40',
        amber:      'FF856404',
        red:        'FFC0392B',
        darkRed:    'FF721C24',
        muted:      'FF999999'
    };

    // Styles
    var darkBg     = { type: 'pattern', pattern: 'solid', fgColor: { argb: COLORS.dark } };
    var tealBg     = { type: 'pattern', pattern: 'solid', fgColor: { argb: COLORS.teal } };
    var lightBg    = { type: 'pattern', pattern: 'solid', fgColor: { argb: COLORS.lightBg } };
    var stripeBg   = { type: 'pattern', pattern: 'solid', fgColor: { argb: COLORS.stripe } };

    var whiteBold  = { name: 'Calibri', size: 11, bold: true, color: { argb: COLORS.white } };
    var normalFont = { name: 'Calibri', size: 11, color: { argb: COLORS.text } };
    var boldFont   = { name: 'Calibri', size: 11, bold: true, color: { argb: COLORS.text } };
    var tealFont   = { name: 'Calibri', size: 11, bold: true, color: { argb: COLORS.teal } };
    var darkBold   = { name: 'Calibri', size: 11, bold: true, color: { argb: COLORS.dark } };

    var thinBorder = {
        top:    { style: 'thin', color: { argb: COLORS.border } },
        left:   { style: 'thin', color: { argb: COLORS.border } },
        bottom: { style: 'thin', color: { argb: COLORS.border } },
        right:  { style: 'thin', color: { argb: COLORS.border } }
    };
    var darkBorder = {
        top:    { style: 'thin', color: { argb: COLORS.borderDark } },
        left:   { style: 'thin', color: { argb: COLORS.borderDark } },
        bottom: { style: 'thin', color: { argb: COLORS.borderDark } },
        right:  { style: 'thin', color: { argb: COLORS.borderDark } }
    };
    var totalBorder = {
        top:    { style: 'medium', color: { argb: COLORS.teal } },
        left:   { style: 'thin', color: { argb: COLORS.borderDark } },
        bottom: { style: 'medium', color: { argb: COLORS.teal } },
        right:  { style: 'thin', color: { argb: COLORS.borderDark } }
    };

    // Number format: space as thousands separator, period decimal, 2 places
    var NUM_FMT = '#\u00A0##0.00';

    function applyFillToRow(row, fill, colCount) {
        for (var i = 2; i <= colCount; i++) {
            row.getCell(i).fill = fill;
        }
    }

    function generate(config) {
        var title       = config.title || 'Report';
        var subtitle    = config.subtitle || '';
        var company     = config.company || '';
        var dateStr     = config.date || '';
        var filename    = config.filename || 'CIMS_Export';
        var headers     = config.headers || [];
        var headerAlign = config.headerAlign || [];
        var colWidths   = config.colWidths || [];
        var rows        = config.rows || [];
        var totals      = config.totals || null;
        var agingCols   = config.agingCols || {};
        var colCount    = headers.length;

        var wb = new ExcelJS.Workbook();
        wb.creator = company;
        var ws = wb.addWorksheet(title.substring(0, 31), {
            pageSetup: { orientation: 'landscape', fitToPage: true, fitToWidth: 1 }
        });

        // Column widths
        var cols = [];
        for (var i = 0; i < colCount; i++) {
            cols.push({ width: colWidths[i] || 16 });
        }
        ws.columns = cols;

        var lastCol = String.fromCharCode(64 + colCount); // A=65, so 7 cols = G

        // --- Row 1: Company Name ---
        ws.mergeCells('A1:' + lastCol + '1');
        var r1 = ws.getRow(1);
        r1.getCell(1).value = company;
        r1.getCell(1).font = { name: 'Calibri', size: 16, bold: true, color: { argb: COLORS.white } };
        r1.getCell(1).fill = darkBg;
        r1.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
        r1.height = 32;
        applyFillToRow(r1, darkBg, colCount);

        // --- Row 2: Report Title ---
        ws.mergeCells('A2:' + lastCol + '2');
        var r2 = ws.getRow(2);
        r2.getCell(1).value = title;
        r2.getCell(1).font = { name: 'Calibri', size: 13, bold: true, color: { argb: COLORS.white } };
        r2.getCell(1).fill = tealBg;
        r2.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
        r2.height = 26;
        applyFillToRow(r2, tealBg, colCount);

        // --- Row 3: Date / Subtitle ---
        var row3Text = dateStr;
        if (subtitle) row3Text = subtitle + '  |  ' + dateStr;
        ws.mergeCells('A3:' + lastCol + '3');
        var r3 = ws.getRow(3);
        r3.getCell(1).value = row3Text;
        r3.getCell(1).font = { name: 'Calibri', size: 11, italic: true, color: { argb: COLORS.dark } };
        r3.getCell(1).fill = lightBg;
        r3.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
        r3.height = 24;
        applyFillToRow(r3, lightBg, colCount);

        // --- Row 4: Spacer ---
        ws.getRow(4).height = 8;

        // --- Row 5: Column Headers ---
        var hRow = ws.getRow(5);
        hRow.height = 28;
        for (var h = 0; h < colCount; h++) {
            var cell = hRow.getCell(h + 1);
            cell.value = headers[h];
            cell.font = whiteBold;
            cell.fill = darkBg;
            cell.alignment = { horizontal: (headerAlign[h] || 'left'), vertical: 'middle' };
            cell.border = darkBorder;
        }

        // --- Data Rows ---
        var rowIdx = 6;
        for (var i = 0; i < rows.length; i++) {
            var rowData = rows[i];
            var cells = rowData.cells || rowData;
            var styles = rowData.styles || {};
            var dRow = ws.getRow(rowIdx);
            var isEven = (i % 2 === 0);

            for (var col = 0; col < colCount; col++) {
                var cell = dRow.getCell(col + 1);
                var val = cells[col];
                cell.value = val;
                cell.border = thinBorder;

                var align = headerAlign[col] || 'left';
                cell.alignment = { horizontal: align, vertical: 'middle' };

                // Number formatting for numeric cells
                if (typeof val === 'number') {
                    cell.numFmt = NUM_FMT;
                    cell.font = normalFont;
                } else {
                    cell.font = normalFont;
                }

                // Stripe background
                if (isEven) cell.fill = stripeBg;

                // Per-cell style overrides
                if (styles[col]) {
                    var s = styles[col];
                    if (s.font === 'teal')    cell.font = tealFont;
                    if (s.font === 'bold')    cell.font = darkBold;
                    if (s.font === 'normal')  cell.font = normalFont;
                    if (s.font === 'boldText') cell.font = boldFont;
                }
            }

            // Aging color coding
            if (agingCols.amber !== undefined && typeof cells[agingCols.amber] === 'number' && cells[agingCols.amber] > 0) {
                dRow.getCell(agingCols.amber + 1).font = { name: 'Calibri', size: 11, color: { argb: COLORS.amber } };
            }
            if (agingCols.red !== undefined && typeof cells[agingCols.red] === 'number' && cells[agingCols.red] > 0) {
                dRow.getCell(agingCols.red + 1).font = { name: 'Calibri', size: 11, color: { argb: COLORS.red } };
            }
            if (agingCols.darkRed !== undefined && typeof cells[agingCols.darkRed] === 'number' && cells[agingCols.darkRed] > 0) {
                dRow.getCell(agingCols.darkRed + 1).font = { name: 'Calibri', size: 11, bold: true, color: { argb: COLORS.darkRed } };
            }

            dRow.height = 22;
            rowIdx++;
        }

        // --- Grand Total Row ---
        if (totals) {
            rowIdx++; // empty spacer row
            var gRow = ws.getRow(rowIdx);
            gRow.height = 30;

            // First cells are label
            var labelCols = colCount - totals.values.length;
            if (labelCols > 1) {
                gRow.getCell(1).value = '';
                gRow.getCell(labelCols).value = totals.label || 'GRAND TOTAL';
                gRow.getCell(labelCols).alignment = { horizontal: 'right', vertical: 'middle' };
            } else {
                gRow.getCell(1).value = totals.label || 'GRAND TOTAL';
            }

            // Total values
            for (var t = 0; t < totals.values.length; t++) {
                var tCell = gRow.getCell(labelCols + t + 1);
                tCell.value = totals.values[t];
                tCell.numFmt = NUM_FMT;
                tCell.alignment = { horizontal: 'right', vertical: 'middle' };
            }

            // Style all cells in total row
            for (var col = 1; col <= colCount; col++) {
                var cell = gRow.getCell(col);
                cell.font = { name: 'Calibri', size: 12, bold: true, color: { argb: COLORS.white } };
                cell.fill = darkBg;
                cell.border = totalBorder;
            }
        }

        // --- Footer Row ---
        rowIdx += 2;
        ws.mergeCells('A' + rowIdx + ':' + lastCol + rowIdx);
        var fRow = ws.getRow(rowIdx);
        var now = new Date();
        var genDate = now.getDate() + '/' + (now.getMonth() + 1 < 10 ? '0' : '') + (now.getMonth() + 1) + '/' + now.getFullYear();
        fRow.getCell(1).value = 'Generated: ' + genDate + '  |  ' + company + '  |  ' + title;
        fRow.getCell(1).font = { name: 'Calibri', size: 9, italic: true, color: { argb: COLORS.muted } };
        fRow.getCell(1).alignment = { horizontal: 'center' };

        // Generate buffer and show save/open dialog
        wb.xlsx.writeBuffer().then(function(buffer) {
            var blob = new Blob([buffer], {
                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            });
            var blobUrl = URL.createObjectURL(blob);
            var fullFilename = filename + '.xlsx';

            // Show SweetAlert with Save + Open options
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Excel Report Ready',
                    html: '<div style="font-family:Poppins,sans-serif;font-size:14px;">' +
                          '<p style="margin-bottom:12px;color:#0d3d56;"><strong>' + fullFilename + '</strong></p>' +
                          '<p style="color:#666;">Choose an option below:</p>' +
                          '</div>',
                    showDenyButton: true,
                    confirmButtonText: '<i class="fa fa-download" style="margin-right:6px;"></i>Download',
                    denyButtonText: '<i class="fa fa-file-excel" style="margin-right:6px;"></i>Open in Excel',
                    confirmButtonColor: '#17A2B8',
                    denyButtonColor: '#0d3d56',
                    reverseButtons: false,
                    allowOutsideClick: true
                }).then(function(result) {
                    if (result.isConfirmed) {
                        saveAs(blob, fullFilename);
                    } else if (result.isDenied) {
                        // Open in new tab — triggers download/open in Excel
                        var a = document.createElement('a');
                        a.href = blobUrl;
                        a.target = '_blank';
                        a.download = fullFilename;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                    }
                });
            } else {
                // Fallback if no SweetAlert — just save directly
                saveAs(blob, fullFilename);
            }
        });
    }

    return generate;
})();
