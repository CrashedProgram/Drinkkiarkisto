<?php
// Näytä virheet kehityksen aikana
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/sanitize.php';
require_once __DIR__ . '/auth.php';
verifyAccess(2);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => true, 'message' => 'Virheellinen pyyntötapa']);
    exit;
}

require_once __DIR__ . '/connection.php';

$input = file_get_contents('php://input');
$data  = json_decode($input, true);

$action = trim((string)($data['action'] ?? ''));

/**
 * Hakee kaikki käyttäjät
 *
 * @param PDO $pdo Tietokantayhteys
 * @return void
 */
function getUsers($pdo)
{
    try {
        $stmt = $pdo->query("SELECT id, username, email, user_type AS access_level FROM users");
        echo json_encode($stmt->fetchAll());
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => true, 'message' => 'Käyttäjien haku epäonnistui']);
    }
}

/**
 * Hakee kaikki juomat
 *
 * @param PDO $pdo Tietokantayhteys
 * @return void
 */
function getDrinks($pdo)
{
    try {
        $stmt = $pdo->query("
            SELECT 
                d.id, 
                d.name, 
                c.name AS category, 
                d.category_id, 
                d.author_id AS added_by,
                d.has_allergens
            FROM drinks d 
            LEFT JOIN categories c 
                ON d.category_id = c.id
        ");
        echo json_encode($stmt->fetchAll());
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => true, 'message' => 'Juomien haku epäonnistui']);
    }
}

/**
 * Hakee kaikki juomaehdotukset
 *
 * @param PDO $pdo Tietokantayhteys
 * @return void
 */
function getDrinkSuggestions($pdo)
{
    try {
        $stmt = $pdo->query("
            SELECT id, name, author_id AS suggested_by, created_at
            FROM drinks
            WHERE is_approved = 0
        ");
        echo json_encode($stmt->fetchAll());
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => true, 'message' => 'Ehdotusten haku epäonnistui']);
    }
}

/**
 * Päivittää juoman tiedot
 *
 * @param PDO $pdo Tietokantayhteys
 * @param array $data Juoman tiedot
 * @return void
 */
function updateDrink($pdo, $data)
{
    try {
        $stmt = $pdo->prepare("
            UPDATE drinks 
            SET name = :name, has_allergens = :has_allergens 
            WHERE id = :id
        ");
        $stmt->execute([
            ':name'          => $data['name'],
            ':has_allergens' => $data['hasAllergens'] ? 1 : 0,
            ':id'            => (int)$data['id']
        ]);
        echo json_encode(['error' => false, 'message' => 'Juoma päivitetty']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => true, 'message' => 'Juoman päivitys epäonnistui']);
    }
}

/**
 * Poistaa juoman
 *
 * @param PDO $pdo Tietokantayhteys
 * @param array $data Juoman tiedot
 * @return void
 */
function deleteDrink($pdo, $data)
{
    try {
        $stmt = $pdo->prepare("DELETE FROM drinks WHERE id = :id");
        $stmt->execute([':id' => (int)$data['id']]);
        echo json_encode(['error' => false, 'message' => 'Juoma poistettu']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => true, 'message' => 'Juoman poisto epäonnistui']);
    }
}

/**
 * Poistaa käyttäjän
 *
 * @param PDO $pdo Tietokantayhteys
 * @param array $data Käyttäjän tiedot
 * @return void
 */
function deleteUser($pdo, $data)
{
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute([':id' => (int)$data['id']]);
        echo json_encode(['error' => false, 'message' => 'Käyttäjä poistettu']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => true, 'message' => 'Käyttäjän poisto epäonnistui']);
    }
}

/**
 * Hyväksyy juomaehdotuksen
 *
 * @param PDO $pdo Tietokantayhteys
 * @param array $data Ehdotuksen tiedot
 * @return void
 */
function approveSuggestion($pdo, $data)
{
    try {
        $stmt = $pdo->prepare("
            UPDATE drinks
            SET is_approved = 1
            WHERE id = :id
        ");
        $stmt->execute([':id' => (int)$data['id']]);
        echo json_encode(['error' => false, 'message' => 'Ehdotus hyväksytty']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => true, 'message' => 'Ehdotuksen hyväksyminen epäonnistui']);
    }
}

/**
 * Poistaa juomaehdotuksen
 *
 * @param PDO $pdo Tietokantayhteys
 * @param array $data Ehdotuksen tiedot
 * @return void
 */
function deleteSuggestion($pdo, $data)
{
    try {
        $stmt = $pdo->prepare("DELETE FROM drinks WHERE id = :id");
        $stmt->execute([':id' => (int)$data['id']]);
        echo json_encode(['error' => false, 'message' => 'Ehdotus poistettu']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => true, 'message' => 'Ehdotuksen poisto epäonnistui']);
    }
}

// Käsittele toiminto
switch ($action) {
    case 'getUsers':
        getUsers($pdo);
        break;

    case 'getDrinks':
        getDrinks($pdo);
        break;

    case 'getDrinkSuggestions':
        getDrinkSuggestions($pdo);
        break;

    case 'updateDrink':
        updateDrink($pdo, $data);
        break;

    case 'deleteDrink':
        deleteDrink($pdo, $data);
        break;

    case 'deleteUser':
        deleteUser($pdo, $data);
        break;

    case 'approveSuggestion':
        approveSuggestion($pdo, $data);
        break;

    case 'deleteSuggestion':
        deleteSuggestion($pdo, $data);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => true, 'message' => 'Virheellinen toiminto']);
        break;
}

exit;
