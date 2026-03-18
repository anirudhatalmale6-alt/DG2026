from playwright.sync_api import sync_playwright
import time

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_page()
    page.set_viewport_size({"width": 1280, "height": 720})

    # Login first
    print("Navigating to login...")
    page.goto("https://smartweigh.co.za/login", wait_until="networkidle", timeout=30000)
    time.sleep(2)
    page.screenshot(path="/var/lib/freelancer/projects/40170867/screenshots/login.png")
    print("Login page loaded")

    # Fill login - use admin credentials
    page.fill('input[name="email"]', 'admin@smartweigh.co.za')
    page.fill('input[name="password"]', 'Admin@2026!')
    page.click('button[type="submit"]')
    time.sleep(3)
    page.screenshot(path="/var/lib/freelancer/projects/40170867/screenshots/after_login.png")
    print(f"After login URL: {page.url}")

    # Navigate to appointments dashboard
    print("Navigating to appointments dashboard...")
    page.goto("https://smartweigh.co.za/cims/appointments/dashboard", wait_until="networkidle", timeout=30000)
    time.sleep(3)
    page.screenshot(path="/var/lib/freelancer/projects/40170867/screenshots/appt_dashboard.png")
    print(f"Dashboard URL: {page.url}")
    print(f"Dashboard title: {page.title()}")

    # Check for errors in console
    errors = []
    page.on("console", lambda msg: errors.append(msg.text) if msg.type == "error" else None)

    # Check page content
    content = page.content()
    if "404" in content[:5000]:
        print("WARNING: Page shows 404 error")
    elif "Appointments" in content:
        print("SUCCESS: Appointments page loaded")
    else:
        print("UNKNOWN: Page content does not match expected")
        print(f"First 500 chars: {content[:500]}")

    browser.close()
