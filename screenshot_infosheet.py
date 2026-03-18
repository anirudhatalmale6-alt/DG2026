from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch()
    context = browser.new_context(viewport={"width": 1280, "height": 900})
    page = context.new_page()
    
    # First login
    page.goto("https://www.smartweigh.co.za/login")
    page.wait_for_load_state("networkidle")
    page.fill('input[name="email"]', 'admin@smartweigh.co.za')
    page.fill('input[name="password"]', 'admin')
    page.click('button[type="submit"]')
    page.wait_for_load_state("networkidle")
    
    # Now go to info sheet
    page.goto("https://www.smartweigh.co.za/cims/pm/client/16/info-sheet")
    page.wait_for_load_state("networkidle")
    page.wait_for_timeout(1500)
    
    page.set_viewport_size({"width": 1280, "height": 900})
    page.screenshot(path="/var/lib/freelancer/projects/40170867/infosheet_screenshot.png")
    
    # Scroll down to see footer
    page.evaluate("window.scrollTo(0, document.body.scrollHeight)")
    page.wait_for_timeout(500)
    page.screenshot(path="/var/lib/freelancer/projects/40170867/infosheet_screenshot2.png")
    
    browser.close()
    print("Screenshots taken successfully")
