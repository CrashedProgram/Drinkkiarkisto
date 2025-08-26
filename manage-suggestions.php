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
    <title>Hyväksy ehdotuksia</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/manage.css">
    <script src="assets/js/manage-suggestions.js" defer></script>
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
        <!-- Ehdotustaulukko -->
        <div class="table-container">
            <div class="table" id="suggestions-table">
                <div class="table-header">
                    <div class="table-header-item"><span>ID</span></div>
                    <div class="table-header-item"><span>Nimi</span></div>
                    <div class="table-header-item"><span>Ehdottaja</span></div>
                    <div class="table-header-item"><span>Luotu</span></div>
                    <div class="table-header-item"><span>Toiminnot</span></div>
                </div>
                <!-- Ehdotusrivin mallipohja -->
                <template id="suggestion-row-template">
                    <div class="table-row">
                        <div class="table-item"><span class="suggestion-id"></span></div>
                        <div class="table-item"><span class="suggestion-name"></span></div>
                        <div class="table-item"><span class="suggestion-by"></span></div>
                        <div class="table-item"><span class="suggestion-date"></span></div>
                        <div class="table-item">
                            <button class="approve-btn"><span>Hyväksy</span></button>
                            <button class="delete-btn"><span>Poista</span></button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </main>
</body>

</html>