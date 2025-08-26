<?php
declare(strict_types=1);
require_once __DIR__ . '/connection.php';

/**
 * Tarkistaa onko käyttäjänimi vapaana
 * 
 * @param string $username Tarkistettava käyttäjänimi
 * @return bool True jos käyttäjänimi on vapaana
 */
function isUsernameValid(string $username): bool
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = :username');
    $stmt->execute(['username' => $username]);
    return ((int)$stmt->fetchColumn()) === 0;
}

/**
 * Tarkistaa onko sähköpostiosoite kelvollinen
 * 
 * @param string $email Tarkistettava sähköpostiosoite
 * @return bool True jos sähköpostiosoite on kelvollinen
 */
function isEmailValid(string $email): bool
{
    global $pdo;
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Tarkistaa vastaako annettu salasana toista salasanaa
 * 
 * @param string $confirm Varmistettava salasana
 * @param string $original Alkuperäinen salasana
 * @return bool True jos salasanat täsmäävät
 */
function isPasswordValid(string $confirm, string $original): bool
{
    return $confirm === $original;
}
