# KaposTransit

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
   
   ```env
SMTP_HOST=smtp.gmail.com
SMTP_USERNAME=kapostransit@gmail.com
SMTP_PASSWORD="jlvr ymug sqlj envp"
SMTP_PORT=587
SMTP_FROM=kapostransit@gmail.com
SMTP_FROM_NAME="KaposTransit"
```

   ```env
   PORT= 3306
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

### Bejelentkezés

### Admin felület
* URL: `http://localhost/kkzrt/admin.php`
* felhasználónév: `Kapostransit`
* jelszó: `KaposTransitAdmin997.@`

### Felhasználói bejelentkezés
* URL: `http://localhost/kkzrt/login.php`
* email: `asd@gmail.com`
* jelszó: `ASD123`

### Főbb funkciók
* menetrend megtekintése
* megállók keresése
* késések kezelése
* járatok böngészése
* hírek megtekintése
* térkép megtekintése (Google Maps API kulcs szükséges)

### Figyelmeztetés
### MÁV Adatok:
* a rendszer MÁV adatokat használ
* az adatok használata költségekkel járhat

### Figyelmeztetés a Google Maps költségekről 
a térkép funkció használatához Google Maps API szükséges, ami költségekkel jár:
* minden térképes megjelenítés és marker létrehozása számlázható
* a MÁV járatok megjelenítése markerenként
* nagy forgalom esetén ez gyorsan összeadódhat

**Nem vállalok felelősséget az esetleges Google Maps API költségekért!**
