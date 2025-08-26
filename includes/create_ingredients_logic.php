<?php
declare(strict_types=1);

require_once __DIR__ . '/sanitize.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/connection.php';

header('Content-Type: application/json; charset=utf-8');

// CSRF AJAX-lomakkeelle
verifyCsrfToken($_POST['csrf_token'] ?? null);

// Vain POST-pyynnöt sallittu
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Virheellinen pyyntötapa']);
    exit;
}

// Puhdista nimi
$name = sanitize($_POST['ingredient-name'] ?? '');
if ($name === '') {
    echo json_encode(['success' => false, 'error' => 'Nimi ei voi olla tyhjä']);
    exit;
}

try {
    // Lisää (tai ohita duplikaatit)
    $ins = $pdo->prepare('INSERT INTO ingredients (name) VALUES (?)');
    $ins->execute([$name]);
    $id = $pdo->lastInsertId();

    // Palauta JSON käyttöliittymälle
    echo json_encode([
        'success' => true,
        'data' => ['id' => $id, 'name' => $name]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Tietokantavirhe']);
}
exit;
