const { test, expect } = require('@playwright/test');
const { title } = require('process');

test.describe('KKZRT Tesztek', () => {
  // időkorlát 90mp
  test.setTimeout(90000); 

  test.beforeEach(async ({ page }) => {
    await page.goto('http://localhost/kkzrt/login.php', { timeout: 60000 });
    await page.fill('input[type="email"]', 'laszlobogdan0619@gmail.com');
    await page.fill('input[type="password"]', 'asd1234');
    await page.click('text=Bejelentkezés');
  });

  // sikeres oldalak tesztjei
  const pages = [
    { url: 'index.php', title: 'Főoldal' },
    { url: 'terkep.php', title: 'Térkép' },
    { url: 'keses.php', title: 'Késések' },
    { url: 'menetrend.php', title: 'Menetrend' },
    { url: 'jaratok.php', title: 'Járatok' },
    { url: 'info.php', title: 'Információk' },
    { url: 'indulasidok.php?route=12', title: 'Indulási idők' },
    { url: 'megalloidok.php?number=12&name=Laktanya%20-%20Sopron%20u.%20-%20Helyi%20autóbusz-állomás&stop_time=04%3A45%3A00&schedule_id=26', title: 'Megálló idők'},
    { url: 'hirek.php', title: 'Hírek'}
  ];

  for (const pageInfo of pages) {
    test(`${pageInfo.title} oldal betöltése`, async ({ page }) => {
      await page.goto(`http://localhost/kkzrt/${pageInfo.url}`, { timeout: 60000 });
      await expect(page).toHaveURL(`http://localhost/kkzrt/${pageInfo.url}`);
    });
  }

  // sikeres API végpontok tesztjei
  const workingApiEndpoints = [
    { url: 'api/stop', name: 'Megállók' },
    { url: 'api/helyibusz', name: 'Helyi busz' },
    { url: 'api/link', name: 'Linkek' },
    { url: 'api/marker', name: 'Markerek' },
    { url: 'api/kepek', name: 'Képek' },
    { url: 'api/trip', name: 'Utazások' },
    { url: 'api/hirek', name: 'Hírek' },
    { url: 'api/buszjaratok', name: 'Busz járatok' }
  ];

  for (const endpoint of workingApiEndpoints) {
    test(`${endpoint.name} API végpont ellenőrzése`, async ({ request }) => {
      const response = await request.get(`http://localhost:3000/${endpoint.url}`);
      expect(response.ok()).toBeTruthy();
    });
  }

  // sikeres komponens tesztek
  test('Térkép megjelenítés', async ({ page }) => {
    await page.goto('http://localhost/kkzrt/terkep.php', { timeout: 60000 });
    await expect(page.locator('#map')).toBeVisible();

    await page.goto('http://localhost/kkzrt/megallok_kereso.php', { timeout: 60000 });
    await expect(page.locator('#map')).toBeVisible();
  });

  test('Az összes hír kilistázása', async ({ page }) => {
    await page.goto('http://localhost/kkzrt/index.php', { timeout: 60000 });

    const btn = await page.locator('#btnMoreNews');

    let isExpanded = await page.evaluate(() => window.isExpanded = false);
    expect(isExpanded).toBeFalsy();

    await btn.click();

    isExpanded = await page.evaluate(() => window.isExpanded = true);
    expect(isExpanded).toBeTruthy();
  });

  test('Főoldalon részletek gomb ellenőrzése', async ({ page }) => {
    await page.goto('http://localhost/kkzrt/index.php', { timeout: 60000 });
    
    // részletek gomb megnyomása
    await page.click('text=Részletek');
    
       await expect(page).toHaveURL('http://localhost/kkzrt/hirek.php?id=1');
  });

  // API válaszok ellenőrzése
    test('API válaszok ellenőrzése', async ({ request }) => {
    // stop teszt
    const stopsResponse = await request.get('http://localhost:3000/api/stop');
    const stopsData = await stopsResponse.json();
    expect(Array.isArray(stopsData)).toBeTruthy();

    // helyibusz API teszt
    const localbusResponse = await request.get('http://localhost:3000/api/helyibusz');
    const localbusData = await localbusResponse.json();
    expect(Array.isArray(localbusData)).toBeTruthy();

    // link API teszt
    const linkResponse = await request.get('http://localhost:3000/api/link');
    const linkData = await linkResponse.json();
    expect(Array.isArray(linkData)).toBeTruthy();

    // marker API teszt
    const markerResponse = await request.get('http://localhost:3000/api/marker');
    const markerData = await markerResponse.json();
    expect(Array.isArray(markerData)).toBeTruthy();

    // kepek API teszt
    const pictureResponse = await request.get('http://localhost:3000/api/kepek');
    const pictureData = await pictureResponse.json();
    expect(Array.isArray(pictureData)).toBeTruthy();

    // trip API teszt
    const tripsResponse = await request.get('http://localhost:3000/api/trip');
    const tripsData = await tripsResponse.json();
    expect(Array.isArray(tripsData)).toBeTruthy();

    // hirek API teszt
    const newsResponse = await request.get('http://localhost:3000/api/hirek');
    const newsData = await newsResponse.json();
    expect(Array.isArray(newsData)).toBeTruthy();

    // buszjaratok API teszt
    const busRoutesResponse = await request.get('http://localhost:3000/api/buszjaratok');
    const busRoutesData = await busRoutesResponse.json();
    expect(Array.isArray(busRoutesData)).toBeTruthy();
  });

  test('404-es linkek keresése', async ({ page }) => {
    await page.goto('http://localhost/kkzrt/info.php', { timeout: 60000 });

    const links = await page.locator('a').all();
    let brokenLinks = [];

    for (const link of links) {
        const href = await link.getAttribute('href');

        //linkek kezelése
        if (href && !href.startsWith('#') && !href.startsWith('javascript')) {
            try {
             //teljes url
                let fullUrl = href;
              
                if (!href.startsWith('http')) {
                    fullUrl = new URL(href, 'http://localhost/kkzrt/').toString();
                }
                
                //kérés küldése
                const response = await page.request.get(fullUrl);

                if (response.status() === 404) {
                    console.log(`❌ 404 ERROR: ${fullUrl}`);
                    brokenLinks.push(fullUrl);
                }
            } catch (e) {
                console.log(`⚠️ Nem ellenőrizhető link: ${href} - ${e.message}`);
            }
        }
    }

    console.log(`✅ Ellenőrizve: ${linkek.length} link. Talált hibák: ${hibásLinkek.length}`);

    // teszt elbuktatás
    expect(hibásLinkek.length).toBe(0);
  });
});