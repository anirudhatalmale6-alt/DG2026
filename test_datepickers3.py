from playwright.sync_api import sync_playwright
import time

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    context = browser.new_context(viewport={"width": 1280, "height": 720})
    page = context.new_page()

    # Capture console logs
    console_logs = []
    page.on("console", lambda msg: console_logs.append(f"[{msg.type}] {msg.text}"))

    # Navigate to Persons create form
    print("Loading Persons create form...")
    page.goto("https://smartweigh.co.za/cims/persons/create")
    time.sleep(5)

    # Print console logs
    print("\n=== Console Logs ===")
    for log in console_logs:
        print(log)
    print("=== End Console Logs ===\n")

    # Check the page HTML around datepicker
    date_of_issue_html = page.evaluate('''() => {
        var el = document.getElementById('date_of_issue');
        if (!el) return 'date_of_issue element NOT FOUND';
        return {
            type: el.type,
            className: el.className,
            value: el.value,
            parentHTML: el.parentElement.innerHTML.substring(0, 500)
        };
    }''')
    print(f"date_of_issue element: {date_of_issue_html}")

    # Check if flatpickr is loaded
    flatpickr_loaded = page.evaluate('() => typeof flatpickr')
    print(f"flatpickr type: {flatpickr_loaded}")

    # Check if SmartDashDates is loaded
    smartdash_loaded = page.evaluate('() => typeof SmartDashDates')
    print(f"SmartDashDates type: {smartdash_loaded}")

    # Check all flatpickr instances
    fp_count = page.evaluate('() => document.querySelectorAll(".flatpickr-calendar").length')
    print(f"Flatpickr calendar elements: {fp_count}")

    # Check all inputs with flatpickr-input class
    fp_inputs = page.evaluate('() => { var inputs = document.querySelectorAll(".flatpickr-input"); return inputs.length; }')
    print(f"Flatpickr input elements: {fp_inputs}")

    # Check all datepicker-past elements
    dp_past = page.evaluate('''() => {
        var els = document.querySelectorAll('.datepicker-past');
        var results = [];
        els.forEach(function(el) {
            results.push({
                id: el.id,
                type: el.type,
                className: el.className,
                value: el.value
            });
        });
        return results;
    }''')
    print(f"datepicker-past elements: {dp_past}")

    # Scroll to date fields and take screenshot
    page.evaluate("window.scrollTo(0, 250)")
    time.sleep(1)
    page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_persons_debug.png")

    # Try clicking the visible input next to date_of_issue
    try:
        # With altInput, the original becomes hidden and a visible clone is created next to it
        result = page.evaluate('''() => {
            var orig = document.getElementById('date_of_issue');
            if (!orig) return 'not found';
            var next = orig.nextElementSibling;
            if (next) {
                return {
                    tag: next.tagName,
                    type: next.type,
                    className: next.className,
                    placeholder: next.placeholder
                };
            }
            return 'no next sibling';
        }''')
        print(f"Next sibling of date_of_issue: {result}")
    except Exception as e:
        print(f"Error: {e}")

    browser.close()
    print("Done!")
