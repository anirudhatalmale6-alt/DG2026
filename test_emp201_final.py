"""
Playwright final test - capture all 5 EMP201 form screenshots.
Login quirk: URL stays on /login but session is established via cookies.
"""
from playwright.sync_api import sync_playwright
import time

OUTPUT_DIR = "/var/lib/freelancer/projects/40170867"

def run():
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        context = browser.new_context(
            viewport={"width": 1280, "height": 720},
            ignore_https_errors=True,
        )
        page = context.new_page()

        # Step 1: Login
        print("[1] Navigating to login page...")
        page.goto("https://smartweigh.co.za/login", wait_until="networkidle", timeout=30000)
        time.sleep(2)

        print("[2] Logging in...")
        page.fill("#email", "admin@atpservices.co.za")
        page.fill("#password", "Sm@rtW31gh2025!")
        time.sleep(0.5)
        page.click("#loginSubmitButton")
        time.sleep(8)  # Wait for AJAX login to complete
        print(f"    URL after login: {page.url}")

        # Step 3: Navigate directly to EMP201 create
        print("[3] Navigating to EMP201 create page...")
        page.goto("https://smartweigh.co.za/cims/emp201/create", wait_until="networkidle", timeout=30000)
        time.sleep(3)
        current_url = page.url
        print(f"    URL: {current_url}")

        if "/login" in current_url:
            print("    Redirected to login - session not established. Aborting.")
            page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_top.png", full_page=False)
            browser.close()
            return

        # Check for PHP errors
        page_content = page.content()
        has_error = False
        for indicator in ["Fatal error", "Parse error", "Whoops", "Stack trace", "ErrorException", "Undefined property"]:
            if indicator in page_content:
                print(f"    ERROR: Found '{indicator}' on the page!")
                has_error = True

        if has_error:
            print("    PHP error detected! Capturing error screenshot...")
            page.evaluate("window.scrollTo(0, 0)")
            time.sleep(1)
            page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_top.png", full_page=False)
            body_text = page.inner_text("body")
            print(f"    Error details:\n{body_text[:2000]}")
            browser.close()
            return

        page_height = page.evaluate("document.body.scrollHeight")
        print(f"    Page loaded successfully. Height: {page_height}px, Title: {page.title()}")

        # Find section positions via JS
        sections = page.evaluate("""
            () => {
                function findSection(keywords) {
                    const els = document.querySelectorAll('h1, h2, h3, h4, h5, h6, .card-header, .section-header, legend, div, span, label, th, td');
                    for (const el of els) {
                        const text = (el.innerText || el.textContent || '').trim();
                        if (text.length > 200) continue;
                        for (const kw of keywords) {
                            if (text.toLowerCase().includes(kw.toLowerCase()) && text.length < 100) {
                                return Math.max(0, el.getBoundingClientRect().top + window.pageYOffset - 50);
                            }
                        }
                    }
                    return null;
                }
                return {
                    demographics: findSection(['Demographics', 'Employer Details']),
                    financials: findSection(['Financials', 'Payroll Tax Calculation', 'ETI Indicator']),
                    part4: findSection(['Voluntary Disclosure', 'Tax Practitioner Details']),
                    upload: findSection(['Upload Documents']),
                    pageHeight: document.body.scrollHeight,
                };
            }
        """)
        print(f"    Section positions: {sections}")

        # ============ SCREENSHOT 1: Top section ============
        print("\n[6] Taking screenshot: Top section (SARS header + Part 1 client selection)...")
        page.evaluate("window.scrollTo(0, 0)")
        time.sleep(1)
        page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_top.png", full_page=False)
        print(f"    Saved: {OUTPUT_DIR}/emp201_sars_form_top.png")

        # ============ SCREENSHOT 2: Demographics ============
        print("[7] Taking screenshot: Demographics section...")
        y = sections.get('demographics')
        if y is not None:
            page.evaluate(f"window.scrollTo(0, {int(y)})")
        else:
            page.evaluate("window.scrollTo(0, 700)")
            print("    (fallback scroll to 700px)")
        time.sleep(1)
        page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_demographics.png", full_page=False)
        print(f"    Saved: {OUTPUT_DIR}/emp201_sars_form_demographics.png")

        # ============ SCREENSHOT 3: Financials ============
        print("[8] Taking screenshot: Financials section...")
        y = sections.get('financials')
        if y is not None:
            page.evaluate(f"window.scrollTo(0, {int(y)})")
        else:
            page.evaluate("window.scrollTo(0, 1400)")
            print("    (fallback scroll to 1400px)")
        time.sleep(1)
        page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_financials.png", full_page=False)
        print(f"    Saved: {OUTPUT_DIR}/emp201_sars_form_financials.png")

        # ============ SCREENSHOT 4: Part 4 ============
        print("[9] Taking screenshot: Part 4 (VDP, Tax Practitioner, Notes)...")
        y = sections.get('part4')
        if y is not None:
            page.evaluate(f"window.scrollTo(0, {int(y)})")
        else:
            page.evaluate("window.scrollTo(0, 2100)")
            print("    (fallback scroll to 2100px)")
        time.sleep(1)
        page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_part4.png", full_page=False)
        print(f"    Saved: {OUTPUT_DIR}/emp201_sars_form_part4.png")

        # ============ SCREENSHOT 5: Upload Documents ============
        print("[10] Taking screenshot: Upload Documents section...")
        y = sections.get('upload')
        if y is not None:
            page.evaluate(f"window.scrollTo(0, {int(y)})")
        else:
            page.evaluate(f"window.scrollTo(0, {sections['pageHeight'] - 720})")
            print("    (fallback scroll to bottom)")
        time.sleep(1)
        page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_uploads.png", full_page=False)
        print(f"    Saved: {OUTPUT_DIR}/emp201_sars_form_uploads.png")

        # Summary
        body_text = page.inner_text("body")
        print(f"\n--- Page title: {page.title()} ---")
        print(f"--- Body text (first 3000 chars) ---")
        print(body_text[:3000])
        print("--- End ---")

        browser.close()

        print("\n=== ALL 5 SCREENSHOTS CAPTURED SUCCESSFULLY ===")
        print(f"1. {OUTPUT_DIR}/emp201_sars_form_top.png")
        print(f"2. {OUTPUT_DIR}/emp201_sars_form_demographics.png")
        print(f"3. {OUTPUT_DIR}/emp201_sars_form_financials.png")
        print(f"4. {OUTPUT_DIR}/emp201_sars_form_part4.png")
        print(f"5. {OUTPUT_DIR}/emp201_sars_form_uploads.png")

if __name__ == "__main__":
    run()
