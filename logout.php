<?php
// Sisällytetään tietoturvatoiminnot
require_once __DIR__ . '/includes/sanitize.php';
// Sisällytetään autentikointi
require_once __DIR__ . '/includes/auth.php';

// Vain kirjautuneet käyttäjät voivat kirjautua ulos
verifyAccess(1);

// Hyväksytään sekä GET että POST uloskirjautumiselle
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Location: index.php');
    exit();
}

// Tarkistetaan CSRF-token vain POST-pyynnöille
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken($_POST['csrf_token'] ?? null);
}

// Tyhjennetään istunto turvallisesti
session_start();
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $p["path"], $p["domain"],
        $p["secure"], $p["httponly"]
    );
}
session_destroy();

// Uudelleenohjataan kirjautumissivulle
header("Location: login.php");
exit();
