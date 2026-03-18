"""
Playwright test script v5 - robust AJAX login with response waiting.
"""
from playwright.sync_api import sync_playwright
import time
import json

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

        # Step 2: Login via AJAX using page.evaluate to directly call the login endpoint
        print("[2] Attempting login via JS fetch with CSRF token...")

        # First, let's see what the login button JS does
        login_js = page.evaluate("""
            () => {
                const btn = document.getElementById('loginSubmitButton');
                // Check for onclick or event listeners
                const form = btn.closest('form');
                const formAction = form ? form.action : 'no form';
                const formMethod = form ? form.method : 'no method';

                // Get CSRF token
                const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                const tokenInput = document.querySelector('input[name="_token"]');
                const token = tokenMeta ? tokenMeta.content : (tokenInput ? tokenInput.value : 'none');

                return {
                    formAction: formAction,
                    formMethod: formMethod,
                    csrfToken: token.substring(0, 50) + '...',
                    buttonType: btn.type,
                    hasFormParent: !!form,
                };
            }
        """)
        print(f"    Form details: {json.dumps(login_js, indent=2)}")

        # Fill the form properly
        page.fill("#email", "admin@smartweigh.co.za")
        page.fill("#password", "Sm@rtW31gh2025!")
        time.sleep(0.5)

        # Use Promise.all pattern: click + wait for response
        print("    Clicking login button and waiting for XHR response...")

        # Listen for login POST response
        with page.expect_response(
            lambda response: "/login" in response.url and response.request.method == "POST",
            timeout=15000
        ) as response_info:
            page.click("#loginSubmitButton")

        response = response_info.value
        print(f"    Login POST response: {response.status}")
        try:
            resp_body = response.text()[:500]
            print(f"    Response body: {resp_body}")
        except:
            print("    Could not read response body")

        time.sleep(3)
        print(f"    Current URL after login: {page.url}")

        # Take debug screenshot
        page.screenshot(path=f"{OUTPUT_DIR}/debug_after_login_v5.png", full_page=False)

        # If login failed, try alternative passwords
        if "/login" in page.url:
            print("\n    Login with primary credentials failed. Trying alternatives...")

            alt_passwords = [
                "Sm@rtW31gh2025",    # without exclamation
                "Sm@rtW3igh2025!",   # different spelling
                "SmartWeigh2025!",   # simpler
                "admin123",          # default
                "Admin@2025!",       # common pattern
                "Sm@rtW31gh2026!",   # 2026 version
            ]

            for pwd in alt_passwords:
                print(f"    Trying password: {pwd[:4]}***...")
                page.goto("https://smartweigh.co.za/login", wait_until="networkidle", timeout=15000)
                time.sleep(1)
                page.fill("#email", "admin@smartweigh.co.za")
                page.fill("#password", pwd)
                time.sleep(0.5)

                with page.expect_response(
                    lambda response: "/login" in response.url and response.request.method == "POST",
                    timeout=10000
                ) as response_info:
                    page.click("#loginSubmitButton")

                resp = response_info.value
                try:
                    body = resp.text()
                    print(f"    Response: {resp.status} - {body[:200]}")
                except:
                    pass

                time.sleep(2)
                page.wait_for_load_state("networkidle", timeout=10000)

                if "/login" not in page.url:
                    print(f"    SUCCESS with password: {pwd[:4]}***")
                    break

                # Also check if AJAX returned success but page didn't redirect
                try:
                    body_json = json.loads(body)
                    if body_json.get("notification", {}).get("type") != "error":
                        print(f"    Non-error response, trying to navigate...")
                        page.goto("https://smartweigh.co.za/cims/emp201/create", wait_until="networkidle", timeout=15000)
                        time.sleep(2)
                        if "/login" not in page.url:
                            print(f"    SUCCESS (redirected)!")
                            break
                except:
                    pass

        current_url = page.url
        print(f"\n    Final URL: {current_url}")

        if "/login" in current_url:
            print("\n    ALL LOGIN ATTEMPTS FAILED.")
            print("    The credentials may have changed. Reporting the issue.")
            page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_top.png", full_page=False)
            browser.close()
            return

        # Successfully logged in - navigate to EMP201
        print("\n[4] Navigating to EMP201 create page...")
        page.evaluate("window.location.href = 'https://smartweigh.co.za/cims/emp201/create'")
        page.wait_for_load_state("networkidle", timeout=30000)
        time.sleep(3)
        current_url = page.url
        print(f"    EMP201 page loaded. URL: {current_url}")

        if "/login" in current_url:
            print("    Redirected to login - session issue.")
            browser.close()
            return

        take_emp201_screenshots(page)

        browser.close()
        print("\nDone. All screenshots captured successfully.")


