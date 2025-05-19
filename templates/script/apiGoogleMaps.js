
function initAutocomplete() {
    if (typeof google === "undefined" || !google.maps.places) {
        console.error("Google Maps API no está disponible.");
        return;
    }

    let address1Field = document.querySelector("#ship-address");
    let address2Field = document.querySelector("#address2");
    let postalField = document.querySelector("#postcode");

    if (!address1Field) {
        console.error("Elemento de dirección no encontrado.");
        return;
    }

    let autocomplete = new google.maps.places.Autocomplete(address1Field, {
        componentRestrictions: { country: ["cl"] },
        fields: ["address_components", "geometry"],
        types: ["address"],
    });

    autocomplete.addListener("place_changed", () => {
        const place = autocomplete.getPlace();
        if (!place || !place.address_components) {
            console.error("No se pudo obtener los datos del lugar.");
            return;
        }

        let address1 = "";
        let postcode = "";

        place.address_components.forEach(component => {
            const componentType = component.types[0];
            switch (componentType) {
                case "street_number":
                    address1 = `${component.long_name} ${address1}`;
                    break;
                case "route":
                    address1 += component.short_name;
                    break;
                case "postal_code":
                    postcode = `${component.long_name}${postcode}`;
                    break;
                case "postal_code_suffix":
                    postcode = `${postcode}-${component.long_name}`;
                    break;
                case "locality":
                    document.querySelector("#locality").value = component.long_name;
                    break;
                case "administrative_area_level_1":
                    document.querySelector("#state").value = component.short_name;
                    break;
                case "country":
                    document.querySelector("#country").value = component.long_name;
                    break;
            }
        });

        address1Field.value = address1;
        postalField.value = postcode;
        address2Field.focus();
    });
}

let autocomplete;
let address1Field;
let address2Field;
let postalField;

// Cargar dinámicamente el script de Google Maps para asegurar que se ejecuta correctamente
function loadGoogleMapsScript() {
    let script = document.createElement("script");
    script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyBQ0DDplgatpypYLs0He7N81bLnrOxuBOQ&libraries=places&loading=async`;
    script.async = true;
    script.defer = true;

    script.onload = function () {
        console.log("Google Maps API cargada correctamente.");
        setTimeout(() => {
            if (typeof google === "undefined" || !google.maps.places) {
                console.error("Google Maps API sigue sin estar disponible.");
                return;
            }
            initAutocomplete();
        }, 1000); // Da tiempo extra para que la API cargue bien
    };

    script.onerror = function () {
        console.error("Error al cargar la API de Google Maps.");
    };

    document.head.appendChild(script);
}

window.onload = loadGoogleMapsScript;



function initAutocomplete() {
    if (typeof google === "undefined" || !google.maps.places) {
        console.error("Google Maps API no está disponible.");
        return;
    }

    address1Field = document.querySelector("#ship-address");
    address2Field = document.querySelector("#address2");
    postalField = document.querySelector("#postcode");

    if (!address1Field) {
        console.error("Elemento de dirección no encontrado.");
        return;
    }

    autocomplete = new google.maps.places.Autocomplete(address1Field, {
        componentRestrictions: { country: ["cl"] },
        fields: ["address_components", "geometry"],
        types: ["address"],
    });

    autocomplete.addListener("place_changed", fillInAddress);
}

function fillInAddress() {
    const place = autocomplete.getPlace();
    if (!place || !place.address_components) {
        console.error("No se pudo obtener los datos del lugar.");
        return;
    }

    let address1 = "";
    let postcode = "";

    place.address_components.forEach(component => {
        const componentType = component.types[0];
        switch (componentType) {
            case "street_number":
                address1 = `${component.long_name} ${address1}`;
                break;
            case "route":
                address1 += component.short_name;
                break;
            case "postal_code":
                postcode = `${component.long_name}${postcode}`;
                break;
            case "postal_code_suffix":
                postcode = `${postcode}-${component.long_name}`;
                break;
            case "locality":
                document.querySelector("#locality").value = component.long_name;
                break;
            case "administrative_area_level_1":
                document.querySelector("#state").value = component.short_name;
                break;
            case "country":
                document.querySelector("#country").value = component.long_name;
                break;
        }
    });

    address1Field.value = address1;
    postalField.value = postcode;
    address2Field.focus();

        // Bloquea los cuadros autocompletados para que no se puedan editar
    document.querySelector("#locality").readOnly = true;
    document.querySelector("#state").readOnly = true;
    document.querySelector("#country").readOnly = true;
    document.querySelector("#postcode").readOnly = true;

}

// Evento para el botón de guardar dirección
document.querySelector('.my-button').addEventListener('click', function () {
    // Recoger los datos de los campos del formulario
    let addressData = {
        shipAddress: document.getElementById('ship-address').value,
        address2: document.getElementById('address2').value,
        city: document.getElementById('locality').value,
        region: document.getElementById('state').value,
        postcode: document.getElementById('postcode').value,
        country: document.getElementById('country').value
    };

    // Almacenar el objeto de dirección en el localStorage (convertido a cadena JSON)
    localStorage.setItem('tempShippingAddress', JSON.stringify(addressData));

    alert('La dirección se ha guardado temporalmente.');
    
    window.close();
});
