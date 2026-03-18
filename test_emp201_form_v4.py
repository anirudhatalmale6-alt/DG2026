"""
Playwright test script v4 - handle AJAX login properly.
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

        # Capture network responses
        login_responses = []
        def capture_response(response):
            url = response.url.lower()
            if "login" in url or "auth" in url:
                try:
                    body = response.text()[:500]
                except:
                    body = "(unreadable)"
                login_responses.append({
                    "url": response.url,
                    "status": response.status,
                    "body": body
                })
        page.on("response", capture_response)

        # Step 1
        print("[1] Navigating to login page...")
        page.goto("https://smartweigh.co.za/login", wait_until="networkidle", timeout=30000)
        time.sleep(2)

        # Step 2: Fill and submit (AJAX)
        print("[2] Filling login form...")
        page.fill("#email", "admin@smartweigh.co.za")
        page.fill("#password", "Sm@rtW31gh2025!")
        time.sleep(0.5)

        print("    Clicking Continue...")
        page.click("#loginSubmitButton")

        # Wait for AJAX response - just wait for network to settle
        time.sleep(5)
        page.wait_for_load_state("networkidle", timeout=15000)
        time.sleep(2)

        print(f"[3] After login click. URL: {page.url}")

        # Print network responses
        for r in login_responses:
            print(f"    Network response: {r['url']} => {r['status']}")
            print(f"    Body: {r['body'][:300]}")

        # Screenshot after login
        page.screenshot(path=f"{OUTPUT_DIR}/debug_after_login_v4.png", full_page=False)

        # Check cookies
        cookies = context.cookies()
        print("    Cookies:")
        for c in cookies:
            print(f"      {c['name']} = {c['value'][:80]}...")

        # If still on login, try to check if the login was AJAX and resulted in redirect
        current_url = page.url
        if "/login" in current_url:
            # Maybe the AJAX returned a redirect URL that JS uses
            # Try navigating directly with cookies
            print("    Still on login page. Trying direct navigation to EMP201...")
            page.goto("https://smartweigh.co.za/cims/emp201/create", wait_until="networkidle", timeout=30000)
            time.sleep(3)
            current_url = page.url
            print(f"    After direct nav: {current_url}")

            if "/login" in current_url:
                print("    Still redirected to login - authentication failed.")
                page.screenshot(path=f"{OUTPUT_DIR}/debug_login_failed.png", full_page=False)
                body = page.inner_text("body")
                print(f"    Body: {body[:500]}")

                # Try with page.goto for login POST directly
                print("\n    Trying alternative: direct POST login via fetch...")
                result = page.evaluate("""
                    async () => {
                        // Get CSRF token
                        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                        const tokenInput = document.querySelector('input[name="_token"]');
                        const token = tokenMeta ? tokenMeta.content : (tokenInput ? tokenInput.value : '');

                        const resp = await fetch('/login', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({
                                email: 'admin@smartweigh.co.za',
                                password: 'Sm@rtW31gh2025!',
                            }),
                            credentials: 'same-origin',
                        });
                        const text = await resp.text();
                        return {status: resp.status, body: text.substring(0, 1000), url: resp.url};
                    }
                """)
                print(f"    Fetch result: status={result['status']}")
                print(f"    Body: {result['body'][:500]}")

                # Try again to navigate
                time.sleep(2)
                page.goto("https://smartweigh.co.za/cims/emp201/create", wait_until="networkidle", timeout=30000)
                time.sleep(3)
                current_url = page.url
                print(f"    After second nav attempt: {current_url}")

                if "/login" in current_url:
                    # Last attempt: form-encoded POST
                    print("\n    Trying form-encoded POST...")
                    page.goto("https://smartweigh.co.za/login", wait_until="networkidle", timeout=15000)
                    time.sleep(1)
                    result2 = page.evaluate("""
                        async () => {
                            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                            const tokenInput = document.querySelector('input[name="_token"]');
                            const token = tokenMeta ? tokenMeta.content : (tokenInput ? tokenInput.value : '');

                            const formData = new URLSearchParams();
                            formData.append('_token', token);
                            formData.append('email', 'admin@smartweigh.co.za');
                            formData.append('password', 'Sm@rtW31gh2025!');

                            const resp = await fetch('/login', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                    'Accept': 'text/html,application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                body: formData.toString(),
                                credentials: 'same-origin',
                                redirect: 'follow',
                            });
                            const text = await resp.text();
                            return {status: resp.status, body: text.substring(0, 1000), url: resp.url, redirected: resp.redirected};
                        }
                    """)
                    print(f"    Form POST result: status={result2['status']}, redirected={result2.get('redirected')}")
                    print(f"    URL: {result2.get('url')}")
                    print(f"    Body: {result2['body'][:500]}")

                    time.sleep(2)
                    page.goto("https://smartweigh.co.za/cims/emp201/create", wait_until="networkidle", timeout=30000)
                    time.sleep(3)
                    current_url = page.url
                    print(f"    After third nav attempt: {current_url}")

        if "/login" not in current_url:
            print("    LOGIN SUCCESSFUL! Proceeding to screenshots...")
            take_emp201_screenshots(page)
        else:
            print("\n    ALL LOGIN ATTEMPTS FAILED.")
            page.screenshot(path=f"{OUTPUT_DIR}/debug_final_failure.png", full_page=False)

        browser.close()
        print("\nDone.")


def take_emp201_screenshots(page):
    page_content = page.content()
    has_error = False
    for indicator in ["Fatal error", "Parse error", "Warning:", "Whoops", "Stack trace", "ErrorException"]:
        if indicator in page_content:
            print(f"    WARNING: Found error: '{indicator}'")
            has_error = True

    page_height = page.evaluate("document.body.scrollHeight")
    print(f"    Page height: {page_height}px")

    # Top
    print("[6] Top section...")
    page.evaluate("window.scrollTo(0, 0)")
    time.sleep(1)
    page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_top.png", full_page=False)
    print(f"    Saved: {OUTPUT_DIR}/emp201_sars_form_top.png")

    # Demographics
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
    print(f"    Saved: {OUTPUT_DIR}/emp201_sars_form_demographics.png")

    # Financials
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
    print(f"    Saved: {OUTPUT_DIR}/emp201_sars_form_financials.png")

    # Part 4
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
    print(f"    Saved: {OUTPUT_DIR}/emp201_sars_form_part4.png")

    # Upload
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
    print(f"    Saved: {OUTPUT_DIR}/emp201_sars_form_uploads.png")

    # Summary
    print(f"\n--- Page title: {page.title()} ---")
    body_text = page.inner_text("body")
    print(f"\n--- Body text (first 3000 chars) ---")
    print(body_text[:3000])
    print("--- End ---")

    if has_error:
        print("\n!!! PHP/Server errors detected! !!!")


if __name__ == "__main__":
    run()
