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
    <title>KaposTransit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <script src="betolt.js"></script>
    <style>
        :root {
            --primary-color:linear-gradient(to right, #211717,#b30000);
            --text-light: #FFFFFF;
            --shadow: 0 10px 20px rgba(0, 0, 0, 0.4);
            --transition: all 0.3s ease;
            --accent-color: #7A7474;
        
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to left, #a6a6a6, #e8e8e8);
            color: #333;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

/*--------------------------------------------------------------------------------------------------------CSS - HEADER---------------------------------------------------------------------------------------------------*/

        .header {
            position: relative;
            background: var(--primary-color);
            color: var(--text-light);
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            text-align: center;
            align-items: left;
            gap: 1rem;
            padding: 16px;
        }
        .nav-wrapper {
            position: absolute;
            top: 1rem;
            left: 1rem;
            z-index: 1000;
        }

        .nav-container {
            position: relative;
            width: 100%;
            left: 0; /* Bal oldalon kezdődjön */
            right: 0; /* Jobb oldalon érjen véget */
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

        .time {
            text-align: center;
            font-size: 24px;
            color: black;
            background-color: white;
            opacity: 0.4;
            padding: 8px 0;
            border-radius: 20px;
        }

        .search-container {
            width: 100%;
            max-width: 700px;
            min-width: 200px;
            position: relative;
            align-content: center;
            margin: 1rem 0;
        }

        #searchBox {
            width: 80%;
            padding: 16px;
            border: none;
            border-radius: 25px;
            background: white;
            box-shadow: var(--shadow);
            font-size: 16px;
            transition: var(--transition);
            align-content: center;
        }

        #searchBox:focus {
            outline: none;
            transform: scale(1.02);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .input-wrapper{
            width: 100%;
        }
        
/*--------------------------------------------------------------------------------------------------------HEADER END-----------------------------------------------------------------------------------------------------*/

/*--------------------------------------------------------------------------------------------------------CSS - OTHER PARTS----------------------------------------------------------------------------------------------*/
        #weekbtn{
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(to right, #211717, #b30000);
            color: white; 
            border-radius: 40px;
            padding: 8px 16px; 
            border: none;
            font-size: 16px; 
            width: auto; 
            max-width: 90%; 
            margin-left: 10%;
            margin-right: 10%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                
        }
        #weekendbtn{
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(to right, #211717, #b30000);
            color: white; 
            border-radius: 40px;
            padding: 8px 16px; 
            border: none;
            font-size: 16px; 
            width: auto;
            margin-right: 10%;
            margin-left: 10%; 
            max-width: 90%; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                
        }

        .route-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .route-button {
            padding: 12px 28px;
            background-color: var(--accent-color);
            color: var(--primary-color);
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.2s, background-color 0.2s;
            display: flex;
            float: right;
            margin-right: 10px;
        }

        .route-button:hover {
            background-color: var(--accent-color);
            transform: translateY(-2px);
        }

        .route-card {
            background: #fcfcfc;
            border-radius: 20px;
            box-shadow: var(--shadow);
            padding: 1.5rem;
            transition: var(--transition);
            animation: fadeIn 0.5s ease-out;
        }

        .route-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .route-number {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid grey;
        }

        .route-details {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }

        .next-departure, {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .show-stops {
            color: var(--accent-color);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 10px;
            transition: var(--transition);
            text-align: center;
            margin-top: 1rem;
        }

        .show-stops:hover {
            background: rgba(0, 31, 63, 0.1);
        }

        .stops-list {
            display: none;
            margin-top: 1rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
            line-height: 1.6;
            color: #000;
        }

        .live-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            background: darkgreen;
            color: white;
            font-weight: bold;
            padding: 5px 12px;
            margin: 0 auto;
            border-radius: 30px;
            font-size: 15px;
            animation: pulse 2s infinite;
            width: 200px; 
            height: 40px;
        }
/*--------------------------------------------------------------------------------------------------------OTHER PARTS END------------------------------------------------------------------------------------------------*/

/*--------------------------------------------------------------------------------------------------------CSS - FOOTER---------------------------------------------------------------------------------------------------*/
        footer {
            text-align: center;
            padding: 10px;
            background-color: var(--primary-color);
            color: var(--text-light);
            border-radius: 10px;
            margin-top: 20px;
            box-shadow: var(--shadow);
            background: var(--primary-color);
            color: var(--text-light);
            padding: 3rem 2rem;
            margin-top: 4rem;
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
            color: var(--text-color);
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

/*--------------------------------------------------------------------------------------------------------CSS - @MEDIA---------------------------------------------------------------------------------------------------*/

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }

        @media (max-width: 480px) {
            .header-content {
                padding: 1rem;
            }

            h1 {
                font-size: 1.5rem;
            }

            .filter-button {
                padding: 0.5rem 0.8rem;
                font-size: 0.8rem;
            }

            .route-container {
                grid-template-columns: 1fr;
                padding: 1rem;
            }

            #weekbtn #weekendbtn {
                font-size: 15px;
                padding: 6px 16px;
            }
        }

        @media (max-width: 768px) {
            #weekbtn, #weekendbtn, {
                font-size: 15px; 
                padding: 8px 24px; 
                max-width: 100%; 
            }
        }

