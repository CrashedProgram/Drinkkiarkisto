<?php
// Jos tämä koodi hajoaa, olemme kaikki tuhon omia.
// Juoskaa. Piiloutukaa. Itkekää.

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Puhdistaa syötteen haitallisesta koodista
 *
 * @param mixed $data Puhdistettava data
 * @return mixed Puhdistettu data
 */
function sanitize($data)
{
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    $s = (string)$data;
    $s = trim($s);
    $s = stripslashes($s);
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Luo CSRF-tokenin istuntoon
 *
 * @return string CSRF-token
 */
function generateCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Tarkistaa CSRF-tokenin oikeellisuuden
 *
 * @param string|null $token Tarkistettava token
 * @return void
 */
function verifyCsrfToken(?string $token): void
{
    // Tarkista vain POST-pyynnöissä
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }
    if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(400);
        exit('Virheellinen CSRF-token');
    }
}
