// hétvégén közlekedő járatok
const weekendRoutes = [13, 31, 32, 33, 41, 42, 47, 51, 61, 62, 70, 71, 72, 75, 83, 87, 88];

function isWeekend(date) {
    const day = date.getDay();
    return day === 0 || day === 6; 
}

function validateRoute(routeValue, date) {
   
    const routeNumber = parseInt(routeValue.split(' ')[0]);
    
    if (isWeekend(date)) {
      
        if (!weekendRoutes.includes(routeNumber)) {
            return {
                valid: false,
                message: 'Ez a járat hétvégén nem közlekedik!'
            };
        }
    }
    
    return {
        valid: true,
        message: ''
    };
}

document.addEventListener('DOMContentLoaded', () => {
    const routeSelect = document.querySelector('select');
    const dateInput = document.getElementById('travel-time');
    
    function validateSelection() {
        if (!dateInput.value || !routeSelect.value) return;
        
        const selectedDate = new Date(dateInput.value);
        const result = validateRoute(routeSelect.value, selectedDate);
        
        if (!result.valid) {
            alert(result.message);
            routeSelect.value = ''; 
        }
    }
    
    // kiválaszttt útvonal és a dátum mező változását figyeljük
    routeSelect.addEventListener('change', validateSelection);
    dateInput.addEventListener('change', validateSelection);
});