/*--------------------------------------------------------------------------------------------------------@MEDIA END-----------------------------------------------------------------------------------------------------*/
                
    </style>
</head>
<body>
<!-- -----------------------------------------------------------------------------------------------------HTML - HEADER----------------------------------------------------------------------------------------------- -->
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
                        <a href="index.php" class="active">
                                <img src="home.png" alt="Főoldal">
                                <span>Főoldal</span>
                            </a>
                        </li>
                        <li>
                            <a href="terkep.php" class="active">
                                <img src="placeholder.png" alt="Térkép">
                                <span>Térkép</span>
                            </a>
                        </li>
                        <li>
                            <a href="keses.php">
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
             <div id="toggle"></div>
                <h1><i class="fas fa-bus"></i> Kaposvár Helyi Járatok</h1>
                <div class="live-indicator">
                    <i class="fas fa-circle"></i>&nbsp Következő indulás
                </div>
                <div style="margin-right: 50%;margin-left: 35%; width: 30%;" id="time" class="time"></div>
                <div style="margin: 0 auto; align-items: center" class="search-container">
                <input type="text" id="searchBox" placeholder="Keress járatszám vagy útvonal alapján..." />
                <div>
                </div>
            </div>    
            </div ><br>
<!-- -----------------------------------------------------------------------------------------------------HEADER END-------------------------------------------------------------------------------------------------- -->

    <div style="display: flex;justify-content: center;"><br>
      <button id="weekbtn"  onclick="week()" >Hétköznap</button>
        <button id="weekendbtn" onclick="weekend()">Hétvége</button>       
    </div> 
   
    <div id="routeContainer" class="route-container"></div>

<!-- -----------------------------------------------------------------------------------------------------HTML - FOOTER------------------------------------------------------------------------------------------------ -->
    <footer>
    <div class="footer-content">
            <div class="footer-section">
                <h2>Kaposvár közlekedés</h2>
                <p style="font-style: italic">Megbízható közlekedési szolgáltatások<br> az Ön kényelméért már több mint 50 éve.</p><br>
                <div class="social-links">
                    <a style="color: darkblue;" href="#"><i class="fab fa-facebook"></i></a>
                    <a style="color: lightblue"href="#"><i class="fab fa-twitter"></i></a>
                    <a style="color: red"href="#"><i class="fab fa-instagram"></i></a>
                </div>
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
<!-- -----------------------------------------------------------------------------------------------------FOOTER END--------------------------------------------------------------------------------------------------- -->

<script>
/*--------------------------------------------------------------------------------------------------------JS - LEGÖRDÜLÖLISTA----------------------------------------------------------------------------------------------*/

      document.getElementById('menuBtn').addEventListener('click', function() {
            this.classList.toggle('active');
            document.getElementById('dropdownMenu').classList.toggle('active');
        });

        // Kívülre kattintás esetén bezárjuk a menüt
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
// API kérés vagy helyi adatok használata
async function fetchBusRoutes() {
    try {
        const response = await fetch('http://localhost:3000/api/kovetkezo_meall');
        
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        
        // válasz ellenőrzése
        const responseText = await response.text();
        console.log("Raw API response:", responseText.substring(0, 200) + "...");
        
        // JSON feldolgozás
        let data;
        try {
            data = JSON.parse(responseText);
        } catch (error) {
            console.error("JSON parse error:", error);
            console.warn("Using hardcoded bus route data");
            return getBusRoutesData();        }
        
        console.log("Successfully fetched bus routes from API:", data);
        
        return processRouteData(data);
    } catch (error) {
        console.error("Error fetching bus routes from API:", error);
        console.warn("Using hardcoded bus route data");
        return getBusRoutesData(); 
    }
}

