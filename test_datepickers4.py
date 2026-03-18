from playwright.sync_api import sync_playwright
import time

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    context = browser.new_context(viewport={"width": 1280, "height": 720})
    page = context.new_page()

    # Navigate to Persons create form
    print("Loading Persons create form...")
    page.goto("https://smartweigh.co.za/cims/persons/create")
    time.sleep(5)

    # Scroll to see date fields
    page.evaluate("window.scrollTo(0, 250)")
    time.sleep(1)

    # Click the visible alt input (next sibling of the hidden date_of_issue)
    page.evaluate('''() => {
        var orig = document.getElementById('date_of_issue');
        var altInput = orig.nextElementSibling;
        altInput.click();
    }''')
    time.sleep(1)
    page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_persons_dp_open.png")
    print("Persons datepicker open!")

    # Close the calendar
    page.keyboard.press("Escape")
    time.sleep(0.5)

    # Now try with auto-login for EMP201
    # Use a temporary auto-login PHP script
    print("Creating auto-login script...")

    browser.close()
    print("Done!")
