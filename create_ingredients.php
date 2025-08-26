<?php
// Sisällytetään tietokantayhteys
include __DIR__ . '/includes/connection.php';
// Sisällytetään syötteiden puhdistus ja CSRF-suojaus
include_once __DIR__ . '/includes/sanitize.php';
// Luodaan CSRF-token lomakkeelle
generateCsrfToken();
// Haetaan kaikki ainesosat tietokannasta aakkosjärjestyksessä
$stmt = $pdo->query('SELECT id, name FROM ingredients ORDER BY name');
$ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etusivu</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/create_ingredients.css">
</head>

<body>
    <?php include "includes/header.php"; ?>

    <main>
        <div class="create-ingredient-container">
            <h2>Lisää ainesosa</h2>
            <form class="create-ingredient-form" id="create-ingredient-form" action="includes/create_ingredients_logic.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <div class="inputs">
                    <span class="input-label">Ainesosan nimi</span>
                    <div class="input-wrapper">
                        <input class="input-field" type="text" id="ingredient-name" name="ingredient-name" placeholder="Ainesosan nimi" required>
                    </div>
                </div>
                <button>
                    <span class="button-text">Lisää ainesosa</span>
                </button>
            </form>
        </div>

        <div class="added-ingredients-container">
            <h2>Lisätyt ainekset</h2>
            <div class="ingredients-list">
                <?php foreach ($ingredients as $ing): ?>
                    <span class="ingredient-item" data-id="<?= $ing['id'] ?>">
                        <?= htmlspecialchars($ing['name'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

</body>

</html>