// adatszerkezet egységesítése
function processRouteData(routes) {
    return routes.map(route => {
        // Megállók kezelése
        let stops = [];
        if (route.stops) {
            if (typeof route.stops === 'string') {
                try {
                    stops = JSON.parse(route.stops);
                } catch (e) {
                    stops = route.stops.split(',').map(s => s.trim());
                }
            } else if (Array.isArray(route.stops)) {
                stops = route.stops;
            }
        }
        
        // közlekedési napok
        let dayGoes = [];
        if (route.daygoes) {
            if (typeof route.daygoes === 'string') {
                try {
                    dayGoes = JSON.parse(route.daygoes);
                } catch (e) {
                    dayGoes = route.daygoes.split(',').map(d => d.trim());
                }
            } else if (Array.isArray(route.daygoes)) {
                dayGoes = route.daygoes;
            }
        }
        
        // indulási idők
        let start = [];
        if (route.start) {
            if (typeof route.start === 'string') {
                try {
                    start = JSON.parse(route.start);
                } catch (e) {
                    if (route.start.includes(';')) {
                        start = route.start.split(';')
                            .map(s => s.trim())
                            .filter(s => s !== '')
                            .map(s => s.split(':').slice(0, 2).join(':'));
                    } else {
                        start = route.start.split(',')
                            .map(s => s.trim())
                            .filter(s => s !== '')
                            .map(s => s.split(':').slice(0, 2).join(':'));
                    }
                }
            } else if (Array.isArray(route.start)) {
                start = route.start;
            }
        }
        
        // időformátum tisztítás
        start = start.map(time => {
            if (typeof time === 'string') {
                const parts = time.split(':');
                if (parts.length > 2) {
                    return `${parts[0]}:${parts[1]}`;
                }
            }
            return time;
        }).filter(time => time && time.length > 0);
        
        // hétvégi indulások
        let startWeekend = [];
        if (route.startWeekend) {
            if (typeof route.startWeekend === 'string') {
                try {
                    startWeekend = JSON.parse(route.startWeekend);
                } catch (e) {
                    if (route.startWeekend.includes(';')) {
                        startWeekend = route.startWeekend.split(';')
                            .map(s => s.trim())
                            .filter(s => s !== '');
                    } else {
                        startWeekend = route.startWeekend.split(',')
                            .map(s => s.trim())
                            .filter(s => s !== '');
                    }
                }
            } else if (Array.isArray(route.startWeekend)) {
                startWeekend = route.startWeekend;
            }
        }
        
        return {
            id: route.id,
            number: route.number.toString(),
            name: route.name,
            stops: stops,
            dayGoes: dayGoes,
            start: start,
            startWeekend: startWeekend
        };
    });
}

// következő indulás keresése
function findNextBusDeparture(routes) {
    const currentDate = new Date();
    const currentDay = currentDate.toLocaleDateString('en-US', { weekday: 'long' });
    const currentTimeString = currentDate.toLocaleTimeString('hu-HU', { 
        hour: '2-digit', 
        minute: '2-digit', 
        hour12: false 
    });

    console.log("Current day:", currentDay);
    console.log("Current time:", currentTimeString);

    return routes.map(route => {
        // járat ellenőrzése
        if (!route) {
            return { 
                number: "?", 
                name: "Ismeretlen járat", 
                stops: [], 
                dayGoes: [],
                start: [],
                startWeekend: [],
                nextBus: 'Érvénytelen járat' 
            };
        }
        
        // mezők biztosítása
        const dayGoes = Array.isArray(route.dayGoes) ? route.dayGoes : [];
        const start = Array.isArray(route.start) ? route.start : [];
        const startWeekend = Array.isArray(route.startWeekend) ? route.startWeekend : [];
        const stops = Array.isArray(route.stops) ? route.stops : [];
        
        // mai nap ellenőrzése
        if (!dayGoes.includes(currentDay)) {
            return { 
                ...route, 
                dayGoes,
                start,
                startWeekend,
                stops,
                nextBus: 'Nincs ma indulás' 
            };
        }

        // megfelelő menetrend
        const isWeekend = ["Saturday", "Sunday"].includes(currentDay);
        const timesToUse = isWeekend && startWeekend.length > 0 ? startWeekend : start;

        // érvényes idők szűrése
        const validTimes = timesToUse.filter(time => {
            if (!time || typeof time !== 'string') return false;
            const parts = time.split(':');
            if (parts.length < 2) return false;
            
            const hours = parseInt(parts[0]);
            const minutes = parseInt(parts[1]);
            return !isNaN(hours) && !isNaN(minutes) && 
                   hours >= 0 && hours < 24 && 
                   minutes >= 0 && minutes < 60;
        }).sort();

        // következő indulás
        const nextDeparture = validTimes.find(time => time > currentTimeString) || validTimes[0];

        return {
            ...route,
            dayGoes,
            start,
            startWeekend,
            stops,
            nextBus: nextDeparture || 'Nincs indulás'
        };
    });
}

