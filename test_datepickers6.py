from playwright.sync_api import sync_playwright
import time

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    context = browser.new_context(viewport={"width": 1280, "height": 720})
    page = context.new_page()

    # Auto-login and go to EMP201 create
    print("Auto-login for EMP201...")
    page.goto("https://smartweigh.co.za/auto_login_dp3.php?to=/cims/emp201/create")
    time.sleep(6)
    print(f"EMP201 URL: {page.url}")
    page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_emp201_form2.png")

    if "/cims/emp201" in page.url:
        print("EMP201 loaded!")

        # Check flatpickr
        fp_check = page.evaluate('''() => {
            return {
                flatpickrType: typeof flatpickr,
                smartDashType: typeof SmartDashDates,
                calendarCount: document.querySelectorAll('.flatpickr-calendar').length
            };
        }''')
        print(f"EMP201 flatpickr check: {fp_check}")

        # Click the declaration_date datepicker
        try:
            page.evaluate('''() => {
                var orig = document.getElementById('declaration_date');
                if (orig && orig.nextElementSibling) {
                    orig.nextElementSibling.click();
                }
            }''')
            time.sleep(1)
            page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_emp201_dp2.png")
            print("EMP201 datepicker screenshot saved!")

            # Check value
            val = page.evaluate('() => document.getElementById("declaration_date").value')
            print(f"declaration_date value: {val}")
        except Exception as e:
            print(f"Error: {e}")
    else:
        print(f"Failed. Current URL: {page.url}")

    # Close calendar
    page.keyboard.press("Escape")
    time.sleep(0.5)

    # Client Master
    print("\nLoading Client Master form...")
    page.goto("https://smartweigh.co.za/auto_login_dp3.php?to=/cims/clients/create")
    time.sleep(6)
    print(f"Clients URL: {page.url}")

    if "/cims/clients" in page.url:
        # Scroll to see date fields
        page.evaluate("window.scrollTo(0, 350)")
        time.sleep(1)
        page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_clients_form2.png")
        print("Clients form screenshot saved!")

        fp_check = page.evaluate('''() => {
            return {
                flatpickrType: typeof flatpickr,
                smartDashType: typeof SmartDashDates,
                calendarCount: document.querySelectorAll('.flatpickr-calendar').length,
                dpPastCount: document.querySelectorAll('.datepicker-past').length
            };
        }''')
        print(f"Clients flatpickr check: {fp_check}")

        # Click first datepicker
        try:
            page.evaluate('''() => {
                var dpElements = document.querySelectorAll('.datepicker-past');
                for (var el of dpElements) {
                    if (el.type === 'hidden' && el.nextElementSibling) {
                        el.nextElementSibling.click();
                        return;
                    }
                }
            }''')
            time.sleep(1)
            page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_clients_dp2.png")
            print("Clients datepicker screenshot saved!")
        except Exception as e:
            print(f"Error: {e}")
    else:
        print(f"Failed. Current URL: {page.url}")
        page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_clients_fail2.png")

    browser.close()
    print("Done!")
