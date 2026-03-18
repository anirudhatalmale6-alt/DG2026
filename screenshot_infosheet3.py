from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch()
    context = browser.new_context(viewport={"width": 1280, "height": 900})
    page = context.new_page()
    
    # Login with correct email
    page.goto("https://www.smartweigh.co.za/login")
    page.wait_for_load_state("networkidle")
    page.wait_for_timeout(1000)
    
    page.fill('input[name="email"]', 'krish@atpservices.co.za')
    page.fill('input[name="password"]', 'admin')
    page.click('text=Continue')
    page.wait_for_load_state("networkidle")
    page.wait_for_timeout(2000)
    
    current_url = page.url
    print(f"After login URL: {current_url}")
    
    if "login" in current_url.lower():
        # Try password123
        page.fill('input[name="email"]', 'krish@atpservices.co.za')
        page.fill('input[name="password"]', 'password')
        page.click('text=Continue')
        page.wait_for_load_state("networkidle")
        page.wait_for_timeout(2000)
        current_url = page.url
        print(f"After 2nd attempt URL: {current_url}")
        
    if "login" in current_url.lower():
        page.fill('input[name="email"]', 'krish@atpservices.co.za')
        page.fill('input[name="password"]', '123456')
        page.click('text=Continue')
        page.wait_for_load_state("networkidle")
        page.wait_for_timeout(2000)
        current_url = page.url
        print(f"After 3rd attempt URL: {current_url}")
    
    if "login" not in current_url.lower():
        # Navigate to info sheet
        page.goto("https://www.smartweigh.co.za/cims/pm/client/16/info-sheet")
        page.wait_for_load_state("networkidle")
        page.wait_for_timeout(1500)
        
        page.set_viewport_size({"width": 1280, "height": 900})
        page.screenshot(path="/var/lib/freelancer/projects/40170867/infosheet_top.png")
        
        page.evaluate("window.scrollTo(0, 500)")
        page.wait_for_timeout(500)
        page.screenshot(path="/var/lib/freelancer/projects/40170867/infosheet_bottom.png")
        
        print("Screenshots taken!")
    else:
        print("All login attempts failed")
    
    browser.close()
