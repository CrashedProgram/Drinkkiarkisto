<?php
// Sisällytetään autentikointi
include_once __DIR__ . '/includes/auth.php';
// Varmistetaan, että käyttäjä on kirjautunut (käyttäjätaso vähintään 1)
verifyAccess(1);
?>
<!DOCTYPE html>
<html lang="fi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hae drinkkejä</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/search.css">
    <script src="assets/js/search.js" defer></script>
</head>

<body>
    <!-- Kohdista hakukenttä automaattisesti sivun latautuessa -->
    <script>
        window.onload = function() {
            setTimeout(() => {
                document.getElementById("search-input").focus();
            }, 350);
        };
    </script>

    <!-- Sisällytä sivuston ylätunniste -->
    <?php include "includes/header.php"; ?>

    <main>
        <div class="search-container">
            <!-- Hakupalkki ja tyhjennysikoni -->
            <div class="search-tools">
                <div class="search-bar">
                    <img class="search-icon" id="search-indicator" src="assets/icons/input-search-22px.svg" alt="">
                    <input type="text" class="search-input" id="search-input" placeholder="Etsi drinkkejä...">
                    <img id="search-clear" class="search-icon" src="assets/icons/input-search-clear-22px.svg" alt="tyhjennä haku">
                </div>

                <!-- Suodatuspainikkeet -->
                <div class="search-filters">
                    <span class="search-filter-label">Suodata:</span>
                    <div class="search-filter-wrapper">
                        <button class="search-filter-button" id="search-filter-all">
                            <span>kaikki</span>
                        </button>
                        <button class="search-filter-button" id="search-filter-name">
                            <span>nimi</span>
                        </button>
                        <button class="search-filter-button" id="search-filter-ingredient">
                            <span>ainesosa</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mallipohjat hakutuloksien näyttämiseen -->
            <template id="result-item-template">
                <div class="result-item">
                    <div class="result-item-overview">
                        <div class="result-item-overview-content">
                            <span class="result-item-name"></span>
                            <span class="result-item-info"></span>
                            <div class="result-item-likes">
                                <img class="result-item-like-icon" src="assets/icons/rating-22px.svg" alt="">
                                <span class="result-item-like-count"></span>
                            </div>
                        </div>
                        <span class="result-item-warning"></span>
                    </div>

                    <div class="result-item-button">
                        <div class="result-item-button-content">
                            <a class="result-item-button-link"></a>
                            <img class="result-item-button-icon" src="assets/icons/chevron-right-22px-green.svg" alt="">
                        </div>
                    </div>
                </div>
            </template>

            <div class="search-results"></div>
        </div>
    </main>
</body>

</html>