"""
Playwright test script v7 - login with reset password, capture all EMP201 screenshots.
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

        print("[2] Logging in with admin@atpservices.co.za...")
        page.fill("#email", "admin@atpservices.co.za")
        page.fill("#password", "Sm@rtW31gh2025!")
        time.sleep(0.5)

        with page.expect_response(
            lambda response: "/login" in response.url and response.request.method == "POST",
            timeout=15000
        ) as response_info:
            page.click("#loginSubmitButton")

        resp = response_info.value
        try:
            body = resp.text()
            print(f"    Login response: {resp.status} - {body[:300]}")
        except:
            print(f"    Login response: {resp.status}")

        time.sleep(3)
        page.wait_for_load_state("networkidle", timeout=15000)
        time.sleep(2)
        print(f"[3] After login. URL: {page.url}")

        # Debug screenshot
        page.screenshot(path=f"{OUTPUT_DIR}/debug_after_login_v7.png", full_page=False)

        if "/login" in page.url:
            print("    Still on login page!")
            body_text = page.inner_text("body")
            print(f"    Page text: {body_text[:500]}")

            # Try the other user
            print("\n    Trying anirudha@smartweigh.co.za...")
            page.goto("https://smartweigh.co.za/login", wait_until="networkidle", timeout=15000)
            time.sleep(1)
            page.fill("#email", "anirudha@smartweigh.co.za")
            page.fill("#password", "Sm@rtW31gh2025!")
            time.sleep(0.5)

            with page.expect_response(
                lambda response: "/login" in response.url and response.request.method == "POST",
                timeout=15000
            ) as response_info:
                page.click("#loginSubmitButton")

            resp2 = response_info.value
            try:
                body2 = resp2.text()
                print(f"    Login response: {resp2.status} - {body2[:300]}")
            except:
                pass

            time.sleep(3)
            page.wait_for_load_state("networkidle", timeout=15000)
            time.sleep(2)
            print(f"    After second login attempt. URL: {page.url}")

            if "/login" in page.url:
                print("    STILL ON LOGIN. Checking if the app uses $2y$ bcrypt...")
                browser.close()
                return

        # Navigate to EMP201
        print("\n[4] Navigating to EMP201 create page...")
        page.evaluate("window.location.href = 'https://smartweigh.co.za/cims/emp201/create'")
        page.wait_for_load_state("networkidle", timeout=30000)
        time.sleep(3)
        current_url = page.url
        print(f"    EMP201 page loaded. URL: {current_url}")

        if "/login" in current_url:
            print("    Redirected back to login.")
            browser.close()
            return

        # Check for errors
        page_content = page.content()
        has_error = False
        for indicator in ["Fatal error", "Parse error", "Warning:", "Whoops", "Stack trace", "ErrorException"]:
            if indicator in page_content:
                print(f"    WARNING: Found error: '{indicator}'")
                has_error = True

        page_height = page.evaluate("document.body.scrollHeight")
        print(f"    Page height: {page_height}px")

        # Get section headings
        headings = page.evaluate("""
            () => {
                const els = document.querySelectorAll('h1, h2, h3, h4, h5, h6, .card-header, .section-header, legend');
                return Array.from(els).slice(0, 40).map(el => ({
                    tag: el.tagName,
                    text: el.innerText.trim().substring(0, 80)
                }));
            }
        """)
        print("    Sections found:")
        for h in headings:
            if h['text']:
                print(f"      [{h['tag']}] {h['text']}")

        # Screenshot 1: Top
        print("\n[6] Top section...")
        page.evaluate("window.scrollTo(0, 0)")
        time.sleep(1)
        page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_top.png", full_page=False)
        print(f"    Saved: emp201_sars_form_top.png")

        # Screenshot 2: Demographics
        print("[7] Demographics...")
        el = (page.query_selector("text=Demographics") or
              page.query_selector("text=DEMOGRAPHICS") or
              page.query_selector("text=Employer Details") or
              page.query_selector("text=EMPLOYER DETAILS"))
        if el:
            el.scroll_into_view_if_needed()
            time.sleep(0.5)
            page.evaluate("window.scrollBy(0, -100)")
        else:
            page.evaluate("window.scrollTo(0, 700)")
        time.sleep(0.5)
        page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_demographics.png", full_page=False)
        print(f"    Saved: emp201_sars_form_demographics.png")

        # Screenshot 3: Financials
        print("[8] Financials...")
        el = (page.query_selector("text=Financials") or
              page.query_selector("text=FINANCIALS") or
              page.query_selector("text=Payroll Tax") or
              page.query_selector("text=ETI Indicator"))
        if el:
            el.scroll_into_view_if_needed()
            time.sleep(0.5)
            page.evaluate("window.scrollBy(0, -100)")
        else:
            page.evaluate("window.scrollTo(0, 1400)")
        time.sleep(0.5)
        page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_financials.png", full_page=False)
        print(f"    Saved: emp201_sars_form_financials.png")

        # Screenshot 4: Part 4
        print("[9] Part 4...")
        el = (page.query_selector("text=Voluntary") or
              page.query_selector("text=Tax Practitioner") or
              page.query_selector("text=VDP") or
              page.query_selector("text=Notes"))
        if el:
            el.scroll_into_view_if_needed()
            time.sleep(0.5)
            page.evaluate("window.scrollBy(0, -100)")
        else:
            page.evaluate("window.scrollTo(0, 2100)")
        time.sleep(0.5)
        page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_part4.png", full_page=False)
        print(f"    Saved: emp201_sars_form_part4.png")

        # Screenshot 5: Upload
        print("[10] Upload Documents...")
        el = (page.query_selector("text=Upload Documents") or
              page.query_selector("text=Upload") or
              page.query_selector("text=UPLOAD"))
        if el:
            el.scroll_into_view_if_needed()
            time.sleep(0.5)
            page.evaluate("window.scrollBy(0, -100)")
        else:
            page.evaluate("window.scrollTo(0, document.body.scrollHeight)")
        time.sleep(0.5)
        page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_uploads.png", full_page=False)
        print(f"    Saved: emp201_sars_form_uploads.png")

        # Summary
        print(f"\n--- Page title: {page.title()} ---")
        body_text = page.inner_text("body")
        print(f"\n--- Body text (first 3000 chars) ---")
        print(body_text[:3000])
        print("--- End ---")

        if has_error:
            print("\n!!! PHP/Server errors detected! !!!")

        browser.close()
        print("\nDone. All screenshots captured.")

if __name__ == "__main__":
    run()
