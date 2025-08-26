<?php

// Näytä virheet kehityksen aikana
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/sanitize.php';
require_once __DIR__ . '/auth.php';
verifyAccess(1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([]);
    exit;
}

require_once __DIR__ . '/connection.php';

$input = file_get_contents('php://input');
$data  = json_decode($input, true);

$query  = trim((string)($data['query'] ?? ''));
$filter = trim((string)($data['filter'] ?? 'all'));

// Tarkista suodattimen valinta
$allowed = ['all', 'drinks', 'description', 'categories', 'name', 'ingredient'];
if (!in_array($filter, $allowed, true)) {
    $filter = 'all';
}

$param = '%' . sanitize($query) . '%';
$approved = 'd.is_approved = 1';

// Rakenna SQL-kyselyt suodattimen perusteella
switch ($filter) {
    case 'name':
        $sql = "
            SELECT 
                d.id,
                d.name,
                c.name AS category,
                d.recipe_notes,
                d.has_allergens AS has_allergens,
                /* Hae äänet drink_votes-taulusta */
                (SELECT COUNT(*) FROM drink_votes dv WHERE dv.drink_id = d.id AND dv.is_like = 1) AS likes_count,
                (SELECT COUNT(*) FROM drink_votes dv WHERE dv.drink_id = d.id AND dv.is_like = 0) AS dislikes_count,
                /* Laske ainesosat */
                (SELECT COUNT(*) FROM recipe_ingredients ri WHERE ri.drink_id = d.id) AS ingredient_count
            FROM 
                drinks d
            LEFT JOIN 
                categories c ON d.category_id = c.id
            WHERE 
                d.is_approved = 1
                AND d.name LIKE :param
        ";
        break;

    case 'ingredient':
        $sql = "
            SELECT DISTINCT
                d.id,
                d.name,
                c.name AS category,
                d.recipe_notes,
                d.has_allergens AS has_allergens,
                /* Hae äänet drink_votes-taulusta */
                (SELECT COUNT(*) FROM drink_votes dv WHERE dv.drink_id = d.id AND dv.is_like = 1) AS likes_count,
                (SELECT COUNT(*) FROM drink_votes dv WHERE dv.drink_id = d.id AND dv.is_like = 0) AS dislikes_count,
                /* Laske ainesosat */
                (SELECT COUNT(*) FROM recipe_ingredients ri WHERE ri.drink_id = d.id) AS ingredient_count
            FROM 
                drinks d
            LEFT JOIN 
                categories c ON d.category_id = c.id
            JOIN 
                recipe_ingredients ri ON d.id = ri.drink_id
            JOIN 
                ingredients i ON ri.ingredient_id = i.id
            WHERE 
                d.is_approved = 1
                AND i.name LIKE :param
        ";
        break;

    case 'drinks':
        $sql = "
            SELECT 
                d.id,
                d.name,
                c.name AS category,
                d.recipe_notes,
                d.has_allergens AS has_allergens,
                /* Hae äänet drink_votes-taulusta */
                (SELECT COUNT(*) FROM drink_votes dv WHERE dv.drink_id = d.id AND dv.is_like = 1) AS likes_count,
                (SELECT COUNT(*) FROM drink_votes dv WHERE dv.drink_id = d.id AND dv.is_like = 0) AS dislikes_count,
                /* Laske ainesosat */
                (SELECT COUNT(*) FROM recipe_ingredients ri WHERE ri.drink_id = d.id) AS ingredient_count
            FROM 
                drinks d
            LEFT JOIN 
                categories c ON d.category_id = c.id
            WHERE 
                d.is_approved = 1
                AND d.name LIKE :param
        ";
        break;

    case 'description':
        $sql = "
            SELECT DISTINCT
                d.id,
                d.name,
                c.name AS category,
                d.recipe_notes,
                d.has_allergens AS has_allergens,
                /* Hae äänet drink_votes-taulusta */
                (SELECT COUNT(*) FROM drink_votes dv WHERE dv.drink_id = d.id AND dv.is_like = 1) AS likes_count,
                (SELECT COUNT(*) FROM drink_votes dv WHERE dv.drink_id = d.id AND dv.is_like = 0) AS dislikes_count,
                /* Laske ainesosat */
                (SELECT COUNT(*) FROM recipe_ingredients ri WHERE ri.drink_id = d.id) AS ingredient_count
            FROM 
                drinks d
            LEFT JOIN 
                categories c ON d.category_id = c.id
            WHERE 
                d.is_approved = 1
                AND d.recipe_notes LIKE :param
        ";
        break;

    case 'categories':
        $sql = "
            SELECT 
                d.id,
                d.name,
                c.name AS category,
                d.recipe_notes,
                d.has_allergens AS has_allergens,
                /* Hae äänet drink_votes-taulusta */
                (SELECT COUNT(*) FROM drink_votes dv WHERE dv.drink_id = d.id AND dv.is_like = 1) AS likes_count,
                (SELECT COUNT(*) FROM drink_votes dv WHERE dv.drink_id = d.id AND dv.is_like = 0) AS dislikes_count,
                /* Laske ainesosat */
                (SELECT COUNT(*) FROM recipe_ingredients ri WHERE ri.drink_id = d.id) AS ingredient_count
            FROM 
                drinks d
            JOIN 
                categories c ON d.category_id = c.id
            WHERE 
                d.is_approved = 1
                AND c.name LIKE :param
        ";
        break;

    case 'all':
    default:
        $sql = "
            SELECT DISTINCT
                d.id,
                d.name,
                c.name AS category,
                d.recipe_notes,
                d.has_allergens AS has_allergens,
                /* Hae äänet drink_votes-taulusta */
                (SELECT COUNT(*) FROM drink_votes dv WHERE dv.drink_id = d.id AND dv.is_like = 1) AS likes_count,
                (SELECT COUNT(*) FROM drink_votes dv WHERE dv.drink_id = d.id AND dv.is_like = 0) AS dislikes_count,
                /* Laske ainesosat */
                (SELECT COUNT(*) FROM recipe_ingredients ri WHERE ri.drink_id = d.id) AS ingredient_count
            FROM 
                drinks d
            LEFT JOIN 
                categories c ON d.category_id = c.id
            LEFT JOIN 
                recipe_ingredients ri ON d.id = ri.drink_id
            LEFT JOIN 
                ingredients i ON ri.ingredient_id = i.id
            WHERE 
                d.is_approved = 1
                AND (
                    d.name LIKE :param_name
                    OR c.name LIKE :param_category
                    OR i.name LIKE :param_ingredient
                    OR d.recipe_notes LIKE :param_description
                )
        ";
        break;
}

$params = [':param' => $param];
if ($filter === 'all') {
    $params = [
        ':param_name' => $param,
        ':param_category' => $param,
        ':param_ingredient' => $param,
        ':param_description' => $param
    ];
}

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode($stmt->fetchAll());
} catch (PDOException $e) {
    http_response_code(500);

    echo json_encode([
        'error' => true,
        'message' => 'Palvelinvirhe tapahtui. Yritä myöhemmin uudelleen.'
    ]);
}

exit;
