<?php
session_start();

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'kkzrt';

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Kapcsolódási hiba: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KaposTransit - Késés Igazolás</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="betolt.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: linear-gradient(to right, #211717, #b30000);
            --primary-dark: #211717;
            --primary-red: li;
            --accent-color: #7A7474;
            --text-light: #FFFFFF;
            --text-dark: #333333;
            --background-light: #f5f5f5;
            --box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
            --transition: all 0.3s ease;
            --border-radius: 10px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', 'Segoe UI', sans-serif;
            background: linear-gradient(to left, #a6a6a6, #e8e8e8);
            color: var(--text-dark);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

/*-----------------------------------------------------------------------------------------------------CSS - HEADER------------------------------------------------------------------------------------------------------*/

.header {
            position: relative;
            background-color: var(--primary-color);
            color: var(--text-light);
            padding: 16px;
            box-shadow: var(--shadow);
            text-align: center;
            margin-bottom: 20px;
        }

        .header {
            position: relative;
            background: var(--primary-color);
            color: var(--text-light);
            padding: 1rem;
            box-shadow: 0 2px 10px var(--shadow-color);
        }

        .header h1 {
            margin: 0;
            text-align: center;
            font-size: 2rem;
            padding: 1rem 0;
        }

        .nav-wrapper {
            position: absolute;
            top: 1rem;
            left: 1rem;
            z-index: 1000;
        }

        .nav-container {
            position: relative;
        }

        .menu-btn {
            background: none;
            border: none;
            border-radius: 8px;
            padding: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px var(--shadow-color);
        }

        .menu-btn:hover {
            background: none;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px var(--shadow-color);
        }

        .hamburger {
            position: relative;
            width: 30px;
            height: 20px;
        }

        .hamburger span {
            position: absolute;
            width: 100%;
            height: 3px;
            background: var(--text-light);
            border-radius: 3px;
            transition: all 0.3s ease;
        }

        .hamburger span:nth-child(1) { top: 0; }
        .hamburger span:nth-child(2) { top: 50%; transform: translateY(-50%); }
        .hamburger span:nth-child(3) { bottom: 0; }

        .menu-btn.active .hamburger span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .menu-btn.active .hamburger span:nth-child(2) {
            opacity: 0;
        }

        .menu-btn.active .hamburger span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -7px);
        }

        .dropdown-menu {
            position: absolute;
            top: calc(100% + 1rem);
            left: 0;
            background: var(--text-light);
            border-radius: 12px;
            min-width: 280px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-20px);
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            box-shadow: 0 10px 30px var(--shadow-color);
            overflow: hidden;
        }

        .dropdown-menu.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .menu-items {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .menu-items li {
            transform: translateX(-100%);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .dropdown-menu.active .menu-items li {
            transform: translateX(0);
            opacity: 1;
        }

        .menu-items li:nth-child(1) { transition-delay: 0.1s; }
        .menu-items li:nth-child(2) { transition-delay: 0.2s; }
        .menu-items li:nth-child(3) { transition-delay: 0.3s; }
        .menu-items li:nth-child(4) { transition-delay: 0.4s; }
        .menu-items li:nth-child(5) { transition-delay: 0.5s; }

        .menu-items a {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            color: black;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .menu-items a:hover {
            background: linear-gradient(to right, #211717,#b30000);
            color: white;
            padding-left: 2rem;
        }

        .menu-items a::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: darkred;
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .menu-items a:hover::before {
            transform: scaleY(1);
        }

        .menu-items a img {
            width: 24px;
            height: 24px;
            margin-right: 12px;
            transition: transform 0.3s ease;
        }

        .menu-items a:hover img {
            transform: scale(1.2) rotate(5deg);
        }

        .menu-items a span {
            font-size: 17px;
        }


        .menu-items a.active {
            background: white;
            color: black;
            font-weight: 600;
        }

        .menu-items a.active::before {
            transform: scaleY(1);
        }

        @keyframes ripple {
            0% {
                transform: scale(0);
                opacity: 1;
            }
            100% {
                transform: scale(2);
                opacity: 0;
            }
        }

        .menu-items a::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: gray;
            left: 0;
            top: 0;
            transform: scale(0);
            opacity: 0;
            pointer-events: none;
            transition: all 0.5s ease;
        }

        .menu-items a:active::after {
            animation: ripple 0.6s ease-out;
        }

        .icon {
            background-color: var(--primary-color);
            border: 0;
            cursor: pointer;
            padding: 0;
            position: relative;
            height: 30px;
            width: 30px;
        }

        .icon:hover{
            background-color: var(--primary-color);
        }

        .icon:focus {
            outline: 0;
        }

        .icon .line {
            background-color: var(--text-light);
            height: 2px;
            width: 20px;
            position: absolute;
            top: 10px;
            left: 5px;
            transition: transform 0.6s linear;
        }

        .icon .line2 {
            top: auto;
            bottom: 10px;
        }

        nav.active .icon .line1 {
            transform: rotate(-765deg) translateY(5.5px);
        }

        nav.active .icon .line2 {
            transform: rotate(765deg) translateY(-5.5px);
        }
/*-----------------------------------------------------------------------------------------------------HEADER END--------------------------------------------------------------------------------------------------------*/

/*--------------------------------------------------------------------------------------------------------CSS - OTHER PARTS----------------------------------------------------------------------------------------------*/
        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 2rem;
            background: linear-gradient(to right, #f4e3f6, #DDEEFF);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            flex: 1;
        }
        h1 {
            color: white;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
            font-weight: 600;
        }

        h1:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100px;
            height: 3px;
            background: var(--primary-gradient);
        }

        .input-group {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        .input-wrapper {
            flex: 1;
            min-width: 250px;
            margin-bottom: 15px;
        }

        .input-label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-dark);
        }

        input, select {
            width: 100%;
            padding: 12px 15px;
            border: 1.5px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
            transition: var(--transition);
            background-color: #fff;
        }

        input:focus, select:focus {
            border-color: var(--primary-red);
            outline: none;
            box-shadow: 0 0 0 3px rgba(179, 0, 0, 0.1);
        }

        select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23333' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
            padding-right: 40px;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin: 30px 0 20px;
            color: var(--primary-dark);
            position: relative;
            padding-bottom: 10px;
            border-bottom: 3px solid var(--primary-dark);
        }

        .button-group {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 35px;
        }

        button {
            padding: 14px 28px;
            background: var(--primary-color);
            color: var(--text-light);
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            font-family: 'Poppins', sans-serif;
        }

        button::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, #b30000, #211717);
            opacity: 0;
            transition: var(--transition);
            z-index: 0;
        }

        button:hover::before {
            opacity: 1;
        }

        button span {
            position: relative;
            z-index: 1;
        }

        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        button:active {
            transform: translateY(0);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        }

        button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        button:disabled:hover {
            transform: none;
        }

        #invoice {
            margin-top: 30px;
            padding: 30px;
            border: 2px solid rgba(179, 0, 0, 0.2);
            border-radius: var(--border-radius);
            background-color: #fff;
            box-shadow: var(--box-shadow);
        }

        #invoiceDetails {
            white-space: pre-wrap;
            font-size: 15px;
            line-height: 1.6;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid var(--primary-red);
        }

        canvas {
            margin-top: 20px;
            border: 1px solid rgba(179, 0, 0, 0.2);
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        input:invalid, select:invalid {
            border-color: #ff4136;
        }

        .error-message {
            color: #ff4136;
            font-size: 0.85rem;
            margin-top: 5px;
            display: none;
        }

        input:invalid + .error-message {
            display: block;
        }

       
/*--------------------------------------------------------------------------------------------------------OTHER PARTS END------------------------------------------------------------------------------------------------*/

/*--------------------------------------------------------------------------------------------------------CSS - FOOTER---------------------------------------------------------------------------------------------------*/
footer {
            text-align: center;
            padding: 10px;
              position: relative;
            background: var(--primary-color);
            color: var(--text-light);
            padding: 1rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            z-index: 100;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .footer-section h2 {
            margin-bottom: 1rem;
            color: var(--text-light);
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.5rem;
        }

        .footer-links a {
            color: var(--text-light);
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-links a:hover {
            color: var(--accent-color);
        }
/*--------------------------------------------------------------------------------------------------------FOOTER END-----------------------------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------------------------CSS - @MEDIA-------------------------------------------------------------------------------------------------------*/
        @media (max-width: 992px) {
            .header h1 {
                font-size: 1.7rem;
            }

            .container {
                padding: 1.5rem;
                margin: 20px 15px;
            }
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 1.5rem;
            }

            .input-group {
                flex-direction: column;
                gap: 15px;
            }
            
            .input-wrapper {
                width: 100%;
                min-width: 100%;
            }
            
            .section-title {
                font-size: 1.1rem;
            }
            
            h1 {
                font-size: 1.7rem;
            }
        }

        @media (max-width: 576px) {
            .header h1 {
                font-size: 1.3rem;
                padding: 0.7rem 0;
            }
            
            .nav-wrapper {
                top: 1rem;
                left: 1rem;
            }
            
            .menu-btn {
                width: 40px;
                height: 40px;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            button {
                width: 100%;
                padding: 12px 20px;
            }
            
            .container {
                padding: 1.2rem;
                margin: 15px 10px;
            }
            
            h1 {
                font-size: 1.5rem;
            }

            .footer-section h2, .footer-section h3 {
                font-size: 1.3rem;
            }
        }
/*-----------------------------------------------------------------------------------------------------@MEDIA END---------------------------------------------------------------------------------------------------------*/
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <div class="nav-wrapper">
        <div class="nav-container">
            <button class="menu-btn" id="menuBtn">
                <div class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </button>
            <nav class="dropdown-menu" id="dropdownMenu">
                <ul class="menu-items">
                    <li>
                        <a href="index.php">
                            <img src="home.png" alt="Főoldal">
                            <span>Főoldal</span>
                        </a>
                    </li>
                    <li>
                        <a href="terkep.php">
                            <img src="placeholder.png" alt="Térkép">
                            <span>Térkép</span>
                        </a>
                    </li>
                    <li>
                        <a href="keses.php" class="active">
                            <img src="tickets.png" alt="Jegyvásárlás">
                            <span>Késés Igazolás</span>
                        </a>
                    </li>
                    <li>
                        <a href="menetrend.php">
                            <img src="calendar.png" alt="Menetrend">
                            <span>Menetrend</span>
                        </a>
                    </li>
                    <li>
                        <a href="jaratok.php">
                            <img src="bus.png" alt="járatok">
                            <span>Járatok</span>
                        </a>
                    </li>
                    <li>
                        <a href="info.php">
                            <img src="information-button.png" alt="Információ">
                            <span>Információ</span>
                        </a>
                    </li>
                    <li>
                        <a href="logout.php">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Kijelentkezés</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <h1>Késés igazolás</h1>
</div>

<!-------------------------------------------------------------------------------- fő tartalom ---------------------------------------------------------->
<div class="container">
    <form id="kesesigazolas">
        <h1 style="color: darkred">Késés Igazolás</h1>
        
        <div class="section-title">Utas adatai</div>
        <div class="input-group">
            <div class="input-wrapper">
                <label class="input-label">Név*</label>
                <input type="text" id="nev" placeholder="Adja meg a teljes nevét" required>
                <div class="error-message">Kérjük, adja meg a nevét</div>
            </div>
            <div class="input-wrapper">
                <label class="input-label">Bérletszám / Jegyszám*</label>
                <input type="text" id="berletszam" placeholder="pl. B12345678" required>
                <div class="error-message">Kérjük, adja meg a bérlet vagy jegy számát</div>
            </div>
        </div>

        <div class="section-title">Járat adatai</div>
        <div class="input-group">
            <div class="input-wrapper">
                <label class="input-label">Járatszám*</label>
                <select id="jaratszam" required>
                    <option value="" disabled selected>Válasszon járatot...</option>
                </select>
                <div class="error-message">Kérjük, válasszon járatot</div>
            </div>
            <div class="input-wrapper">
                <label class="input-label">Dátum*</label>
                <input type="date" id="datum" readonly>
            </div>
        </div>

        <div class="input-group">
            <div class="input-wrapper">
                <label class="input-label">Tervezett indulás*</label>
                <input type="time" id="tervezett_indulas" readonly>
            </div>
            <div class="input-wrapper">
                <label class="input-label">Tényleges indulás*</label>
                <input type="time" id="tenyleges_indulas" readonly>
            </div>
        </div>

        <div class="button-group">
            <button type="submit" disabled title="Válasszon járatot és ellenőrizze a késést">
                <span>Igazolás generálása</span>
            </button>
        </div>
    </form>
</div>

<footer>
        <div class="footer-content">
            <div class="footer-section">
                <h2>Kaposvár közlekedés</h2>
                <p style="font-style: italic">Megbízható közlekedési szolgáltatások<br> az Ön kényelméért már több mint 50 éve.</p><br>
               
            </div>
           
            <div  class="footer-section">
                <h3>Elérhetőség</h3>
                <ul class="footer-links">
                    <li><i class="fas fa-phone"></i> +36-82/411-850</li>
                    <li><i class="fas fa-envelope"></i> titkarsag@kkzrt.hu</li>
                    <li><i class="fas fa-map-marker-alt"></i> 7400 Kaposvár, Cseri út 16.</li>
                    <li><i class="fas fa-map-marker-alt"></i> Áchim András utca 1.</li>
                </ul>
            </div>
        </div>
        <div style="text-align: center; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1);">
            <p>© 2024 Kaposvár közlekedési Zrt. Minden jog fenntartva.</p>
        </div>
    </footer>
<script>
    // Nav
document.getElementById('menuBtn').addEventListener('click', function() {
    this.classList.toggle('active');
    document.getElementById('dropdownMenu').classList.toggle('active');
});

// Kívülre kattintás || bezárjuk a menüt
document.addEventListener('click', function(event) {
    const menu = document.getElementById('dropdownMenu');
    const menuBtn = document.getElementById('menuBtn');
    
    if (!menu.contains(event.target) && !menuBtn.contains(event.target)) {
        menu.classList.remove('active');
        menuBtn.classList.remove('active');
    }
});

// Aktív oldal jelölése
document.addEventListener('DOMContentLoaded', function() {
    const currentPage = window.location.pathname.split('/').pop();
    const menuItems = document.querySelectorAll('.menu-items a');
    
    menuItems.forEach(item => {
        if (item.getAttribute('href') === currentPage) {
            item.classList.add('active');
        }
    });
});

</script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('kesesigazolas');
    const jaratszamSelect = document.getElementById('jaratszam');
    const tervezettInput = document.getElementById('tervezett_indulas');
    const tenyelegesInput = document.getElementById('tenyleges_indulas');
    const submitButton = document.querySelector('#kesesigazolas button[type="submit"]');
    
    // járatok adatai és késések
    let routesData = [];
    
    // dátum mező mai napra állítva
    const datumField = document.getElementById('datum');
    const today = new Date();
    const formattedDate = today.toISOString().split('T')[0];
    datumField.value = formattedDate;
    
    // adatok betöltése
    loadKesesData();
    
    // járatok és késés adatok betöltése
    async function loadKesesData() {
        try {
            // Járatok lekérése
            const jaratResponse = await fetch('http://localhost:3000/api/kovetkezo_meall');
            let jaratData = [];
            
            if (jaratResponse.ok) {
                jaratData = await jaratResponse.json();
                console.log('Járat adatok betöltve:', jaratData);
            } else {
                console.error(`Járat API hiba: ${jaratResponse.status}`);
                throw new Error('Nem sikerült betölteni a járat adatokat');
            }
            
            // késések lekérése  http://loclahos:3000/api/keses-ről
            const kesesResponse = await fetch('http://localhost:3000/api/keses');
            let kesesData = [];
            
            if (kesesResponse.ok) {
                kesesData = await kesesResponse.json();
                console.log('Késés adatok betöltve:', kesesData);
            } else {
                console.error(`Késés API hiba: ${kesesResponse.status}`);
            }
            
            // adatok feldolgozása és járatok betöltése
            processData(jaratData, kesesData);
            
        } catch (error) {
            console.error('Hiba az API adatok betöltésekor:', error);
        
            console.log('Statikus adatok betöltése...');
            routesData = getDefaultRoutes();
            populateRoutes(routesData);
            
            // eseménykezelők hozzáadása
            addEventListeners();
        }
    }
    
    // adatok feldolgozása
    function processData(jaratData, kesesData) {
        routesData = [];
        
        // járat adatok
        jaratData.forEach(jarat => {
            const jaratId = jarat.id;
            const jaratSzam = jarat.number;
            
            // indulási idők
            let startTimes = [];
            try {
                if (typeof jarat.start === 'string') {
                    const cleanStart = jarat.start.replace(/[\[\]\']/g, '');
                    startTimes = cleanStart.split(';').filter(time => time.trim() !== '');
                }
            } catch (e) {
                console.warn(`Nem sikerült az indulási időket feldolgozni a ${jaratSzam} járatnál:`, e);
            }
            
// megállólista
let stops = [];
            try {
                if (typeof jarat.stops === 'string' && jarat.stops.trim() !== '') {
                    stops = JSON.parse(jarat.stops);
                } else if (Array.isArray(jarat.stops)) {
                    stops = jarat.stops;
                }
            } catch (e) {
                console.warn(`Nem sikerült a megállókat feldolgozni a ${jaratSzam} járatnál:`, e);
            }
            
            // késés keresés
            const delay = kesesData.find(k => k.route_name == jaratSzam.toString());
            const kesesPerc = delay ? parseInt(delay.keses_perc) : 0; 
            const kesesIdo = delay ? delay.ido : null;
            
            // adatok tárolás
            routesData.push({
                jaratId: jaratId,
                jaratSzam: jaratSzam,
                nev: jarat.name || `${jaratSzam} számú járat`,
                stops: stops,
                startTimes: startTimes,
                kesesPerc: kesesPerc,
                kesesIdo: kesesIdo
            });
        });
        
        // hiányzó járatok
        kesesData.forEach(keses => {
            const jaratSzam = keses.route_name;
            
            // új járat felvétel
            if (!routesData.some(route => route.jaratSzam.toString() === jaratSzam)) {
                console.log(`Új járat találva csak késés alapján: ${jaratSzam}`);
                
                // indulási idők
                const defaultTimes = [];
                // késés idő tárolás
                if (keses.ido && keses.ido !== "00:00:00") {
                    defaultTimes.push(keses.ido);
                }
                
                // járat hozzáadás
                routesData.push({
                    jaratId: -1,  
                    jaratSzam: parseInt(jaratSzam) || jaratSzam, 
                    nev: `${jaratSzam} számú járat`,
                    stops: [],
                    startTimes: defaultTimes,
                    kesesPerc: parseInt(keses.keses_perc) || 0,
                    kesesIdo: keses.ido
                });
            }
        });
        
        // menü feltöltés
        populateRoutes(routesData);
        
        // kezelők beállítás
        addEventListeners();
        
        console.log('Adatok feldolgozva:', routesData);
    }
    
    // menü feltöltés
    function populateRoutes(routes) {
        // reset
        jaratszamSelect.innerHTML = '';
        
        // alap opció
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = 'Válasszon járatot...';
        defaultOption.selected = true;
        defaultOption.disabled = true;
        jaratszamSelect.appendChild(defaultOption);
        
        // rendezés
        routes.sort((a, b) => a.jaratSzam - b.jaratSzam);
        
        // járatok listázás
        routes.forEach(route => {
            const option = document.createElement('option');
            option.value = route.jaratSzam;
            
            // szöveg összeállítás
            let displayText = `${route.jaratSzam} - ${route.nev}`;
            
            // késés jelzés
            if (route.kesesPerc > 0) {
                displayText += ` (${route.kesesPerc} perc késés)`;
            }
            
            option.textContent = displayText;
            
            // extra adatok
            option.dataset.kesesPerc = route.kesesPerc;
            option.dataset.routeId = route.jaratId;
            
            // indulási idők
            if (route.startTimes && route.startTimes.length > 0) {
                option.dataset.startTimes = JSON.stringify(route.startTimes);
            }
            
            // késés idő
            if (route.kesesIdo) {
                option.dataset.kesesIdo = route.kesesIdo;
            }
            
            jaratszamSelect.appendChild(option);
        });
        
        // input aktiválás
        tervezettInput.disabled = false;
        tenyelegesInput.disabled = false;
    }
    
    // eseménykezelők
    function addEventListeners() {
        // járatválasztás
        jaratszamSelect.addEventListener('change', handleJaratChange);
        
        // indulás változás
        tervezettInput.addEventListener('change', handlePlannedDepartureChange);
        
        // késés ellenőrzés
        tenyelegesInput.addEventListener('change', validateDelay);
        
        // űrlap küldés
        form.addEventListener('submit', handleFormSubmit);
    }
    
    // járatválasztás
    function handleJaratChange() {
        const selectedOption = jaratszamSelect.options[jaratszamSelect.selectedIndex];
        const kesesPerc = parseInt(selectedOption.dataset.kesesPerc || 0);
        const kesesIdo = selectedOption.dataset.kesesIdo || null;
        
        console.log(`Kiválasztott járat: ${selectedOption.value}, késés: ${kesesPerc} perc, késés idő: ${kesesIdo}`);
        
        // indulási idő
        let startTime = "";
        
        if (selectedOption.dataset.startTimes) {
            try {
                const startTimes = JSON.parse(selectedOption.dataset.startTimes);
                
                if (startTimes && startTimes.length > 0) {
                    // legközelebbi idő
                    startTime = getNextDepartureTime(startTimes, kesesIdo);
                }
            } catch (e) {
                console.warn('Nem sikerült az indulási időket feldolgozni:', e);
            }
        }
        
        // idő generálás
        if (!startTime) {
            const now = new Date();
            const offset = kesesPerc > 0 ? 30 + kesesPerc : 30; // késés figyelés
            now.setMinutes(now.getMinutes() - offset); 
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            startTime = `${hours}:${minutes}`;
            console.log(`Generált indulási idő: ${startTime}`);
        }
        
        // idő generálás
        if (!startTime) {
            const now = new Date();
            const offset = kesesPerc > 0 ? 30 + kesesPerc : 30; // késés figyelés
            now.setMinutes(now.getMinutes() - offset); 
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            startTime = `${hours}:${minutes}`;
        }
        
        // idő beállítás
        tervezettInput.value = startTime;
        
        // késés számítás
        calculateActualDepartureTime(startTime, kesesPerc);
        
        // késés ellenőrzés
        validateDelay();
    }
    
    // tervezett változás
    function handlePlannedDepartureChange() {
        if (jaratszamSelect.value) {
            const selectedOption = jaratszamSelect.options[jaratszamSelect.selectedIndex];
            const kesesPerc = parseInt(selectedOption.dataset.kesesPerc || 0);
            
            // tényleges számítás
            calculateActualDepartureTime(this.value, kesesPerc);
            
            // késés ellenőrzés
            validateDelay();
        }
    }
    
    // tényleges számítás
    function calculateActualDepartureTime(plannedTime, delayMinutes) {
        try {
            const [hours, minutes] = plannedTime.split(':').map(Number);
            const plannedDate = new Date();
            plannedDate.setHours(hours, minutes, 0, 0);
            
            // késés hozzáadás
            if (delayMinutes > 0) {
                const actualDate = new Date(plannedDate.getTime() + delayMinutes * 60000);
                const actualHours = String(actualDate.getHours()).padStart(2, '0');
                const actualMinutes = String(actualDate.getMinutes()).padStart(2, '0');
                tenyelegesInput.value = `${actualHours}:${actualMinutes}`;
            } else {
                // nincs késés
                tenyelegesInput.value = plannedTime;
            }
        } catch (error) {
            console.error('Hiba a tényleges indulás számításánál:', error);
            tenyelegesInput.value = ''; // törlés
        }
    }
    
    // késés ellenőrzés
    function validateDelay() {
        const tervezett = tervezettInput.value;
        const tenyleges = tenyelegesInput.value;
        
        if (tervezett && tenyleges) {
            try {
                const kesesPerc = kesesSzamitas(tervezett, tenyleges);
                
                if (kesesPerc > 0) {
                    // van késés
                    submitButton.removeAttribute('disabled');
                    submitButton.title = "Igazolás generálása";
                    
                    // késés mentés
                    form.dataset.kesesPerc = kesesPerc;
                } else {
                    // nincs késés
                    submitButton.setAttribute('disabled', 'disabled');
                    submitButton.title = "Nincs késés, nem lehet igazolást generálni";
                }
            } catch (error) {
                console.error('Hiba a késés számításánál:', error);
            }
        }
    }
    
    // késés számítás
    function kesesSzamitas(tervezett, tenyleges) {
        try {
            const tervIdopont = new Date(`1970-01-01T${tervezett}`);
            const tenyIdopont = new Date(`1970-01-01T${tenyleges}`);
            
            if (isNaN(tervIdopont.getTime()) || isNaN(tenyIdopont.getTime())) {
                throw new Error('Érvénytelen időformátum');
            }
            
            return Math.round((tenyIdopont - tervIdopont) / (1000 * 60));
        } catch (error) {
            console.error('Hiba a késés számításánál:', error);
            throw error;
        }
    }
    
    // indulási idő
    function getNextDepartureTime(startTimes, kesesIdo = null) {
        if (kesesIdo && kesesIdo !== "00:00:00") {
            console.log(`Korábbi késés ideje használva: ${kesesIdo}`);
            return kesesIdo;
        }
        
        if (!startTimes || startTimes.length === 0) {
            console.log('Nincsenek indulási idők');
            return null;
        }
        
        // idő rendezés
        const sortedTimes = [...startTimes].sort();
        
        // aktuális idő
        const now = new Date();
        const currentHours = now.getHours();
        const currentMinutes = now.getMinutes();
        const currentTimeValue = currentHours * 60 + currentMinutes;
        
        // közeli idő keresés
        const maxDistanceMinutes = 90; // max 1.5 óra
        const minDistanceMinutes = 10; // min 10 perc
        
        let closestPastTime = null;
        let closestDistance = Infinity;
        
        for (let i = 0; i < sortedTimes.length; i++) {
            const timeStr = sortedTimes[i];
            
            // érvényes formátum
            if (!timeStr || !timeStr.includes(':')) {
                continue;
            }
            
            const [hours, minutes] = timeStr.split(':').map(Number);
            if (isNaN(hours) || isNaN(minutes)) {
                continue;
            }
            
            const timeValue = hours * 60 + minutes;
            
            // elmúlt idő
            if (timeValue < currentTimeValue) {
                const distance = currentTimeValue - timeValue;
                
                // közelebbi idő
                if (distance < closestDistance && distance >= minDistanceMinutes && distance <= maxDistanceMinutes) {
                    closestPastTime = timeStr;
                    closestDistance = distance;
                }
            }
        }
        
        // közeli idő visszaadás
        if (closestPastTime) {
            console.log(`Legközelebbi előző indulási idő: ${closestPastTime}, távolság: ${closestDistance} perc`);
            return closestPastTime;
        }
        
        for (let i = sortedTimes.length - 1; i >= 0; i--) {
            const timeStr = sortedTimes[i];
            
            if (!timeStr || !timeStr.includes(':')) {
                continue;
            }
            
            const [hours, minutes] = timeStr.split(':').map(Number);
            if (isNaN(hours) || isNaN(minutes)) {
                continue;
            }
            
            const timeValue = hours * 60 + minutes;
            
            if (timeValue < currentTimeValue) {
                console.log(`Távolabbi előző indulási idő: ${timeStr}`);
                return timeStr;
            }
        }
        
        console.log(`Nem találtunk előző indulási időt, az utolsó időpont használva: ${sortedTimes[sortedTimes.length - 1]}`);
        return sortedTimes[sortedTimes.length - 1] || '';
    }
    
    function handleFormSubmit(e) {
        e.preventDefault();
        
        // adatok ellenőrzés
        const required = ['nev', 'berletszam', 'jaratszam', 'datum', 'tervezett_indulas', 'tenyleges_indulas'];
        const hianyzo = required.filter(field => {
            const elem = document.getElementById(field);
            return !elem || !elem.value;
        });
        
        if (hianyzo.length > 0) {
            alert(`Kérjük, töltse ki a következő kötelező mezőket: ${hianyzo.join(', ')}`);
            return;
        }
        
        // késés ellenőrzés
        const tervezett = tervezettInput.value;
        const tenyleges = tenyelegesInput.value;
        let kesesPerc;
        
        try {
            kesesPerc = kesesSzamitas(tervezett, tenyleges);
        } catch (error) {
            console.error('Hiba a késés számításánál:', error);
            alert('Hiba történt a késés számításánál. Kérjük, ellenőrizze az időpontokat.');
            return;
        }
        
        if (kesesPerc <= 0) {
            alert('Nem generálható igazolás, mert nincs késés.');
            return;
        }
        
        // járat név
        const selectedOption = jaratszamSelect.options[jaratszamSelect.selectedIndex];
        
        // adatok összeállítás
        const adatok = {
            nev: document.getElementById('nev').value,
            berletszam: document.getElementById('berletszam').value,
            jaratszam: selectedOption.textContent,
            route_id: jaratszamSelect.value,
            datum: document.getElementById('datum').value,
            tervezett_indulas: tervezett,
            tenyleges_indulas: tenyleges,
            keses_perc: kesesPerc
        };
        
        // PDF készítés
        pdfKeszites(adatok).catch(error => {
            console.error('Hiba a PDF generálása során:', error);
            alert('Váratlan hiba történt a PDF generálása során. Kérjük, próbálja újra később.');
        });
    }
    
    // PDF generálás
    async function pdfKeszites(adatok) {
        try {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            const margin = 25;
            const pageWidth = 210;
            const contentWidth = pageWidth - (2 * margin);

            // fejléc háttér
            doc.setFillColor(245, 245, 245);
            doc.rect(0, 0, pageWidth, 40, 'F');

            // fejléc szöveg
            doc.setTextColor(51, 51, 51);
            doc.setFontSize(20);
            doc.setFont('helvetica', 'bold');
            doc.text('Kaposvári Közlekedési Zrt.', margin + 20, 22);

            // alcím sáv
            doc.setFillColor(230, 230, 230);
            doc.rect(0, 40, pageWidth, 10, 'F');
            doc.setTextColor(51, 51, 51);
            doc.setFontSize(14);
            doc.text('KÉSÉS IGAZOLÁS', pageWidth/2, 47, { align: 'center' });

            // adatok rész
            let y = 65;
            doc.setTextColor(68, 68, 68);
            doc.setFontSize(11);

            function addDataField(label, value, yPos) {
                if (!label || !value) return yPos; 
                
                if ((yPos - 65) / 8 % 2 === 0) {
                    doc.setFillColor(250, 250, 250);
                    doc.rect(margin, yPos - 4, contentWidth, 8, 'F');
                }
                
                doc.setFont('helvetica', 'bold');
                doc.text(label, margin, yPos);
                
                doc.setFont('helvetica', 'normal');
                doc.text(String(value), margin + 50, yPos);
                
                return yPos + 8;
            }

            // adatok kiírás
            y = addDataField('Utas neve:', adatok.nev, y);
            y = addDataField('Bérletszám:', adatok.berletszam, y);
            y = addDataField('Járatszám:', adatok.jaratszam, y);
            y = addDataField('Dátum:', formatDate(adatok.datum), y);
            y = addDataField('Tervezett indulás:', adatok.tervezett_indulas, y);
            y = addDataField('Tényleges indulás:', adatok.tenyleges_indulas, y);

            // késés kiemelés
            y += 10;
            doc.setFillColor(178, 0, 0);
            doc.rect(margin, y - 4, contentWidth, 12, 'F');
            doc.setFont('helvetica', 'bold');
            doc.setTextColor(255, 255, 255);
            doc.setFontSize(12);
            doc.text(`KÉSÉS MÉRTÉKE: ${adatok.keses_perc} PERC`, pageWidth/2, y + 3, { align: 'center' });

            // aláírás rész
            y = 160;
            doc.setFont('helvetica', 'italic');
            doc.setFontSize(10);
            doc.setTextColor(68, 68, 68);
            doc.text('Elektronikusan hitelesítve', margin, y + 10);

            // lábléc vonal
            y = 200;
            doc.setFillColor(200, 200, 200);
            doc.rect(margin, y, contentWidth, 0.5, 'F');

            // lábléc szöveg
            y += 10;
            doc.setTextColor(102, 102, 102);
            doc.setFontSize(9);
            doc.setFont('helvetica', 'normal');
            const maiDatum = new Date().toLocaleDateString('hu-HU', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            doc.text(`Kiállítás dátuma: ${maiDatum}`, margin, y);

            // dokumentum azonosító
            y += 6;
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(8);
            const docId = Math.random().toString(36).substr(2, 9).toUpperCase();
            doc.text('Az igazolást a rendszer automatikusan generálta.', margin, y);
            doc.text(`Dokumentum azonosító: ${docId}`, pageWidth - margin, y, { align: 'right' });

            // fájlnév készítés
            const tisztaNev = adatok.nev
                ? adatok.nev.replace(/[^a-zA-Z0-9]/g, '_').toLowerCase()
                : 'keses_igazolas';
            const fajlnev = `keses_igazolas_${tisztaNev}_${docId}.pdf`;

            // mentés
            doc.save(fajlnev);
            
            // értesítés
            alert('A késés igazolás sikeresen elkészült!');

        } catch (error) {
            console.error('Részletes hiba a PDF generálása során:', error);
            alert(`Hiba történt a PDF generálása során: ${error.message}`);
        }
    }
    
    // dátum formázás
    function formatDate(dateString) {
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('hu-HU', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        } catch (error) {
            console.warn('Nem sikerült a dátum formázása:', error);
            return dateString; 
        }
    }
    
    // alap járatok
    function getDefaultRoutes() {
        return [
            { jaratId: 1, jaratSzam: 12, nev: "Helyi autóbusz-állomás - Sopron u. - Laktanya", kesesPerc: 20 },
            { jaratId: 2, jaratSzam: 13, nev: "Helyi autóbusz-állomás - Kecelhegy - Helyi autóbusz-állomás", kesesPerc: 5 },
            { jaratId: 3, jaratSzam: 20, nev: "Raktár u. - Laktanya - Videoton", kesesPerc: 10 },
            { jaratId: 4, jaratSzam: 21, nev: "Raktár u. - Videoton", kesesPerc: 0 },
            { jaratId: 5, jaratSzam: 23, nev: "Kaposfüred forduló - Füredi csp. - Kaposvári Egyetem", kesesPerc: 15 }
        ];
    }
});
    </script>
    </body>
    </html>