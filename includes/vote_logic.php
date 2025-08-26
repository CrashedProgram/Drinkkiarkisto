<?php
// Poista kaikki virheilmoitukset, jotta ne eivät sotke JSON-vastauksia
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(0);

// Aseta sisältötyyppi ennen muuta tulostusta
header('Content-Type: application/json; charset=utf-8');

// Korjaa include-polut - niiden tulee olla suhteessa nykyiseen tiedoston sijaintiin
require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/auth.php';
verifyAccess(1);

// Aloita istunto vain jos ei ole jo aktiivinen
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? null;

$input = json_decode(file_get_contents('php://input'), true);
$drink_id = filter_var($input['drink_id'] ?? 0, FILTER_VALIDATE_INT);
$is_like = isset($input['is_like']) ? (bool)$input['is_like'] : null;

if (!$user_id || !$drink_id || $is_like === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

try {
    // Tarkista onko ääni jo olemassa
    $stmt = $pdo->prepare("SELECT is_like FROM drink_votes WHERE user_id = ? AND drink_id = ?");
    $stmt->execute([$user_id, $drink_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        // Päivitä olemassaoleva ääni
        $stmt = $pdo->prepare("UPDATE drink_votes SET is_like = ? WHERE user_id = ? AND drink_id = ?");
        $stmt->execute([$is_like ? 1 : 0, $user_id, $drink_id]);
    } else {
        // Lisää uusi ääni
        $stmt = $pdo->prepare("INSERT INTO drink_votes (user_id, drink_id, is_like) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $drink_id, $is_like ? 1 : 0]);
    }
    
    // Hae päivitetyt laskurit
    $stmt = $pdo->prepare("
        SELECT 
            COALESCE(SUM(is_like = 1), 0) as likes_count,
            COALESCE(SUM(is_like = 0), 0) as dislikes_count
        FROM drink_votes 
        WHERE drink_id = ?
    ");
    $stmt->execute([$drink_id]);
    $counts = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode($counts);
    
} catch (Exception $e) {
    // Kirjaa virhe, mutta älä paljasta yksityiskohtia
    error_log('Äänestyvirhe: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
