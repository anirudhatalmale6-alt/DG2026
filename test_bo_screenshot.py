from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch()
    page = browser.new_page()
    page.set_viewport_size({"width": 1280, "height": 720})

    # Login
    page.goto("https://smartweigh.co.za/login")
    page.wait_for_load_state("networkidle")
    page.fill('input[name="email"]', 'krish@atpgroup.co.za')
    page.fill('input[name="password"]', 'Admin@2024')
    page.locator('button:has-text("Continue")').click()
    page.wait_for_load_state("networkidle")
    page.wait_for_timeout(3000)

    # Go to job card show page
    page.goto("https://smartweigh.co.za/job-cards/1")
    page.wait_for_load_state("networkidle")
    page.wait_for_timeout(2000)
    page.screenshot(path="bo_screenshot_1.png")
    print("Screenshot 1 saved")

    # Try to find the BO panel
    bo_header = page.query_selector('.bo-header')
    if bo_header:
        bo_header.click()
        page.wait_for_timeout(3000)
        page.screenshot(path="bo_screenshot_2.png")
        print("Screenshot 2 saved (expanded)")

        # Scroll to BO panel
        page.evaluate("document.querySelector('.bo-panel').scrollIntoView({block:'start'})")
        page.wait_for_timeout(500)
        page.screenshot(path="bo_screenshot_3.png")
        print("Screenshot 3 saved (scrolled)")
    else:
        print("BO panel not found - check page HTML")
        # Take HTML dump
        html = page.content()
        with open("bo_debug.html", "w") as f:
            f.write(html[:5000])
        print("Saved first 5000 chars of HTML to bo_debug.html")

    browser.close()
