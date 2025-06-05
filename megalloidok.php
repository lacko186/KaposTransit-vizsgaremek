
<?php
session_start();
require_once 'config.php';
// config

error_log("Session tartalma: " . print_r($_SESSION, true));

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
if (!isset($_SESSION['user_id'])) {
    error_log("Nincs bejelentkezve, átirányítás a login.php-ra");
    header("Location: login.php");
    exit();
}
?>
<!--------------------------------------------------------------------------------Ha nincs bejelentkezve átirányítás a login.php-ra(ellenőrzés)------------------------------------------------------------------------------>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KaposTransit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <script src="betolt.js"></script>


    <style>
/*-----------------------------------------------------------------------------------------------Egész oldalra vonatkozó CSS beállítások----------------------------------------------------------------------------------------------------*/

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
/*--------------------------------------------------------------------------------------------------------HEADER VÉGE-----------------------------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------------------CSS - MINDEN MÁS RÉSZ STÍLUSBEÁLLÍTÁSA----------------------------------------------------------------------------------------------*/
        .time-container {
            display: grid;
            padding: 2rem;
            max-width: 1000px;
            margin: 0 auto;
        }

        .time-card {
            background: #fcfcfc;
            width: 950px;
            border-radius: 20px;
            box-shadow: var(--shadow);
            padding: 1.5rem;
            transition: var(--transition);
            animation: fadeIn 0.5s ease-out;
            margin-bottom: 20px;
            font-size: 1.5rem;
            color: #636363;
        }

        .time-card:hover{
            color: 000;
            background: #E9E8E8;
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .timeCon{
            background: #fbfbfb;
            width: 90%;
            margin-bottom: 5px;
            padding: 20px;
            margin: 0 auto;
            border-radius: 10px;
        }

        .time-number {
            background: #b30000;
            display: inline-block;
            width: 3%;
            height: 60%;
            font-size: 2.5rem;
            font-weight: bold;
            border-radius: 5px;
            padding-left: 20px;
            padding-right: 15px;
            color: var(--text-light);
            margin-left: 17%;
        }

        .time-name{
            display: inline-block;
            color: #636363;
            font-size: 1.5rem;
            font-weight: bold;
            margin-left: 13%;
            background: #e8e8e8;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .switchBtn{
            display: inline;
            float: right;
            background: #fbfbfb;
            margin-right: 16%;
        }

        .switchBtn:hover{
            background: #E9E8E8;
        }

        .time{
            display: inline-block;
            float: right;
            font-size: 1.5rem;
            font-weight: bold;
            margin-right: 16%;
            margin-top: 1%;
        }

        .time-details {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }       
/*---------------------------------------------------------------------------------------------MINDEN MÁS RÉSZ STÍLUSBEÁLLÍTÁS VÉGE------------------------------------------------------------------------------------------------*/

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
/*--------------------------------------------------------------------------------------------------------FOOTER VÉGE-----------------------------------------------------------------------------------------------------*/


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

            .time-container {
                grid-template-columns: 1fr;
                padding: 1rem;
            }

            .time-card{
                width: 335px;
            }

            .time-number{
                margin-left: 0;
                padding-right: 60px;
            }

            .time{
                margin-right: 0%;
                margin-top: 4%;
            }

            .time-name{
                display: inline-block;
                margin-left: 0;
                font-size: 1.25rem;
                max-width: 90%;
            }

            .timeCon{
                width: 371px;
            }

            .switchBtn{
                margin-right: 0;
                margin-top: 5%;
                display: inline-block;
            }

            .header h1{
                margin-left: 2%;
            }

            #datePicker{
                margin-left: 3%;
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

            .time-container {
                grid-template-columns: 1fr;
                padding: 1rem;
            }

            .time-card{
                width: 295px;
            }

            .time-number{
                margin-left: 0;
                padding-right: 45px;
                padding-left: 10px;
            }

            .time{
                margin-right: 0%;
                margin-top: 4%;
            }

            .time-name{
                display: inline-block;
                margin-left: 0;
                font-size: 1.25rem;
                max-width: 73%;
            }

            .timeCon{
                width: 335px;
            }

            .switchBtn{
                margin-right: 0;
                margin-top: 5%;
                display: inline-block;
            }

            .header h1{
                margin-left: 2%;
            }

            #datePicker{
                margin-left: 4%;
                font-size: 1.15rem;
            }

            .backBtn{
                width: 15%;
            }
        }
