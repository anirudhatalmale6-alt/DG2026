from playwright.sync_api import sync_playwright
import time

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    context = browser.new_context(viewport={"width": 1280, "height": 720})
    page = context.new_page()

    # Login to CIMS (SmartDash)
    print("Logging in via CIMS...")
    page.goto("https://smartweigh.co.za/cims/persons")
    time.sleep(3)

    # Check if we need to login
    if "login" in page.url.lower() or "Sign in" in page.content():
        print(f"At login page: {page.url}")
        # Try the CIMS login
        try:
            page.fill('input[name="email"]', 'admin@smartweigh.co.za')
            page.fill('input[name="password"]', 'Admin@2026')
            page.click('button[type="submit"]')
            time.sleep(3)
            print(f"After login: {page.url}")
        except Exception as e:
            print(f"Login error: {e}")

    # Navigate to Persons create form
    print("Loading Persons create form...")
    page.goto("https://smartweigh.co.za/cims/persons/create")
    time.sleep(4)
    page.set_viewport_size({"width": 1280, "height": 720})

    # Scroll down to see the date fields
    page.evaluate("window.scrollTo(0, 300)")
    time.sleep(1)
    page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_persons_dates.png")
    print(f"Persons page URL: {page.url}")

    # Try clicking on a date field's visible input
    try:
        # The flatpickr alt input should be visible
        visible_inputs = page.locator('input.form-control.flatpickr-input[type="text"]')
        count = visible_inputs.count()
        print(f"Found {count} flatpickr visible inputs")

        if count > 0:
            visible_inputs.first.click()
            time.sleep(1)
            page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_persons_dp_open.png")
            print("Persons datepicker open screenshot saved.")
    except Exception as e:
        print(f"Error with flatpickr: {e}")

    # EMP201 create form
    print("Loading EMP201 create form...")
    page.goto("https://smartweigh.co.za/cims/emp201/create")
    time.sleep(4)
    page.set_viewport_size({"width": 1280, "height": 720})
    page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_emp201_dates.png")
    print(f"EMP201 page URL: {page.url}")

    # Try clicking date field
    try:
        visible_inputs = page.locator('input.form-control.flatpickr-input[type="text"]')
        count = visible_inputs.count()
        print(f"Found {count} flatpickr visible inputs on EMP201")

        if count > 0:
            visible_inputs.first.click()
            time.sleep(1)
            page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_emp201_dp_open.png")
            print("EMP201 datepicker open screenshot saved.")
    except Exception as e:
        print(f"Error: {e}")

    # Client Master create form
    print("Loading Client Master create form...")
    page.goto("https://smartweigh.co.za/cims/clients/create")
    time.sleep(4)
    page.set_viewport_size({"width": 1280, "height": 720})

    # Scroll down to see date fields
    page.evaluate("window.scrollTo(0, 400)")
    time.sleep(1)
    page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_clients_dates.png")
    print(f"Clients page URL: {page.url}")

    # Check for flatpickr
    try:
        visible_inputs = page.locator('input.form-control.flatpickr-input[type="text"]')
        count = visible_inputs.count()
        print(f"Found {count} flatpickr visible inputs on Clients")

        # Also check for datepicker-past elements
        dp_past = page.locator('.datepicker-past')
        dp_count = dp_past.count()
        print(f"Found {dp_count} datepicker-past elements on Clients")

        if count > 0:
            visible_inputs.first.click()
            time.sleep(1)
            page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_clients_dp_open.png")
            print("Clients datepicker open screenshot saved.")
    except Exception as e:
        print(f"Error: {e}")

    browser.close()
    print("Done!")
