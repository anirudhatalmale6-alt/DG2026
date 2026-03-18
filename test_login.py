from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch()
    page = browser.new_page()
    page.set_viewport_size({"width": 1280, "height": 720})

    page.goto("https://smartweigh.co.za/login")
    page.wait_for_load_state("networkidle")
    page.wait_for_timeout(2000)

    # Get all form inputs
    inputs = page.query_selector_all('input')
    for inp in inputs:
        name = inp.get_attribute('name')
        typ = inp.get_attribute('type')
        print(f"Input: name={name}, type={typ}")

    # Get all buttons
    buttons = page.query_selector_all('button')
    for btn in buttons:
        text = btn.text_content().strip()
        typ = btn.get_attribute('type')
        print(f"Button: text={text}, type={typ}")

    # Try filling and submitting
    page.fill('input[name="email"]', 'krish@atpgroup.co.za')
    page.fill('input[name="password"]', 'Admin@2024')
    page.screenshot(path="login_filled.png")
    print("Filled form")

    # Submit the form
    page.click('button:has-text("Continue")')
    page.wait_for_timeout(5000)
    print(f"After login URL: {page.url}")
    page.screenshot(path="login_after.png")

    browser.close()
