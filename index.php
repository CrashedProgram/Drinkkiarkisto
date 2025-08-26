<?php
// Sisällytetään autentikointi
include_once __DIR__ . '/includes/auth.php';
// Jos käyttäjä on jo kirjautunut, ohjataan hakusivulle
if (getUserRank() > 0) {
    header('Location: search.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etusivu</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/index.css">
</head>

<body>
    <?php include "includes/header.php"; ?>

    <!-- Jatka-painikkeen toiminnallisuus ohjaa kirjautumissivulle -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const button = document.getElementById("continue-button")
            button.addEventListener("click", function() {
                window.location.href = "login.php"
            });
        });
    </script>

    <main>
        <div class="container">
            <h2>Tervetuloa</h2>
            <span>Jatka kirjautumalla sisään</span>
            <button id="continue-button">
                <span>jatka</span>
            </button>
        </div>
    </main>


</body>

</html>