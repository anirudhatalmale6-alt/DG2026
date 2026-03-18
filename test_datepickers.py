from playwright.sync_api import sync_playwright
import time

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    context = browser.new_context(viewport={"width": 1280, "height": 720})
    page = context.new_page()

    # First, auto-login
    print("Logging in...")
    page.goto("https://smartweigh.co.za/login")
    time.sleep(2)

    # Fill login form
    page.fill('input[name="email"]', 'admin@smartweigh.co.za')
    page.fill('input[name="password"]', 'Admin@2026')
    page.click('button[type="submit"]')
    time.sleep(3)

    # Screenshot 1: Persons - edit form with datepicker
    print("Loading Persons form...")
    page.goto("https://smartweigh.co.za/cims/persons/create")
    time.sleep(3)
    page.set_viewport_size({"width": 1280, "height": 720})
    page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_persons_form.png")
    print("Persons form screenshot saved.")

    # Try clicking on a datepicker field in Persons
    try:
        date_input = page.locator('.datepicker-past').first
        if date_input.count() > 0:
            # With flatpickr altInput, the visible input is the alt input (sibling)
            alt_input = page.locator('input.flatpickr-input[readonly]').first
            if alt_input.count() > 0:
                alt_input.click()
                print("Clicked flatpickr alt input")
            else:
                date_input.click()
                print("Clicked datepicker-past input directly")
            time.sleep(1)
            page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_persons_datepicker.png")
            print("Persons datepicker open screenshot saved.")
        else:
            print("No datepicker-past found on page")
    except Exception as e:
        print(f"Error clicking datepicker: {e}")
        page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_persons_datepicker.png")

    # Screenshot 2: Client Master - edit form with datepicker
    print("Loading Client Master form...")
    page.goto("https://smartweigh.co.za/cims/clients/create")
    time.sleep(3)
    page.set_viewport_size({"width": 1280, "height": 720})
    page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_clients_form.png")
    print("Client form screenshot saved.")

    # Try clicking on a datepicker field in Client Master
    try:
        alt_input = page.locator('input.flatpickr-input[readonly]').first
        if alt_input.count() > 0:
            alt_input.click()
            print("Clicked client flatpickr alt input")
        else:
            date_input = page.locator('.datepicker-past').first
            if date_input.count() > 0:
                date_input.click()
                print("Clicked client datepicker-past directly")
            else:
                print("No datepicker found on client page")
        time.sleep(1)
        page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_clients_datepicker.png")
        print("Client datepicker screenshot saved.")
    except Exception as e:
        print(f"Error: {e}")
        page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_clients_datepicker.png")

    # Screenshot 3: EMP201 form with datepicker
    print("Loading EMP201 form...")
    page.goto("https://smartweigh.co.za/cims/emp201/create")
    time.sleep(3)
    page.set_viewport_size({"width": 1280, "height": 720})
    page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_emp201_form.png")
    print("EMP201 form screenshot saved.")

    browser.close()
    print("Done!")
