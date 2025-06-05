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
            --accent-color: #7A7474;
            --text-light: #fbfbfb;
            --secondary-color: #3498db;
            --hover-color: #2980b9;
            --background-light: #f8f9fa;
            --shadow: rgba(0, 0, 0, 0.1);
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
            margin-left: 35%;
            display: inline-block;
        }

        .backBtn{
            display: inline-block;
            width: 3%;
            background: #372E2E;
            border: none;
            box-shadow: 0 2px 10px var(--shadow-color);
        }

        .backBtn:hover{
            background: #b40000;
        }

        .backBtn i{
            height: 30px;
            color: var(--text-light);
            padding-top: 20px;
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

        .routeNumCon{
            background-color: #fcfcfc;
        }

        .route-card {
            background: #fcfcfc;
            width: 950px;
            border-radius: 20px;
            box-shadow: var(--shadow);
            padding: 1.5rem;
            transition: var(--transition);
            animation: fadeIn 0.5s ease-out;
            margin-bottom: 10px;
            font-size: 1.5rem;
            color: #636363;
        }

        .start-time-card {
            margin: 5px 0;
        }

        .route-card:hover{
            color: 000;
            background: #E9E8E8;
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .routeCon{
            background: #fbfbfb;
            width: 90%;
            margin-bottom: 5px;
            padding: 20px;
            margin: 0 auto;
            border-radius: 10px;
        }

        .route-number {
            background: #b30000;
            display: inline;
            width: 3%;
            height: 60%;
            font-size: 2.5rem;
            font-weight: bold;
            border-radius: 5px;
            padding: 5px 20px;
            color: var(--text-light);
            margin-left: 18%;
            margin-bottom: 25px;
        }

        .route-name{
            display: inline;
            color: #636363;
            font-size: 1.5rem;
            font-weight: bold;
            margin-left: 10%;
            margin-right: 16%;
            background: #e8e8e8;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .switchBtn{
            display: inline;
            float: right;
            background: #fbfbfb;
            margin-right: 20%;
        }

        .switchBtn:hover{
            background: #E9E8E8;
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

            .route-container {
                grid-template-columns: 1fr;
                padding: 1rem;
            }

            .route-card{
                width: 340px;
            }

            .route-number{
                margin-left: 0;
                padding-right: 25px;
                margin-right: 75%;
            }

            .route-name{
                margin-left: 0;
                font-size: 1rem;
                margin-right: 65%;
            }

            .routeCon,{
                width: 365px;
            }

            .route, .routeNumCon{
                width: 365px;
            }

            .switchBtn{
                margin-right: 0;
            }

            .header h1{
                margin-left: 2%;
            }

            #datePicker{
                margin-left: 28%;
            }

            .backBtn{
                width: 15%;
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

            .route-card{
                width: 295px;
            }

            .route-number{
                margin-left: 0;
                padding-right: 30px;
                margin-right: 55%;
            }

            .route-name{
                font-size: 1.25rem;
                margin-left: 0;
                margin-right: 35%;
            }

            .routeNumCon{
                height: 200px;
            }

            .routeCon{
                width: 335px;
            }

            .route{
                width: 335px;
            }

            .switchBtn{
                margin-right: 0;
            }

            .header h1{
                margin-left: 2%;
            }

            #datePicker{
                margin-left: 28%;
            }

            .backBtn{
                width: 15%;
            }
        }
/*--------------------------------------------------------------------------------------------------------@MEDIA END-----------------------------------------------------------------------------------------------------*/
        
    </style>
</head>
<body>

<!-- -----------------------------------------------------------------------------------------------------HTML - HEADER------------------------------------------------------------------------------------------------ -->
        <div class="header">
            <button class="backBtn" id=backBtn><i class="fa-solid fa-chevron-left"></i></button>
            <h1><i class="fas fa-bus"></i> Kaposvár Helyi Járatok</h1> 
            <input type="date" id="datePicker" require />
        </div>

        <div id="route">
        <div id="routeNumCon" class="routeCon"></div>
        </div>
<!-- -----------------------------------------------------------------------------------------------------HEADER END--------------------------------------------------------------------------------------------------- -->

<!-- -----------------------------------------------------------------------------------------------------HTML - BODY CONTENT------------------------------------------------------------------------------------------ -->
    <div id="routeContainer" class="route-container"></div>
<!-- -----------------------------------------------------------------------------------------------------BODY CONTENT END--------------------------------------------------------------------------------------------- -->

<!-- -----------------------------------------------------------------------------------------------------HTML - FOOTER------------------------------------------------------------------------------------------------ -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h2>Kaposvár közlekedés</h2>
                <p style="font-style: italic">Megbízható közlekedési szolgáltatások<br> az Ön kényelméért már több mint 50 éve.</p><br>
                <div class="social-links">
                    <a style="color: darkblue;" href="https://www.facebook.com/VOLANBUSZ/"><i class="fab fa-facebook"></i></a>
                    <a style="color: lightblue"href="https://x.com/volanbusz_hu?mx=2"><i class="fab fa-twitter"></i></a>
                    <a style="color: red"href="https://www.instagram.com/volanbusz/"><i class="fab fa-instagram"></i></a>
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
/*--------------------------------------------------------------------------------------------------------JAVASCRIPT - DATE PICKER---------------------------------------------------------------------------------------*/
    const today = new Date();
    document.getElementById("datePicker").value = today.toISOString().split("T")[0];
    document.getElementById("datePicker").min = today.toISOString().split("T")[0];
/*--------------------------------------------------------------------------------------------------------DATE PICKER END------------------------------------------------------------------------------------------------*/

/*---------------------------------------------------------------------------------------------------------JAVASCRIPT - BACK BUTTON--------------------------------------------------------------------------------------*/
    document.getElementById('backBtn').addEventListener('click', function() {
            window.location.href = 'jaratok.php';
    });
/*---------------------------------------------------------------------------------------------------------BACK BUTTON END-----------------------------------------------------------------------------------------------*/
   
/*--------------------------------------------------------------------------------------------------------JAVASCRIPT - DISPLAY ROUTES------------------------------------------------------------------------------------*/
       // URL-ből járatszám kinyerése
const routeNumber = new URLSearchParams(window.location.search).get('route');

// busz menetrendek lekérése és megjelenítése
function fetchBusSchedules() {
    const datePicker = document.getElementById('datePicker');
    const selectedDate = new Date(datePicker.value); // Kiválasztott dátum lekérése
    const dayOfWeek = selectedDate.getDay(); // A hét napjának lekérése (0-6, ahol 0 a vasárnap)

    // hétköznap vagy hétvége ellenőrzése
    const isWeekday = dayOfWeek >= 1 && dayOfWeek <= 5;

    fetch('http://localhost:3000/api/buszjaratok') // API végpont a menetrendekhez
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load bus schedule data');
            }
            return response.json();
        })
        .then(data => {
            // járatok szűrése a kiválasztott járatszám alapján
            const filteredRoutes = data.filter(route => route.number === routeNumber);

            if (filteredRoutes.length > 0) {
                // szűrés nap típusa szerint (hétköznap vagy hétvége)
                const dayFilteredRoutes = filteredRoutes.filter(route => {
                    return isWeekday ? route.weekday !== null : route.weekend !== null;
                });

                if (dayFilteredRoutes.length === 0) {
                    console.log("No matching routes for the selected day type (weekend or weekday).");
                    return; 
                }

                // adatok csoportosítása menetrend azonosító szerint
                const groupedData = dayFilteredRoutes.reduce((acc, item) => {
                    if (!acc[item.schedule_id]) {
                        acc[item.schedule_id] = [];
                    }
                    acc[item.schedule_id].push(item);
                    return acc;
                }, {});

                // az első szűrt járat adatainak kinyerése
                const route = filteredRoutes[0];
                const routeNumber = route.number;
                const routeName = route.name;

                // adatok átadása a megjelenítő függvénynek
                renderStartTimes(groupedData, isWeekday, routeNumber, routeName);
            } else {
                console.warn("No matching routes found for the route number.");
            }
        })
        .catch(error => {
            console.error('Error fetching bus schedules:', error);
        });
}

