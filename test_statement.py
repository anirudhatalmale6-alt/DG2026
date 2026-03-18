from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch()
    page = browser.new_page()
    page.set_viewport_size({"width": 1280, "height": 720})
    
    # Login first
    page.goto("https://smartweigh.co.za/login")
    page.fill('input[name="email"]', 'admin@example.com')
    page.fill('input[name="password"]', 'admin')
    page.click('button[type="submit"]')
    page.wait_for_load_state('networkidle')
    
    # Navigate to statement page
    page.goto("https://smartweigh.co.za/cims/emp201/statement")
    page.wait_for_load_state('networkidle')
    page.wait_for_timeout(2000)
    
    page.screenshot(path="/var/lib/freelancer/projects/40170867/stmt_page1.png")
    print("Screenshot 1 taken - initial page")
    
    # Try selecting a client and tax year
    # First check what dropdowns are available
    clients = page.query_selector_all('#selClient option')
    print(f"Found {len(clients)} client options")
    for c in clients[:5]:
        print(f"  Option: {c.get_attribute('value')} = {c.inner_text()}")
    
    tax_years = page.query_selector_all('#selTaxYear option')
    print(f"Found {len(tax_years)} tax year options")
    for ty in tax_years[:5]:
        print(f"  Option: {ty.get_attribute('value')} = {ty.inner_text()}")
    
    # Select first real client and tax year
    if len(clients) > 1 and len(tax_years) > 1:
        client_val = clients[1].get_attribute('value')
        year_val = tax_years[1].get_attribute('value')
        page.select_option('#selClient', client_val)
        page.select_option('#selTaxYear', year_val)
        print(f"Selected client={client_val}, year={year_val}")
        
        page.wait_for_timeout(500)
        
        # Click Generate
        page.click('#btnLoad')
        page.wait_for_timeout(3000)
        
        page.screenshot(path="/var/lib/freelancer/projects/40170867/stmt_page2.png")
        print("Screenshot 2 taken - after generate")
        
        # Check for errors
        content = page.content()
        if 'swal2' in content.lower() or 'error' in content.lower():
            # Check browser console
            pass
    
    browser.close()
