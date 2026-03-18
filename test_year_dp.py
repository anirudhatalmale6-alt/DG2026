from playwright.sync_api import sync_playwright
import time

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    context = browser.new_context(viewport={"width": 1280, "height": 720})
    page = context.new_page()

    errors = []
    page.on("console", lambda msg: errors.append(f"[{msg.type}] {msg.text}") if msg.type == "error" else None)

    # Load EMP201 create form (uses layouts.default which needs auth check)
    # Persons doesn't need auth, but EMP201 goes through GrowCRM auth
    # Let's try the CIMS Persons page first to verify layout, then try EMP201
    print("Loading EMP201 create form...")
    page.goto("https://smartweigh.co.za/cims/emp201/create")
    time.sleep(5)
    print(f"URL: {page.url}")

    if "/cims/emp201" in page.url:
        page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_emp201_year.png")
        print("EMP201 form loaded!")

        # Check for Year dropdown
        year_check = page.evaluate('''() => {
            var yearSelect = document.getElementById('tax_year');
            var periodSelect = document.getElementById('period_id');
            return {
                yearExists: !!yearSelect,
                yearOptions: yearSelect ? yearSelect.options.length : 0,
                periodExists: !!periodSelect,
                periodDisabled: periodSelect ? periodSelect.disabled : null
            };
        }''')
        print(f"Year/Period check: {year_check}")
    else:
        print(f"Redirected to: {page.url}")
        page.screenshot(path="/var/lib/freelancer/projects/40170867/ss_emp201_year_fail.png")

    if errors:
        print("\nConsole errors:")
        for e in errors[:5]:
            print(e)

    browser.close()
    print("Done!")
