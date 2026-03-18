#!/usr/bin/env python3
"""
CIMS Master Input Sheet PDF Generator
Professional printable data-collection form for client onboarding.
"""

from reportlab.lib.pagesizes import A4
from reportlab.lib.units import mm, cm
from reportlab.lib.colors import HexColor, white, black, Color
from reportlab.pdfgen import canvas
from reportlab.lib.enums import TA_LEFT, TA_CENTER, TA_RIGHT
import os

# ── Constants ──
PAGE_W, PAGE_H = A4  # 210mm x 297mm
MARGIN_L = 15 * mm
MARGIN_R = 15 * mm
MARGIN_T = 15 * mm
MARGIN_B = 20 * mm
CONTENT_W = PAGE_W - MARGIN_L - MARGIN_R

# Colors
TEAL = HexColor('#00BCD4')
DARK_NAVY = HexColor('#1a1f36')
LIGHT_TEAL = HexColor('#E0F7FA')
MEDIUM_TEAL = HexColor('#B2EBF2')
LIGHT_GRAY = HexColor('#F5F5F5')
BORDER_GRAY = HexColor('#CCCCCC')
TEXT_DARK = HexColor('#333333')
WHITE = white

# Field dimensions
FIELD_H = 12 * mm  # Height for handwriting
FIELD_H_SMALL = 10 * mm
LABEL_SIZE = 7.5
FIELD_GAP = 2 * mm
ROW_GAP = 3 * mm

OUTPUT_PATH = "/var/lib/freelancer/projects/40170867/CIMS_Master_Input_Sheet.pdf"


