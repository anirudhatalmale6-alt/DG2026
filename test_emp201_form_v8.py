"""
Playwright test script v8 - fixed form, capture all EMP201 screenshots.
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

        with page.expect_response(
            lambda response: "/login" in response.url and response.request.method == "POST",
            timeout=15000
        ) as response_info:
            page.click("#loginSubmitButton")

        resp = response_info.value
        print(f"    Login response: {resp.status}")

        time.sleep(3)
        page.wait_for_load_state("networkidle", timeout=15000)
        time.sleep(2)
        print(f"[3] After login. URL: {page.url}")

        if "/login" in page.url:
            print("    Login failed!")
            browser.close()
            return

        # Step 4: Navigate to EMP201 create page
        print("[4] Navigating to EMP201 create page...")
        page.evaluate("window.location.href = 'https://smartweigh.co.za/cims/emp201/create'")
        page.wait_for_load_state("networkidle", timeout=30000)
        time.sleep(3)
        current_url = page.url
        print(f"    URL: {current_url}")

        if "/login" in current_url:
            print("    Redirected to login.")
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
            page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_top.png", full_page=False)
            # Get the error text
            body_text = page.inner_text("body")
            print(f"    Error page text (first 1000 chars):\n{body_text[:1000]}")
            browser.close()
            return

        page_height = page.evaluate("document.body.scrollHeight")
        print(f"    Page height: {page_height}px")
        print(f"    Page title: {page.title()}")

        # Get all section headings
        headings = page.evaluate("""
            () => {
                const els = document.querySelectorAll('h1, h2, h3, h4, h5, h6, .card-header, .section-header, legend, .emp201-section-header');
                return Array.from(els).slice(0, 50).map(el => ({
                    tag: el.tagName,
                    text: el.innerText.trim().substring(0, 80),
                    top: el.getBoundingClientRect().top + window.pageYOffset
                }));
            }
        """)
        print("    Sections found:")
        for h in headings:
            if h['text']:
                print(f"      [{h['tag']}] at y={int(h['top'])}px: {h['text']}")

        # Screenshot 1: Top
        print("\n[6] Top section (SARS header + Part 1)...")
        page.evaluate("window.scrollTo(0, 0)")
        time.sleep(1)
        page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_top.png", full_page=False)
        print(f"    Saved: {OUTPUT_DIR}/emp201_sars_form_top.png")

        # Screenshot 2: Demographics - find by searching for keywords
        print("[7] Demographics section...")
        demo_pos = page.evaluate("""
            () => {
                const els = document.querySelectorAll('*');
                for (const el of els) {
                    const text = el.innerText || el.textContent || '';
                    if (text.includes('Demographics') || text.includes('DEMOGRAPHICS') || text.includes('Employer Details') || text.includes('EMPLOYER DETAILS')) {
                        if (el.tagName.match(/^(H[1-6]|DIV|SPAN|LEGEND|LABEL)$/i) && text.length < 100) {
                            return el.getBoundingClientRect().top + window.pageYOffset;
                        }
                    }
                }
                return null;
            }
        """)
        if demo_pos is not None:
            page.evaluate(f"window.scrollTo(0, {int(demo_pos) - 50})")
        else:
            print("    No Demographics heading found, scrolling to 700px")
            page.evaluate("window.scrollTo(0, 700)")
        time.sleep(1)
        page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_demographics.png", full_page=False)
        print(f"    Saved: {OUTPUT_DIR}/emp201_sars_form_demographics.png")

        # Screenshot 3: Financials
        print("[8] Financials section...")
        fin_pos = page.evaluate("""
            () => {
                const els = document.querySelectorAll('*');
                for (const el of els) {
                    const text = el.innerText || el.textContent || '';
                    if (text.includes('Financials') || text.includes('FINANCIALS') || text.includes('Payroll Tax Calculation') || text.includes('ETI Indicator')) {
                        if (el.tagName.match(/^(H[1-6]|DIV|SPAN|LEGEND|LABEL)$/i) && text.length < 100) {
                            return el.getBoundingClientRect().top + window.pageYOffset;
                        }
                    }
                }
                return null;
            }
        """)
        if fin_pos is not None:
            page.evaluate(f"window.scrollTo(0, {int(fin_pos) - 50})")
        else:
            print("    No Financials heading found, scrolling to 1400px")
            page.evaluate("window.scrollTo(0, 1400)")
        time.sleep(1)
        page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_financials.png", full_page=False)
        print(f"    Saved: {OUTPUT_DIR}/emp201_sars_form_financials.png")

        # Screenshot 4: Part 4
        print("[9] Part 4 (VDP / Tax Practitioner / Notes)...")
        p4_pos = page.evaluate("""
            () => {
                const els = document.querySelectorAll('*');
                for (const el of els) {
                    const text = el.innerText || el.textContent || '';
                    if (text.includes('Voluntary Disclosure') || text.includes('Tax Practitioner') || text.includes('VDP')) {
                        if (el.tagName.match(/^(H[1-6]|DIV|SPAN|LEGEND|LABEL)$/i) && text.length < 100) {
                            return el.getBoundingClientRect().top + window.pageYOffset;
                        }
                    }
                }
                return null;
            }
        """)
        if p4_pos is not None:
            page.evaluate(f"window.scrollTo(0, {int(p4_pos) - 50})")
        else:
            print("    No Part 4 heading found, scrolling to 2100px")
            page.evaluate("window.scrollTo(0, 2100)")
        time.sleep(1)
        page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_part4.png", full_page=False)
        print(f"    Saved: {OUTPUT_DIR}/emp201_sars_form_part4.png")

        # Screenshot 5: Upload Documents
        print("[10] Upload Documents section...")
        upload_pos = page.evaluate("""
            () => {
                const els = document.querySelectorAll('*');
                for (const el of els) {
                    const text = el.innerText || el.textContent || '';
                    if (text.includes('Upload Documents') || text.includes('UPLOAD DOCUMENTS')) {
                        if (el.tagName.match(/^(H[1-6]|DIV|SPAN|LEGEND|LABEL)$/i) && text.length < 100) {
                            return el.getBoundingClientRect().top + window.pageYOffset;
                        }
                    }
                }
                // Fallback: scroll to bottom
                return document.body.scrollHeight - 720;
            }
        """)
        page.evaluate(f"window.scrollTo(0, {int(upload_pos) - 50})")
        time.sleep(1)
        page.screenshot(path=f"{OUTPUT_DIR}/emp201_sars_form_uploads.png", full_page=False)
        print(f"    Saved: {OUTPUT_DIR}/emp201_sars_form_uploads.png")

        # Print page body text summary
        body_text = page.inner_text("body")
        print(f"\n--- Page title: {page.title()} ---")
        print(f"--- Body text (first 3000 chars) ---")
        print(body_text[:3000])
        print("--- End ---")

        browser.close()
        print("\nDone. All 5 screenshots captured successfully.")

if __name__ == "__main__":
    run()
