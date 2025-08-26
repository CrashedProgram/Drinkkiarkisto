<?php
include_once __DIR__ . '/includes/auth.php';
include_once __DIR__ . '/includes/sanitize.php';
verifyAccess(0);
generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="fi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>rekisteröidy käyttäjäksi</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/register.css">
    <script src="assets/js/register.js" defer></script>
</head>

<body>
    <script>
        window.onload = function() {
            setTimeout(() => {
                document.getElementById("username").focus();
            }, 350);
        };
    </script>

    <?php
    include_once __DIR__ . "/includes/header.php";
    include_once __DIR__ . "/includes/register_logic.php";
    ?>

    <form action="register.php" method="post" id="register-form">
        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
        <div id="account-info">
            <div class="account-info-container">
                <div class="inputs">
                    <div class="input-wrapper">
                        <input class="input-field" id="username" name="username" type="text" placeholder="Käyttäjänimi" autocomplete="username" required>
                        <img class="input-checker-icon" id="username-input-icon" src="assets/icons/input-loader-22px.svg">
                    </div>
                    <div class="input-wrapper">
                        <input class="input-field" id="email" name="email" type="email" placeholder="sähkö@posti.com" autocomplete="email" required>
                        <img class="input-checker-icon" id="email-input-icon" src="assets/icons/input-loader-22px.svg">
                    </div>
                </div>
                <button class="next-button" id="account-info-next-button" type="button">
                    <span class="next-button-text">jatka</span>
                </button>
            </div>
            <a class="login-link" id="login-link-button">kirjaudu sisään</a> <!-- href="login.php" -->
        </div>

        <div id="account-credentials">
            <div class="account-credentials-container">
                <div class="inputs">
                    <div class="input-wrapper">
                        <input class="input-field" id="password" name="password" type="password" placeholder="salasana" autocomplete="new-password" required>
                        <img class="input-checker-icon" id="password-input-icon" src="assets/icons/input-loader-22px.svg">
                    </div>
                    <div class="input-wrapper">
                        <input class="input-field" id="password-confirm" name="password-confirm" type="password" placeholder="vahvista salasana" autocomplete="new-password" required>
                        <img class="input-checker-icon" id="password-confirm-input-icon" src="assets/icons/input-loader-22px.svg">
                    </div>
                </div>
                <button class="next-button" id="account-credentials-next-button" type="button">
                    <span class="next-button-text">rekisteröidy</span>
                </button>
            </div>
            <a class="back-button" id="account-credentials-back">palaa takaisin</a>
        </div>

        <div id="account-agreement">
            <div class="account-agreement-container">
                <div class="account-agreement-text-content">
                    <span class="account-agreement-title">Käyttöehdot ja tietosuojaseloste</span>
                    <span class="account-agreement-text">rekisteröitymällä Drinkkiarkiston käyttäjäksi hyväksyt <a class="account-agreement-link" href="privacy.php">tietosuojaselosteemme</a></span>
                </div>

                <button type="submit" class="next-button" id="account-agreement-next-button">
                    <span class="next-button-text">hyväksyn</span>
                </button>
            </div>
            <a class="back-button" id="account-agreement-back">ei missään nimessä</a>
        </div>
    </form>
</body>

</html>