class MasterInputSheetPDF:
    def __init__(self):
        self.c = canvas.Canvas(OUTPUT_PATH, pagesize=A4)
        self.c.setTitle("CIMS - Client Master Input Sheet")
        self.c.setAuthor("CIM Solutions")
        self.page_num = 0
        self.y = PAGE_H - MARGIN_T

    def new_page(self):
        if self.page_num > 0:
            self.c.showPage()
        self.page_num += 1
        self.y = PAGE_H - MARGIN_T

    def add_page_footer(self):
        """Add page number and footer line"""
        self.c.setStrokeColor(BORDER_GRAY)
        self.c.setLineWidth(0.5)
        self.c.line(MARGIN_L, MARGIN_B - 5 * mm, PAGE_W - MARGIN_R, MARGIN_B - 5 * mm)
        self.c.setFont("Helvetica", 7)
        self.c.setFillColor(HexColor('#999999'))
        self.c.drawString(MARGIN_L, MARGIN_B - 9 * mm, "CIM Solutions — Client Master Input Sheet")
        self.c.drawRightString(PAGE_W - MARGIN_R, MARGIN_B - 9 * mm, f"Page {self.page_num}")
        self.c.setFillColor(TEXT_DARK)

    def check_space(self, needed):
        """Check if enough space on page, start new page if not"""
        if self.y - needed < MARGIN_B:
            self.add_page_footer()
            self.new_page()
            return True
        return False

    # ── Drawing Helpers ──

    def draw_section_header(self, icon_text, title):
        """Draw a teal section header bar"""
        self.check_space(12 * mm)
        h = 9 * mm
        self.c.setFillColor(TEAL)
        self.c.roundRect(MARGIN_L, self.y - h, CONTENT_W, h, 2 * mm, fill=1, stroke=0)
        self.c.setFillColor(WHITE)
        self.c.setFont("Helvetica-Bold", 10)
        self.c.drawString(MARGIN_L + 5 * mm, self.y - h + 2.8 * mm, f"{icon_text}  {title}")
        self.c.setFillColor(TEXT_DARK)
        self.y -= h + 4 * mm

    def draw_field(self, x, width, label, hint=""):
        """Draw a labeled field box at current y position"""
        # Label
        self.c.setFont("Helvetica", LABEL_SIZE)
        self.c.setFillColor(DARK_NAVY)
        label_text = label
        if hint:
            label_text += f"  ({hint})"
        self.c.drawString(x + 1 * mm, self.y + 1 * mm, label_text)
        # Box
        self.c.setStrokeColor(BORDER_GRAY)
        self.c.setLineWidth(0.7)
        self.c.setFillColor(WHITE)
        box_y = self.y - FIELD_H
        self.c.rect(x, box_y, width, FIELD_H, fill=1, stroke=1)
        # Light baseline for writing
        self.c.setStrokeColor(HexColor('#E0E0E0'))
        self.c.setLineWidth(0.3)
        self.c.line(x + 2 * mm, box_y + 3 * mm, x + width - 2 * mm, box_y + 3 * mm)
        self.c.setFillColor(TEXT_DARK)
        self.c.setStrokeColor(BORDER_GRAY)

    def draw_field_row(self, fields, gap=3 * mm):
        """Draw a row of fields. fields = [(label, width_ratio, hint), ...]"""
        total_ratio = sum(f[1] for f in fields)
        usable_w = CONTENT_W - gap * (len(fields) - 1)
        x = MARGIN_L
        for label, ratio, *rest in fields:
            hint = rest[0] if rest else ""
            w = usable_w * ratio / total_ratio
            self.draw_field(x, w, label, hint)
            x += w + gap
        self.y -= FIELD_H + ROW_GAP + LABEL_SIZE + 2

    def draw_full_field(self, label, hint=""):
        """Draw a single full-width field"""
        self.draw_field(MARGIN_L, CONTENT_W, label, hint)
        self.y -= FIELD_H + ROW_GAP + LABEL_SIZE + 2

    def draw_checkbox_options(self, label, options):
        """Draw a label with checkbox options inline"""
        self.c.setFont("Helvetica", LABEL_SIZE)
        self.c.setFillColor(DARK_NAVY)
        self.c.drawString(MARGIN_L + 1 * mm, self.y + 1 * mm, label)
        box_y = self.y - FIELD_H
        self.c.setStrokeColor(BORDER_GRAY)
        self.c.setFillColor(WHITE)
        self.c.rect(MARGIN_L, box_y, CONTENT_W, FIELD_H, fill=1, stroke=1)
        # Draw checkboxes inside
        x = MARGIN_L + 5 * mm
        self.c.setFont("Helvetica", 8)
        self.c.setFillColor(TEXT_DARK)
        for opt in options:
            # Checkbox square
            cb_size = 3.5 * mm
            cb_y = box_y + (FIELD_H - cb_size) / 2
            self.c.setStrokeColor(DARK_NAVY)
            self.c.setLineWidth(0.8)
            self.c.rect(x, cb_y, cb_size, cb_size, fill=0, stroke=1)
            self.c.drawString(x + cb_size + 2 * mm, box_y + 3.5 * mm, opt)
            x += self.c.stringWidth(opt, "Helvetica", 8) + cb_size + 8 * mm
        self.c.setStrokeColor(BORDER_GRAY)
        self.c.setFillColor(TEXT_DARK)
        self.y -= FIELD_H + ROW_GAP + LABEL_SIZE + 2

    # ── Pages ──

    def draw_cover_page(self):
        self.new_page()

        # Top decorative bar
        self.c.setFillColor(DARK_NAVY)
        self.c.rect(0, PAGE_H - 85 * mm, PAGE_W, 85 * mm, fill=1, stroke=0)

        # Teal accent stripe
        self.c.setFillColor(TEAL)
        self.c.rect(0, PAGE_H - 88 * mm, PAGE_W, 3 * mm, fill=1, stroke=0)

        # Company name
        self.c.setFillColor(WHITE)
        self.c.setFont("Helvetica-Bold", 32)
        self.c.drawCentredString(PAGE_W / 2, PAGE_H - 35 * mm, "CIM SOLUTIONS")

        # Tagline
        self.c.setFont("Helvetica", 12)
        self.c.setFillColor(TEAL)
        self.c.drawCentredString(PAGE_W / 2, PAGE_H - 45 * mm, "Practice Management System")

        # Title
        self.c.setFillColor(WHITE)
        self.c.setFont("Helvetica-Bold", 22)
        self.c.drawCentredString(PAGE_W / 2, PAGE_H - 65 * mm, "CLIENT MASTER INPUT SHEET")

        # Subtitle
        self.c.setFont("Helvetica", 13)
        self.c.setFillColor(HexColor('#90CAF9'))
        self.c.drawCentredString(PAGE_W / 2, PAGE_H - 75 * mm, "Information Gathering Form")

        # Main content area
        self.y = PAGE_H - 105 * mm

        # Cover fields — large blocks
        cover_fields = [
            ("Client Name", ""),
            ("Client Code", "Auto-generated by system"),
        ]
        for label, hint in cover_fields:
            self.c.setFont("Helvetica-Bold", 10)
            self.c.setFillColor(DARK_NAVY)
            self.c.drawString(MARGIN_L + 25 * mm, self.y, label)
            self.y -= 3 * mm
            # Large box
            box_h = 14 * mm
            self.c.setStrokeColor(BORDER_GRAY)
            self.c.setLineWidth(0.8)
            self.c.setFillColor(WHITE)
            self.c.roundRect(MARGIN_L + 25 * mm, self.y - box_h, CONTENT_W - 50 * mm, box_h, 2 * mm, fill=1, stroke=1)
            if hint:
                self.c.setFont("Helvetica-Oblique", 7)
                self.c.setFillColor(HexColor('#999999'))
                self.c.drawString(MARGIN_L + 28 * mm, self.y - box_h + 3 * mm, hint)
            self.c.setFillColor(TEXT_DARK)
            self.y -= box_h + 8 * mm

        # Two columns: Date Prepared | Prepared By
        col_w = (CONTENT_W - 50 * mm - 5 * mm) / 2
        x1 = MARGIN_L + 25 * mm
        x2 = x1 + col_w + 5 * mm

        for x, label in [(x1, "Date Prepared"), (x2, "Prepared By")]:
            self.c.setFont("Helvetica-Bold", 10)
            self.c.setFillColor(DARK_NAVY)
            self.c.drawString(x, self.y, label)
        self.y -= 3 * mm
        box_h = 14 * mm
        for x in [x1, x2]:
            self.c.setStrokeColor(BORDER_GRAY)
            self.c.setFillColor(WHITE)
            self.c.roundRect(x, self.y - box_h, col_w, box_h, 2 * mm, fill=1, stroke=1)
        self.y -= box_h + 15 * mm

        # Footer note
        self.c.setFillColor(HexColor('#666666'))
        self.c.setFont("Helvetica-Oblique", 9)
        note = "This form is used to gather all client information for capture into the CIMS Practice Management System."
        self.c.drawCentredString(PAGE_W / 2, self.y, note)
        self.y -= 5 * mm
        note2 = "Please complete all applicable sections. Leave blank if not applicable."
        self.c.drawCentredString(PAGE_W / 2, self.y, note2)

        # Bottom decorative bar
        self.c.setFillColor(DARK_NAVY)
        self.c.rect(0, 0, PAGE_W, 12 * mm, fill=1, stroke=0)
        self.c.setFillColor(TEAL)
        self.c.rect(0, 12 * mm, PAGE_W, 2 * mm, fill=1, stroke=0)
        self.c.setFillColor(WHITE)
        self.c.setFont("Helvetica", 7)
        self.c.drawCentredString(PAGE_W / 2, 4 * mm, "CIM Solutions  |  Practice Management System  |  cimsolutions.co.za")

        self.add_page_footer()

    def draw_company_information(self):
        self.new_page()
        self.draw_section_header("01", "COMPANY INFORMATION")

        self.check_space(25 * mm)
        self.draw_full_field("Registered Company Name", "As per CIPC records")
        self.check_space(25 * mm)
        self.draw_field_row([("Client Code", 1), ("Date of Company Registration", 2, "DD/MM/YYYY")])
        self.check_space(25 * mm)
        self.draw_field_row([
            ("Company Reg No.", 3, "YYYY/NNNNNN/NN"),
            ("Company Type", 2),
            ("BizPortal No.", 2),
            ("Fin. Year End", 2, "Month"),
        ])
        self.check_space(25 * mm)
        self.draw_full_field("Trading Name")
        self.check_space(25 * mm)
        self.draw_field_row([
            ("Number of Directors", 1),
            ("Number of Shares", 1),
            ("Share Type", 1, "e.g. Ordinary / Non Par"),
        ])

    def draw_income_tax(self):
        self.check_space(35 * mm)
        self.draw_section_header("02", "INCOME TAX REGISTRATION")
        self.check_space(25 * mm)
        self.draw_field_row([
            ("Company Income Tax Number", 1),
            ("Date of IT Registration", 1, "DD/MM/YYYY"),
        ])

    def draw_payroll(self):
        self.check_space(70 * mm)
        self.draw_section_header("03", "PAYROLL REGISTRATION")
        self.check_space(25 * mm)
        self.draw_field_row([
            ("PAYE Number", 1),
            ("SDL Number", 1),
            ("UIF Number", 1),
            ("Date of Liability", 1, "DD/MM/YYYY"),
        ])
        self.check_space(25 * mm)
        self.draw_checkbox_options("Payroll Status", [
            "Active", "Dormant", "Suspended", "Deregistered", "Pending", "Ceased Trading"
        ])
        self.check_space(25 * mm)
        self.draw_field_row([
            ("EMP201 Status", 1),
            ("EMP501 Status", 1),
        ])
        self.check_space(25 * mm)
        self.draw_field_row([
            ("Dept of Labour Number", 1),
            ("WCA - COIDA Number", 1),
        ])

    def draw_vat(self):
        self.check_space(75 * mm)
        self.draw_section_header("04", "VAT REGISTRATION")
        self.check_space(25 * mm)
        self.draw_field_row([
            ("VAT Number", 1),
            ("Date of Registration", 1, "DD/MM/YYYY"),
        ])
        self.check_space(25 * mm)
        self.draw_checkbox_options("Return Cycle", [
            "Monthly", "Bi-Monthly", "4-Monthly", "6-Monthly", "Annually"
        ])
        self.check_space(25 * mm)
        self.draw_field_row([
            ("With Effect From", 1, "DD/MM/YYYY"),
        ])
        self.check_space(25 * mm)
        self.draw_checkbox_options("VAT Status", ["Active", "De-Registered", "Suspended"])
        self.check_space(25 * mm)
        self.draw_checkbox_options("VAT Basis", ["Invoice Basis (Standard)", "Payment Basis (SARS Approved)"])
        self.check_space(25 * mm)
        self.draw_field_row([
            ("Last VAT Return Date", 1, "DD/MM/YYYY"),
            ("VAT Cycle", 1),
        ])

    def draw_contact(self):
        self.check_space(65 * mm)
        self.draw_section_header("05", "CONTACT INFORMATION")
        self.check_space(25 * mm)
        self.draw_field_row([
            ("Business Phone", 1),
            ("Direct", 1),
            ("Mobile", 1),
            ("WhatsApp", 1),
        ])
        self.check_space(25 * mm)
        self.draw_field_row([
            ("Email Address (Compliance)", 1),
            ("Email Address (Admin)", 1),
        ])
        self.check_space(25 * mm)
        self.draw_full_field("Website", "https://")

    def draw_single_address_block(self, address_num):
        """Draw one address block"""
        self.check_space(95 * mm)

        # Address type sub-header
        self.c.setFont("Helvetica-Bold", 8.5)
        self.c.setFillColor(DARK_NAVY)
        self.c.drawString(MARGIN_L + 1 * mm, self.y, f"Address {address_num}")
        self.y -= 5 * mm

        self.draw_checkbox_options("Address Type", ["Registered", "Physical", "Postal", "Other"])

        self.check_space(25 * mm)
        self.draw_field_row([("Unit No.", 1), ("Complex / Building Name", 3)])
        self.check_space(25 * mm)
        self.draw_field_row([("Street No.", 1), ("Street Name", 3)])
        self.check_space(25 * mm)
        self.draw_field_row([("Suburb", 1), ("City", 1)])
        self.check_space(25 * mm)
        self.draw_field_row([("Postal Code", 1), ("Province", 2)])
        self.check_space(25 * mm)
        self.draw_full_field("Country")

    def draw_addresses(self):
        self.check_space(30 * mm)
        self.draw_section_header("06", "ADDRESS DETAILS")
        for i in range(1, 4):
            self.draw_single_address_block(i)
            if i < 3:
                self.check_space(5 * mm)
                # Separator line
                self.c.setStrokeColor(MEDIUM_TEAL)
                self.c.setLineWidth(0.5)
                self.c.setDash(3, 2)
                self.c.line(MARGIN_L + 10 * mm, self.y, PAGE_W - MARGIN_R - 10 * mm, self.y)
                self.c.setDash()
                self.y -= 4 * mm

    def draw_directors_table(self):
        self.check_space(30 * mm)
        self.draw_section_header("07", "DIRECTORS / SHAREHOLDERS")

        # Table headers
        cols = [
            ("Full Name", 42 * mm),
            ("ID Number", 28 * mm),
            ("Type", 20 * mm),
            ("Status", 16 * mm),
            ("Date Engaged", 22 * mm),
            ("Date Resigned", 22 * mm),
            ("Shares", 16 * mm),
            ("% Share", 14 * mm),
        ]
        header_h = 8 * mm
        row_h = 12 * mm
        total_w = sum(c[1] for c in cols)

        # Draw header
        self.check_space(header_h + row_h * 6 + 15 * mm)
        x = MARGIN_L
        self.c.setFillColor(DARK_NAVY)
        self.c.rect(x, self.y - header_h, total_w, header_h, fill=1, stroke=0)
        self.c.setFillColor(WHITE)
        self.c.setFont("Helvetica-Bold", 6.5)
        cx = x
        for name, w in cols:
            self.c.drawCentredString(cx + w / 2, self.y - header_h + 2.5 * mm, name)
            cx += w
        self.y -= header_h
        self.c.setFillColor(TEXT_DARK)

        # Draw 5 empty rows
        for row in range(5):
            fill = LIGHT_GRAY if row % 2 == 0 else WHITE
            cx = x
            for _, w in cols:
                self.c.setFillColor(fill)
                self.c.setStrokeColor(BORDER_GRAY)
                self.c.setLineWidth(0.5)
                self.c.rect(cx, self.y - row_h, w, row_h, fill=1, stroke=1)
                cx += w
            # Row number
            self.c.setFillColor(HexColor('#BBBBBB'))
            self.c.setFont("Helvetica", 7)
            self.c.drawString(x + 1.5 * mm, self.y - row_h + 4 * mm, str(row + 1))
            self.y -= row_h

        self.c.setFillColor(TEXT_DARK)
        self.y -= 3 * mm

        # Totals row
        self.c.setFont("Helvetica-Bold", 9)
        self.c.setFillColor(DARK_NAVY)
        totals_y = self.y
        self.c.drawString(MARGIN_L, totals_y, "Totals:")

        # Total Shares box
        tx = MARGIN_L + 100 * mm
        self.c.setFont("Helvetica", 8)
        self.c.drawString(tx, totals_y, "Total Shares:")
        self.c.setStrokeColor(BORDER_GRAY)
        self.c.setFillColor(WHITE)
        self.c.rect(tx + 25 * mm, totals_y - 2 * mm, 25 * mm, 8 * mm, fill=1, stroke=1)

        # Total % box
        tx2 = tx + 60 * mm
        self.c.setFillColor(DARK_NAVY)
        self.c.drawString(tx2, totals_y, "Total %:")
        self.c.setFillColor(WHITE)
        self.c.rect(tx2 + 18 * mm, totals_y - 2 * mm, 20 * mm, 8 * mm, fill=1, stroke=1)

        self.c.setFillColor(TEXT_DARK)
        self.y -= 15 * mm

    def draw_sars_efiling(self):
        self.check_space(50 * mm)
        self.draw_section_header("08", "SARS E-FILING LOGIN DETAILS")
        self.check_space(25 * mm)
        self.draw_field_row([("SARS Login", 1), ("SARS Password", 1)])
        self.check_space(25 * mm)
        self.draw_field_row([("Mobile for SARS OTP", 1), ("Email for SARS OTP", 1)])

    def draw_single_bank_block(self, bank_num):
        """Draw one banking details block"""
        self.check_space(25 * mm)
        self.c.setFont("Helvetica-Bold", 8.5)
        self.c.setFillColor(DARK_NAVY)
        self.c.drawString(MARGIN_L + 1 * mm, self.y, f"Bank Account {bank_num}")
        self.y -= 5 * mm

        self.check_space(25 * mm)
        self.draw_full_field("Account Holder")
        self.check_space(25 * mm)
        self.draw_field_row([("Bank Name", 2), ("Account Number", 2), ("Account Type", 1)])
        self.check_space(25 * mm)
        self.draw_field_row([("Branch Name", 2), ("Branch Code", 1), ("Swift Code", 1)])
        self.check_space(25 * mm)
        self.draw_field_row([
            ("Date Account Opened", 1, "DD/MM/YYYY"),
            ("Account Status", 1),
            ("Statement Frequency", 1),
        ])
        self.check_space(25 * mm)
        self.draw_field_row([
            ("Statement Cut Off Date", 1, "DD/MM/YYYY"),
        ])
        self.check_space(25 * mm)
        self.draw_checkbox_options("Default Bank", ["Yes", "No"])

    def draw_banking(self):
        self.check_space(30 * mm)
        self.draw_section_header("09", "BANKING DETAILS")
        for i in range(1, 3):
            self.draw_single_bank_block(i)
            if i < 2:
                self.check_space(5 * mm)
                self.c.setStrokeColor(MEDIUM_TEAL)
                self.c.setLineWidth(0.5)
                self.c.setDash(3, 2)
                self.c.line(MARGIN_L + 10 * mm, self.y, PAGE_W - MARGIN_R - 10 * mm, self.y)
                self.c.setDash()
                self.y -= 4 * mm

    def draw_bee(self):
        self.check_space(65 * mm)
        self.draw_section_header("10", "BEE INFORMATION")
        self.check_space(25 * mm)
        self.draw_field_row([
            ("BEE Level", 1, "1-8 or Non-Compliant"),
            ("BEE Certificate Expiry Date", 1, "DD/MM/YYYY"),
        ])
        self.check_space(25 * mm)
        self.draw_field_row([
            ("BEE Certificate Number", 1),
            ("BEE Verification Agency", 1),
        ])
        self.check_space(25 * mm)
        self.draw_field_row([
            ("Black Ownership %", 1),
            ("Black Women Ownership %", 1),
        ])

    def draw_general(self):
        self.check_space(70 * mm)
        self.draw_section_header("11", "GENERAL")
        self.check_space(25 * mm)
        self.draw_field_row([("Client Status", 1), ("Client Category", 1)])

        # Services — large multi-line box
        self.check_space(50 * mm)
        self.c.setFont("Helvetica", LABEL_SIZE)
        self.c.setFillColor(DARK_NAVY)
        self.c.drawString(MARGIN_L + 1 * mm, self.y + 1 * mm, "Services Required")
        box_h = 35 * mm
        self.c.setStrokeColor(BORDER_GRAY)
        self.c.setFillColor(WHITE)
        self.c.setLineWidth(0.7)
        self.c.rect(MARGIN_L, self.y - box_h, CONTENT_W, box_h, fill=1, stroke=1)
        # Writing lines
        self.c.setStrokeColor(HexColor('#E0E0E0'))
        self.c.setLineWidth(0.3)
        for i in range(1, 5):
            ly = self.y - box_h + i * (box_h / 5)
            self.c.line(MARGIN_L + 3 * mm, ly, MARGIN_L + CONTENT_W - 3 * mm, ly)
        self.c.setFillColor(TEXT_DARK)
        self.y -= box_h + ROW_GAP + LABEL_SIZE + 2

    def draw_document_checklist(self):
        self.add_page_footer()
        self.new_page()

        # Section header
        self.draw_section_header(">>", "REQUIRED DOCUMENTS CHECKLIST")

        documents = [
            "COR 14.3 Registration Certificate",
            "Company Income Tax Registration Notice",
            "Payroll Notice of Registration (EMP201)",
            "VAT Registration Certificate",
            "Confirmation of Banking Letter (per bank)",
            "Certified ID Copy — Director 1",
            "Certified ID Copy — Director 2",
            "Certified ID Copy — Director 3",
            "Proof of Address — Director 1",
            "Proof of Address — Director 2",
            "Proof of Address — Director 3",
            "Passport Copy (if foreign national)",
            "Signature Specimen — Director 1",
            "Signature Specimen — Director 2",
            "Signature Specimen — Director 3",
            "Profile Photo — Director 1",
            "Profile Photo — Director 2",
            "Profile Photo — Director 3",
            "BEE Certificate",
            "Tax Clearance Certificate",
            "CIPC Annual Return",
            "Share Certificate(s)",
            "",  # blank
            "",  # blank
        ]

        # Table columns: #(10), Description(80), Received(18), Date(30), Notes(42)
        col_widths = [10 * mm, 78 * mm, 18 * mm, 30 * mm, CONTENT_W - 136 * mm]
        col_headers = ["#", "Document Description", "Recv'd", "Date Received", "Notes"]
        header_h = 7 * mm
        row_h = 8 * mm

        # Header row
        x = MARGIN_L
        self.c.setFillColor(DARK_NAVY)
        self.c.rect(x, self.y - header_h, CONTENT_W, header_h, fill=1, stroke=0)
        self.c.setFillColor(WHITE)
        self.c.setFont("Helvetica-Bold", 7)
        cx = x
        for i, (hdr, w) in enumerate(zip(col_headers, col_widths)):
            self.c.drawCentredString(cx + w / 2, self.y - header_h + 2 * mm, hdr)
            cx += w
        self.y -= header_h
        self.c.setFillColor(TEXT_DARK)

        # Rows
        for idx, doc in enumerate(documents):
            if self.y - row_h < MARGIN_B:
                self.add_page_footer()
                self.new_page()
                # Re-draw header on new page
                x = MARGIN_L
                self.c.setFillColor(DARK_NAVY)
                self.c.rect(x, self.y - header_h, CONTENT_W, header_h, fill=1, stroke=0)
                self.c.setFillColor(WHITE)
                self.c.setFont("Helvetica-Bold", 7)
                cx = x
                for hdr, w in zip(col_headers, col_widths):
                    self.c.drawCentredString(cx + w / 2, self.y - header_h + 2 * mm, hdr)
                    cx += w
                self.y -= header_h
                self.c.setFillColor(TEXT_DARK)

            fill = LIGHT_GRAY if idx % 2 == 0 else WHITE
            cx = x
            for i, w in enumerate(col_widths):
                self.c.setFillColor(fill)
                self.c.setStrokeColor(BORDER_GRAY)
                self.c.setLineWidth(0.4)
                self.c.rect(cx, self.y - row_h, w, row_h, fill=1, stroke=1)
                cx += w

            # Cell content
            self.c.setFillColor(TEXT_DARK)
            self.c.setFont("Helvetica", 7)
            # #
            self.c.drawCentredString(x + col_widths[0] / 2, self.y - row_h + 2.5 * mm, str(idx + 1))
            # Description
            self.c.drawString(x + col_widths[0] + 2 * mm, self.y - row_h + 2.5 * mm, doc)
            # Received checkbox
            cb_x = x + col_widths[0] + col_widths[1] + (col_widths[2] - 3.5 * mm) / 2
            cb_y = self.y - row_h + (row_h - 3.5 * mm) / 2
            self.c.setStrokeColor(DARK_NAVY)
            self.c.setLineWidth(0.7)
            self.c.rect(cb_x, cb_y, 3.5 * mm, 3.5 * mm, fill=0, stroke=1)

            self.y -= row_h

        self.c.setFillColor(TEXT_DARK)
        self.y -= 10 * mm

        # Sign-off section
        self.check_space(25 * mm)
        sign_y = self.y
        self.c.setFont("Helvetica", 8)
        self.c.setFillColor(DARK_NAVY)

        # Row 1
        self.c.drawString(MARGIN_L, sign_y, "Prepared by:")
        self.c.setStrokeColor(BORDER_GRAY)
        self.c.line(MARGIN_L + 22 * mm, sign_y - 1, MARGIN_L + 75 * mm, sign_y - 1)
        self.c.drawString(MARGIN_L + 80 * mm, sign_y, "Date:")
        self.c.line(MARGIN_L + 90 * mm, sign_y - 1, MARGIN_L + 125 * mm, sign_y - 1)

        sign_y -= 10 * mm
        self.c.drawString(MARGIN_L, sign_y, "Reviewed by:")
        self.c.line(MARGIN_L + 22 * mm, sign_y - 1, MARGIN_L + 75 * mm, sign_y - 1)
        self.c.drawString(MARGIN_L + 80 * mm, sign_y, "Date:")
        self.c.line(MARGIN_L + 90 * mm, sign_y - 1, MARGIN_L + 125 * mm, sign_y - 1)

    def generate(self):
        # Cover page
        self.draw_cover_page()

        # Section 1 — Company Information
        self.draw_company_information()

        # Section 2 — Income Tax
        self.draw_income_tax()

        # Section 3 — Payroll
        self.draw_payroll()

        # Section 4 — VAT
        self.draw_vat()
        self.add_page_footer()

        # Section 5 — Contact
        self.draw_contact()

        # Section 6 — Address Details
        self.draw_addresses()
        self.add_page_footer()

        # Section 7 — Directors
        self.draw_directors_table()

        # Section 8 — SARS E-Filing
        self.draw_sars_efiling()
        self.add_page_footer()

        # Section 9 — Banking
        self.draw_banking()
        self.add_page_footer()

        # Section 10 — BEE
        self.draw_bee()

        # Section 11 — General
        self.draw_general()
        self.add_page_footer()

        # Document Checklist (last page)
        self.draw_document_checklist()
        self.add_page_footer()

        self.c.save()
        print(f"PDF generated: {OUTPUT_PATH}")
        print(f"Total pages: {self.page_num}")


if __name__ == "__main__":
    os.makedirs(os.path.dirname(OUTPUT_PATH), exist_ok=True)
    pdf = MasterInputSheetPDF()
    pdf.generate()
