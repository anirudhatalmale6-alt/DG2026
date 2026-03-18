"""
Playwright test script to capture screenshots of the EMP201 form page.
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

        # Step 1: Navigate to login page
        print("[1] Navigating to login page...")
        page.goto("https://smartweigh.co.za/login", wait_until="networkidle", timeout=30000)
        time.sleep(2)
        print(f"    Current URL: {page.url}")

        # Step 2: Login
        print("[2] Logging in...")
        page.fill("#email", "admin@smartweigh.co.za")
        page.fill("#password", "Sm@rtW31gh2025!")
        page.click('button[type="submit"]')
        print("    Submitted login form, waiting for navigation...")

        # Step 3: Wait for dashboard
        page.wait_for_load_state("networkidle", timeout=30000)
        time.sleep(3)
        print(f"[3] Dashboard loaded. URL: {page.url}")

        # Step 4: Navigate to EMP201 create page
        print("[4] Navigating to EMP201 create page...")
        page.evaluate("window.location.href = 'https://smartweigh.co.za/cims/emp201/create'")
        page.wait_for_load_state("networkidle", timeout=30000)
        time.sleep(3)
        print(f"    EMP201 page loaded. URL: {page.url}")

        # Check for PHP errors on the page
        page_content = page.content()
        has_error = False
        error_indicators = [
            "Fatal error", "Parse error", "Warning:", "Notice:",
            "Symfony\\Component\\", "ErrorException",
            "Whoops", "Stack trace", "Exception"
        ]
        for indicator in error_indicators:
            if indicator in page_content:
                print(f"    WARNING: Found potential error indicator: '{indicator}'")
                has_error = True

        # Step 6: Screenshot - Top section (SARS header + Part 1)
        print("[6] Taking screenshot: Top section (SARS header + Part 1)...")
        page.evaluate("window.scrollTo(0, 0)")
        time.sleep(1)
        path1 = f"{OUTPUT_DIR}/emp201_sars_form_top.png"
        page.screenshot(path=path1, full_page=False)
        print(f"    Saved: {path1}")

        # Step 7: Scroll to Demographics section
        print("[7] Taking screenshot: Demographics section...")
        demo_el = page.query_selector("text=Demographics")
        if demo_el is None:
            demo_el = page.query_selector("text=DEMOGRAPHICS")
        if demo_el is None:
            demo_el = page.query_selector("text=Part 2")
        if demo_el is None:
            demo_el = page.query_selector("text=PART 2")

        if demo_el:
            demo_el.scroll_into_view_if_needed()
            time.sleep(1)
            page.evaluate("window.scrollBy(0, -80)")
            time.sleep(0.5)
        else:
            print("    Could not find Demographics element, scrolling down manually...")
            page.evaluate("window.scrollTo(0, 600)")
            time.sleep(1)

        path2 = f"{OUTPUT_DIR}/emp201_sars_form_demographics.png"
        page.screenshot(path=path2, full_page=False)
        print(f"    Saved: {path2}")

        # Step 8: Scroll to Financials section
        print("[8] Taking screenshot: Financials section...")
        fin_el = page.query_selector("text=Financials")
        if fin_el is None:
            fin_el = page.query_selector("text=FINANCIALS")
        if fin_el is None:
            fin_el = page.query_selector("text=Part 3")
        if fin_el is None:
            fin_el = page.query_selector("text=PART 3")
        if fin_el is None:
            fin_el = page.query_selector("text=Payment")
        if fin_el is None:
            fin_el = page.query_selector("text=PAYMENT")

        if fin_el:
            fin_el.scroll_into_view_if_needed()
            time.sleep(1)
            page.evaluate("window.scrollBy(0, -80)")
            time.sleep(0.5)
        else:
            print("    Could not find Financials element, scrolling down manually...")
            page.evaluate("window.scrollTo(0, 1200)")
            time.sleep(1)

        path3 = f"{OUTPUT_DIR}/emp201_sars_form_financials.png"
        page.screenshot(path=path3, full_page=False)
        print(f"    Saved: {path3}")

        # Step 9: Scroll to Part 4 (VDP, Tax Practitioner, Notes)
        print("[9] Taking screenshot: Part 4 (VDP / Tax Practitioner / Notes)...")
        p4_el = page.query_selector("text=Tax Practitioner")
        if p4_el is None:
            p4_el = page.query_selector("text=TAX PRACTITIONER")
        if p4_el is None:
            p4_el = page.query_selector("text=VDP")
        if p4_el is None:
            p4_el = page.query_selector("text=Part 4")
        if p4_el is None:
            p4_el = page.query_selector("text=PART 4")
        if p4_el is None:
            p4_el = page.query_selector("text=Notes")

        if p4_el:
            p4_el.scroll_into_view_if_needed()
            time.sleep(1)
            page.evaluate("window.scrollBy(0, -80)")
            time.sleep(0.5)
        else:
            print("    Could not find Part 4 element, scrolling down manually...")
            page.evaluate("window.scrollTo(0, 1800)")
            time.sleep(1)

        path4 = f"{OUTPUT_DIR}/emp201_sars_form_part4.png"
        page.screenshot(path=path4, full_page=False)
        print(f"    Saved: {path4}")

        # Step 10: Scroll to Upload Documents section
        print("[10] Taking screenshot: Upload Documents section...")
        upload_el = page.query_selector("text=Upload")
        if upload_el is None:
            upload_el = page.query_selector("text=UPLOAD")
        if upload_el is None:
            upload_el = page.query_selector("text=Documents")
        if upload_el is None:
            upload_el = page.query_selector("text=Attachments")

        if upload_el:
            upload_el.scroll_into_view_if_needed()
            time.sleep(1)
            page.evaluate("window.scrollBy(0, -80)")
            time.sleep(0.5)
        else:
            print("    Could not find Upload element, scrolling to bottom...")
            page.evaluate("window.scrollTo(0, document.body.scrollHeight)")
            time.sleep(1)

        path5 = f"{OUTPUT_DIR}/emp201_sars_form_uploads.png"
        page.screenshot(path=path5, full_page=False)
        print(f"    Saved: {path5}")

        # Print page title and any console errors
        print(f"\n--- Page title: {page.title()} ---")

        # Get the full text content for analysis
        body_text = page.inner_text("body")
        print(f"\n--- Page body text (first 2000 chars) ---")
        print(body_text[:2000])
        print("--- End body text ---")

        if has_error:
            print("\n!!! PHP/Server errors detected on the page. Check screenshots for details. !!!")

        browser.close()
        print("\nDone. All screenshots captured.")

if __name__ == "__main__":
    run()
