<?php
// Sisällytetään autentikointi
include_once __DIR__ . '/includes/auth.php';
// Varmistetaan, että käyttäjä ei ole kirjautunut (käyttäjätaso 0)
verifyAccess(0);
// Sisällytetään kirjautumislogiikka
include_once __DIR__ . '/includes/login_logic.php';
// Sisällytetään syötteiden puhdistus
include_once __DIR__ . '/includes/sanitize.php';
// Luodaan CSRF-token lomakkeelle
generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="fi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>kirjaudu sisään</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <script>
        // Pass login error state to JavaScript
        const loginError = <?php echo isset($loginError) && $loginError ? 'true' : 'false'; ?>;
    </script>
    <script src="assets/js/login.js" defer></script>
</head>

<body>
    <!-- We don't need this auto-focus since it's causing issues -->
    
    <?php include "includes/header.php"; ?>

    <form method="post" action="login.php">
        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
        <div id="login-container">
            <div class="login">
                <div class="inputs">
                    <div class="input-wrapper">
                        <input class="input-field" id="username" name="username" type="text" placeholder="Käyttäjänimi" required>
                        <img class="input-checker-icon" id="username-input-icon" src="assets/icons/input-loader-22px.svg" style="opacity: 0;">
                    </div>
                    <div class="input-wrapper">
                        <input class="input-field" id="password" name="password" type="password" placeholder="••••••••" required>
                        <img class="input-checker-icon" id="password-input-icon" src="assets/icons/input-loader-22px.svg" style="opacity: 0;">
                    </div>
                </div>
                <button class="submit-button" id="submit-button" type="submit">
                    <span class="submit-button-text">kirjaudu sisään</span>
                </button>
            </div>
            <a class="register-link" href="register.php" id="register-link-button">rekisteröidy</a>
        </div>
    </form>
</body>

</html>