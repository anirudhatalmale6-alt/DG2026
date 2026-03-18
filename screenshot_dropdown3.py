from playwright.sync_api import sync_playwright
import time

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    context = browser.new_context(viewport={"width": 1280, "height": 720})
    page = context.new_page()
    
    page.goto("https://smartweigh.co.za/temp_login7.php", wait_until="networkidle", timeout=30000)
    time.sleep(3)
    
    # Scroll down to see table
    page.evaluate("window.scrollBy(0, 380)")
    time.sleep(1)
    
    # Click the three-dot action button in the table row
    # Target specifically: the button with ellipsis-v inside the emp201-table
    action_btn = page.locator("table.emp201-table tbody td:last-child button").first
    action_btn.click()
    time.sleep(1)
    
    page.screenshot(path="/var/lib/freelancer/projects/40170867/idx_v4_dropdown3.png")
    
    browser.close()
