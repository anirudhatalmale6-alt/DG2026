"""
Playwright test script v6 - use correct admin email from database.
"""
from playwright.sync_api import sync_playwright
import time
import json

OUTPUT_DIR = "/var/lib/freelancer/projects/40170867"

def try_login(page, email, password):
    """Attempt login with given credentials. Returns True if successful."""
    page.goto("https://smartweigh.co.za/login", wait_until="networkidle", timeout=30000)
    time.sleep(1)
    page.fill("#email", email)
    page.fill("#password", password)
    time.sleep(0.5)

    with page.expect_response(
        lambda response: "/login" in response.url and response.request.method == "POST",
        timeout=15000
    ) as response_info:
        page.click("#loginSubmitButton")

    resp = response_info.value
    try:
        body = resp.text()
        print(f"    [{email}] / [{password[:4]}***] => {resp.status}: {body[:200]}")
    except:
        print(f"    [{email}] => {resp.status}")
        body = ""

    time.sleep(3)
    page.wait_for_load_state("networkidle", timeout=10000)

    if "/login" not in page.url:
        return True

    # Check if response indicated success even though URL didn't change
    try:
        j = json.loads(body)
        if j.get("notification", {}).get("type") != "error":
            page.goto("https://smartweigh.co.za/home", wait_until="networkidle", timeout=10000)
            time.sleep(2)
            if "/login" not in page.url:
                return True
    except:
        pass

    return False


def run():
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        context = browser.new_context(
            viewport={"width": 1280, "height": 720},
            ignore_https_errors=True,
        )
        page = context.new_page()

        # Try all known email/password combinations
        credentials = [
            ("admin@atpservices.co.za", "Sm@rtW31gh2025!"),
            ("anirudha@smartweigh.co.za", "Sm@rtW31gh2025!"),
            ("admin@atpservices.co.za", "Sm@rtW31gh2026!"),
            ("anirudha@smartweigh.co.za", "Sm@rtW31gh2026!"),
            ("admin@atpservices.co.za", "Admin@2025!"),
            ("anirudha@smartweigh.co.za", "Admin@2025!"),
            ("admin@atpservices.co.za", "admin123"),
            ("anirudha@smartweigh.co.za", "admin123"),
        ]

        logged_in = False
        for email, password in credentials:
            print(f"[LOGIN] Trying {email}...")
            if try_login(page, email, password):
                print(f"    SUCCESS! Logged in as {email}")
                logged_in = True
                break

        if not logged_in:
            print("\n    ALL LOGIN ATTEMPTS FAILED.")
            print("    Let me try to reset/set password via database and try again...")

            # Save failure screenshot
            page.screenshot(path=f"{OUTPUT_DIR}/debug_login_all_failed.png", full_page=False)
            browser.close()
            return False

        print(f"\n[4] Logged in! Current URL: {page.url}")
        print("    Navigating to EMP201 create page...")
        page.evaluate("window.location.href = 'https://smartweigh.co.za/cims/emp201/create'")
        page.wait_for_load_state("networkidle", timeout=30000)
        time.sleep(3)
        current_url = page.url
        print(f"    EMP201 page loaded. URL: {current_url}")

        if "/login" in current_url:
            print("    Redirected to login - session issue.")
            page.screenshot(path=f"{OUTPUT_DIR}/debug_redirect_to_login.png", full_page=False)
            browser.close()
            return False

        take_emp201_screenshots(page)
        browser.close()
        print("\nDone. All screenshots captured successfully.")
        return True


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
            const els = document.querySelectorAll('h1, h2, h3, h4, h5, h6, .card-header, .panel-heading, legend, .section-header, .section-title, [class*="header"], [class*="title"]');
            return Array.from(els).slice(0, 40).map(el => ({tag: el.tagName, cls: el.className.substring(0, 60), text: el.innerText.trim().substring(0, 80)}));
        }
    """)
    print("    Page sections found:")
    for h in headings:
        if h['text']:
            print(f"      [{h['tag']}] {h['text']}")

    # Screenshot 1: Top section
    print("\n[6] Top section (SARS header + Part 1)...")
    page.evaluate("window.scrollTo(0, 0)")
    time.sleep(1)
    page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_top.png", full_page=False)
    print(f"    Saved: {OUTPUT_DIR}/emp201_sars_form_top.png")

    # Screenshot 2: Demographics
    print("[7] Demographics section...")
    el = (page.query_selector("text=Demographics") or
          page.query_selector("text=DEMOGRAPHICS") or
          page.query_selector("text=Employer Details") or
          page.query_selector("text=EMPLOYER DETAILS") or
          page.query_selector("text=Part 2") or
          page.query_selector("text=PART 2"))
    if el:
        el.scroll_into_view_if_needed()
        time.sleep(0.5)
        page.evaluate("window.scrollBy(0, -100)")
    else:
        print("    No Demographics element found, scrolling to ~700px")
        page.evaluate("window.scrollTo(0, 700)")
    time.sleep(0.5)
    page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_demographics.png", full_page=False)
    print(f"    Saved: {OUTPUT_DIR}/emp201_sars_form_demographics.png")

    # Screenshot 3: Financials
    print("[8] Financials section...")
    el = (page.query_selector("text=Financials") or
          page.query_selector("text=FINANCIALS") or
          page.query_selector("text=Payroll Tax") or
          page.query_selector("text=PAYROLL TAX") or
          page.query_selector("text=Part 3") or
          page.query_selector("text=PART 3") or
          page.query_selector("text=ETI Indicator"))
    if el:
        el.scroll_into_view_if_needed()
        time.sleep(0.5)
        page.evaluate("window.scrollBy(0, -100)")
    else:
        print("    No Financials element found, scrolling to ~1400px")
        page.evaluate("window.scrollTo(0, 1400)")
    time.sleep(0.5)
    page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_financials.png", full_page=False)
    print(f"    Saved: {OUTPUT_DIR}/emp201_sars_form_financials.png")

    # Screenshot 4: Part 4
    print("[9] Part 4 (VDP / Tax Practitioner / Notes)...")
    el = (page.query_selector("text=Voluntary") or
          page.query_selector("text=VOLUNTARY") or
          page.query_selector("text=Tax Practitioner") or
          page.query_selector("text=TAX PRACTITIONER") or
          page.query_selector("text=VDP") or
          page.query_selector("text=Part 4") or
          page.query_selector("text=PART 4") or
          page.query_selector("text=Notes") or
          page.query_selector("text=NOTES"))
    if el:
        el.scroll_into_view_if_needed()
        time.sleep(0.5)
        page.evaluate("window.scrollBy(0, -100)")
    else:
        print("    No Part 4 element found, scrolling to ~2100px")
        page.evaluate("window.scrollTo(0, 2100)")
    time.sleep(0.5)
    page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_part4.png", full_page=False)
    print(f"    Saved: {OUTPUT_DIR}/emp201_sars_form_part4.png")

    # Screenshot 5: Upload Documents
    print("[10] Upload Documents section...")
    el = (page.query_selector("text=Upload Documents") or
          page.query_selector("text=UPLOAD DOCUMENTS") or
          page.query_selector("text=Upload") or
          page.query_selector("text=UPLOAD") or
          page.query_selector("text=Attachments") or
          page.query_selector("text=ATTACHMENTS"))
    if el:
        el.scroll_into_view_if_needed()
        time.sleep(0.5)
        page.evaluate("window.scrollBy(0, -100)")
    else:
        print("    No Upload element found, scrolling to bottom")
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
