from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch()
    context = browser.new_context(viewport={"width": 1280, "height": 900})
    page = context.new_page()
    
    # Login
    page.goto("https://www.smartweigh.co.za/login")
    page.wait_for_load_state("networkidle")
    page.wait_for_timeout(1000)
    
    # Fill credentials
    page.fill('input[name="email"]', 'admin@smartweigh.co.za')
    page.fill('input[name="password"]', 'admin')
    
    # Click Continue button
    page.click('text=Continue')
    page.wait_for_load_state("networkidle")
    page.wait_for_timeout(2000)
    
    # Check if we're logged in
    current_url = page.url
    print(f"After login URL: {current_url}")
    
    if "login" in current_url.lower():
        # Try different credentials
        page.screenshot(path="/var/lib/freelancer/projects/40170867/login_fail.png")
        print("Login failed - still on login page")
        # Check for error messages
        error = page.query_selector('.alert, .error, .text-danger')
        if error:
            print(f"Error: {error.inner_text()}")
    else:
        # Navigate to info sheet
        page.goto("https://www.smartweigh.co.za/cims/pm/client/16/info-sheet")
        page.wait_for_load_state("networkidle")
        page.wait_for_timeout(1500)
        
        page.set_viewport_size({"width": 1280, "height": 900})
        page.screenshot(path="/var/lib/freelancer/projects/40170867/infosheet_top.png")
        
        # Scroll to see full content
        page.evaluate("window.scrollTo(0, 400)")
        page.wait_for_timeout(500)
        page.screenshot(path="/var/lib/freelancer/projects/40170867/infosheet_mid.png")
        
        page.evaluate("window.scrollTo(0, document.body.scrollHeight)")
        page.wait_for_timeout(500)
        page.screenshot(path="/var/lib/freelancer/projects/40170867/infosheet_bottom.png")
        
        print("Screenshots taken!")
    
    browser.close()
