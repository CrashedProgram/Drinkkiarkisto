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
    <title>Hallitse käyttäjiä</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/manage.css">
    <script src="assets/js/manage-users.js" defer></script>
</head>

<body>
    <?php include "includes/header.php"; ?>
    <main>
        <!-- Sivuvalikko ylläpitäjän toiminnoille -->
        <aside>
            <h2>Ylläpitäjän hallinta</h2>
            <div class="admin-links">
                <a href="manage-drinks.php">Hallitse drinkkejä</a>
                <a href="manage-users.php">Hallitse käyttäjiä</a>
                <a href="manage-suggestions.php">Hyväksy ehdotuksia</a>
            </div>
        </aside>
        <!-- Käyttäjätaulukko -->
        <div class="table-container">
            <div class="table" id="users-table">
                <div class="table-header">
                    <div class="table-header-item"><span>ID</span></div>
                    <div class="table-header-item"><span>Nimi</span></div>
                    <div class="table-header-item"><span>Sähköposti</span></div>
                    <div class="table-header-item"><span>Toiminnot</span></div>
                </div>
                <!-- Käyttäjärivin mallipohja -->
                <template id="user-row-template">
                    <div class="table-row">
                        <div class="table-item"><span class="user-id"></span></div>
                        <div class="table-item"><span class="user-name"></span></div>
                        <div class="table-item"><span class="user-email"></span></div>
                        <div class="table-item">
                            <button class="delete-btn"><span>Poista</span></button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        <!-- Poistovahvistuksen ponnahdusikkuna -->
        <div class="overlay">
            <div class="popup" id="delete-user-popup">
                <div class="popup-header">
                    <h2>Poista käyttäjä</h2>
                </div>
                <section class="popup-content">
                    <p>Haluatko varmasti poistaa tämän käyttäjän?</p>
                    <div class="popup-actions">
                        <button class="delete-confirm-btn"><span>Poista</span></button>
                        <button class="delete-cancel-btn"><span>Peruuta</span></button>
                    </div>
                </section>
            </div>
        </div>
    </main>
</body>

</html>