/*--------------------------------------------------------------------------------------------------------@MEDIA VÉGE-----------------------------------------------------------------------------------------------------*/
        
    </style>
</head>
<body>
<!-- -----------------------------------------------------------------------------------------------------HTML - HEADER------------------------------------------------------------------------------------------------ -->

    <div class="header">
            <button class="backBtn" id=bckBtn><i class="fa-solid fa-chevron-left"></i></button>
            <h1><i class="fas fa-bus"></i> Kaposvár Helyi Járatok</h1> 
            
        </div>
<!-- -----------------------------------------------------------------------------------------------------HEADER VÉGE------------------------------------------------------------------------------------------------ -->

<!--JáratIdőInformáció Container--> 
        <div id="timeNumCon" class="timeCon"></div>


<!--Kártyák Container-->        
        <div id="timeContainer" class="time-container"></div>


<!-- -----------------------------------------------------------------------------------------------------HTML - FOOTER------------------------------------------------------------------------------------------------ -->
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
<!-- -----------------------------------------------------------------------------------------------------FOOTER VÉGE--------------------------------------------------------------------------------------------------- -->

    <script>
/*---------------------------------------------------------------------------------------------------------JAVASCRIPT - BACK BUTTON--------------------------------------------------------------------------------------*/
        document.getElementById('bckBtn').addEventListener('click', function() {
            window.location.href = 'jaratok.php'; // Átirányítás a jaratok.php oldalra
        });
/*---------------------------------------------------------------------------------------------------------BACK BUTTON VÉGE-----------------------------------------------------------------------------------------------*/
       
        // URL paramétereinek lekérése
        const getQueryParams = () => ({
            number: new URLSearchParams(window.location.search).get("number"),
            name: new URLSearchParams(window.location.search).get("name"),
            stop_time: new URLSearchParams(window.location.search).get("stop_time"),
            schedule_id: new URLSearchParams(window.location.search).get("schedule_id")
        });

        // útvonal lekérése végpontról
        async function fetchBusData() {
            const { number, name, stop_time, schedule_id } = getQueryParams();
            if (!number) {
                console.error("Number is missing");
                return;
            }

            try {
                const response = await fetch("http://localhost:3000/api/buszjaratok");// api végpont 
                const data = await response.json();// válasz

                // adott útvonal megállóinak kiszűrése
                const filteredStops = data.filter(stop => stop.number == number && stop.schedule_id == schedule_id);
                if (filteredStops.length === 0) {
                    console.error("No stops found for this route.");
                    return;
                }

                // megállónevek és időpontok kinyerése
                const stops = filteredStops.map(stop => stop.stop_name);
                const stopsTime = filteredStops.map(stop => stop.stop_time);

                // adatszerkezet
                const busData = {
                    number: number,
                    name: name,
                    stop: stop_time,
                    schedule: schedule_id,
                    stops: stops,
                    stopsTime: stopsTime
                };

                // adatok megjelenítése
                displayBusData(busData);
            } catch (error) {
                console.error("Error fetching bus data:", error);
            }
        }

         // függvény a kártyák adatainak megjelenítésére
        function displayBusData(busData) {
            const timeContainer = document.getElementById('timeContainer');

            function formatTime(time) {
                const [hour, minute] = time.split(":");
                return `${hour}:${minute}`;
            }

            document.getElementById('timeNumCon').innerHTML = `
                <div class="time-number">${busData.number}</div>
                <div class="time">${formatTime(busData.stop)}</div>
                <div class="time-name">${busData.name}</div>
            `;

            timeContainer.innerHTML = ""; // meglévő tartalom törlése
            busData.stops.forEach((stop, index) => {
                const timeCard = document.createElement('div');
                timeCard.className = 'time-card';
                timeCard.innerHTML = `
                    <div class="time-stop" style="font-weight: bold;">${stop}</div>
                    <div class="time-time">${formatTime(busData.stopsTime[index])}</div>
                `;
                timeContainer.appendChild(timeCard);
            });
        }

        fetchBusData();
  
</script>
</body>
</html>
