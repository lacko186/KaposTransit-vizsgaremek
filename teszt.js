const { test, expect } = require('@playwright/test');

test.describe('Térkép Oldal Tesztek', () => {
  let page;

  test.beforeEach(async ({ browser }) => {
    // új oldal
    page = await browser.newPage();
    await page.goto('http://localhost/kkzrt/terkep.php');
    
    // console figyelés
    page.on('console', msg => {
      console.log('Browser console:', msg.text());
      if (msg.type() === 'error' && msg.text().includes('Google Maps JavaScript API')) {
        console.error('Google Maps API hiba észlelve:', msg.text());
      }
    });
  });

  test.afterEach(async () => {
    await page.close();
  });

  // alapvető teszt
  test('Alapvető oldalelemek ellenőrzése', async () => {
    // fejléc
    await expect(page.locator('.header h1')).toHaveText('Kaposvár Közlekedési Zrt.');
    
    // térkép ellenőrzés
    const mapContainer = page.locator('#map');
    await expect(mapContainer).toBeVisible();
    await expect(mapContainer).toHaveCSS('height', '650px');
    
    // input mezők
    await expect(page.locator('#start')).toBeVisible();
    await expect(page.locator('#end')).toBeVisible();
    await expect(page.locator('#travel-time')).toBeVisible();
  });

  // maps api teszt
  test('Google Maps API betöltés', async () => {
    // script ellenőrzés
    const apiScript = await page.evaluate(() => {
      return document.querySelector('script[src*="maps.googleapis.com"]') !== null;
    });
    expect(apiScript).toBeTruthy();

    // api kulcs teszt
    const invalidApiKeyError = await page.evaluate(() => {
      return new Promise(resolve => {
        let hasError = false;
        const errorHandler = (event) => {
          if (event.message.includes('Google Maps JavaScript API error')) {
            hasError = true;
          }
        };
        window.addEventListener('error', errorHandler);
        setTimeout(() => {
          window.removeEventListener('error', errorHandler);
          resolve(hasError);
        }, 2000);
      });
    });
    
    if (invalidApiKeyError) {
      console.warn('⚠️ Google Maps API kulcs hiba: Az API kulcs lejárt vagy érvénytelen');
    }
  });

  // gombok teszt
  test('Útvonaltervező gombok működése', async () => {
    // közlekedési gombok
    const transitButtons = await page.locator('.transit-mode-btn').all();
    for (const button of transitButtons) {
      await button.click();
      await expect(button).toHaveClass(/active/);
      
      // aktív gomb
      const activeButtons = await page.locator('.transit-mode-btn.active').count();
      expect(activeButtons).toBe(1);
    }

    // összetett útvonal
    await page.locator('.transit-mode-btn[data-mode="complex"]').click();
    await expect(page.locator('#complex-route-select')).toBeVisible();
    
    await page.locator('.transit-mode-btn[data-mode="bus"]').click();
    await expect(page.locator('#complex-route-select')).toBeHidden();
  });

  // útvonaltervezés
  test('Útvonaltervezés folyamat', async () => {
    // adatok megadása
    await page.fill('#start', 'Kaposvár, Vasútállomás');
    await page.fill('#end', 'Kaposvár, Egyetem');
    
    // dátum beállítás
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const dateTimeString = tomorrow.toISOString().slice(0, 16);
    await page.fill('#travel-time', dateTimeString);

    // kereső gomb
    const findRouteButton = page.locator('#find-route');
    await expect(findRouteButton).toBeEnabled();
    await findRouteButton.click();

    // eredmény ellenőrzés
    await expect(page.locator('#route-details')).toBeVisible();
    await expect(page.locator('#route-info')).not.toBeEmpty();
  });

  // járat teszt
  test('Helyi járat kiválasztás', async () => {
    // mód aktiválás
    await page.locator('.transit-mode-btn[data-mode="complex"]').click();
    
    // select mező
    const routeSelect = page.locator('#complex-route');
    await expect(routeSelect).toBeVisible();
    
    // járat választás
    await routeSelect.selectOption('20');
    
    // megállók ellenőrzés
    await expect(page.locator('#route-info')).toBeVisible();
  });

  // markerek teszt
  test('Térképmarkerek és útvonalak', async () => {
    // útvonal tervezés
    await page.fill('#start', 'Kaposvár, Vasútállomás');
    await page.fill('#end', 'Kaposvár, Egyetem');
    await page.click('#find-route');

    // markerek ellenőrzés
    const markersExist = await page.evaluate(() => {
      return document.querySelectorAll('.gm-style img[src*="markers"]').length > 0;
    });
    expect(markersExist).toBeTruthy();
  });

  // hibaüzenetek
  test('Hibaüzenetek és visszajelzések', async () => {
    // üres mezők
    await page.click('#find-route');
    
    // alert ellenőrzés
    const alertVisible = await page.evaluate(() => {
      return document.querySelector('.alert') !== null;
    });
    expect(alertVisible).toBeTruthy();

    // érvénytelen cím
    await page.fill('#start', 'Nemlétező utca 123');
    await page.click('#find-route');
    
    // hibaüzenet
    await expect(page.locator('.alert')).toContainText('nem található');
  });

  // teljesítmény
  test('Teljesítmény metrikák', async () => {
    // betöltési idő
    const timing = await page.evaluate(() => {
      return {
        loadTime: performance.timing.loadEventEnd - performance.timing.navigationStart,
        domReady: performance.timing.domContentLoadedEventEnd - performance.timing.navigationStart
      };
    });
    
    expect(timing.loadTime).toBeLessThan(5000); // max 5 mp
    expect(timing.domReady).toBeLessThan(3000); // max 3 mp
  });

  // reszponzív teszt
  test('Responsive design', async () => {
    // mobil
    await page.setViewportSize({ width: 375, height: 667 });
    await expect(page.locator('#map')).toBeVisible();
    await expect(page.locator('.menu-btn')).toBeVisible();

    // tablet
    await page.setViewportSize({ width: 768, height: 1024 });
    await expect(page.locator('#map')).toBeVisible();

    // desktop
    await page.setViewportSize({ width: 1920, height: 1080 });
    await expect(page.locator('#map')).toBeVisible();
  });

  // console hibák
  test('Console hibák elemzése', async () => {
    const consoleErrors = [];
    
    // hibák gyűjtése
    page.on('console', msg => {
      if (msg.type() === 'error') {
        consoleErrors.push(msg.text());
      }
    });

    // újratöltés
    await page.reload();

    // várakozás
    await page.waitForTimeout(2000);

    // elemzés
    for (const error of consoleErrors) {
      if (error.includes('Google Maps JavaScript API')) {
        console.warn('⚠️ Google Maps API hiba:', error);
      } else {
        fail(`Nem várt console hiba: ${error}`);
      }
    }
  });
});

