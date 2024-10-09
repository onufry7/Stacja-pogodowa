let appConfig;
let longitude;
let latitude;
let apiKey;


function timeFormat(h, min, s, config) {
    let hour;

    if (config.hours == 1) {
        let ampm = (h >= 12) ? 'PM' : 'AM';
        hour = h % 12;
        hour = hour || 12;
        if (hour < 10) hour = "0" + hour;
        hour = `${hour}:${min}:${s} ${ampm}`;
    }
    else {
        hour = `${h}`.padStart(2, "0");
        hour = `${hour}:${min}:${s}`;
    }
    return hour;
}


function dateFormat(d, m, y, config) {
    let date;
    const separator = (['/', '.', '-'].includes(config.dateSeparator)) ? config.dateSeparator : '.';
    y = (config.shortYear == 1) ? y.toString().substring(2) : y;

    switch (config.dateFormat) {
        case 'rrrr-mm-dd':
            date = `${y}${separator}${m}${separator}${d}`;
            break;
        case 'rrrr-dd-mm':
            date = `${y}${separator}${d}${separator}${m}`;
            break;
        case 'mm-dd-rrrr':
            date = `${m}${separator}${d}${separator}${y}`;
            break;
        default: // dd-mm-rrrr
            date = `${d}${separator}${m}${separator}${y}`;
    }

    return date;
}


function time() {
    const timeInput = document.querySelector('.time');
    const dateInput = document.querySelector('.date');
    const config = appConfig.dateTime;

    const now = new Date();
    const h = now.getHours();
    const s = `${now.getSeconds()}`.padStart(2, "0");
    const min = `${now.getMinutes()}`.padStart(2, "0");
    const d = `${now.getDate()}`.padStart(2, "0");
    const m = `${now.getMonth() + 1}`.padStart(2, "0");
    const y = now.getFullYear();

    timeInput.textContent = timeFormat(h, min, s, config);
    dateInput.textContent = dateFormat(d, m, y, config);

    setTimeout(time, 1000);
}


function setCoordinates() {
    if (!navigator.geolocation) throw new Error('Brak wsparcia dla lokalizacji!');

    navigator.geolocation.getCurrentPosition(function (position) {
        longitude = position.coords.longitude;
        latitude = position.coords.latitude;
    });
}


async function loadAppConfig() {
    const response = await fetch("sys/config.json");

    if (response.status == 404) throw new Error("Nie znaleziono pliku config!")
    else if (!response.ok) throw new Error("HTTP status: " + response.status);

    const responseJson = await response.json();
    if (responseJson.dateTime == undefined) throw new Error("Plik nie zawiera konfiguracji daty i czasu!");
    if (responseJson.weather == undefined) throw new Error("Plik nie zawiera konfiguracji pogody!");

    appConfig = responseJson;

    return responseJson;
}


function setUnits() {
    let windUnit, tempUnit;

    switch (appConfig.weather.units) {
        case 'imperial':
            windUnit = " mi/s";
            tempUnit = "\u2109";
            break;
        case 'metric':
            windUnit = " m/s";
            tempUnit = "\u2103";
            break;
        default: // standard
            windUnit = " m/s";
            tempUnit = "\u212A";
    }

    return { windUnit, tempUnit };
}


function setWeatherFields(data) {
    console.log(data)
    const temp = document.querySelector('.temp > span');
    const feelsTemp = document.querySelector('.feels-like > span');
    const rain = document.querySelector('.rain > span');
    const humidity = document.querySelector('.humidity > span');
    const windSpeed = document.querySelector('.wind-speed > span');
    const snow = document.querySelector('.snow > span');
    const clouds = document.querySelector('.clouds > span');
    const img = document.querySelector('#weather-img > img');
    const imgDescription = document.querySelector('#weather-img > span');

    const { windUnit, tempUnit } = setUnits();

    let dataTemp = (data.main.temp ?? "-") + tempUnit;
    let dataFeelTemp = (data.main.feels_like ?? "-") + tempUnit;
    let dataRain = (data.rain ?? "0.00") + " mm/h";
    let dataHumidity = (data.main.humidity ?? "-") + "%";
    let dataWind = (data.wind.speed ?? "-") + windUnit;
    let dataSnow = (data.snow ?? "0.00") + " mm/h";
    let dataClouds = (data.clouds.all ?? "-") + '%';
    let icon = data.weather[0].icon;
    let description = data.weather[0].description;
    description = description.charAt(0).toUpperCase() + description.slice(1);

    temp.textContent = dataTemp;
    feelsTemp.textContent = dataFeelTemp;
    rain.textContent = dataRain;
    humidity.textContent = dataHumidity;
    windSpeed.textContent = dataWind;
    snow.textContent = dataSnow;
    clouds.textContent = dataClouds;
    img.src = `https://openweathermap.org/img/wn/${icon}@2x.png`;
    imgDescription.textContent = description;
}


async function loadApiKey() {
    const response = await fetch("sys/api_key.json");

    if (response.status == 404) {
        throw new Error("Plik api_key.json nie znaleziony, utwórz plik <code> sys/api_key.json </code> z kluczem API.");
    } else if (!response.ok) {
        throw new Error(`Nie udało się załadować pliku. HTTP status: ${response.status}`);
    }

    const data = await response.json();
    if (!data.api_key) throw new Error("Brak klucza API w pliku JSON!");

    apiKey = data.api_key;
}


async function getWeather() {
    const { units, lang } = appConfig.weather;
    let { lon, lat } = appConfig.geolocation;

    if (lon == '' || lat == '') {
        lon = longitude;
        lat = latitude;
    }

    const url = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&lang=${lang}&units=${units}&appid=${apiKey}`;
    const response = await fetch(url);
    const responseJson = await response.json();
    if (!response.ok) throw new Error("OpenWeatherMap: " + responseJson.message);

    setWeatherFields(responseJson);

    setTimeout(getWeather, 300000);
}


function generateErrorInfo(title, message) {
    const content = `<div class="error">${title}<br>${message}</div>`;
    const divError = document.querySelector('#error');
    divError.innerHTML = content;
}


function init() {
    time();
    getWeather()
        .then(response => {
            console.log("Pogoda załadowana.");
        })
        .catch(error => {
            console.log('Nie udało się załadować pogody!');
            console.log(error.message);
            generateErrorInfo('Nie udało się załadować pogody!', error.message);
        });
}


document.addEventListener('DOMContentLoaded', (event) => {

    if (window.location.pathname != '/settings') {
        setCoordinates();
        Promise.all([loadAppConfig(), loadApiKey()])
            .then(() => {
                console.log("Konfiguracja załadowana.");
                init();
            })
            .catch(error => {
                console.log('Błąd podczas ładowania konfiguracji!');
                console.log(error.message);
                generateErrorInfo('Błąd podczas ładowania konfiguracji!', error.message);
            });
    }

}, false);
