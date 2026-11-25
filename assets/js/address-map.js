(function () {

    var box = document.querySelector(".addr-box");
    var countrySel = document.getElementById("addrCountry");
    var citySel = document.getElementById("addrCity");
    var cityOther = document.getElementById("addrCityOther");

    if (!box) return;
    if (!countrySel) return;
    if (!citySel) return;

    // Layout: map and fields side by side on big screens
    var layout = document.createElement("div");
    layout.className = "addr-layout";
    box.parentNode.insertBefore(layout, box);
    layout.appendChild(box);

    var wrap = document.createElement("div");
    wrap.className = "addr-map-wrap";
    wrap.innerHTML =
        '<div class="addr-map" id="addrMap"></div>' +
        '<div class="addr-map-note" id="addrMapNote">Choose your country to see it on the map.</div>';
    layout.appendChild(wrap);

    if (typeof L === "undefined") {
        wrap.style.display = "none";
        return;
    }

    // [latitude, longitude, zoom]
    var coords = {
        "Albania": [41.15, 20.17, 7], "Andorra": [42.55, 1.58, 10], "Austria": [47.52, 14.55, 7],
        "Belarus": [53.71, 27.95, 6], "Belgium": [50.50, 4.47, 8], "Bosnia and Herzegovina": [43.92, 17.68, 7],
        "Bulgaria": [42.73, 25.49, 7], "Croatia": [45.10, 15.20, 7], "Cyprus": [35.13, 33.43, 8],
        "Czechia": [49.82, 15.47, 7], "Denmark": [56.26, 9.50, 7], "Estonia": [58.60, 25.01, 7],
        "Finland": [61.92, 25.75, 5], "France": [46.60, 2.40, 6], "Germany": [51.17, 10.45, 6],
        "Greece": [39.07, 21.82, 6], "Hungary": [47.16, 19.50, 7], "Iceland": [64.96, -19.02, 6],
        "Ireland": [53.41, -8.24, 7], "Italy": [41.87, 12.57, 6], "Kosovo": [42.60, 20.90, 8],
        "Latvia": [56.88, 24.60, 7], "Liechtenstein": [47.17, 9.56, 11], "Lithuania": [55.17, 23.88, 7],
        "Luxembourg": [49.82, 6.13, 9], "Malta": [35.94, 14.38, 10], "Moldova": [47.41, 28.37, 7],
        "Monaco": [43.74, 7.42, 13], "Montenegro": [42.71, 19.37, 8], "Netherlands": [52.13, 5.29, 7],
        "North Macedonia": [41.61, 21.75, 8], "Norway": [60.47, 8.47, 5], "Poland": [51.92, 19.15, 6],
        "Portugal": [39.40, -8.22, 6], "Romania": [45.94, 24.97, 6], "Russia": [61.52, 105.32, 3],
        "San Marino": [43.94, 12.46, 12], "Serbia": [44.02, 21.01, 7], "Slovakia": [48.67, 19.70, 7],
        "Slovenia": [46.15, 14.99, 8], "Spain": [40.46, -3.75, 6], "Sweden": [60.13, 18.64, 5],
        "Switzerland": [46.82, 8.23, 7], "Ukraine": [48.38, 31.17, 6], "United Kingdom": [55.38, -3.44, 5],
        "Vatican City": [41.90, 12.45, 14],

        "Argentina": [-38.42, -63.62, 4], "Bolivia": [-16.29, -63.59, 5], "Brazil": [-14.24, -51.93, 4],
        "Chile": [-35.68, -71.54, 4], "Colombia": [4.57, -74.30, 5], "Ecuador": [-1.83, -78.18, 6],
        "Guyana": [4.86, -58.93, 6], "Paraguay": [-23.44, -58.44, 6], "Peru": [-9.19, -75.02, 5],
        "Suriname": [3.92, -56.03, 6], "Uruguay": [-32.52, -55.77, 7], "Venezuela": [6.42, -66.59, 6],

        "Afghanistan": [33.94, 67.71, 6], "Armenia": [40.07, 45.04, 8], "Azerbaijan": [40.14, 47.58, 7],
        "Bahrain": [26.07, 50.56, 10], "Bangladesh": [23.68, 90.36, 7], "Bhutan": [27.51, 90.43, 8],
        "Brunei": [4.54, 114.73, 9], "Cambodia": [12.57, 104.99, 7], "China": [35.86, 104.20, 4],
        "Georgia": [42.32, 43.36, 7], "Hong Kong": [22.32, 114.17, 10], "India": [20.59, 78.96, 4],
        "Indonesia": [-0.79, 113.92, 4], "Iran": [32.43, 53.69, 5], "Iraq": [33.22, 43.68, 6],
        "Israel": [31.05, 34.85, 7], "Japan": [36.20, 138.25, 5], "Jordan": [30.59, 36.24, 7],
        "Kazakhstan": [48.02, 66.92, 4], "Kuwait": [29.31, 47.48, 8], "Kyrgyzstan": [41.20, 74.77, 6],
        "Laos": [19.86, 102.50, 6], "Lebanon": [33.85, 35.86, 8], "Malaysia": [4.21, 101.98, 6],
        "Maldives": [3.20, 73.22, 6], "Mongolia": [46.86, 103.85, 5], "Myanmar": [21.91, 95.96, 5],
        "Nepal": [28.39, 84.12, 7], "North Korea": [40.34, 127.51, 6], "Oman": [21.47, 55.98, 6],
        "Pakistan": [30.38, 69.35, 5], "Palestine": [31.95, 35.23, 8], "Philippines": [12.88, 121.77, 5],
        "Qatar": [25.35, 51.18, 9], "Saudi Arabia": [23.89, 45.08, 5], "Singapore": [1.35, 103.82, 11],
        "South Korea": [35.91, 127.77, 6], "Sri Lanka": [7.87, 80.77, 7], "Syria": [34.80, 38.99, 7],
        "Taiwan": [23.70, 120.96, 7], "Tajikistan": [38.86, 71.28, 6], "Thailand": [15.87, 100.99, 5],
        "Timor-Leste": [-8.87, 125.73, 8], "Turkey": [38.96, 35.24, 6], "Turkmenistan": [38.97, 59.56, 6],
        "United Arab Emirates": [23.42, 53.85, 7], "Uzbekistan": [41.38, 64.59, 5], "Vietnam": [14.06, 108.28, 5],
        "Yemen": [15.55, 48.52, 6]
    };

    var reduce = false;
    if (window.matchMedia) {
        reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    }

    var map = L.map("addrMap", {
        center: [20, 20],
        zoom: 2,
        minZoom: 2,
        scrollWheelZoom: false,
        dragging: !L.Browser.mobile,
        worldCopyJump: true
    });

    L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 18,
        keepBuffer: 6,
        updateWhenZooming: false,
        attribution: "&copy; OpenStreetMap contributors"
    }).addTo(map);

    var pin = null;
    var note = document.getElementById("addrMapNote");
    var token = 0;

    function fly(lat, lng, zoom) {
        if (reduce) {
            map.setView([lat, lng], zoom, { animate: false });
        } else {
            map.flyTo([lat, lng], zoom, { duration: 0.8 });
        }
    }

    function setPin(lat, lng) {
        if (!pin) {
            pin = L.marker([lat, lng], {
                icon: L.divIcon({ className: "addr-pin", iconSize: [18, 18], iconAnchor: [9, 9] }),
                interactive: false,
                keyboard: false
            }).addTo(map);
        } else {
            pin.setLatLng([lat, lng]);
        }
    }

    function removePin() {
        if (pin) {
            map.removeLayer(pin);
            pin = null;
        }
    }

    function showCountry() {
        token++;
        var c = coords[countrySel.value];
        if (!c) {
            removePin();
            fly(20, 20, 2);
            note.textContent = "Choose your country to see it on the map.";
            return;
        }
        setPin(c[0], c[1]);
        fly(c[0], c[1], c[2]);
        note.textContent = countrySel.value;
    }

    function findPlace(query) {
        var mine = ++token;
        var url = "https://nominatim.openstreetmap.org/search?format=json&limit=1&q=" + encodeURIComponent(query);
        fetch(url)
            .then(function (r) { return r.json(); })
            .then(function (list) {
                if (mine !== token) return;
                if (!list.length) return;
                var lat = parseFloat(list[0].lat);
                var lng = parseFloat(list[0].lon);
                setPin(lat, lng);
                fly(lat, lng, 12);
            })
            .catch(function () {});
    }

    countrySel.addEventListener("change", showCountry);

    citySel.addEventListener("change", function () {
        var city = citySel.value;
        if (city === "__other") return;
        if (city === "") {
            showCountry();
            return;
        }
        note.textContent = city + ", " + countrySel.value;
        findPlace(city + ", " + countrySel.value);
    });

    if (cityOther) {
        cityOther.addEventListener("change", function () {
            var text = cityOther.value.trim();
            if (text === "") return;
            note.textContent = text + ", " + countrySel.value;
            findPlace(text + ", " + countrySel.value);
        });
    }

    setTimeout(function () { map.invalidateSize(); }, 300);
    window.addEventListener("resize", function () { map.invalidateSize(); });

})();
