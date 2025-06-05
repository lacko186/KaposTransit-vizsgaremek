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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="betolt.js"></script>

    <style>
        :root {
            --primary-color:linear-gradient(to right, #211717,#b30000);
            --accent-color: #FFC107;
            --text-light: #fbfbfb;
            --shadow: 0 2px 4px rgba(0,0,0,0.1);
            --secondary-color: #3498db;
            --hover-color: #2980b9;
            --background-light: #f8f9fa;
            --shadow-color: rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
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
            padding: 1rem;
            box-shadow: 0 2px 10px var(--shadow-color);
        }

        .header h1 {
            margin-left: 2%;
            text-align: center;
            font-size: 2rem;
            padding: 1rem 0;
            margin-right: 5%;
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

        #datePicker{
            margin-left: 45%;
            font-size: 1rem;
            background-color: #fbfbfb;
            color: #211717;
            border: 1px solid #fff;
        }      
/*--------------------------------------------------------------------------------------------------------HEADER END-----------------------------------------------------------------------------------------------------*/

/*--------------------------------------------------------------------------------------------------------CSS - BODY CONTENT----------------------------------------------------------------------------------------------*/
        .route-container {
            display: grid;
            padding: 2rem;
            max-width: 1000px;
            margin: 0 auto;
        }

        .route-card {
            background: #fcfcfc;
            width: 950px;
            border-radius: 20px;
            box-shadow: var(--shadow);
            padding: 1rem;
            transition: var(--transition);
            animation: fadeIn 0.5s ease-out;
            margin-bottom: 10px;
        }

        .route-card:hover{
            color: 000;
            background: #E9E8E8;
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .route-number {
            background: #b30000;
            display: inline-block;
            width: 3%;
            height: 35px;
            font-size: 1.5rem;
            font-weight: bold;
            border-radius: 5px;
            color: var(--text-light);
            padding: 5px 20px;
        }

        .route-name{
            display: inline-block;
            color: #636363;
            font-size: 1.5rem;
            font-weight: bold;
            margin-left: 5px;
        }

        .route-details {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }       
/*--------------------------------------------------------------------------------------------------------BODY CONTENT END------------------------------------------------------------------------------------------------*/

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

            .route-container {
                grid-template-columns: 1fr;
                padding: 1rem;
            }

            #datePicker{
                margin-left: 28%;
            }

            .route-number{
                display: inline;
                padding-right: 15px;
            }

            .route-name{
                display: inline;
            }

            .route-card{
                width: 330px;
            }

            .nav-wrapper{
                left: 0.01rem;
            }
        }

        @media (max-width: 380px) {
            .header-content {
                padding: 1rem;
            }

            h1 {
                font-size: 1.5rem;
            }

            .route-container {
                grid-template-columns: 1fr;
                padding: 1rem;
            }

            #datePicker{
                margin-left: 28%;
            }

            .route-number{
                display: inline;
                padding-right: 15px;
            }

            .route-name{
                display: inline;
            }

            .route-card{
                width: 295px;
            }

            .nav-wrapper{
                left: 0.01rem;
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
            <h1><i class="fas fa-bus"></i> Kaposvár Helyi Járatok</h1>
            <input type="date" id="datePicker" require /> 
        </div>
<!-- -----------------------------------------------------------------------------------------------------HEADER END-------------------------------------------------------------------------------------------------- -->

<!-- -----------------------------------------------------------------------------------------------------HTML - BODY CONTENT----------------------------------------------------------------------------------------- -->
    <div id="routeContainer" class="route-container"></div>
<!-- -----------------------------------------------------------------------------------------------------BODY CONTENT END-------------------------------------------------------------------------------------------- -->

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

/*--------------------------------------------------------------------------------------------------------JAVASCRIPT - NAV-----------------------------------------------------------------------------------------------*/
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
/*--------------------------------------------------------------------------------------------------------NAV END--------------------------------------------------------------------------------------------------------*/

/*--------------------------------------------------------------------------------------------------------JAVASCRIPT - DATE PICKER---------------------------------------------------------------------------------------*/
    const today = new Date();
    document.getElementById("datePicker").value = today.toISOString().split("T")[0];
    document.getElementById("datePicker").min = today.toISOString().split("T")[0];
/*--------------------------------------------------------------------------------------------------------DATE PICKER END------------------------------------------------------------------------------------------------*/

/*--------------------------------------------------------------------------------------------------------JAVASCRIPT - DISPLAY ROUTES------------------------------------------------------------------------------------*/
let busRoutes = []; 

// api kérés
function fetchBusRoutes() {
    fetch('http://localhost:3000/api/buszjaratok')
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load bus routes data');
            }
            return response.json();
        })
        .then(data => {
            console.log('API Response:', data);

            busRoutes = [];
            let addedNumbers = new Set();

            data.forEach(route => {
                if (!addedNumbers.has(route.number)) {
                    busRoutes.push(route);
                    addedNumbers.add(route.number);
                }
            });

            console.log('Unique Bus Routes:', busRoutes);
            displayRoutes();
        })
        .catch(error => {
            console.error('Error fetching bus routes:', error);
        });
}

// hétvége ellenőrzés
function isWeekend(date) {
    const day = date.getDay(); 
    return day === 0 || day === 6;
}

// járatok listázása
function displayRoutes(filter = "all", selectedDate = new Date()) {
    const routeContainer = document.getElementById('routeContainer');
    routeContainer.innerHTML = "";

    const isWeekendDate = isWeekend(selectedDate);

    const filteredRoutes = busRoutes.filter(route => {
        // hétvégei járatok
        if (isWeekendDate) {
            return route.weekend !== null; 
        } else {
            // hétköznapi járatok
            return route.weekday !== null; 
        }
    });

    // kártyák létrehozása
    filteredRoutes.forEach((route, index) => {
        const routeCard = document.createElement('div');
        routeCard.className = 'route-card';
        routeCard.style.animationDelay = `${index * 0.1}s`;

        // kártya tartalma
        routeCard.innerHTML = `
            <div class="route-number" id="routeNumber">
                ${route.number}
            </div>&nbsp;&nbsp;
            <div class="route-name">
                ${route.name}
            </div>
        `;

        // járatszám tárolás
        routeCard.setAttribute('data-route-number', route.number);

        // kattintás kezelés
        routeCard.addEventListener('click', () => {
            const selectedRoute = routeCard.getAttribute('data-route-number'); // szám lekérés
            const url = new URL('kkzrt/indulasidok.php', window.location.origin);
            url.searchParams.set('route', selectedRoute);

            // átirányítás
            window.location.href = url.toString();
        });

        routeContainer.appendChild(routeCard);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    fetchBusRoutes();

    const datePicker = document.getElementById('datePicker');
    datePicker.addEventListener('change', (event) => {
        const selectedDate = new Date(event.target.value); // dátum lekérés
        if (selectedDate) {
            displayRoutes("all", selectedDate); // frissítés
        }
    });
});
/*--------------------------------------------------------------------------------------------------------DISPLAY ROUTES END---------------------------------------------------------------------------------------------*/

    </script>
</body>
</html>