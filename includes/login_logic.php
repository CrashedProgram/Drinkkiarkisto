<?php
declare(strict_types=1);

require_once __DIR__ . '/sanitize.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/connection.php';

// parse JSON body if present
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
$isAjax = json_last_error() === JSON_ERROR_NONE && is_array($data);

if ($isAjax) {
    // map JSON to POST-like vars for CSRF check
    $_POST['csrf_token'] = $data['csrf_token'] ?? null;
    $username = sanitize((string)($data['username']  ?? ''));
    $password = sanitize((string)($data['password']  ?? ''));
} else {
    $username = sanitize($_POST['username'] ?? '');
    $password = sanitize($_POST['password'] ?? '');
}

// CSRF-tarkistus
verifyCsrfToken($_POST['csrf_token'] ?? null);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $stmt = $pdo->prepare(
        "SELECT id, password_hash, user_type FROM users WHERE username = :username"
    );
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_rank'] = (int)$user['user_type'];

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => true]);
            exit;
        }
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    error_log('Kirjautumisvirhe: ' . $e->getMessage());
}

// failed login
if ($isAjax) {
    // Return 200 OK but with success:false instead of 401
    // This makes it easier for the frontend to handle
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false]);
    exit;
}

// fallback for normal form submit
$loginError = true;
