# KaposTransit

**VIZSGAREMEK PROJEKT** - Ez a webalkalmazás szakmai vizsgaremek keretében készült oktatási célra. Nem hivatalos szolgáltatás!


### Projekt jellege
Ez a webalkalmazás **vizsgaremek/szakdolgozat keretében készült oktatási célú demonstrációs projekt**.

### Fontos figyelmeztetések
- NEM hivatalos Kaposvári közlekedési alkalmazás
- NEM áll kapcsolatban a Kaposvár Közlekedési Zrt.-vel vagy bármely hivatalos közlekedési szolgáltatóval
- NEM kereskedelmi célú termék
- Kizárólag oktatási/vizsga célú bemutató
- Nem akarunk semmilyen jogokat megsérteni

### Adathasználat
- **MÁV GTFS adatok:** kizárólag oktatási demonstrációs célra
- **Közlekedési információk:** nyilvános adatok alapján, csak tanulmányi célra
- **Minden adat csak vizsgaremek keretében kerül felhasználásra**

## Telepítés

### XAMPP telepítése és indítása

### Adatbázisok importálása phpMyAdmin-ban:
    - Importálja a következő fájlokat:
     - `adatbazis_dump/kkzrt.sql`
     - `adatbazis_dump/kaposvar.sql`

### Függőségek telepítése:
   ```bash
   # Főkönyvtárban 
   composer install
   
   # Frontend függőségek a főkönyvtárban 
   npm install
   npx tailwindcss init
   npm run build
   
   # Playwright tesztek
   cd kkzrt/kkzrt-tests
   npm install
   npx playwright install
   ```

### Backend szerver indítása:
   ```bash
   # Backend indítása
   cd kkzrt/backend
   npm install
   npm start
   ```

### Környezeti változók beállítása:

**Backend .env fájl:**
   ```env
   SMTP_HOST=smtp.gmail.com
   SMTP_USERNAME=your-email@gmail.com
   SMTP_PASSWORD=your-app-password
   SMTP_PORT=587
   SMTP_FROM=your-email@gmail.com
   SMTP_FROM_NAME="KaposTransit"
   ```

**FONTOS:** Saját Gmail fiókot és alkalmazásjelszót használjon. Gmail alkalmazásjelszó generálásához engedélyezze a 2FA-t, majd látogasson el a Google Account Settings oldalra.

**Adatbázis konfiguráció:**
   ```env
   PORT=3000
   DB_HOST=localhost
   DB_USER=root
   DB_NAME=kaposvar
   ```


* `adatbazis_dump/` - sql fájlok importáláshoz
* `dokumentacio/` - dokumentáció és képernyőképek
* `kkzrt/` - fő projekt könyvtár
   * `backend/` - szerver 
   * `kkzrt-tests/` - playwright tesztek
   * `vendor` - composer
   * `node_modules`

### API Dokumentáció
* Swagger UI: `http://localhost:3000/api-docs`
* API végpontok dokumentáció

### Bejelentkezés (Demo adatok - vizsgaremek célra)

### Admin felület
* URL: `http://localhost/kkzrt/admin.php`
* felhasználónév: `KaposTransit`
* jelszó: `KaposTransitAdmin997.@`

### Felhasználói bejelentkezés
* URL: `http://localhost/kkzrt/login.php`
* email: `asd@gmail.com`
* jelszó: `Abcde1234@`

**FIGYELEM:** Ezek demo/teszt adatok! Éles környezetben soha ne használjon ilyen egyszerű jelszavakat!

### Főbb funkciók
* menetrend megtekintése
* megállók keresése
* késések kezelése
* járatok böngészése
* hírek megtekintése
* térkép megtekintése (Google Maps API kulcs szükséges)

### Figyelmeztetések

### MÁV Adatok:
* a rendszer MÁV adatokat használ **kizárólag vizsgaremek/oktatási célra**
* az adatok használata költségekkel járhat
* **nem célja a jogok megsértése**

### Figyelmeztetés a Google Maps költségekről 
a térkép funkció használatához Google Maps API szükséges, ami költségekkel jár:
* minden térképes megjelenítés és marker létrehozása számlázható
* a MÁV járatok megjelenítése markerenként
* nagy forgalom esetén ez gyorsan összeadódhat

**Nem vállalok felelősséget az esetleges Google Maps API költségekért!**

### Vizsgaremek státusz
* Ez a projekt **szakmai vizsga** keretében készült
* Célja a **tudás és képességek bemutatása**
* **Nem kereskedelmi** és **nem hivatalos** célú
* Minden felhasznált adat és szolgáltatás **oktatási célú**

## Záró megjegyzés

Ez a webalkalmazás kizárólag oktatási és demonstrációs célokat szolgál. A projekt célja a megszerzett tudás és programozási készségek bemutatása volt a vizsgaremek keretében. Minden felhasznált adat, név és információ kizárólag tanulmányi célból, jóhiszeműen került felhasználásra.

**Projekt státusza:** Vizsgaremek - Oktatási célú bemutató
