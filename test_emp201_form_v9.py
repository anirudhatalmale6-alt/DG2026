"""
Playwright test script v9 - debug login response body.
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

        # Step 1: Login
        print("[1] Navigating to login page...")
        page.goto("https://smartweigh.co.za/login", wait_until="networkidle", timeout=30000)
        time.sleep(2)

        print("[2] Logging in...")
        page.fill("#email", "admin@atpservices.co.za")
        page.fill("#password", "Sm@rtW31gh2025!")
        time.sleep(0.5)

        # Capture ALL network responses during login
        responses = []
        def on_response(response):
            responses.append({
                "url": response.url,
                "status": response.status,
                "method": response.request.method,
            })
        page.on("response", on_response)

        page.click("#loginSubmitButton")
        time.sleep(8)

        print(f"[3] Current URL: {page.url}")
        print(f"    Network responses during login ({len(responses)} total):")
        for r in responses:
            print(f"      {r['method']} {r['url']} => {r['status']}")

        # Check if we're actually logged in by looking at cookies and page content
        cookies = context.cookies()
        print(f"    Cookies ({len(cookies)}):")
        for c in cookies:
            print(f"      {c['name']}: {c['value'][:60]}...")

        page.screenshot(path=f"{OUTPUT_DIR}/debug_v9_after_login.png", full_page=False)

        # Check page content
        body = page.inner_text("body")
        print(f"    Body (first 300): {body[:300]}")

        # Even if URL shows /login, try navigating to EMP201
        print("\n[4] Attempting to navigate to EMP201...")
        page.goto("https://smartweigh.co.za/cims/emp201/create", wait_until="networkidle", timeout=30000)
        time.sleep(3)
        print(f"    URL after navigate: {page.url}")

        if "/login" not in page.url:
            print("    We're past login! Page loaded.")
        else:
            # Maybe need to re-check password
            print("    Still redirected to login. Let me verify password hash...")

        page.screenshot(path=f"{OUTPUT_DIR}/debug_v9_emp201.png", full_page=False)
        body2 = page.inner_text("body")
        print(f"    Body: {body2[:500]}")

        browser.close()

if __name__ == "__main__":
    run()