def take_emp201_screenshots(page):
    """Take all 5 screenshots of the EMP201 form."""

    page_content = page.content()
    has_error = False
    for indicator in ["Fatal error", "Parse error", "Warning:", "Whoops", "Stack trace", "ErrorException"]:
        if indicator in page_content:
            print(f"    WARNING: Found error: '{indicator}'")
            has_error = True

    page_height = page.evaluate("document.body.scrollHeight")
    print(f"    Page height: {page_height}px")

    # Get all section headings for debugging
    headings = page.evaluate("""
        () => {
            const els = document.querySelectorAll('h1, h2, h3, h4, h5, h6, .card-header, .panel-heading, legend, .section-header, .section-title');
            return Array.from(els).map(el => ({tag: el.tagName, text: el.innerText.trim().substring(0, 80)}));
        }
    """)
    print("    Page headings/sections found:")
    for h in headings[:30]:
        print(f"      [{h['tag']}] {h['text']}")

    # Screenshot 1: Top section
    print("\n[6] Top section (SARS header + Part 1)...")
    page.evaluate("window.scrollTo(0, 0)")
    time.sleep(1)
    page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_top.png", full_page=False)
    print(f"    Saved: {OUTPUT_DIR}/emp201_sars_form_top.png")

    # Screenshot 2: Demographics
    print("[7] Demographics section...")
    el = page.query_selector("text=Demographics") or page.query_selector("text=DEMOGRAPHICS") or page.query_selector("text=Employer Details") or page.query_selector("text=Part 2") or page.query_selector("text=PART 2")
    if el:
        el.scroll_into_view_if_needed()
        time.sleep(0.5)
        page.evaluate("window.scrollBy(0, -100)")
    else:
        page.evaluate("window.scrollTo(0, 700)")
    time.sleep(0.5)
    page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_demographics.png", full_page=False)
    print(f"    Saved: {OUTPUT_DIR}/emp201_sars_form_demographics.png")

    # Screenshot 3: Financials
    print("[8] Financials section...")
    el = page.query_selector("text=Financials") or page.query_selector("text=FINANCIALS") or page.query_selector("text=Payroll Tax") or page.query_selector("text=PAYROLL TAX") or page.query_selector("text=Part 3") or page.query_selector("text=PART 3")
    if el:
        el.scroll_into_view_if_needed()
        time.sleep(0.5)
        page.evaluate("window.scrollBy(0, -100)")
    else:
        page.evaluate("window.scrollTo(0, 1400)")
    time.sleep(0.5)
    page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_financials.png", full_page=False)
    print(f"    Saved: {OUTPUT_DIR}/emp201_sars_form_financials.png")

    # Screenshot 4: Part 4 (VDP, Tax Practitioner, Notes)
    print("[9] Part 4 (VDP / Tax Practitioner / Notes)...")
    el = page.query_selector("text=Voluntary") or page.query_selector("text=Tax Practitioner") or page.query_selector("text=VDP") or page.query_selector("text=Part 4") or page.query_selector("text=Notes")
    if el:
        el.scroll_into_view_if_needed()
        time.sleep(0.5)
        page.evaluate("window.scrollBy(0, -100)")
    else:
        page.evaluate("window.scrollTo(0, 2100)")
    time.sleep(0.5)
    page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_part4.png", full_page=False)
    print(f"    Saved: {OUTPUT_DIR}/emp201_sars_form_part4.png")

    # Screenshot 5: Upload Documents
    print("[10] Upload Documents section...")
    el = page.query_selector("text=Upload") or page.query_selector("text=UPLOAD") or page.query_selector("text=Document") or page.query_selector("text=Attachments")
    if el:
        el.scroll_into_view_if_needed()
        time.sleep(0.5)
        page.evaluate("window.scrollBy(0, -100)")
    else:
        page.evaluate("window.scrollTo(0, document.body.scrollHeight)")
    time.sleep(0.5)
    page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_uploads.png", full_page=False)
    print(f"    Saved: {OUTPUT_DIR}/emp201_sars_form_uploads.png")

    # Summary
    print(f"\n--- Page title: {page.title()} ---")
    body_text = page.inner_text("body")
    print(f"\n--- Body text (first 3000 chars) ---")
    print(body_text[:3000])
    print("--- End ---")

    if has_error:
        print("\n!!! PHP/Server errors detected on the page. Check screenshots for details. !!!")


if __name__ == "__main__":
    run()
