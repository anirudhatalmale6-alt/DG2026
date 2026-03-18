from playwright.sync_api import sync_playwright
import time

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    context = browser.new_context(viewport={"width": 1280, "height": 720})
    page = context.new_page()

    # Capture console errors
    errors = []
    page.on("console", lambda msg: errors.append(f"[{msg.type}] {msg.text}") if msg.type == "error" else None)

    # Navigate to Persons create form (no auth required for CIMS)
    print("Loading Persons create form...")
    page.goto("https://smartweigh.co.za/cims/persons/create")
    time.sleep(5)

    # Check if bootstrap material datepicker is loaded
    check = page.evaluate('''() => {
        return {
            moment: typeof moment,
            bootstrapMDP: typeof $.fn.bootstrapMaterialDatePicker,
            flatpickr: typeof flatpickr,
            SmartDashDates: typeof SmartDashDates,
            dateOfIssue: document.getElementById('date_of_issue') ? {
                type: document.getElementById('date_of_issue').type,
                value: document.getElementById('date_of_issue').value,
                className: document.getElementById('date_of_issue').className
            } : 'not found',
            dateOfIssueDisplay: document.getElementById('date_of_issue_display') ? {
                type: document.getElementById('date_of_issue_display').type,
                value: document.getElementById('date_of_issue_display').value,
                className: document.getElementById('date_of_issue_display').className
            } : 'not found'
        };
    }''')
    print(f"Checks: {check}")

    # Scroll to see date fields
    page.evaluate("window.scrollTo(0, 250)")
    time.sleep(1)
    page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_persons_material.png")

    # Try clicking the date field to open material datepicker
    try:
        # The legacy converter should have created a _display input
        display_input = page.locator('#date_of_issue_display')
        if display_input.count() > 0:
            display_input.click()
            print("Clicked date_of_issue_display")
        else:
            # Fallback - try original input
            orig = page.locator('#date_of_issue')
            if orig.count() > 0 and orig.is_visible():
                orig.click()
                print("Clicked date_of_issue directly")
            else:
                print("No visible date input found")

        time.sleep(2)
        page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_persons_material_open.png")
        print("Material datepicker screenshot saved!")
    except Exception as e:
        print(f"Error clicking: {e}")
        page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_persons_material_error.png")

    # Print any console errors
    if errors:
        print("\nConsole errors:")
        for e in errors:
            print(e)

    browser.close()
    print("Done!")
