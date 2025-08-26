<?php
declare(strict_types=1);

require_once __DIR__ . '/sanitize.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/connection.php';

$uniqueNamesEnabled = false;

// Käsittele JSON POST-sisältö
if (stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
        $_POST = $data;
    }
}

// CSRF-suojaus
verifyCsrfToken($_POST['csrf_token'] ?? null);

global $pdo;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['drink-name'] ?? '');
    $categoryId = sanitize($_POST['drink-category'] ?? '');
    $hasAllergens = isset($_POST['has-allergens']) ? 1 : 0;
    $instructions = sanitize($_POST['instructions'] ?? '');
    $approveInstantly = isset($_POST['approve-instantly']) ? 1 : 0;
    $ingredientNames = sanitize($_POST['ingredient-name'] ?? []);
    $ingredientQuantities = sanitize($_POST['ingredient-quantity'] ?? []);
    $ingredientUnits = sanitize($_POST['ingredient-unit'] ?? []);

    // Tarkista onko juoman nimi jo käytössä, jos ominaisuus on päällä
    if ($uniqueNamesEnabled) {
        $chk = $pdo->prepare("SELECT COUNT(*) FROM drinks WHERE name = :n");
        $chk->execute([':n' => $name]);
        if ($chk->fetchColumn() > 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'name_taken']);
            exit;
        }
    }

    try {
        // Lisää juoma
        $stmt = $pdo->prepare("
            INSERT INTO drinks
                (name, category_id, author_id, has_allergens, recipe_notes, is_approved)
            VALUES
                (:name, :cat, :auth, :all, :notes, :is_approved)
        ");
        $result = $stmt->execute([
            ':name' => $name,
            ':cat' => $categoryId,
            ':auth' => getUserId(),
            ':all' => $hasAllergens,
            ':notes' => $instructions,
            ':is_approved' => $approveInstantly,
        ]);

        $drinkId = $pdo->lastInsertId();

        $selectIngStmt = $pdo->prepare("SELECT id FROM ingredients WHERE name = :n");
        $insertIngStmt = $pdo->prepare("INSERT INTO ingredients (name) VALUES (:n)");
        $recipeStmt = $pdo->prepare("
            INSERT INTO recipe_ingredients
                (drink_id, ingredient_id, amount, unit)
            VALUES (:d, :i, :a, :u)
        ");

        // Lisää ainesosat juomaan ilman duplikaatteja
        foreach ($ingredientNames as $i => $ingName) {
            $n = trim($ingName);
            if ($n === '') {
                continue;
            }

            // Etsi olemassaoleva ainesosa
            $selectIngStmt->execute([':n' => $n]);
            $row = $selectIngStmt->fetch();
            if ($row) {
                $ingId = (int)$row['id'];
            } else {
                $insertIngStmt->execute([':n' => $n]);
                $ingId = (int)$pdo->lastInsertId();
            }

            $amount = $ingredientQuantities[$i] ?? null;
            $unit = $ingredientUnits[$i] ?? null;

            $recipeStmt->execute([
                ':d' => $drinkId,
                ':i' => $ingId,
                ':a' => $amount,
                ':u' => $unit
            ]);
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Resepti ehdotettu onnistuneesti!'
        ]);
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'database_error'
        ]);
    }
    exit;
}