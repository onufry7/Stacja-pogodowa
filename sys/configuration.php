<?php

$fileConfigSrc = 'sys/config.json';
$languages = ['pl','it','de','en','pt','es','ru','zh_cn','ja'];
$units = ['metric','imperial','standard'];
$dateFormats = ['rrrr-mm-dd','rrrr-dd-mm','mm-dd-rrrr','dd-mm-rrrr'];
$separators = ['-','/','.'];
$message = null;


function setSelect(&$array, $value) {
    foreach ($array as $key) {
        $array[$key] = ($key == $value) ? 'selected' : '';
    }
}


// Zapis pliku
if(isset($_POST['saveConfig']) && $_POST['saveConfig'] === "true")
{
    unset($_POST['saveConfig']);

    $is12HourFormat = (int)$_POST['dateTime']['hours'];
    $isShortYear = (int)$_POST['dateTime']['shortYear'];
    $geoLongitude = (double)$_POST['geolocation']['lon'];
    $geoLatitude = (double)$_POST['geolocation']['lat'];
    $language = $_POST['weather']['lang'];
    $unit = $_POST['weather']['units'];
    $dateFormat = $_POST['dateTime']['dateFormat'];
    $dateSeparator = $_POST['dateTime']['dateSeparator'];

    $error = '';
    $message = null;

    if ($is12HourFormat != 0 && $is12HourFormat != 1) {
        $error = 'Typ godziny - błędna wartość!';
    }

    if ($isShortYear != 0 && $isShortYear != 1) {
        $error = 'Rok - błędna wartość!';
    }

    if(!preg_match("/^-?(\d|[1-9]\d|1[0-7]\d|180)\.\d{4}$/", $geoLongitude)) {
        $error = 'Długość geograficzna - błędna wartość!';
    }

    if(!preg_match("/^-?(\d|[1-8]\d|90)\.\d{4}$/", $geoLatitude)) {
        $error = 'Szerokość geograficzna - błędna wartość!';
    }

    if(!in_array($language, $languages)) {
        $error = 'Język - błędna wartość!';
    }

    if(!in_array($unit, $units)) {
        $error = 'Jednostki - błędna wartość!';
    }

    if(!in_array($dateFormat, $dateFormats)) {
        $error = 'Format daty - błędna wartość!';
    }

    if(!in_array($dateSeparator, $separators)) {
        $error = 'Separator daty - błędna wartość!';
    }

    if($error != '') {
        $message = $error;
    } else {
        $newJsonConfig = json_encode($_POST, JSON_PRETTY_PRINT);
        file_put_contents($fileConfigSrc, $newJsonConfig);
        $message = 'Zapisane.';
    }
}


// Załadowanie pliku konfiguracyjnego
$config = json_decode(file_get_contents($fileConfigSrc), true);

// Ustawienie pól w formularzu
$hourYes = ( ($is12HourFormat ?? $config['dateTime']['hours']) == 1) ? 'checked' : '';
$hourNo = ( ($is12HourFormat ?? $config['dateTime']['hours']) == 0) ? 'checked' : '';

$yearYes = ( ($isShortYear ?? $config['dateTime']['shortYear']) == 1) ? 'checked' : '';
$yearNo = ( ($isShortYear ?? $config['dateTime']['shortYear']) == 0) ? 'checked' : '';

$longitude = $geoLongitude ?? $config['geolocation']['lon'];
$latitude = $geoLatitude ?? $config['geolocation']['lat'];

$languageConf = $language ?? $config['weather']['lang'];
$unitsConf = $unit ?? $config['weather']['units'];
$dateFormatConf = $dateFormat ?? $config['dateTime']['dateFormat'];
$separatorConf = $dateSeparator ?? $config['dateTime']['dateSeparator'];

setSelect($languages, $languageConf);
setSelect($units, $unitsConf);
setSelect($dateFormats, $dateFormatConf);
setSelect($separators, $separatorConf);
