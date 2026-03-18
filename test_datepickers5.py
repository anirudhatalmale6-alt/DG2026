from playwright.sync_api import sync_playwright
import time

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    context = browser.new_context(viewport={"width": 1280, "height": 720})
    page = context.new_page()

    # Auto-login and go to EMP201 create
    print("Auto-login for EMP201...")
    page.goto("https://smartweigh.co.za/auto_login_dp.php?to=/cims/emp201/create")
    time.sleep(5)
    print(f"EMP201 URL: {page.url}")

    if "/cims/emp201" in page.url:
        page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_emp201_top.png")
        print("EMP201 form screenshot saved.")

        # Check flatpickr initialization
        fp_check = page.evaluate('''() => {
            return {
                flatpickrType: typeof flatpickr,
                smartDashType: typeof SmartDashDates,
                calendarCount: document.querySelectorAll('.flatpickr-calendar').length,
                dpPastCount: document.querySelectorAll('.datepicker-past').length
            };
        }''')
        print(f"EMP201 flatpickr check: {fp_check}")

        # Click the declaration_date alt input
        try:
            page.evaluate('''() => {
                var orig = document.getElementById('declaration_date');
                if (orig) {
                    var altInput = orig.nextElementSibling;
                    if (altInput) altInput.click();
                }
            }''')
            time.sleep(1)
            page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_emp201_dp_open.png")
            print("EMP201 datepicker opened!")

            # Check the date value
            date_val = page.evaluate('''() => {
                var orig = document.getElementById('declaration_date');
                return orig ? { value: orig.value, type: orig.type } : 'not found';
            }''')
            print(f"declaration_date: {date_val}")
        except Exception as e:
            print(f"Error: {e}")
    else:
        page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_emp201_fail.png")
        print("Failed to reach EMP201 page")

    # Close calendar
    page.keyboard.press("Escape")
    time.sleep(0.5)

    # Now check Client Master
    print("\nLoading Client Master form...")
    page.goto("https://smartweigh.co.za/auto_login_dp.php?to=/cims/clients/create")
    time.sleep(5)
    print(f"Clients URL: {page.url}")

    if "/cims/clients" in page.url:
        # Scroll to see date fields
        page.evaluate("window.scrollTo(0, 350)")
        time.sleep(1)
        page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_clients_dates2.png")
        print("Clients form screenshot saved.")

        # Check flatpickr
        fp_check = page.evaluate('''() => {
            return {
                flatpickrType: typeof flatpickr,
                smartDashType: typeof SmartDashDates,
                calendarCount: document.querySelectorAll('.flatpickr-calendar').length,
                dpPastCount: document.querySelectorAll('.datepicker-past').length
            };
        }''')
        print(f"Clients flatpickr check: {fp_check}")

        # Click a datepicker
        try:
            page.evaluate('''() => {
                var dpPast = document.querySelectorAll('.datepicker-past');
                for (var i = 0; i < dpPast.length; i++) {
                    if (dpPast[i].type === 'text') {
                        dpPast[i].click();
                        return;
                    }
                }
                // Try the hidden one's next sibling
                if (dpPast.length > 0 && dpPast[0].type === 'hidden') {
                    var alt = dpPast[0].nextElementSibling;
                    if (alt) alt.click();
                }
            }''')
            time.sleep(1)
            page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_clients_dp_open.png")
            print("Clients datepicker opened!")
        except Exception as e:
            print(f"Error: {e}")
    else:
        page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_clients_fail.png")
        print("Failed to reach Clients page")

    browser.close()
    print("Done!")
