<?php
session_start();
require_once 'config.php';
error_log("Session tartalma: " . print_r($_SESSION, true));

// ellenőrizzük, hogy a felhasználó be van jelentkezve
if (!isset($_SESSION['user_id'])) {
    error_log("Nincs bejelentkezve, átirányítás a login.php-ra");
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KaposTransit</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css" rel="stylesheet">
    <script src="betolt.js"></script>
    <link href="header.css" rel="stylesheet">
    
    <!-- Google Maps API -->
    <script src="Google Maps API" async defer></script>
    <script src="hetvegefigyelo.js"></script>

<script>
  document.getElementById('menuBtn').addEventListener('click', function() {
    this.classList.toggle('active');
    document.getElementById('dropdownMenu').classList.toggle('active');
});

// kivülre kattintás esetén bezárjuk a menüt 
document.addEventListener('click', function(event) {
    const menu = document.getElementById('dropdownMenu');
    const menuBtn = document.getElementById('menuBtn');
    
    if (!menu.contains(event.target) && !menuBtn.contains(event.target)) {
        menu.classList.remove('active');
        menuBtn.classList.remove('active');
    }
});

// aktív oldal jelölése
document.addEventListener('DOMContentLoaded', function() {
    const currentPage = window.location.pathname.split('/').pop();
    const menuItems = document.querySelectorAll('.menu-items a');
    
    menuItems.forEach(item => {
        if (item.getAttribute('href') === currentPage) {
            item.classList.add('active');
        }
    });
});
//Nav end
</script>

    <style>
        :root {
            --primary-color:linear-gradient(to right, #211717,#b30000);
            --accent-color: #7A7474;
            --text-light: #fbfbfb;
            --background-light: #f8f9fa;
            --transition: all 0.3s ease;
        }

        body{
            background: linear-gradient(to left, #a6a6a6, #e8e8e8);
        }

/*--------------------------------------------------------------------------------------------------------CSS - HEADER---------------------------------------------------------------------------------------------------*/
    .header {
        position: relative;
        background: var(--primary-color);
        color: var(--text-light);
        padding: 1rem;
    }

    .header h1 {
        text-align: center;
        font-size: 2rem;
        padding: 1rem 0;
        margin-left: 38%;
        display: inline-block;
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
/*--------------------------------------------------------------------------------------------------------HEADER END-----------------------------------------------------------------------------------------------------*/

        /* Custom map and UI enhancements */
        #map {
            height: 650px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        .transit-mode-btn {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .transit-mode-btn.active {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        #next{
            font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
            font-weight: bolder;
            font-style: italic;
            color: blue;
        }
        button {
            /* Variables */
            --button_radius: 0.75em;
            --button_color: #e8e8e8;
            --button_outline_color: #000000;
            font-size: 17px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            border-radius: var(--button_radius);
            background: var(--button_outline_color);
        }

        .button_top {
            display: block;
            box-sizing: border-box;
            border: 2px solid var(--button_outline_color);
            border-radius: var(--button_radius);
            padding: 0.75em 1.5em;
            background: var(--button_color);
            color: var(--button_outline_color);
            transform: translateY(-0.2em);
            transition: transform 0.1s ease;
        }

        button:hover .button_top {
            transform: translateY(-0.33em);
        }

        button:active .button_top {
            transform: translateY(0);
         }

   
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
                <h1>Kaposvár Közlekedési Zrt.</h1>
        </div>
<!-- -----------------------------------------------------------------------------------------------------HEADER END-------------------------------------------------------------------------------------------------- -->

    <div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-2xl rounded-3xl p-8">
    <h1 class="text-4xl font-bold text-center text-red-700 mb-8">
        <i class="fas fa-map-marked-alt mr-3"></i>Kaposvár Mobil Útitárs
    </h1>

    <!-- indulás és végpont mező-->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div>
            <label class="block text-gray-700 mb-2">Indulási pont</label>
            <div class="relative">
                <i class="fas fa-map-pin absolute left-4 top-4 text-blue-500"></i>
                <input
                    id="start"
                    type="text"
                    placeholder="pl. Vasútállomás"
                    class="w-full pl-12 pr-4 py-3 border-2 rounded-lg focus:ring-2 focus:ring-blue-500"
                >
            </div>
        </div>
        <div>
            <label class="block text-gray-700 mb-2">Érkezési pont</label>
            <div class="relative">
                <i class="fas fa-flag-checkered absolute left-4 top-4 text-green-500"></i>
                <input
                    id="end"
                    type="text"
                    placeholder="pl. Kossuth tér"
                    class="w-full pl-12 pr-4 py-3 border-2 rounded-lg focus:ring-2 focus:ring-blue-500"
                >
            </div>
        </div>
        <div>
            <label class="block text-gray-700 mb-2">Utazás ideje</label>
            <div class="relative">
                <i class="fas fa-clock absolute left-4 top-4 text-purple-500"></i>
                <input
                    id="travel-time"
                    type="datetime-local"
                    class="w-full pl-12 pr-4 py-3 border-2 rounded-lg focus:ring-2 focus:ring-blue-500"
                >
            </div>
        </div>
    </div>

    <!-- közlekedési módok-->
    <div class="flex justify-between space-x-4 mb-6">
        <button class="transit-mode-btn flex-1 p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition" data-mode="bus">
            <i class="fas fa-bus text-3xl text-blue-600"></i>
            <span class="block mt-2 font-semibold">Helyi Busz</span>
        </button>
        <button class="transit-mode-btn flex-1 p-3 bg-green-50 rounded-lg hover:bg-green-100 transition" data-mode="train">
            <i class="fas fa-train text-3xl text-green-600"></i>
            <span class="block mt-2 font-semibold">Vonat</span>
        </button>
        <button class="transit-mode-btn flex-1 p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition" data-mode="complex">
            <i class="fas fa-network-wired text-3xl text-purple-600"></i>
            <span class="block mt-2 font-semibold">Helyi Járat</span>
        </button>
    </div>

    <!-- helyi járatos közlekedés-->
    <div id="complex-route-select" class="hidden mb-6">
        <label class="block text-gray-700 mb-2">Válasszon induló járatot</label>
        <select id="complex-route" class="w-full p-3 border-2 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">Válasszon</option>
            <option value="12">12 - Helyi autóbusz-állomás - Sopron u. - Laktanya</option>
            <option value="12 vissza">12 vissza - Laktanya - Sopron u. - Helyi autóbusz-állomás</option>
            <option value="13">13 - Helyi autóbusz-állomás - Kecelhegy - Helyi autóbusz-állomás</option>
            <option value="20">20 - Raktár u. - Laktanya - Videoton</option>
            <option value="20 vissza">20 vissza - Videoton - Laktanya - Raktár u.</option>
            <option value="21">21 - Raktár u. - Videoton</option>
            <option value="21 vissza">21 vissza - Videoton - Raktár u.</option>
            <option value="23">23 - Kaposfüred forduló - Füredi csp. - Kaposvári Egyetem</option>
            <option value="23 vissza">23 vissza - Kaposvári Egyetem - Füredi csp. - Kaposfüred forduló</option>
            <option value="26">26 - Kaposfüred forduló - Losonc köz - Videoton - METYX</option>
            <option value="26 vissza">26 vissza - METYX - Videoton - Losonc köz - Kaposfüred forduló</option>
            <option value="27">27 - Laktanya - Füredi u. csp. - KOMÉTA</option>
            <option value="27 vissza">27 vissza - KOMÉTA - Füredi u. csp. - Laktanya</option>
            <option value="31">31 - Helyi autóbusz-állomás - Egyenesi u. forduló</option>
            <option value="31 vissza">31 vissza - Egyenesi u. forduló - Helyi autóbusz-állomás</option>
            <option value="32">32 - Helyi autóbuszállomás - Kecelhegy - Helyi autóbusz-állomás</option>
            <option value="33">33 - Helyi aut. áll. - Egyenesi u. - Kecelhegy - Helyi aut. áll.</option>
            <option value="40">40 - Koppány vezér u - 67-es út - Raktár u.</option>
            <option value="40 vissza">40 vissza - Raktár u. - 67-es út - Koppány vezér u</option>
            <option value="41">41 - Koppány vezér u - Bartók B. u. - Raktár u.</option>
            <option value="41 vissza">41 vissza - Raktár u. - Bartók B. u. - Koppány vezér u</option>
            <option value="42">42 - Töröcske forduló - Kórház - Laktanya</option>
            <option value="42 vissza">42 vissza - Laktanya - Kórház - Töröcske forduló</option>
            <option value="43">43 - Helyi autóbusz-állomás - Kórház- Laktanya - Raktár utca - Helyi autóbusz-állomás</option>
            <option value="44">44 - Helyi autóbusz-állomás - Raktár utca - Laktanya -Arany János tér - Helyi autóbusz-állomás</option>
            <option value="45">45 - Helyi autóbusz-állomás - 67-es út - Koppány vezér u.</option>
            <option value="45 vissza">45 vissza - Koppány vezér u. - 67-es út - Helyi autóbusz-állomás</option>
            <option value="46">46 - Helyi autóbusz-állomás - Töröcske forduló</option>
            <option value="46 vissza">46 vissza - Töröcske forduló - Helyi autóbusz-állomás</option>
            <option value="47">47 - Koppány vezér u.- Kórház - Kaposfüred forduló</option>
            <option value="47 vissza">47 vissza - Kaposfüred forduló - Kórház - Koppány vezér u.</option>
            <option value="61">61 - Helyi- autóbuszállomás - Béla király u.</option>
            <option value="61 vissza">61 vissza - Béla király u. - Helyi autóbusz-állomás</option>
            <option value="62">62 - Helyi autóbusz-állomás - Városi fürdő - Béla király u.</option>
            <option value="62 vissza">62 vissza - Béla király u. - Városi fürdő - Helyi autóbusz-állomás</option>
            <option value="70">70 - Helyi autóbusz-állomás - Kaposfüred</option>
            <option value="70 vissza">70 vissza - Kaposfüred - Helyi autóbusz-állomás</option>
            <option value="71">71 - Kaposfüred forduló - Kaposszentjakab forduló</option>
            <option value="71 vissza">71 vissza - Kaposszentjakab forduló - Kaposfüred forduló</option>
            <option value="72">72 - Kaposfüred forduló - Hold u. - Kaposszentjakab forduló</option>
            <option value="72 vissza">72 vissza - Kaposszentjakab forduló - Hold u. - Kaposfüred forduló</option>
            <option value="73">73 - Kaposfüred forduló - KOMÉTA - Kaposszentjakab forduló</option>
            <option value="73 vissza">73 vissza - Kaposszentjakab forduló - KOMÉTA - Kaposfüred forduló</option>
            <option value="74">74 - Hold utca - Helyi autóbusz-állomás</option>
            <option value="75">75 - Helyi autóbusz-állomás - Kaposszentjakab</option>
            <option value="75 vissza">75 vissza - Kaposszentjakab - Helyi autóbusz-állomás</option>
            <option value="81">81 - Helyi autóbusz-állomás - Hősök temploma - Toponár forduló</option>
            <option value="81 vissza">81 vissza - Toponár forduló - Hősök temploma - Helyi autóbusz-állomás</option>
            <option value="82">82 - Helyi autóbusz-állomás - Kórház - Toponár Szabó P. u.</option>
            <option value="82 vissza">82 vissza - Toponár Szabó P. u. - Kórház - Helyi autóbusz-állomás</option>
            <option value="83">83 - Helyi autóbusz-állomás - Szabó P. u. - Toponár forduló</option>
            <option value="83 vissza">83 vissza - Toponár forduló - Szabó P. u. - Helyi autóbusz-állomás</option>
            <option value="84">84 - Helyi autóbusz-állomás - Toponár, forduló - Répáspuszta</option>
            <option value="84 vissza">84 vissza - Répáspuszta - Toponár, forduló - Helyi autóbusz-állomás</option>
            <option value="85">85 - Helyi autóbusz-állomás - Kisgát- Helyi autóbusz-állomás</option>
            <option value="86">86 - Helyi autóbusz-állomás - METYX - Szennyvíztelep</option>
            <option value="86 vissza">86 vissza - Szennyvíztelep - METYX - Helyi autóbusz-állomás</option>
            <option value="87">87 - Helyi autóbusz állomás - Videoton - METYX</option>
            <option value="87 vissza">87 vissza - METYX - Videoton - Helyi autóbusz állomás</option>
            <option value="88">88 - Helyi autóbusz-állomás - Videoton</option>
            <option value="88 vissza">88 vissza - Videoton - Helyi autóbusz-állomás</option>
            <option value="89">89 - Helyi autóbusz-állomás - Kaposvári Egyetem</option>
            <option value="89 vissza">89 vissza - Kaposvári Egyetem - Helyi autóbusz-állomás</option>
            <option value="90">90 - Helyi autóbusz-állomás - Rómahegy</option>
            <option value="90 vissza">90 vissza - Rómahegy - Helyi autóbusz-állomás</option>
            <option value="91">91 - Füredi u. csp - Pázmány P. u. - Rómahegy</option>
            <option value="91 vissza">91 vissza - Rómahegy - Pázmány P u. - Füredi u. csp</option>
        </select>
    </div>

    <!-- útvonal keresése gomb -->
    <button id="find-route" class="w-full bg-red-700 text-white py-4 rounded-lg hover:bg-black transition mb-6 flex items-center justify-center">
        <i class="fas fa-route mr-3"></i>Útvonal keresése
    </button>

    <!-- térkép és részletek -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
            <div id="map" class="w-full rounded-2xl"></div>
        </div>

        <!-- útvonal részletei info panel -->
        <div id="route-details" class="bg-gray-50 p-6 rounded-2xl">
            <h3 class="text-2xl font-semibold mb-4 text-gray-800 flex items-center">
                <i class="fas fa-info-circle mr-3 text-red-700"></i>Útvonal Részletek
            </h3>
            <div id="route-info" class="space-y-4">
              
        <!--megálló kereső gomb átirányít a megallok_kereso.php-ra amin név szerint lehet megállót keresni, mindezt Api segtségével-->
            </div>
        </div>          
        <a href="megallok_kereso.php"><button>
        <span style="font-weight:bold" class="button_top">Megálló keresése ➤</span>
        </button>
        </a>
    </div>
</div>

    <script>

/*--------------------------------------------------------------------------------------------------------JS - DROPDOWNMENU----------------------------------------------------------------------------------------------*/
    

/*--------------------------------------------------------------------------------------------------------DROPDOWNMENU END-----------------------------------------------------------------------------------------------*/
// változók
let map;
let directionsService;
let directionsRenderer;
let markers = [];
let currentPolyline = null;
let currentMarkers = [];
let routesData = {};
let activeInfoWindow = null;

// Kaposvár központ
const KAPOSVAR_CENTER = {
    lat: 46.3593,
    lng: 17.7967
};

// legördülő menü 
function setupDropdownMenu() {
    const menuBtn = document.getElementById('menuBtn');
    const dropdownMenu = document.getElementById('dropdownMenu');

    if (menuBtn && dropdownMenu) {
        menuBtn.addEventListener('click', function() {
            this.classList.toggle('active');
            dropdownMenu.classList.toggle('active');
        });

        // menü bezárás 
        document.addEventListener('click', function(event) {
            if (!dropdownMenu.contains(event.target) && !menuBtn.contains(event.target)) {
                dropdownMenu.classList.remove('active');
                menuBtn.classList.remove('active');
            }
        });

        // Aktív oldal jelölése
        const currentPage = window.location.pathname.split('/').pop();
        const menuItems = document.querySelectorAll('.menu-items a');
        
        menuItems.forEach(item => {
            if (item.getAttribute('href') === currentPage) {
                item.classList.add('active');
            }
        });
    }
}


// map beálltása
function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: KAPOSVAR_CENTER,
        zoom: 13,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        mapTypeControl: true,
        mapTypeControlOptions: {
            style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
            position: google.maps.ControlPosition.TOP_RIGHT
        },
        zoomControl: true,
        zoomControlOptions: {
            position: google.maps.ControlPosition.RIGHT_CENTER
        },
        scaleControl: true,
        streetViewControl: true,
        streetViewControlOptions: {
            position: google.maps.ControlPosition.RIGHT_BOTTOM
        },
        fullscreenControl: true,
        styles: [
            {
                featureType: "transit.station",
                elementType: "labels.icon",
                stylers: [{ visibility: "on" }]
            }
        ]
    });

    // útvonaltervezés beállításai
    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer({
        map: map,
        suppressMarkers: true,
        preserveViewport: true
    });

    //utvonlbetölt, automatikus kitölt,eseményfigyelő  
    setupAutocomplete();
    setupEventListeners();
    loadRouteData();
}

// automatikus kitöltés
function setupAutocomplete() {
    const options = {
        componentRestrictions: { country: 'hu' },
        types: ['geocode'],
        bounds: new google.maps.LatLngBounds(
            new google.maps.LatLng(46.2093, 17.6467),
            new google.maps.LatLng(46.5093, 17.9467)
        ),
        strictBounds: true,
        fields: ['address_components', 'geometry', 'name', 'formatted_address']
    };

    const startInput = document.getElementById('start');
    const endInput = document.getElementById('end');
    
    if (startInput && endInput) {
        const startAutocomplete = new google.maps.places.Autocomplete(startInput, options);
        const endAutocomplete = new google.maps.places.Autocomplete(endInput, options);

        startAutocomplete.addListener('place_changed', () => handlePlaceChanged(startAutocomplete, 'start'));
        endAutocomplete.addListener('place_changed', () => handlePlaceChanged(endAutocomplete, 'end'));
    }
}

// további szűrés helykiválasztás
function handlePlaceChanged(autocomplete, type) {
    const place = autocomplete.getPlace();
    if (!place.geometry) {
        showAlert('Kérem válasszon egy létező helyszínt a listából');
        return;
    }

    const input = document.getElementById(type);
    const formattedAddress = place.formatted_address || place.name;
    input.value = formattedAddress.replace(/,?\s*Magyarország/g, '').trim();

    if (type === 'start') {
        map.setCenter(place.geometry.location);
        map.setZoom(15);
    }
}

// esemény figyelő
function setupEventListeners() {
    const findRouteButton = document.getElementById('find-route');
    if (findRouteButton) {
        findRouteButton.addEventListener('click', calculateRoute);
    }

    document.querySelectorAll('.transit-mode-btn').forEach(button => {
        button.addEventListener('click', function() {
            // útvonal törlés
            clearRoute();

            // kiválasztott mode visszavonása
            document.querySelectorAll('.transit-mode-btn').forEach(btn => 
                btn.classList.remove('active')
            );
            
            // kiválasztott mode változtatás (aktiv)
            this.classList.add('active');
            
            // helyi járat útvonal megjelenítés
            const complexSelect = document.getElementById('complex-route-select');
            if (this.dataset.mode === 'complex') {
                complexSelect.classList.remove('hidden');
            } else {
                complexSelect.classList.add('hidden');
            }
            // mode alapján frissítés 
            updateRouteOptions(this.dataset.mode);
        });
    });

    // helyi járatos útvonal kiválasztás
    const complexRouteSelect = document.getElementById('complex-route');
    if (complexRouteSelect) {
        complexRouteSelect.addEventListener('change', (event) => {
            const selectedRoute = event.target.value;
            if (selectedRoute) {
                displayLocalRoute(selectedRoute);
            }
        });
    }
}

function updateRouteOptions(mode) {
    const complexRouteSelect = document.getElementById('complex-route');
    if (!complexRouteSelect) return;

    // alaphelyzet "Válasszon útvonalat"
    complexRouteSelect.innerHTML = '<option value="">Válasszon útvonalat</option>';

    // útvonalszűrés mode alapján
    Object.keys(routesData)
        .filter(route => {
            switch(mode) {
                case 'bus': return route.includes('stopBus');
                case 'train': return route.includes('stopTrain');
                case 'complex': return true;
                default: return false;
            }
        })
        .forEach(route => {
            const option = document.createElement('option');
            option.value = route.replace('stop', '').replace('Back', ' vissza');
            option.textContent = option.value;
            complexRouteSelect.appendChild(option);
        });
}

// útvonalszámítása
function calculateRoute() {
    // meglévő útvonal törlése
    clearRoute();
    
    // beviteli adatok
    const start = document.getElementById('start').value.trim();
    const end = document.getElementById('end').value.trim();
    const travelTime = document.getElementById('travel-time').value;
    //alapértelmezett vezető mode
    const activeMode = document.querySelector('.transit-mode-btn.active')?.dataset.mode || 'driving';
    const complexRoute = document.getElementById('complex-route').value;

    // beviteli adatok ellenőrzés
    if (!start || !end) {
        showAlert('Kérem adja meg az indulási és érkezési pontot!');
        return;
    }

    // helyi járat kezelése
    if (activeMode === 'complex' && complexRoute) {
        displayLocalRoute(complexRoute);
        return;
    }

    // kérés felépítés
    const request = {
        origin: start,
        destination: end,
        travelMode: google.maps.TravelMode.DRIVING,
        region: 'hu',
        language: 'hu',
        optimizeWaypoints: true,
        provideRouteAlternatives: true
    };

    // mode kezelés Transit TravelMode
    if (activeMode) {
        const departureTime = travelTime ? new Date(travelTime) : new Date();
        
        switch (activeMode) {
            case 'train':
                request.travelMode = google.maps.TravelMode.TRANSIT;
                request.transitOptions = {
                    modes: ['TRAIN', 'RAIL'],
                    routingPreference: 'FEWER_TRANSFERS',
                    departureTime: departureTime
                };
                break;
            case 'bus':
                request.travelMode = google.maps.TravelMode.TRANSIT;
                request.transitOptions = {
                    modes: ['BUS'],
                    routingPreference: 'FEWER_TRANSFERS',
                    departureTime: departureTime
                };
                break;
        }
    }

    // útvonal számítás
    directionsService.route(request, (result, status) => {
        if (status === google.maps.DirectionsStatus.OK) {
            directionsRenderer.setDirections(result);
            displayRouteDetails(result);
            addRouteMarkers(result);
        } else {
            handleDirectionsError(status);
        }
    });
}

// útvonal megjelenítése több info
function displayRouteDetails(result) {
    const route = result.routes[0];
    const routeInfo = document.getElementById('route-info');
    
    if (!route || !route.legs || !route.legs[0]) {
        routeInfo.innerHTML = '<p>Nem található útvonal információ</p>';
        return;
    }

    const leg = route.legs[0];
    const departureTime = leg.departure_time ? leg.departure_time.text : '';
    const arrivalTime = leg.arrival_time ? leg.arrival_time.text : '';

    let html = `
        <div class="route-panel bg-white rounded-lg shadow-lg p-4">
            <div class="route-header border-b pb-4 mb-4">
                <div class="text-xl font-bold mb-2">${departureTime} - ${arrivalTime}</div>
                <div class="text-gray-600">
                    <span class="font-semibold">${leg.distance.text}</span> • 
                    <span class="font-semibold">${leg.duration.text}</span>
                </div>
            </div>
            
            <div class="route-steps space-y-4">
                <div class="step-item flex items-start">
                    <div class="step-icon mr-3">
                        <i class="fas fa-map-marker-alt text-red-600 text-xl"></i>
                    </div>
                    <div class="step-content">
                        <div class="font-semibold">${leg.start_address}</div>
                        <div class="text-sm text-gray-600">${departureTime}</div>
                    </div>
                </div>`;

    //kimenet
    leg.steps.forEach((step, index) => {
        if (step.travel_mode === 'TRANSIT') {
            const line = step.transit.line;
            const vehicle = line.vehicle.type.toLowerCase();
            const iconClass = vehicle === 'train' ? 'fa-train' : 'fa-bus';
            
            html += `
                <div class="step-item flex items-start">
                    <div class="step-icon mr-3">
                        <i class="fas ${iconClass} text-blue-600 text-xl"></i>
                    </div>
                    <div class="step-content">
                        <div class="font-semibold">${line.short_name || line.name}</div>
                        <div class="text-sm">
                            ${step.transit.departure_stop.name} → ${step.transit.arrival_stop.name}
                        </div>
                        <div class="text-sm text-gray-600">
                            ${step.transit.departure_time.text} - ${step.transit.arrival_time.text}
                            (${step.duration.text}, ${step.transit.num_stops} megálló)
                        </div>
                    </div>
                </div>`;
        } else if (step.travel_mode === 'WALKING') {
            html += `
                <div class="step-item flex items-start">
                    <div class="step-icon mr-3">
                        <i class="fas fa-walking text-green-600 text-xl"></i>
                    </div>
                    <div class="step-content">
                        <div class="text-sm">
                            Gyaloglás ${step.duration.text} (${step.distance.text})
                        </div>
                    </div>
                </div>`;
        }
    });

    // célállomás
    html += `
        <div class="step-item flex items-start">
            <div class="step-icon mr-3">
                <i class="fas fa-flag-checkered text-green-600 text-xl"></i>
            </div>
            <div class="step-content">
                <div class="font-semibold">${leg.end_address}</div>
                <div class="text-sm text-gray-600">${arrivalTime}</div>
            </div>
        </div>
    </div>
    <div class="route-footer mt-4 pt-4 border-t text-sm text-gray-600">
        <div class="mb-2">
            <i class="fas fa-info-circle mr-2"></i>
            Szolgáltató: Kaposvári Közlekedési Zrt.
        </div>
        <div class="text-xs">
            Díjakkal, menetrenddel és forgalmi változásokkal kapcsolatos információk:<br>
            Tel: +36 1 349 4949
        </div>
    </div>
</div>`;

    routeInfo.innerHTML = html;
}

// útvonal jelölők Marker hozzáadás
function addRouteMarkers(result) {
    const route = result.routes[0];
    if (!route || !route.legs || !route.legs[0]) return;

    const leg = route.legs[0];
    
    // marker kezdeti helyzet
    new google.maps.Marker({
        position: leg.start_location,
        map: map,
        icon: {
            url: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
        },
        title: 'Indulás'
    });

    // marker végpont helyzete
    new google.maps.Marker({
        position: leg.end_location,
        map: map,
        icon: {
            url: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'
        },
        title: 'Érkezés'
    });

    // megállók helyzete
    leg.steps.forEach(step => {
        if (step.travel_mode === 'TRANSIT') {
            new google.maps.Marker({
                position: step.transit.departure_stop.location,
                map: map,
                icon: {
                    url: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                }
            });
        }
    });
}

//útvonalinformáció a loaclhostról "helyibusz" helyi járatos közlekedéshez
async function loadRouteData() {
    try {
        //válasz
        const response = await fetch('http://localhost:3000/api/helyibusz');
        
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        
        const data = await response.json();
        
        // adatok felfolgozása
        routesData = data.reduce((acc, stop) => {
            
            if (!acc[stop.option]) {
                acc[stop.option] = [];
            }
            
            // hozzáadás a megfelelő útvonalhoz
            acc[stop.option].push({
                name: stop.name,
                lat: parseFloat(stop.lat),
                lng: parseFloat(stop.lng)
            });
            
            return acc;
        }, {});

        // útvonalak Rendezés 
        Object.keys(routesData).forEach(route => {
            routesData[route].sort((a, b) => {

                return a.lat - b.lat;
            });
        });

        console.log('Routes loaded:', Object.keys(routesData));
        
     
        updateRouteOptions(document.querySelector('.transit-mode-btn.active')?.dataset.mode || 'complex');
    } catch (error) {
        console.error('Hiba az útvonal adatok betöltésekor:', error);
        
        // helyi járatos adatok annak esetére ha nem töltődne be az Api
        const mockRouteData = {
            'stop12': [
                { name: 'Helyi autóbusz-állomás', lat: 46.3615, lng: 17.7968 },
                { name: 'Sopron u.', lat: 46.3635, lng: 17.7988 },
                { name: 'Laktanya', lat: 46.3655, lng: 17.8008 }
            ],
            'stop12Back': [
                { name: 'Laktanya', lat: 46.3655, lng: 17.8008 },
                { name: 'Sopron u.', lat: 46.3635, lng: 17.7988 },
                { name: 'Helyi autóbusz-állomás', lat: 46.3615, lng: 17.7968 }
            ]
        };
        
        routesData = mockRouteData;
        console.warn('Visszatérés előre megadott útvonalakhoz.');
        showAlert('Nem sikerült betölteni a helyi járatok adatait. Alapértelmezett adatok használata.');
    }
}

// útvonal megjelenítés
function displayLocalRoute(routeId) {
    //meglévő útvonal törlése
    clearRoute();
    
    let formattedRouteId = routeId.includes('vissza') 
        ? 'stop' + routeId.replace(' vissza', 'Back')
        : 'stop' + routeId;

    const routeStops = routesData[formattedRouteId];
    
    if (!routeStops || routeStops.length === 0) {
        showAlert('Nem található útvonal információ.');
        return;
    }

    // útvonal koordináták
    const routeCoordinates = routeStops.map(stop => ({
        lat: stop.lat,
        lng: stop.lng
    }));

    // útvonal megrajzolása
    currentPolyline = new google.maps.Polyline({
        path: routeCoordinates,
        geodesic: true,
        strokeColor: '#FF0000',
        strokeOpacity: 1.0,
        strokeWeight: 3,
        map: map
    });

    // megálló marker hozzáadása
    routeStops.forEach((stop, index) => {
        const marker = createStopMarker(stop, index, routeStops.length, routeId);
        currentMarkers.push(marker);
    });

    // térképhez illesztés
    const bounds = new google.maps.LatLngBounds();
    routeCoordinates.forEach(coord => bounds.extend(coord));
    map.fitBounds(bounds);

    // Útvonal info megjelenítés
    displayLocalRouteInfo(routeId, routeStops);
}

// megálló marker 
function createStopMarker(stop, index, totalStops, routeId) {
    const marker = new google.maps.Marker({
        position: { 
            lat: stop.lat,
            lng: stop.lng
        },
        map: map,
        title: stop.name,
        icon: {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 10,
            fillColor: '#1E88E5',
            fillOpacity: 1,
            strokeWeight: 2,
            strokeColor: '#FFFFFF'
        },
        label: {
            text: (index + 1).toString(),
            color: '#FFFFFF',
            fontSize: '11px'
        }
    });

    const infoWindow = createInfoWindow(stop, index, totalStops, routeId);

    marker.addListener('click', () => {
        if (activeInfoWindow) {
            activeInfoWindow.close();
        }
        infoWindow.open(map, marker);
        activeInfoWindow = infoWindow;
    });

    marker.infoWindow = infoWindow;
    return marker;
}

// megálló adatai panel
function createInfoWindow(stop, index, totalStops, routeId) {
    const infoContent = `
        <div class="stop-info p-4 max-w-sm">
            <h3 class="text-lg font-bold text-blue-600 border-b-2 border-blue-200 pb-2 mb-3">
                ${stop.name}
            </h3>
            <div class="info-details space-y-2">
                <div class="coordinate-info text-sm">
                    <div><strong>Szélesség:</strong> ${stop.lat.toFixed(6)}</div>
                    <div><strong>Hosszúság:</strong> ${stop.lng.toFixed(6)}</div>
                </div>
                <div class="stop-position text-sm">
                    <strong>Megálló sorszáma:</strong> ${index + 1} / ${totalStops}
                </div>
                <div class="route-info text-sm">
                    <strong>Járat:</strong> ${routeId}
                </div>
            </div>
            <div class="text-xs text-gray-500 mt-3">
                Kattintson a térképre a bezáráshoz
            </div>
        </div>
    `;

    return new google.maps.InfoWindow({
        content: infoContent,
        maxWidth: 300
    });
}

// útvonal info megjelenítés
function displayLocalRouteInfo(routeId, stops) {
    const routeInfo = document.getElementById('route-info');
    
    let html = `
        <div class="local-route-panel bg-white rounded-lg shadow-lg p-4">
            <div class="route-header border-b pb-4 mb-4">
                <div class="text-xl font-bold mb-2">Járat: ${routeId}</div>
                <div class="text-gray-600">
                    <span class="font-semibold">${stops.length} megálló</span>
                </div>
            </div>
            
            <div class="stops-list space-y-3">`;
            
    stops.forEach((stop, index) => {
        html += `
            <div class="stop-item flex items-center">
                <div class="stop-number w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center mr-3">
                    ${index + 1}
                </div>
                <div class="stop-details">
                    <div class="font-semibold">${stop.name}</div>
                </div>
            </div>`;
    });

    html += `
            </div>
            <div class="route-footer mt-4 pt-4 border-t text-sm text-gray-600">
                <div class="mb-2">
                    <i class="fas fa-info-circle mr-2"></i>
                    Szolgáltató: Kaposvári Közlekedési Zrt.
                </div>
                <div class="text-xs">
                    Menetrendi információk:<br>
                    Tel: +36 1 349 4949
                </div>
            </div>
        </div>`;

    routeInfo.innerHTML = html;
}

// útvonal törlés
function clearRoute() {
    if (currentPolyline) {
        currentPolyline.setMap(null);
        currentPolyline = null;
    }

    // jelölő markerek törlése
    currentMarkers.forEach(marker => {
        if (marker.infoWindow) {
            marker.infoWindow.close();
        }
        marker.setMap(null);
    });
    currentMarkers = [];

    // jelenlegi info panel bezárása
    if (activeInfoWindow) {
        activeInfoWindow.close();
        activeInfoWindow = null;
    }

    // visszaállítás
    if (directionsRenderer) {
        directionsRenderer.setMap(null);
        directionsRenderer.setMap(map);
    }
}

// útvonaltervezés hiba kezelés
function handleDirectionsError(status) {
    let errorMessage = 'Ismeretlen hiba történt az útvonaltervezés során.';
    
    switch (status) {
        case google.maps.DirectionsStatus.ZERO_RESULTS:
            errorMessage = 'Nem található útvonal a megadott pontok között.';
            break;
        case google.maps.DirectionsStatus.NOT_FOUND:
            errorMessage = 'A megadott címek egyike vagy mindegyike nem található.';
            break;
        case google.maps.DirectionsStatus.OVER_QUERY_LIMIT:
            errorMessage = 'Az útvonaltervezési kérések száma túllépte a limitet. Kérjük próbálja később.';
            break;
        case google.maps.DirectionsStatus.REQUEST_DENIED:
            errorMessage = 'Az útvonaltervezési kérés elutasítva. Ellenőrizze az API kulcsot.';
            break;
        case google.maps.DirectionsStatus.INVALID_REQUEST:
            errorMessage = 'Érvénytelen útvonaltervezési kérés.';
            break;
    }

    showAlert(errorMessage);
}

// üzenet alert megjelenítés
function showAlert(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'fixed top-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-lg z-50';
    alertDiv.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}


document.addEventListener('DOMContentLoaded', () => {
    setupDropdownMenu();

    const script = document.createElement('script');
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
});

window.initMap = initMap;   



</script>

</body>
</html>
