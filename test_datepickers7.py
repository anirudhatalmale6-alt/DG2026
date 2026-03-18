from playwright.sync_api import sync_playwright
import time

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    context = browser.new_context(viewport={"width": 1280, "height": 720})
    page = context.new_page()

    # Login through the GrowCRM login form
    print("Logging in via GrowCRM form...")
    page.goto("https://smartweigh.co.za/login")
    time.sleep(3)

    # Fill login form
    page.fill('input[name="email"]', 'krish@atpservices.co.za')
    page.fill('input[name="password"]', 'Admin@2026')

    # Submit
    page.click('button:has-text("Continue")')
    time.sleep(5)
    print(f"After login URL: {page.url}")
    page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_after_login.png")

    # Now navigate to EMP201
    print("Loading EMP201 form...")
    page.goto("https://smartweigh.co.za/cims/emp201/create")
    time.sleep(5)
    print(f"EMP201 URL: {page.url}")

    if "/cims/emp201" in page.url:
        page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_emp201_form3.png")
        print("EMP201 loaded!")

        # Click declaration_date datepicker
        try:
            page.evaluate('''() => {
                var orig = document.getElementById('declaration_date');
                if (orig && orig.nextElementSibling) {
                    orig.nextElementSibling.click();
                }
            }''')
            time.sleep(1)
            page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_emp201_dp3.png")

            val = page.evaluate('() => document.getElementById("declaration_date") ? document.getElementById("declaration_date").value : "not found"')
            print(f"declaration_date value: {val}")
        except Exception as e:
            print(f"Error: {e}")
    else:
        print(f"Failed to reach EMP201. URL: {page.url}")
        page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_emp201_fail2.png")

    # Close and navigate to Clients
    page.keyboard.press("Escape")
    time.sleep(0.5)

    print("\nLoading Client Master form...")
    page.goto("https://smartweigh.co.za/cims/clients/create")
    time.sleep(5)
    print(f"Clients URL: {page.url}")

    if "/cims/clients" in page.url:
        page.evaluate("window.scrollTo(0, 350)")
        time.sleep(1)
        page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_clients_form3.png")
        print("Clients form loaded!")

        fp_check = page.evaluate('''() => {
            return {
                flatpickrType: typeof flatpickr,
                smartDashType: typeof SmartDashDates,
                calendarCount: document.querySelectorAll('.flatpickr-calendar').length
            };
        }''')
        print(f"Clients flatpickr: {fp_check}")

        try:
            page.evaluate('''() => {
                var dpElements = document.querySelectorAll('.datepicker-past');
                for (var el of dpElements) {
                    if (el.type === 'hidden' && el.nextElementSibling) {
                        el.nextElementSibling.click();
                        return 'clicked alt input';
                    }
                }
                return 'no hidden dp found';
            }''')
            time.sleep(1)
            page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_clients_dp3.png")
            print("Clients datepicker screenshot saved!")
        except Exception as e:
            print(f"Error: {e}")
    else:
        page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_clients_fail3.png")

    browser.close()
    print("Done!")
