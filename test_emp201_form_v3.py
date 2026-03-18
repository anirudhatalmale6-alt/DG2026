"""
Playwright test script v3 - handle AJAX login, wait for response, capture network.
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

        # Capture console messages
        console_messages = []
        page.on("console", lambda msg: console_messages.append(f"{msg.type}: {msg.text}"))

        # Capture network responses for login
        login_responses = []
        def capture_response(response):
            if "login" in response.url.lower() or "auth" in response.url.lower():
                try:
                    body = response.text()[:500]
                except:
                    body = "(could not read body)"
                login_responses.append(f"URL: {response.url}, Status: {response.status}, Body: {body}")
        page.on("response", capture_response)

        # Step 1: Navigate to login page
        print("[1] Navigating to login page...")
        page.goto("https://smartweigh.co.za/login", wait_until="networkidle", timeout=30000)
        time.sleep(2)

        # Step 2: Fill and submit
        print("[2] Filling login form...")
        page.fill("#email", "admin@smartweigh.co.za")
        page.fill("#password", "Sm@rtW31gh2025!")
        time.sleep(0.5)

        # Click and wait for navigation or response
        print("    Clicking Continue and waiting for response...")
        with page.expect_navigation(timeout=15000, wait_until="networkidle") as nav_info:
            page.click("#loginSubmitButton")

        time.sleep(3)
        print(f"[3] After login. URL: {page.url}")

        # Print captured network responses
        for resp in login_responses:
            print(f"    Network: {resp[:300]}")

        # Print console messages
        for msg in console_messages[:10]:
            print(f"    Console: {msg}")

        # Take screenshot
        page.screenshot(path=f"{OUTPUT_DIR}/debug_after_login_v3.png", full_page=False)

        if "/login" in page.url:
            print("    STILL ON LOGIN. Checking page for error messages...")
            # Check for any validation errors or toasts
            page_html = page.content()
            if "These credentials" in page_html or "invalid" in page_html.lower():
                print("    >>> Invalid credentials error detected!")
            if "CSRF" in page_html or "csrf" in page_html:
                print("    >>> CSRF token issue detected!")

            # Let's try checking if it's a SPA that doesn't change URL
            body = page.inner_text("body")
            print(f"    Body text: {body[:1000]}")

            # Maybe the form submits via JS and we need to wait differently
            # Try waiting for any redirect or check cookies
            cookies = context.cookies()
            for c in cookies:
                if "session" in c["name"].lower() or "token" in c["name"].lower() or "laravel" in c["name"].lower():
                    print(f"    Cookie: {c['name']}={c['value'][:50]}...")

            browser.close()
            return

        # If we got past login, navigate to EMP201
        print("[4] Navigating to EMP201 create page...")
        page.evaluate("window.location.href = 'https://smartweigh.co.za/cims/emp201/create'")
        page.wait_for_load_state("networkidle", timeout=30000)
        time.sleep(3)
        print(f"    EMP201 page loaded. URL: {page.url}")

        # Check for errors
        page_content = page.content()
        has_error = False
        for indicator in ["Fatal error", "Parse error", "Warning:", "Whoops", "Stack trace", "ErrorException"]:
            if indicator in page_content:
                print(f"    WARNING: Found error: '{indicator}'")
                has_error = True

        page_height = page.evaluate("document.body.scrollHeight")
        print(f"    Page height: {page_height}px")

        # Screenshots
        print("[6] Top section...")
        page.evaluate("window.scrollTo(0, 0)")
        time.sleep(1)
        page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_top.png", full_page=False)

        print("[7] Demographics...")
        el = page.query_selector("text=Demographics") or page.query_selector("text=DEMOGRAPHICS") or page.query_selector("text=Part 2") or page.query_selector("text=PART 2")
        if el:
            el.scroll_into_view_if_needed()
            time.sleep(0.5)
            page.evaluate("window.scrollBy(0, -80)")
        else:
            page.evaluate("window.scrollTo(0, 700)")
        time.sleep(0.5)
        page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_demographics.png", full_page=False)

        print("[8] Financials...")
        el = page.query_selector("text=Financials") or page.query_selector("text=FINANCIALS") or page.query_selector("text=Part 3") or page.query_selector("text=PART 3") or page.query_selector("text=Payment") or page.query_selector("text=PAYMENT")
        if el:
            el.scroll_into_view_if_needed()
            time.sleep(0.5)
            page.evaluate("window.scrollBy(0, -80)")
        else:
            page.evaluate("window.scrollTo(0, 1400)")
        time.sleep(0.5)
        page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_financials.png", full_page=False)

        print("[9] Part 4...")
        el = page.query_selector("text=Tax Practitioner") or page.query_selector("text=TAX PRACTITIONER") or page.query_selector("text=VDP") or page.query_selector("text=Part 4") or page.query_selector("text=PART 4")
        if el:
            el.scroll_into_view_if_needed()
            time.sleep(0.5)
            page.evaluate("window.scrollBy(0, -80)")
        else:
            page.evaluate("window.scrollTo(0, 2100)")
        time.sleep(0.5)
        page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_part4.png", full_page=False)

        print("[10] Upload section...")
        el = page.query_selector("text=Upload") or page.query_selector("text=UPLOAD") or page.query_selector("text=Attachments")
        if el:
            el.scroll_into_view_if_needed()
            time.sleep(0.5)
            page.evaluate("window.scrollBy(0, -80)")
        else:
            page.evaluate("window.scrollTo(0, document.body.scrollHeight)")
        time.sleep(0.5)
        page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_uploads.png", full_page=False)

        # Summary
        print(f"\n--- Page title: {page.title()} ---")
        body_text = page.inner_text("body")
        print(f"\n--- Page body text (first 3000 chars) ---")
        print(body_text[:3000])
        print("--- End ---")

        if has_error:
            print("\n!!! PHP/Server errors detected! Check screenshots. !!!")

        browser.close()
        print("\nDone.")

if __name__ == "__main__":
    run()
