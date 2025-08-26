<?php
// Sisällytetään autentikointi
include_once __DIR__ . '/includes/auth.php';
// Varmistetaan, että käyttäjä on ylläpitäjä (käyttäjätaso 2)
verifyAccess(2);
?>
<!DOCTYPE html>
<html lang="fi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hae drinkkejä</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/manage.css">
    <script src="assets/js/manage-drinks.js" defer></script>
</head>

<body>
    <?php include "includes/header.php"; ?>

    <main>
        <!-- Sivuvalikko ylläpitäjän toiminnoille -->
        <aside>
            <h2>Ylläpitäjän hllintapaneeli</h2>

            <div class="admin-links">
                <a href="manage-drinks.php">Hallitse drinkkejä</a>
                <a href="manage-users.php">Hallitse käyttäjiä</a>
                <a href="manage-suggestions.php">Hyväksy ehdotuksia</a>
            </div>
        </aside>

        <!-- Juomataulukko -->
        <div class="table-container">
            <div class="table" id="drinks-table">
                <div class="table-header">
                    <div class="table-header-item">
                        <span>ID</span>
                    </div>
                    <div class="table-header-item">
                        <span>Nimi</span>
                    </div>
                    <div class="table-header-item">
                        <span>Kategoria</span>
                    </div>
                    <div class="table-header-item">
                        <span>Lisääjä ID</span>
                    </div>
                    <div class="table-header-item">
                        <span>Toiminnot</span>
                    </div>
                </div>

                <!-- Juomarivin mallipohja -->
                <template id="drink-row-template">
                    <div class="table-row">
                        <div class="table-item">
                            <span class="drink-id"></span>
                        </div>
                        <div class="table-item">
                            <span class="drink-name"></span>
                        </div>
                        <div class="table-item">
                            <span class="drink-category"></span>
                        </div>
                        <div class="table-item">
                            <span class="drink-adder-id"></span>
                        </div>
                        <div class="table-item">
                            <button class="edit-btn">
                                <span>Muokkaa</span>
                            </button>
                            <button class="delete-btn">
                                <span>Poista</span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Muokkaus- ja poistoikkunat -->
        <div class="overlay">
            <!-- Muokkausikkuna -->
            <div class="popup" id="edit-drink-popup">
                <div class="popup-header">
                    <h2>Muokkaa drinkkiä</h2>
                </div>
                <form class="popup-content" id="edit-drink-form">
                    <input type="hidden" id="edit-drink-id" name="drink-id">
                    <section>
                        <span class="section-label">juoman tiedot</span>
                        <div class="inputs">
                            <span class="input-label">Juoman tiedot</span>
                            <div class="input-wrapper">
                                <input class="input-field"
                                    type="text"
                                    id="edit-drink-name"
                                    name="drink-name"
                                    placeholder="Juoman nimi"
                                    required>
                                <img class="input-checker-icon"
                                    src="assets/icons/input-loader-22px.svg">
                            </div>
                            <div class="checkbox-wrapper">
                                <input class="checkbox-field"
                                    type="checkbox"
                                    id="edit-has-allergens"
                                    name="has-allergens">
                                <label for="edit-has-allergens">sisältää allergeenejä</label>
                            </div>
                        </div>
                    </section>
                    <button type="submit">
                        <span>tallenna ja sulje</span>
                    </button>
                </form>
            </div>
            <!-- Poistoikkuna -->
            <div class="popup" id="delete-drink-popup">
                <div class="popup-header">
                    <h2>Poista drinkki</h2>
                </div>
                <section class="popup-content">
                    <p>Oletko varma, että haluat poistaa tämän drinkin?</p>
                    <div class="popup-actions">
                        <button class="delete-confirm-btn">
                            <span>Poista</span>
                        </button>
                        <button class="delete-cancel-btn">
                            <span>Peruuta</span>
                        </button>
                    </div>
                </section>
            </div>
        </div>

    </main>
</body>

</html>