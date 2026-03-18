from playwright.sync_api import sync_playwright
import time

with sync_playwright() as p:
    browser = p.chromium.launch()
    page = browser.new_page()
    page.set_viewport_size({"width": 1280, "height": 720})
    page.goto("http://localhost:3098/", wait_until="networkidle", timeout=15000)
    time.sleep(2)
    
    # Hero section
    page.screenshot(path="atp_hero.png")
    
    # Scroll to services
    page.evaluate("window.scrollTo(0, 800)")
    time.sleep(1)
    page.screenshot(path="atp_services.png")
    
    # Scroll to about/stats
    page.evaluate("window.scrollTo(0, 1800)")
    time.sleep(1)
    page.screenshot(path="atp_about.png")
    
    # Scroll to process
    page.evaluate("window.scrollTo(0, 2600)")
    time.sleep(1)
    page.screenshot(path="atp_process.png")
    
    # Scroll to testimonials + CTA
    page.evaluate("window.scrollTo(0, 3400)")
    time.sleep(1)
    page.screenshot(path="atp_testimonials.png")
    
    # Scroll to contact
    page.evaluate("window.scrollTo(0, 4200)")
    time.sleep(1)
    page.screenshot(path="atp_contact.png")
    
    # Scroll to footer
    page.evaluate("window.scrollTo(0, document.body.scrollHeight)")
    time.sleep(1)
    page.screenshot(path="atp_footer.png")
    
    browser.close()
    print("All screenshots taken!")
