from playwright.sync_api import sync_playwright
import sys

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_page()
    page.set_viewport_size({"width": 1280, "height": 720})
    
    console_messages = []
    page.on("console", lambda msg: console_messages.append(f"{msg.type}: {msg.text}"))
    
    page.on("pageerror", lambda err: console_messages.append(f"PAGE ERROR: {err}"))
    
    try:
        page.goto("https://smartweigh.co.za/cims/clientmaster/create", timeout=30000, wait_until="networkidle")
    except Exception as e:
        print(f"Navigation error: {e}")
    
    page.screenshot(path="/var/lib/freelancer/projects/40170867/page_test.png")
    
    print("=== Console Messages ===")
    for msg in console_messages:
        print(msg)
    
    print(f"\nPage title: {page.title()}")
    print(f"Page URL: {page.url}")
    
    browser.close()