// konfiguráció
module.exports = {
  testDir: './tests',
  timeout: 30000,
  retries: 2,
  use: {
    baseURL: 'http://localhost/kkzrt/',
    screenshot: 'only-on-failure',
    video: 'retain-on-failure',
    trace: 'retain-on-failure',
  },
  expect: {
    timeout: 10000,
  },
  reporter: [
    ['list'],
    ['html', { outputFolder: 'test-results/html' }],
    ['junit', { outputFile: 'test-results/results.xml' }]
  ]
};

// segédfüggvények
const helpers = {
  // api kulcs ellenőrzés
  async checkGoogleMapsApiKey(page) {
    return await page.evaluate(() => {
      return new Promise(resolve => {
        let status = {
          isValid: false,
          error: null
        };
        
        window.gm_authFailure = () => {
          status.error = 'API kulcs érvénytelen vagy lejárt';
          resolve(status);
        };
        
        setTimeout(() => {
          if (!status.error && window.google && window.google.maps) {
            status.isValid = true;
          }
          resolve(status);
        }, 2000);
      });
    });
  },

  // útvonal teszt
  async testRouteCalculation(page, start, end) {
    await page.fill('#start', start);
    await page.fill('#end', end);
    await page.click('#find-route');
    
    const routeInfo = await page.locator('#route-info');
    const isVisible = await routeInfo.isVisible();
    const content = await routeInfo.textContent();
    
    return {
      success: isVisible && content.length > 0,
      details: content
    };
  },

  // markerek ellenőrzés
  async checkMapMarkers(page) {
    return await page.evaluate(() => {
      const markers = document.querySelectorAll('.gm-style img[src*="markers"]');
      return {
        count: markers.length,
        positions: Array.from(markers).map(marker => ({
          top: marker.style.top,
          left: marker.style.left
        }))
      };
    });
  }
};