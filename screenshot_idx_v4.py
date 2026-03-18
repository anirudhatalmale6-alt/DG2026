import asyncio
from playwright.async_api import async_playwright

async def main():
    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)
        context = await browser.new_context(
            viewport={"width": 1280, "height": 720},
            ignore_https_errors=True
        )
        page = await context.new_page()

        # Step 1: Auto-login via temp_login5.php (sets session + redirects to /cims/emp201)
        print("Navigating to temp_login5.php for auto-login...")
        await page.goto("https://smartweigh.co.za/temp_login5.php", wait_until="networkidle", timeout=60000)
        print(f"After login, URL: {page.url}")

        # Wait for the page to fully load
        await page.wait_for_load_state("networkidle")
        await asyncio.sleep(2)
        print(f"Final URL: {page.url}")

        # Scroll down 350px
        await page.evaluate("window.scrollBy(0, 350)")
        await asyncio.sleep(1)

        # Take screenshot of the table area
        await page.screenshot(path="/var/lib/freelancer/projects/40170867/idx_v4_table.png")
        print("Saved idx_v4_table.png")

        # Find and click the three-dot action button (first one in the table)
        # Look for the action dropdown trigger button
        action_btn = page.locator("button[data-toggle='dropdown'], a[data-toggle='dropdown'], .dropdown-toggle").first
        if await action_btn.count() > 0:
            print("Found dropdown toggle, clicking...")
            await action_btn.click()
        else:
            # Try alternative selectors
            action_btn = page.locator(".btn-group .btn, .action-btn, [data-toggle='dropdown']").first
            if await action_btn.count() > 0:
                print("Found alternative dropdown toggle, clicking...")
                await action_btn.click()
            else:
                print("WARNING: Could not find dropdown toggle button")

        # Wait for dropdown animation
        await asyncio.sleep(0.5)

        # Take screenshot with dropdown open
        await page.screenshot(path="/var/lib/freelancer/projects/40170867/idx_v4_dropdown.png")
        print("Saved idx_v4_dropdown.png")

        await browser.close()

asyncio.run(main())