// járatok megjelenítése
function updateRouteDetails(routes, filter = "all") {
    const container = document.getElementById('routeContainer');
    if (!container) {
        console.error("Container element not found");
        return;
    }

    container.innerHTML = '<div style="text-align: center; padding: 20px;">Adatok betöltése...</div>';

    const currentDate = new Date();
    const dayOfWeek = currentDate.toLocaleDateString('en-US', {weekday: 'long'});
    
    //szűrés alkalmazása
    let filteredRoutes;
    if (filter === "weekday") {
        filteredRoutes = routes.filter(route => 
            Array.isArray(route.dayGoes) && 
            route.dayGoes.some(day => ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"].includes(day))
        );
    } else if (filter === "weekend") {
        filteredRoutes = routes.filter(route => 
            Array.isArray(route.dayGoes) && 
            route.dayGoes.some(day => ["Saturday", "Sunday"].includes(day))
        );
    } else {
        filteredRoutes = routes.filter(route => 
            Array.isArray(route.dayGoes) && 
            route.dayGoes.includes(dayOfWeek)
        );
    }
    
    // tartalom törlése
    container.innerHTML = '';
    
    if (filteredRoutes.length === 0) {
        container.innerHTML = '<div style="text-align: center; padding: 20px;">Nincsenek elérhető járatok a kiválasztott időszakban</div>';
        return;
    }

    // kártyák létrehozása
    filteredRoutes.forEach(route => {
        const routeElement = document.createElement('div');
        routeElement.classList.add('route-card');

        const stopsList = Array.isArray(route.stops) && route.stops.length > 0
            ? route.stops.map(stop => `<div><i class="fas fa-stop"></i> ${stop}</div>`).join('')
            : '<div>Nincs megálló információ</div>';

        routeElement.innerHTML = `
            <div class="route-number">${route.number}</div>
            <div class="route-name">${route.name}</div>
            <div class="next-departure">
                <i class="far fa-clock"></i>
                &nbsp;Következő indulás: ${route.nextBus} <span style="font-weight: bold;font-size:40px;margin-left:3%">→</span>
            </div>
            <div class="show-stops" onclick="toggleStops(this)">
                <i class="fas fa-map-marker-alt"></i>
                Megállók megjelenítése
            </div>
            <div class="stops-list">
                ${stopsList}
            </div>
        `;

        container.appendChild(routeElement);
    });
}

// Buszjáratok frissítése
async function updateBusRoutes(filterType = "all") {
    try {
        // Töltőképernyő
        const container = document.getElementById('routeContainer');
        if (container) {
            container.innerHTML = '<div style="text-align: center; padding: 20px;">Adatok betöltése...</div>';
        }
        
        // járatok lekérése
        const busRoutes = await fetchBusRoutes();
        
        if (!busRoutes || busRoutes.length === 0) {
            console.error("No bus routes available!");
            if (container) {
                container.innerHTML = '<div style="text-align: center; padding: 20px;">Nincsenek elérhető járatok</div>';
            }
            return;
        }
        
        // indulások számítása
        const routesWithNextBus = findNextBusDeparture(busRoutes);
        
        // DOM frissítés
        updateRouteDetails(routesWithNextBus, filterType);
    } catch (error) {
        console.error("Error updating bus routes:", error);
        const container = document.getElementById('routeContainer');
        if (container) {
            container.innerHTML = '<div style="text-align: center; padding: 20px;">Hiba történt az adatok betöltése közben</div>';
        }
    }
}

// hétköznapi szűrés
async function week() {
    console.log("Hétköznapi közlekedés");
    await updateBusRoutes("weekday");
}

// hétvégi szűrés
async function weekend() {
    console.log("Hétvégi közlekedés");
    await updateBusRoutes("weekend");
}

// megállók mutatása/rejtése
function toggleStops(element) {
    const stopsList = element.nextElementSibling;
    const isVisible = stopsList.style.display === "block";
    
    stopsList.style.display = isVisible ? "none" : "block";
    element.innerHTML = isVisible
        ? '<i class="fas fa-map-marker-alt"></i> Megállók megjelenítése'
        : '<i class="fas fa-map-marker-alt"></i> Megállók elrejtése';
}

// alap járatadatok
function getBusRoutesData() {
    return [
        {
            "id": 1,
            "number": "12",
            "name": "Helyi autóbusz-állomás - Sopron u. - Laktanya",
            "stops": ["Helyi autóbusz-állomás", "Corso", "Zárda u.", "Honvéd u.", "Arany J. tér", "Losonc-köz", "Brassó u.", "Sopron u.", "Búzavirág u.", "Laktanya"],
            "dayGoes": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
            "start": ["05:00", "05:30", "05:55", "06:10", "06:30", "07:05", "07:30", "09:50", "10:00", "10:35", "11:00", "12:30", "13:00", "13:30", "14:20", "15:00", "15:45", "16:00", "16:30", "16:45", "17:00", "17:15", "17:30", "19:00", "20:30"],
            "startWeekend": []
        },
        {
            "id": 2,
            "number": "13",
            "name": "Helyi autóbusz-állomás - Kecelhegy - Helyi autóbusz-állomás",
            "stops": ["Helyi autóbusz-állomás", "Berzsenyi u. felüljáró", "Berzsenyi u. 30.", "Füredi utcai csomópont", "Városi könyvtár", "Vasútköz", "Hajnóczy u. csp.", "Mátyás k. u., forduló", "Kecelhegyalja u.", "Kőrösi Cs. S. u.", "Kecelhegyi iskola", "Bethlen G. u.", "Magyar Nobel-díjasok tere", "Eger u.", "Állatkórház", "Kölcsey u.", "Tompa M. u.", "Berzsenyi u. felüljáró", "Helyi autóbusz-állomás"],
            "dayGoes": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
            "start": ["06:15", "06:40", "08:00", "10:00", "13:05", "14:15", "16:20", "21:10"],
            "startWeekend": ["06:20", "08:10", "09:10", "10:10", "11:10", "13:10", "15:10", "19:10"]
        },
        {
            "id": 3,
            "number": "20",
            "name": "Raktár u. - Laktanya - Videoton",
            "stops": ["Raktár u.", "Jutai u. 45.", "Tóth Á. u.", "Jutai u. 24.", "Hajnóczy u. csp.", "Vasútköz", "Városi könyvtár", "Füredi utcai csomópont", "Toldi lakónegyed", "Kinizsi ltp.", "Búzavirág u.", "Laktanya", "Búzavirág u.", "Nagyszeben u.", "Losonc-köz", "Arany J. tér", "ÁNTSZ", "Pázmány P. u.", "Kisgát", "Mező u. csp.", "Kenyérgyár u. 1.", "Videoton"],
            "dayGoes": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
            "start": ["05:20", "07:00", "17:40"],
            "startWeekend": []
        }
    ];
}

document.addEventListener('DOMContentLoaded', async () => {
    console.log("DOM loaded, updating routes");
    
    // gombok kezelése
    const weekBtn = document.getElementById('weekbtn');
    const weekendBtn = document.getElementById('weekendbtn');
    
    if (weekBtn) weekBtn.addEventListener('click', week);
    if (weekendBtn) weekendBtn.addEventListener('click', weekend);
    
    // óra frissítés
    updateTime();
    setInterval(updateTime, 1000);
    
    // járatok töltése
    await updateBusRoutes();
    
    // keresés inicializálás
    initializeSearch();
    
    // frissítés
    setInterval(() => updateBusRoutes(), 60000);
});

// kereső funkció
function initializeSearch() {
    const searchBox = document.getElementById('searchBox');
    if (!searchBox) return;
    
    searchBox.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase().trim();
        const routeCards = document.querySelectorAll('.route-card');
        
        if (searchTerm === '') {
            // minden kártya mutatása
            routeCards.forEach(card => {
                card.style.display = 'block';
            });
            return;
        }
        
        routeCards.forEach(card => {
            // kártya szöveg
            const routeNumber = card.querySelector('.route-number')?.textContent.trim() || '';
            const routeName = card.querySelector('.route-name')?.textContent.toLowerCase() || '';
            const stopsList = card.querySelector('.stops-list');
            const stops = stopsList?.textContent.toLowerCase() || '';
            
            // pontos egyezés
            if (routeNumber === searchTerm) {
                card.style.display = 'block';
                card.style.animation = 'fadeIn 0.5s ease-out';
                return;
            }
            
            // részleges egyezés
            if (routeNumber.includes(searchTerm) || 
                routeName.includes(searchTerm) || 
                stops.includes(searchTerm)) {
                card.style.display = 'block';
                card.style.animation = 'fadeIn 0.5s ease-out';
            } else {
                card.style.display = 'none';
            }
        });
    });
}

// idő kijelzése
function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('hu-HU');
    const timeElement = document.getElementById('time');
    if (timeElement) {
        timeElement.textContent = timeString;
    }
}
    </script>
</body>
</html>