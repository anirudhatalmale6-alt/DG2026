from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch()
    page = browser.new_page()
    page.set_viewport_size({"width": 1280, "height": 720})
    
    # Login
    page.goto("https://smartweigh.co.za/login")
    page.wait_for_load_state('networkidle')
    
    # Check if there's a login form
    email_field = page.query_selector('input[name="email"]')
    if email_field:
        page.fill('input[name="email"]', 'admin@smartweigh.co.za')
        page.fill('input[name="password"]', 'password')
        page.click('button[type="submit"]')
        page.wait_for_load_state('networkidle')
        page.wait_for_timeout(2000)
        
        url = page.url
        print(f"After login URL: {url}")
        page.screenshot(path="/var/lib/freelancer/projects/40170867/stmt_login.png")
    
    # Try direct navigation  
    page.goto("https://smartweigh.co.za/cims/emp201/statement")
    page.wait_for_load_state('networkidle')
    page.wait_for_timeout(2000)
    
    url = page.url
    print(f"Statement page URL: {url}")
    
    # Check for dropdowns
    clients = page.query_selector_all('#selClient option')
    print(f"Client options: {len(clients)}")
    
    page.screenshot(path="/var/lib/freelancer/projects/40170867/stmt_page3.png")
    
    browser.close()
