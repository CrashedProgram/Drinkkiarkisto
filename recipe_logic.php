<?php
// Piilotetaan virheilmoitukset
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
// Sisällytetään tietokantayhteys
require_once __DIR__ . '/includes/connection.php';
// Sisällytetään autentikointi
require_once __DIR__ . '/includes/auth.php';
// Varmistetaan, että käyttäjä on kirjautunut (käyttäjätaso vähintään 1)
verifyAccess(1);

// Haetaan ja validoidaan juoman ID URL-parametrista
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    exit('Virheellinen juoma-ID');
}

// Haetaan juoman tiedot
$sql = "
    SELECT d.id,
           d.name,
           c.name AS category,
           d.recipe_notes,
           d.has_allergens,
           /* Lasketaan tykkäykset ja ei-tykkäykset drink_votes-taulusta */
           (SELECT COUNT(*) FROM drink_votes dv WHERE dv.drink_id = d.id AND dv.is_like = 1) AS likes_count,
           (SELECT COUNT(*) FROM drink_votes dv WHERE dv.drink_id = d.id AND dv.is_like = 0) AS dislikes_count
    FROM drinks d
    LEFT JOIN categories c ON d.category_id = c.id
    WHERE d.id = :id AND d.is_approved = 1
";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$drink = $stmt->fetch();
if (!$drink) {
    http_response_code(404);
    exit('Juomaa ei löytynyt');
}

// Haetaan juoman ainesosat
$sql2 = "
    SELECT i.name, ri.amount, ri.unit
    FROM recipe_ingredients ri
    JOIN ingredients i ON ri.ingredient_id = i.id
    WHERE ri.drink_id = :id
";
$stmt2 = $pdo->prepare($sql2);
$stmt2->execute([':id' => $id]);
$ingredients = $stmt2->fetchAll();

// Lisätään käyttäjän mahdollinen äänestys
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$user_id = $_SESSION['user_id'] ?? null;
if ($user_id) {
    $sql3 = "
        SELECT is_like
        FROM drink_votes
        WHERE drink_id = :id AND user_id = :uid
    ";
    $stmt3 = $pdo->prepare($sql3);
    $stmt3->execute([':id' => $id, ':uid' => $user_id]);
    $vote = $stmt3->fetch();
    $drink['user_vote'] = $vote !== false ? (int)$vote['is_like'] : null;
} else {
    $drink['user_vote'] = null;
}
