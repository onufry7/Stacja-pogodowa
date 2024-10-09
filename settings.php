<?php include_once 'sys/configuration.php'; ?>

<!doctype html>

<html lang="pl-PL">

<head>
    <meta charset="utf-8">
    <meta name="description" content="Stacja Pogodowa">
    <meta name="keywords" content="Stacja Pogodowa, Szymon Burnejko">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="sys/style.css">
    <script src="sys/scripts.js"></script>

    <title>Stacja Pogodowa</title>
</head>

<body>

    <header>
        <h1>Symulator stacji pogodowej</h1>
    </header>

    <main class="settings">

        <a href="index.html"><img src="sys/icons/clock.png" alt="settings" class="icon"></a>

        <h2>Ustawienia</h2>

        <div class="formContent">

            <?php if(!empty($message)) : ?>
                <p class="message"><?= $message ?></p>
            <?php endif; ?>

            <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">

                <input type="hidden" name="saveConfig" value="true">

                <div class="row">
                    <div class="label">Typ 12 godzinny:</div>
                    <div>
                        <input type="radio" id="hourYes" <?= $hourYes; ?> name="dateTime[hours]" value="1">
                        <label for="hourYes">Tak</label>

                        <input type="radio" id="hourNo" <?= $hourNo; ?> name="dateTime[hours]" value="0">
                        <label for="hourNo">Nie</label>
                    </div>
                </div>

                <div class="row">
                    <div class="label">Rok dwu cyfrowy:</div>
                    <div>
                        <input type="radio" id="yearYes" <?= $yearYes; ?> name="dateTime[shortYear]" value="1">
                        <label for="yearYes">Tak</label>

                        <input type="radio" id="yearNo" <?= $yearNo; ?> name="dateTime[shortYear]" value="0">
                        <label for="yearNo">Nie</label>
                    </div>
                </div>

                <div class="row">
                    <label for="dateFormat">Format daty: </label>
                    <select name="dateTime[dateFormat]" id="dateFormat">
                        <option value="rrrr-mm-dd" <?= $dateFormats['rrrr-mm-dd'] ?>>rrrr-mm-dd</option>
                        <option value="rrrr-dd-mm" <?= $dateFormats['rrrr-dd-mm'] ?>>rrrr-dd-mm</option>
                        <option value="mm-dd-rrrr" <?= $dateFormats['mm-dd-rrrr'] ?>>mm-dd-rrrr</option>
                        <option value="dd-mm-rrrr" <?= $dateFormats['dd-mm-rrrr'] ?>>dd-mm-rrrr</option>
                    </select>
                </div>

                <div class="row">
                    <label for="separator">Separator daty: </label>
                    <select name="dateTime[dateSeparator]" id="separator">
                        <option value="-" <?= $separators['-'] ?>>Kreska (-)</option>
                        <option value="/" <?= $separators['/'] ?>>Ukośnik (/)</option>
                        <option value="." <?= $separators['.'] ?>>Kropka (.)</option>
                    </select>
                </div>

                <div class="row">
                    <label for="units">Jednostki dla pogody: </label>
                    <select name="weather[units]" id="units">
                        <option value="metric" <?= $units['metric'] ?>>Metryczne (&#8451;, m/s)</option>
                        <option value="imperial" <?= $units['imperial'] ?>>Imperialne (&#8457;, mi/s)</option>
                        <option value="standard" <?= $units['standard'] ?>>Standardowe (&#8490;, m/s)</option>
                    </select>
                </div>

                <div class="row">
                    <label for="lang">Język komunikatów pogodowych: </label>
                    <select name="weather[lang]" id="lang">
                        <option value="pl" <?= $languages['pl'] ?>>Polski</option>
                        <option value="it" <?= $languages['it'] ?>>Italiano</option>
                        <option value="de" <?= $languages['de'] ?>>Deutsch</option>
                        <option value="en" <?= $languages['en'] ?>>English</option>
                        <option value="pt" <?= $languages['pt'] ?>>Português</option>
                        <option value="es" <?= $languages['es'] ?>>Español</option>
                        <option value="ru" <?= $languages['ru'] ?>>Pусский</option>
                        <option value="zh_cn" <?= $languages['zh_cn'] ?>>简体中文</option>
                        <option value="ja" <?= $languages['ja'] ?>>日本</option>
                    </select>
                </div>

                <div class="row">
                    <label for="longitude">Długość geograficzna: </label>
                    <input type="number" name="geolocation[lon]" value="<?= $longitude; ?>" id="longitude" step="0.0001">
                </div>

                <div class="row">
                    <label for="latitude">Szerokość geograficzna: </label>
                    <input type="number" name="geolocation[lat]" value="<?= $latitude; ?>" id="latitude" step="0.0001">
                </div>

                <div>
                    <input type="submit" value="Zapisz" class="button">
                </div>

            </form>

        </div>


    </main>

    <footer>Dane pogodowe dostarczane przez: <a href="https://openweathermap.org"> OpenWeather </a> </footer>

</body>

</html>
