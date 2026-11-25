(function () {

    var field = document.querySelector('[name="address"]');
    if (!field) return;
    var form = field.form;
    if (!form) return;

    var europe = {
        "Albania": "Tirana,Durres,Vlore,Shkoder,Elbasan",
        "Andorra": "Andorra la Vella,Escaldes-Engordany,Encamp",
        "Austria": "Vienna,Graz,Linz,Salzburg,Innsbruck,Klagenfurt",
        "Belarus": "Minsk,Gomel,Brest,Grodno,Vitebsk,Mogilev",
        "Belgium": "Brussels,Antwerp,Ghent,Bruges,Liege,Leuven,Charleroi",
        "Bosnia and Herzegovina": "Sarajevo,Banja Luka,Mostar,Tuzla,Zenica",
        "Bulgaria": "Sofia,Plovdiv,Varna,Burgas,Ruse",
        "Croatia": "Zagreb,Split,Rijeka,Osijek,Zadar,Dubrovnik",
        "Cyprus": "Nicosia,Limassol,Larnaca,Paphos",
        "Czechia": "Prague,Brno,Ostrava,Plzen,Liberec",
        "Denmark": "Copenhagen,Aarhus,Odense,Aalborg,Esbjerg",
        "Estonia": "Tallinn,Tartu,Narva,Parnu",
        "Finland": "Helsinki,Espoo,Tampere,Vantaa,Oulu,Turku",
        "France": "Paris,Marseille,Lyon,Toulouse,Nice,Nantes,Strasbourg,Bordeaux,Lille,Montpellier",
        "Germany": "Berlin,Hamburg,Munich,Cologne,Frankfurt,Stuttgart,Dusseldorf,Leipzig,Dresden,Hanover",
        "Greece": "Athens,Thessaloniki,Patras,Heraklion,Larissa",
        "Hungary": "Budapest,Debrecen,Szeged,Miskolc,Pecs",
        "Iceland": "Reykjavik,Kopavogur,Hafnarfjordur,Akureyri",
        "Ireland": "Dublin,Cork,Limerick,Galway,Waterford",
        "Italy": "Rome,Milan,Naples,Turin,Palermo,Genoa,Bologna,Florence,Venice,Bari",
        "Kosovo": "Pristina,Prizren,Peja,Gjakova",
        "Latvia": "Riga,Daugavpils,Liepaja,Jelgava",
        "Liechtenstein": "Vaduz,Schaan,Balzers",
        "Lithuania": "Vilnius,Kaunas,Klaipeda,Siauliai",
        "Luxembourg": "Luxembourg City,Esch-sur-Alzette,Differdange",
        "Malta": "Valletta,Sliema,Birkirkara,Mosta",
        "Moldova": "Chisinau,Balti,Tiraspol,Bender",
        "Monaco": "Monaco,Monte Carlo",
        "Montenegro": "Podgorica,Niksic,Budva,Bar",
        "Netherlands": "Amsterdam,Rotterdam,The Hague,Utrecht,Eindhoven,Groningen",
        "North Macedonia": "Skopje,Bitola,Kumanovo,Ohrid",
        "Norway": "Oslo,Bergen,Trondheim,Stavanger,Tromso",
        "Poland": "Warsaw,Krakow,Lodz,Wroclaw,Poznan,Gdansk,Szczecin,Lublin",
        "Portugal": "Lisbon,Porto,Braga,Coimbra,Faro,Funchal",
        "Romania": "Bucharest,Cluj-Napoca,Timisoara,Iasi,Constanta,Brasov",
        "Russia": "Moscow,Saint Petersburg,Kazan,Novosibirsk,Yekaterinburg,Sochi",
        "San Marino": "San Marino,Serravalle,Borgo Maggiore",
        "Serbia": "Belgrade,Novi Sad,Nis,Kragujevac,Subotica",
        "Slovakia": "Bratislava,Kosice,Presov,Zilina",
        "Slovenia": "Ljubljana,Maribor,Celje,Koper",
        "Spain": "Madrid,Barcelona,Valencia,Seville,Zaragoza,Malaga,Bilbao,Alicante,Palma",
        "Sweden": "Stockholm,Gothenburg,Malmo,Uppsala,Linkoping",
        "Switzerland": "Zurich,Geneva,Basel,Bern,Lausanne,Lucerne",
        "Ukraine": "Kyiv,Kharkiv,Odesa,Dnipro,Lviv,Zaporizhzhia",
        "United Kingdom": "London,Manchester,Birmingham,Leeds,Glasgow,Liverpool,Edinburgh,Bristol,Cardiff,Belfast",
        "Vatican City": "Vatican City"
    };

    var southAmerica = {
        "Argentina": "Buenos Aires,Cordoba,Rosario,Mendoza,La Plata,Tucuman,Mar del Plata,Salta",
        "Bolivia": "La Paz,Santa Cruz,Cochabamba,Sucre,El Alto,Oruro",
        "Brazil": "Sao Paulo,Rio de Janeiro,Brasilia,Salvador,Fortaleza,Belo Horizonte,Manaus,Curitiba,Recife,Porto Alegre,Belem",
        "Chile": "Santiago,Valparaiso,Concepcion,Antofagasta,Vina del Mar,Temuco",
        "Colombia": "Bogota,Medellin,Cali,Barranquilla,Cartagena,Bucaramanga",
        "Ecuador": "Quito,Guayaquil,Cuenca,Ambato,Manta",
        "Guyana": "Georgetown,Linden,New Amsterdam",
        "Paraguay": "Asuncion,Ciudad del Este,Encarnacion,Luque",
        "Peru": "Lima,Arequipa,Trujillo,Cusco,Chiclayo,Piura",
        "Suriname": "Paramaribo,Lelydorp,Nieuw Nickerie",
        "Uruguay": "Montevideo,Salto,Punta del Este,Paysandu",
        "Venezuela": "Caracas,Maracaibo,Valencia,Barquisimeto,Maracay"
    };

    var asia = {
        "Afghanistan": "Kabul,Kandahar,Herat,Mazar-i-Sharif,Jalalabad",
        "Armenia": "Yerevan,Gyumri,Vanadzor",
        "Azerbaijan": "Baku,Ganja,Sumqayit,Mingachevir",
        "Bahrain": "Manama,Riffa,Muharraq,Hamad Town",
        "Bangladesh": "Dhaka,Chittagong,Khulna,Rajshahi,Sylhet",
        "Bhutan": "Thimphu,Phuntsholing,Paro",
        "Brunei": "Bandar Seri Begawan,Kuala Belait,Seria",
        "Cambodia": "Phnom Penh,Siem Reap,Battambang,Sihanoukville",
        "China": "Beijing,Shanghai,Guangzhou,Shenzhen,Chengdu,Hangzhou,Wuhan,Chongqing,Tianjin",
        "Georgia": "Tbilisi,Batumi,Kutaisi,Rustavi",
        "Hong Kong": "Hong Kong Island,Kowloon,New Territories",
        "India": "Delhi,Mumbai,Bengaluru,Chennai,Kolkata,Hyderabad,Pune,Ahmedabad,Jaipur,Lucknow",
        "Indonesia": "Jakarta,Surabaya,Bandung,Medan,Bali,Semarang,Makassar",
        "Iran": "Tehran,Mashhad,Isfahan,Shiraz,Tabriz",
        "Iraq": "Baghdad,Basra,Erbil,Mosul,Najaf",
        "Israel": "Jerusalem,Tel Aviv,Haifa,Beersheba,Netanya",
        "Japan": "Tokyo,Osaka,Yokohama,Nagoya,Sapporo,Fukuoka,Kyoto,Kobe",
        "Jordan": "Amman,Zarqa,Irbid,Aqaba",
        "Kazakhstan": "Astana,Almaty,Shymkent,Karaganda",
        "Kuwait": "Kuwait City,Hawalli,Salmiya,Farwaniya",
        "Kyrgyzstan": "Bishkek,Osh,Jalal-Abad",
        "Laos": "Vientiane,Luang Prabang,Pakse,Savannakhet",
        "Lebanon": "Beirut,Tripoli,Sidon,Tyre,Jounieh",
        "Malaysia": "Kuala Lumpur,George Town,Johor Bahru,Ipoh,Kuching,Kota Kinabalu,Malacca",
        "Maldives": "Male,Addu City,Fuvahmulah",
        "Mongolia": "Ulaanbaatar,Erdenet,Darkhan",
        "Myanmar": "Yangon,Mandalay,Naypyidaw,Mawlamyine",
        "Nepal": "Kathmandu,Pokhara,Lalitpur,Biratnagar",
        "North Korea": "Pyongyang,Hamhung,Chongjin",
        "Oman": "Muscat,Salalah,Sohar,Nizwa",
        "Pakistan": "Karachi,Lahore,Islamabad,Rawalpindi,Faisalabad,Peshawar,Multan",
        "Palestine": "Gaza,Ramallah,Hebron,Nablus,Bethlehem",
        "Philippines": "Manila,Quezon City,Cebu City,Davao,Makati",
        "Qatar": "Doha,Al Rayyan,Al Wakrah,Lusail",
        "Saudi Arabia": "Riyadh,Jeddah,Mecca,Medina,Dammam,Khobar",
        "Singapore": "Singapore",
        "South Korea": "Seoul,Busan,Incheon,Daegu,Daejeon,Gwangju",
        "Sri Lanka": "Colombo,Kandy,Galle,Jaffna,Negombo",
        "Syria": "Damascus,Aleppo,Homs,Latakia",
        "Taiwan": "Taipei,Kaohsiung,Taichung,Tainan",
        "Tajikistan": "Dushanbe,Khujand,Kulob",
        "Thailand": "Bangkok,Chiang Mai,Phuket,Pattaya,Khon Kaen",
        "Timor-Leste": "Dili,Baucau,Maliana",
        "Turkey": "Istanbul,Ankara,Izmir,Bursa,Antalya,Adana",
        "Turkmenistan": "Ashgabat,Turkmenabat,Dashoguz",
        "United Arab Emirates": "Dubai,Abu Dhabi,Sharjah,Ajman,Ras Al Khaimah",
        "Uzbekistan": "Tashkent,Samarkand,Bukhara,Namangan",
        "Vietnam": "Hanoi,Ho Chi Minh City,Da Nang,Hai Phong,Hue",
        "Yemen": "Sanaa,Aden,Taiz,Hodeidah"
    };

    // Postal code formats: [list of accepted patterns, example]
    var D3 = /^\d{3}$/;
    var D4 = /^\d{4}$/;
    var D5 = /^\d{5}$/;
    var D6 = /^\d{6}$/;
    var S32 = /^\d{3}\s?\d{2}$/;
    var NONE = [[/^[A-Za-z0-9 -]{2,12}$/], "00000", true];

    var postal = {
        "Albania": [[D4], "1001"],
        "Andorra": [[/^AD\d{3}$/i], "AD500"],
        "Austria": [[D4], "1010"],
        "Belarus": [[D6], "220030"],
        "Belgium": [[D4], "1000"],
        "Bosnia and Herzegovina": [[D5], "71000"],
        "Bulgaria": [[D4], "1000"],
        "Croatia": [[D5], "10000"],
        "Cyprus": [[D4], "1010"],
        "Czechia": [[S32], "110 00"],
        "Denmark": [[D4], "1050"],
        "Estonia": [[D5], "10111"],
        "Finland": [[D5], "00100"],
        "France": [[D5], "75001"],
        "Germany": [[D5], "10115"],
        "Greece": [[S32], "104 31"],
        "Hungary": [[D4], "1051"],
        "Iceland": [[D3], "101"],
        "Ireland": [[/^[A-Za-z]\d{2}\s?[A-Za-z0-9]{4}$/, /^D6W\s?[A-Za-z0-9]{4}$/i], "D02 X285"],
        "Italy": [[D5], "00100"],
        "Kosovo": [[D5], "10000"],
        "Latvia": [[/^(LV-?)?\d{4}$/i], "LV-1050"],
        "Liechtenstein": [[D4], "9490"],
        "Lithuania": [[/^(LT-?)?\d{5}$/i], "LT-01100"],
        "Luxembourg": [[/^(L-?)?\d{4}$/i], "L-1111"],
        "Malta": [[/^[A-Za-z]{3}\s?\d{2,4}$/], "VLT 1117"],
        "Moldova": [[/^(MD-?)?\d{4}$/i], "MD-2001"],
        "Monaco": [[/^980\d{2}$/], "98000"],
        "Montenegro": [[D5], "81000"],
        "Netherlands": [[/^\d{4}\s?[A-Za-z]{2}$/], "1012 AB"],
        "North Macedonia": [[D4], "1000"],
        "Norway": [[D4], "0150"],
        "Poland": [[/^\d{2}-\d{3}$/], "00-950"],
        "Portugal": [[/^\d{4}-\d{3}$/], "1000-001"],
        "Romania": [[D6], "010011"],
        "Russia": [[D6], "101000"],
        "San Marino": [[/^4789\d$/], "47890"],
        "Serbia": [[D5], "11000"],
        "Slovakia": [[S32], "811 01"],
        "Slovenia": [[D4], "1000"],
        "Spain": [[D5], "28001"],
        "Sweden": [[S32], "111 22"],
        "Switzerland": [[D4], "8001"],
        "Ukraine": [[D5], "01001"],
        "United Kingdom": [[/^[A-Za-z]{1,2}\d[A-Za-z\d]?\s?\d[A-Za-z]{2}$/], "SW1A 1AA"],
        "Vatican City": [[/^00120$/], "00120"],

        "Argentina": [[D4, /^[A-Za-z]\d{4}[A-Za-z]{3}$/], "C1000AAA"],
        "Bolivia": NONE,
        "Brazil": [[/^\d{5}-?\d{3}$/], "01310-100"],
        "Chile": [[/^\d{7}$/, /^\d{3}-\d{4}$/], "8320000"],
        "Colombia": [[D6], "110111"],
        "Ecuador": [[D6], "170150"],
        "Guyana": NONE,
        "Paraguay": [[D4, D6], "1209"],
        "Peru": [[D5], "15001"],
        "Suriname": NONE,
        "Uruguay": [[D5], "11000"],
        "Venezuela": [[D4], "1010"],

        "Afghanistan": [[D4], "1001"],
        "Armenia": [[D4], "0010"],
        "Azerbaijan": [[/^(AZ-?\s?)?\d{4}$/i], "AZ1000"],
        "Bahrain": [[D3, D4], "317"],
        "Bangladesh": [[D4], "1000"],
        "Bhutan": [[D5], "11001"],
        "Brunei": [[/^[A-Za-z]{2}\d{4}$/], "BS8711"],
        "Cambodia": [[D5, D6], "12000"],
        "China": [[D6], "100000"],
        "Georgia": [[D4], "0105"],
        "Hong Kong": NONE,
        "India": [[D6], "110001"],
        "Indonesia": [[D5], "10110"],
        "Iran": [[/^\d{5}-?\d{5}$/], "1136813518"],
        "Iraq": [[D5], "10001"],
        "Israel": [[D5, /^\d{7}$/], "9446723"],
        "Japan": [[/^\d{3}-?\d{4}$/], "100-0001"],
        "Jordan": [[D5], "11118"],
        "Kazakhstan": [[D6], "010000"],
        "Kuwait": [[D5], "13001"],
        "Kyrgyzstan": [[D6], "720000"],
        "Laos": [[D5], "01000"],
        "Lebanon": [[D4, /^\d{4}\s?\d{4}$/], "1100 2030"],
        "Malaysia": [[D5], "50000"],
        "Maldives": [[D5], "20002"],
        "Mongolia": [[D5], "14200"],
        "Myanmar": [[D5], "11181"],
        "Nepal": [[D5], "44600"],
        "North Korea": NONE,
        "Oman": [[D3], "100"],
        "Pakistan": [[D5], "44000"],
        "Palestine": NONE,
        "Philippines": [[D4], "1000"],
        "Qatar": NONE,
        "Saudi Arabia": [[D5, /^\d{5}-?\d{4}$/], "11564"],
        "Singapore": [[D6], "238858"],
        "South Korea": [[D5], "04524"],
        "Sri Lanka": [[D5], "00100"],
        "Syria": NONE,
        "Taiwan": [[D3, D5, D6], "100"],
        "Tajikistan": [[D6], "734000"],
        "Thailand": [[D5], "10110"],
        "Timor-Leste": NONE,
        "Turkey": [[D5], "34000"],
        "Turkmenistan": [[D6], "744000"],
        "United Arab Emirates": NONE,
        "Uzbekistan": [[D6], "100000"],
        "Vietnam": [[D5, D6], "100000"],
        "Yemen": NONE
    };
    var cityLists = {};
    var k;
    for (k in europe) { cityLists[k] = europe[k]; }
    for (k in southAmerica) { cityLists[k] = southAmerica[k]; }
    for (k in asia) { cityLists[k] = asia[k]; }

    // Find the old address label before we add our own fields
    var oldLabels = [];
    if (field.id) {
        var byFor = document.querySelector('label[for="' + field.id + '"]');
        if (byFor) oldLabels.push(byFor);
    }
    var prev = field.previousElementSibling;
    if (prev && prev.tagName === "LABEL") oldLabels.push(prev);

    var box = document.createElement("div");
    box.className = "addr-box";
    box.innerHTML =
        '<label for="addrCountry">Country</label>' +
        '<select id="addrCountry" required></select>' +
        '<label for="addrCity">City / Region</label>' +
        '<select id="addrCity" required disabled></select>' +
        '<input type="text" id="addrCityOther" class="addr-other" placeholder="Type your city or region" maxlength="60" style="display:none">' +
        '<label for="addrStreet">Street address and house number</label>' +
        '<input type="text" id="addrStreet" placeholder="e.g. 12 Main Street, Apartment 4" maxlength="120" required>' +
        '<label for="addrPostal">Postal code</label>' +
        '<input type="text" id="addrPostal" placeholder="Choose your country first" maxlength="12" required>' +
        '<small class="addr-hint" id="addrHint"></small>';

    field.parentNode.insertBefore(box, field);

    field.required = false;
    field.style.display = "none";
  oldLabels.forEach(function (l) { l.style.display = "none"; });

    form.querySelectorAll("label").forEach(function (l) {
        if (box.contains(l)) return;
        var t = l.textContent.trim().toLowerCase();
        if (t.indexOf("address") !== -1) { l.style.display = "none"; }
    });

    var countrySel = document.getElementById("addrCountry");
    var citySel = document.getElementById("addrCity");
    var cityOther = document.getElementById("addrCityOther");
    var street = document.getElementById("addrStreet");
    var postalInput = document.getElementById("addrPostal");
    var hint = document.getElementById("addrHint");

    function addOption(select, text, value, parent) {
        var o = document.createElement("option");
        o.textContent = text;
        o.value = value;
        (parent ? parent : select).appendChild(o);
    }

    function addGroup(label, list) {
        var g = document.createElement("optgroup");
        g.label = label;
        Object.keys(list).sort().forEach(function (name) {
            addOption(countrySel, name, name, g);
        });
        countrySel.appendChild(g);
    }

    addOption(countrySel, "Choose your country", "");
    addGroup("Europe", europe);
    addGroup("South America", southAmerica);
    addGroup("Asia", asia);

    function getRule(country) {
        var r = postal[country];
        if (!r) r = NONE;
        return r;
    }

    function checkPostal() {
        var country = countrySel.value;
        var value = postalInput.value.trim();

        if (country === "") {
            postalInput.setCustomValidity("");
            return;
        }

        var rule = getRule(country);
        var ok = rule[0].some(function (re) { return re.test(value); });

        if (ok) {
            postalInput.setCustomValidity("");
        } else if (rule[2]) {
            postalInput.setCustomValidity("Type your P.O. Box, area code or 00000.");
        } else {
            postalInput.setCustomValidity("Enter a valid postal code for " + country + ", for example " + rule[1] + ".");
        }
    }

    function compose() {
        var city = citySel.value === "__other" ? cityOther.value.trim() : citySel.value;
        var parts = [street.value.trim(), city, postalInput.value.trim(), countrySel.value];
        field.value = parts.filter(function (p) { return p !== ""; }).join(", ");
    }

    countrySel.addEventListener("change", function () {
        citySel.innerHTML = "";
        cityOther.style.display = "none";
        cityOther.required = false;
        cityOther.value = "";
        if (countrySel.value === "") {
            citySel.disabled = true;
            addOption(citySel, "Choose a country first", "");
            postalInput.placeholder = "Choose your country first";
            hint.textContent = "";
        } else {
            citySel.disabled = false;
            addOption(citySel, "Choose your city / region", "");
            cityLists[countrySel.value].split(",").forEach(function (c) {
                addOption(citySel, c, c);
            });
            addOption(citySel, "Other (type it)", "__other");

            var rule = getRule(countrySel.value);
            if (rule[2]) {
                postalInput.placeholder = "P.O. Box, area code or 00000";
                hint.textContent = countrySel.value + " has no postal codes. Type your P.O. Box, area code or 00000.";
            } else {
                postalInput.placeholder = "e.g. " + rule[1];
                hint.textContent = "Postal code example for " + countrySel.value + ": " + rule[1];
            }
        }
        checkPostal();
        compose();
    });

    citySel.addEventListener("change", function () {
        var other = citySel.value === "__other";
        cityOther.style.display = other ? "block" : "none";
        cityOther.required = other;
        if (other) cityOther.focus();
        compose();
    });

    postalInput.addEventListener("input", function () {
        checkPostal();
        compose();
    });

    [cityOther, street].forEach(function (el) {
        el.addEventListener("input", compose);
    });

    form.addEventListener("submit", function () {
        checkPostal();
        compose();
    });

    addOption(citySel, "Choose a country first", "");
    compose();

})();
