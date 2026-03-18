"""
Playwright test script v2 - inspect login form fields and fix login.
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

        # Inspect the form fields
        print("[DEBUG] Inspecting form fields...")
        inputs = page.query_selector_all("input")
        for inp in inputs:
            inp_type = inp.get_attribute("type") or "?"
            inp_id = inp.get_attribute("id") or "?"
            inp_name = inp.get_attribute("name") or "?"
            inp_placeholder = inp.get_attribute("placeholder") or "?"
            print(f"    Input: type={inp_type}, id={inp_id}, name={inp_name}, placeholder={inp_placeholder}")

        # Inspect buttons
        buttons = page.query_selector_all("button")
        for btn in buttons:
            btn_type = btn.get_attribute("type") or "?"
            btn_text = btn.inner_text()
            btn_id = btn.get_attribute("id") or "?"
            print(f"    Button: type={btn_type}, id={btn_id}, text={btn_text}")

        # Also check for form element
        forms = page.query_selector_all("form")
        for form in forms:
            action = form.get_attribute("action") or "?"
            method = form.get_attribute("method") or "?"
            print(f"    Form: action={action}, method={method}")

        # Try filling by different selectors
        print("\n[2] Attempting login...")

        # Try input by placeholder
        email_input = page.query_selector('input[placeholder="Email"]')
        if email_input:
            print("    Found email input by placeholder")
            email_input.fill("admin@smartweigh.co.za")
        else:
            # Try by type
            email_input = page.query_selector('input[type="email"]')
            if email_input:
                print("    Found email input by type=email")
                email_input.fill("admin@smartweigh.co.za")
            else:
                # Try by name
                email_input = page.query_selector('input[name="email"]')
                if email_input:
                    print("    Found email input by name=email")
                    email_input.fill("admin@smartweigh.co.za")
                else:
                    print("    FALLBACK: Using first text-like input")
                    page.fill('input:first-of-type', 'admin@smartweigh.co.za')

        pw_input = page.query_selector('input[placeholder="Password"]')
        if pw_input:
            print("    Found password input by placeholder")
            pw_input.fill("Sm@rtW31gh2025!")
        else:
            pw_input = page.query_selector('input[type="password"]')
            if pw_input:
                print("    Found password input by type=password")
                pw_input.fill("Sm@rtW31gh2025!")
            else:
                print("    FALLBACK: Using name=password")
                page.fill('input[name="password"]', 'Sm@rtW31gh2025!')

        # Take screenshot before clicking login
        page.screenshot(path=f"{OUTPUT_DIR}/debug_login_filled.png", full_page=False)
        print("    Saved debug screenshot of filled form")

        # Click the Continue button
        continue_btn = page.query_selector('button:has-text("Continue")')
        if continue_btn:
            print("    Clicking 'Continue' button...")
            continue_btn.click()
        else:
            print("    No 'Continue' button found, trying submit...")
            page.click('button[type="submit"]')

        # Wait for navigation
        try:
            page.wait_for_url("**/home**", timeout=10000)
        except:
            try:
                page.wait_for_url("**/dashboard**", timeout=5000)
            except:
                pass

        page.wait_for_load_state("networkidle", timeout=15000)
        time.sleep(3)
        print(f"[3] After login. URL: {page.url}")

        # Take a debug screenshot
        page.screenshot(path=f"{OUTPUT_DIR}/debug_after_login.png", full_page=False)

        # Check if we're still on login
        if "/login" in page.url:
            print("    STILL ON LOGIN PAGE - checking for error messages...")
            error_text = page.query_selector(".alert, .error, .text-danger, .invalid-feedback")
            if error_text:
                print(f"    Error message: {error_text.inner_text()}")
            # Check page content for errors
            body = page.inner_text("body")
            print(f"    Page text: {body[:500]}")
            browser.close()
            return

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
            "Whoops", "Stack trace"
        ]
        for indicator in error_indicators:
            if indicator in page_content:
                print(f"    WARNING: Found potential error: '{indicator}'")
                has_error = True

        # Get page height for scroll calculations
        page_height = page.evaluate("document.body.scrollHeight")
        print(f"    Page height: {page_height}px")

        # Step 6: Top section
        print("[6] Taking screenshot: Top section...")
        page.evaluate("window.scrollTo(0, 0)")
        time.sleep(1)
        path1 = f"{OUTPUT_DIR}/emp201_sars_form_top.png"
        page.screenshot(path=path1, full_page=False)
        print(f"    Saved: {path1}")

        # Step 7: Demographics
        print("[7] Taking screenshot: Demographics section...")
        demo_el = page.query_selector("text=Demographics") or page.query_selector("text=DEMOGRAPHICS") or page.query_selector("text=Part 2") or page.query_selector("text=PART 2")
        if demo_el:
            demo_el.scroll_into_view_if_needed()
            time.sleep(1)
            page.evaluate("window.scrollBy(0, -80)")
            time.sleep(0.5)
        else:
            print("    Could not find Demographics, scrolling manually...")
            page.evaluate("window.scrollTo(0, 700)")
            time.sleep(1)
        path2 = f"{OUTPUT_DIR}/emp201_sars_form_demographics.png"
        page.screenshot(path=path2, full_page=False)
        print(f"    Saved: {path2}")

        # Step 8: Financials
        print("[8] Taking screenshot: Financials section...")
        fin_el = page.query_selector("text=Financials") or page.query_selector("text=FINANCIALS") or page.query_selector("text=Part 3") or page.query_selector("text=PART 3") or page.query_selector("text=Payment") or page.query_selector("text=PAYMENT")
        if fin_el:
            fin_el.scroll_into_view_if_needed()
            time.sleep(1)
            page.evaluate("window.scrollBy(0, -80)")
            time.sleep(0.5)
        else:
            print("    Could not find Financials, scrolling manually...")
            page.evaluate("window.scrollTo(0, 1400)")
            time.sleep(1)
        path3 = f"{OUTPUT_DIR}/emp201_sars_form_financials.png"
        page.screenshot(path=path3, full_page=False)
        print(f"    Saved: {path3}")

        # Step 9: Part 4
        print("[9] Taking screenshot: Part 4...")
        p4_el = page.query_selector("text=Tax Practitioner") or page.query_selector("text=TAX PRACTITIONER") or page.query_selector("text=VDP") or page.query_selector("text=Part 4") or page.query_selector("text=PART 4")
        if p4_el:
            p4_el.scroll_into_view_if_needed()
            time.sleep(1)
            page.evaluate("window.scrollBy(0, -80)")
            time.sleep(0.5)
        else:
            print("    Could not find Part 4, scrolling manually...")
            page.evaluate("window.scrollTo(0, 2100)")
            time.sleep(1)
        path4 = f"{OUTPUT_DIR}/emp201_sars_form_part4.png"
        page.screenshot(path=path4, full_page=False)
        print(f"    Saved: {path4}")

        # Step 10: Upload
        print("[10] Taking screenshot: Upload Documents section...")
        upload_el = page.query_selector("text=Upload") or page.query_selector("text=UPLOAD") or page.query_selector("text=Attachments")
        if upload_el:
            upload_el.scroll_into_view_if_needed()
            time.sleep(1)
            page.evaluate("window.scrollBy(0, -80)")
            time.sleep(0.5)
        else:
            print("    Could not find Upload, scrolling to bottom...")
            page.evaluate("window.scrollTo(0, document.body.scrollHeight)")
            time.sleep(1)
        path5 = f"{OUTPUT_DIR}/emp201_sars_form_uploads.png"
        page.screenshot(path=path5, full_page=False)
        print(f"    Saved: {path5}")

        # Summary
        print(f"\n--- Page title: {page.title()} ---")
        body_text = page.inner_text("body")
        print(f"\n--- Page body text (first 3000 chars) ---")
        print(body_text[:3000])
        print("--- End body text ---")

        if has_error:
            print("\n!!! PHP/Server errors detected on the page. Check screenshots for details. !!!")

        browser.close()
        print("\nDone. All screenshots captured.")

if __name__ == "__main__":
    run()