// irány változó kezdőértéke (oda)
let isForward = true;

// irányváltás függvény
function switchDirection() {
    isForward = !isForward; 
    updateSchedule(); 
    // frissítés az új iránnyal
}

// menetrend frissítése
function updateSchedule() {
    if (!window.currentGroupedData) return; // hiba megelőzése

    renderStartTimes(
        window.currentGroupedData, 
        window.isWeekday, 
        window.currentRouteNumber, 
        window.currentRouteName, 
        isForward
    );
}

// indulási idők megjelenítése
function renderStartTimes(groupedData, isWeekday, routeNumber, routeName, isForward) {
    const container = document.getElementById('routeContainer');
    container.innerHTML = ""; 

    // adatok tárolása a váltáshoz
    window.currentGroupedData = groupedData;
    window.isWeekday = isWeekday;
    window.currentRouteNumber = routeNumber;
    window.currentRouteName = routeName;

    // járatnév módosítása
    let modifiedRouteName = routeName;
    if (!isForward) {
        const parts = routeName.split(" - "); // név elválasztás " - " 
        if (parts.length > 1) {
            modifiedRouteName = `${parts[parts.length - 1]} - ${parts.slice(1, -1).join(" - ")} - ${parts[0]}`;
        }
    }

    // járat adatok beállítása
    const routeNumCon = document.getElementById('routeNumCon');
    routeNumCon.innerHTML = `
        <div class="route-number">${routeNumber}</div>
        <div class="route-name">${modifiedRouteName}</div>
        <button id="switchBtn" onclick="switchDirection()">
            <img src="switch.png" alt="Switch" style="width: 20px; height: 25px;">
        </button>
    `;
    route.appendChild(routeNumCon);

    // Menetrend szűrése a kiválasztott irány alapján
    let foundRoutes = false; 

    for (const scheduleId in groupedData) {
        const schedule = groupedData[scheduleId];

        // járatok szűrése
        const filteredSchedule = schedule.filter(route => {
            if (route.goes_back === 0) {
                return route.stop_id === 1; // egyirányú járatok 
            }
            return isForward ? route.direction === 'forward' && route.stop_id === 1 
                            : route.direction === 'backward' && route.stop_id === 1;
        });

        if (filteredSchedule.length > 0) {
            foundRoutes = true;
            filteredSchedule.forEach(route => {
                // járatkártya létrehozása
                const card = document.createElement('div');
                card.classList.add('route-card');

                const [hour, minute] = route.stop_time.split(':'); // idő formázás
                card.innerHTML = `<div>${hour}:${minute}</div>`;

                // adatok tárolása 
                card.setAttribute('data-number', routeNumber);
                card.setAttribute('data-name', modifiedRouteName);
                card.setAttribute('data-stop-time', route.stop_time);
                card.setAttribute('data-schedule-id', route.schedule_id);

                container.appendChild(card);

                // kattintás eseménykezeő
                card.addEventListener('click', () => {
                    const number = card.getAttribute('data-number');
                    const name = card.getAttribute('data-name');
                    const stop_time = card.getAttribute('data-stop-time');
                    const schedule_id = card.getAttribute('data-schedule-id');

                    // átirányítás a megalloidok.php
                    window.location.href = `megalloidok.php?number=${encodeURIComponent(number)}&name=${encodeURIComponent(name)}&stop_time=${encodeURIComponent(stop_time)}&schedule_id=${encodeURIComponent(schedule_id)}`;
                });
            });
        }
    }

    // Ha nincs találat, üzenet megjelenítése
    if (!foundRoutes) {
        container.innerHTML = `<div class="no-routes">Nincs elérhető útvonal.</div>`;
    }
}


// kezdeti irány (oda)
isForward = true;
updateSchedule();

// menetrendek lekérése betöltéskor
fetchBusSchedules();
/*--------------------------------------------------------------------------------------------------------DISPLAY ROUTES END--------------------------------------------------------------------------------------------*/

    </script>
</body>
</html>