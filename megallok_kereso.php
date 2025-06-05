<?php
session_start();
require_once 'config.php';
//config

error_log("Session tartalma: " . print_r($_SESSION, true));

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
    <link href ="header.css" rel="stylesheet">
    <link href ="footer.css" rel="stylesheet"> 
     
    <script async
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyArXtWdllsylygVw5t_k-22sXUJn-jMU8k&libraries=places&callback=initMap&loading=async">
    </script>

    <style>
      
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
        #route{
            font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
            font-weight: bolder;
            font-style: italic;
            color: blue;
        }
        button {
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
    <div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-2xl rounded-3xl p-8">
    <h1 class="text-4xl font-bold text-center text-red-700 mb-8">
        <i class="fas fa-map-marked-alt mr-3"></i>Megálló Keresés
    </h1>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label class="block text-gray-700 mb-2">Megálló keresése név alapján</label>
        <div class="relative">
            <i class="fas fa-bus-simple absolute left-4 top-4 text-blue-500"></i>
            <input
                id="stop-search"
                type="text"
                placeholder="pl. Kaposvár"
                class="w-full pl-12 pr-4 py-3 border-2 rounded-lg focus:ring-2 focus:ring-blue-500"
            >
        </div>
    </div>
    <div>
        <div class="relative">
          
        </div>
    </div>
</div>

<button id="search-button" class="w-full bg-red-600 text-white py-3 rounded-lg hover:bg-blue-700 transition mb-6">
    <i class="fas fa-search mr-2"></i>Keresés
</button>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
            <div id="map" class="w-full rounded-2xl"></div>
        </div>

        <div class="md:col-span-1">
    <div id="route-info" class="bg-white rounded-2xl p-4 shadow-lg">
    </div>
</div>

<a href="terkep.php"><button>
  <span style="font-weight:bold" class="button_top">Útvonaltervezés ➤</span>
</button>
</a>
    <script>
// alap térkép beállítás
class StopMapManager {
    constructor(mapElementId = 'map') {
        this.map = null;
        this.markers = [];
        this.infoWindows = [];
        this.mapElementId = mapElementId;
        this.geocodeCache = new Map();
        this.initializeMap();
    }

    initializeMap() {
        this.map = new google.maps.Map(document.getElementById(this.mapElementId), {
            center: { lat: 47.162494, lng: 19.503304 }, 
            zoom: 7,
            styles: [
                {
                    featureType: "transit.station",
                    elementType: "all",
                    stylers: [{ visibility: "on" }]
                }
            ]
        });
    }

    // hely nevének lekérés
    async getLocationName(position) {
        const cacheKey = `${position.lat},${position.lng}`;
        
        if (this.geocodeCache.has(cacheKey)) {
            return this.geocodeCache.get(cacheKey);
        }

        return new Promise((resolve, reject) => {
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ location: position }, (results, status) => {
                if (status === 'OK' && results[0]) {
                    const addressComponents = results[0].address_components;
                    const city = addressComponents.find(component => 
                        component.types.includes('locality') || 
                        component.types.includes('postal_town') ||
                        component.types.includes('administrative_area_level_2')
                    );
                    
                    const locationName = city ? city.long_name : results[0].formatted_address;
                    this.geocodeCache.set(cacheKey, locationName);
                    resolve(locationName);
                } else {
                    resolve('Unknown Location');
                }
            });
        });
    }

    // marker törlése
    clearMarkers() {
        this.markers.forEach(marker => marker.setMap(null));
        this.infoWindows.forEach(window => window.close());
        this.markers = [];
        this.infoWindows = [];
    }

    // uj jelölő marker
    async createMarker(stop) {
        if (!stop.latitude || !stop.longitude) return null;

        const position = {
            lat: parseFloat(stop.latitude),
            lng: parseFloat(stop.longitude)
        };

        if (isNaN(position.lat) || isNaN(position.lng)) return null;

        const locationName = await this.getLocationName(position);

        const agencyDisplay = stop.agency_ids === '12' ? locationName : (stop.agency_ids || 'Unknown Agency');

        const marker = new google.maps.Marker({
            position: position,
            map: this.map,
            title: stop.id,
            icon: {
                url: 'https://maps.gstatic.com/mapfiles/ms2/micons/red-dot.png',
                scaledSize: new google.maps.Size(30, 30)
            }
        });

        const infoWindow = new google.maps.InfoWindow({
            content: `
                <div class="p-4">
                    <h3 class="font-bold text-lg mb-2">${locationName}</h3>
                    <p class="text-sm mb-2">Station ID: ${stop.id}</p>
                    <p class="text-sm mb-2">Location: ${locationName}</p>
                    <p class="text-sm mb-2">Frequency: ${stop.frequency || '0'} utasok/nap</p>
                    <p class="text-sm mb-2">Agency: ${agencyDisplay}</p>
                    <p class="text-xs mt-2">
                        Coordinates: ${position.lat.toFixed(6)}, ${position.lng.toFixed(6)}
                    </p>
                </div>
            `
        });

        marker.addListener('click', () => {
            this.infoWindows.forEach(w => w.close());
            infoWindow.open(this.map, marker);
        });

        return { marker, infoWindow, locationName };
    }

    // megállók betöltése
    async loadStops(stops, batchSize = 100) {
        this.clearMarkers();
        
        const bounds = new google.maps.LatLngBounds();
        const progressContainer = document.getElementById('route-info');

        for (let i = 0; i < stops.length; i += batchSize) {
            const batch = stops.slice(i, i + batchSize);
            
            const batchResults = await Promise.all(
                batch.map(stop => this.createMarker(stop))
            );

            const validResults = batchResults.filter(result => result !== null);

            validResults.forEach(result => {
                this.markers.push(result.marker);
                this.infoWindows.push(result.infoWindow);
                
                bounds.extend(result.marker.getPosition());
            });

            if (progressContainer) {
                progressContainer.innerHTML = `
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h4 class="font-bold text-lg mb-3">Loading stops... (${i + validResults.length}/${stops.length})</h4>
                        <div class="progress-bar">
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full" 
                                     style="width: ${((i + validResults.length) / stops.length * 100)}%">
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            await new Promise(resolve => setTimeout(resolve, 10));
        }

        if (this.markers.length > 0) {
            this.map.fitBounds(bounds);
        }

        this.updateStopsList(stops);
    }

    // lista frissítése
    async updateStopsList(stops) {
        const progressContainer = document.getElementById('route-info');
        if (!progressContainer) return;

        const stopListHTML = await Promise.all(stops.map(async (stop) => {
            if (!stop.latitude || !stop.longitude) return '';

            const position = {
                lat: parseFloat(stop.latitude),
                lng: parseFloat(stop.longitude)
            };

            const locationName = await this.getLocationName(position);

            return `
                <div class="mb-3 p-3 bg-gray-50 rounded hover:bg-gray-100 cursor-pointer"
                     onclick="window.focusStop(${stop.latitude}, ${stop.longitude})">
                    <p class="font-bold">${stop.id}</p>
                    <p class="text-sm text-gray-600">Location: ${locationName}</p>
                    <p class="text-sm text-gray-500">Frequency: ${stop.frequency || '0'} passengers/day</p>
                </div>
            `;
        }));

        progressContainer.innerHTML = `
            <div class="bg-white p-4 rounded-lg shadow">
                <h4 class="font-bold text-lg mb-3">All Stops (${stops.length})</h4>
                <div class="max-h-96 overflow-y-auto">
                    ${stopListHTML.join('')}
                </div>
            </div>
        `;
    }
}

// keresett Megálló 
window.focusStop = function(lat, lng) {
    if (!window.stopMapManager) return;
    
    const position = { lat: parseFloat(lat), lng: parseFloat(lng) };
    window.stopMapManager.map.setCenter(position);
    window.stopMapManager.map.setZoom(15);
};

// térkép
window.initMap = function() {
    window.stopMapManager = new StopMapManager();
    fetchAndDisplayStops();
};

// API adatok
async function fetchAndDisplayStops() {
    try {
        const response = await fetch('http://localhost:3000/api/stop', {
            headers: {
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const stops = await response.json();
        
        if (!Array.isArray(stops)) {
            throw new Error('érvénytelen formátum');
        }

        await window.stopMapManager.loadStops(stops);

    } catch (error) {
        console.error('Error:', error);
        
        const progressContainer = document.getElementById('route-info');
        if (progressContainer) {
            progressContainer.innerHTML = `
                <div class="bg-white p-4 rounded-lg shadow text-red-600">
                    <h4 class="font-bold text-lg mb-3">Error Loading Stops</h4>
                    <p>${error.message}</p>
                </div>
            `;
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('Oldal betöltés');
});
</script>

</body>
</html>