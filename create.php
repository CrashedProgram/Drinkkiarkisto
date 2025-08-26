<?php
// Sisällytetään tietokantayhteys
require_once __DIR__ . "/includes/connection.php";
// Sisällytetään autentikointi
require_once __DIR__ . "/includes/auth.php";
// Varmistetaan, että käyttäjä on kirjautunut (käyttäjätaso vähintään 1)
verifyAccess(1);

// Sisällytetään syötteiden puhdistus
include_once __DIR__ . '/includes/sanitize.php';
// Luodaan CSRF-token lomakkeelle
generateCsrfToken();

// Haetaan juomakategoriat alasvetovalikkoa varten
$stmt = $pdo->prepare("SELECT id, name FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etusivu</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/create.css">
    <script src="assets/js/create.js" defer></script>
</head>

<body>
    <?php include "includes/header.php"; ?>

    <main>
        <div class="create-recipe-container">
            <h1>Ehdota juomaa</h1>

            <!-- Virheiden ja onnistumisten näyttäminen -->
            <div id="create-error" class="error-message" style="display:none"></div>
            <div id="create-success" class="success-message" style="display:none"></div>

            <form class="create-recipe-form" method="post">

                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

                <!-- Juoman perustiedot -->
                <div class="inputs">
                    <span class="input-label">Juoman tiedot</span>
                    <div class="input-wrapper">
                        <input class="input-field" type="text" id="drink-name" name="drink-name" placeholder="Juoman nimi" required>
                        <img class="input-checker-icon" id="ingredient-name-input-icon" src="assets/icons/input-loader-22px.svg">
                    </div>
                    <div class="input-wrapper">
                        <select class="select-field" id="drink-category" name="drink-category" required>
                            <option value="" disabled selected>Kategoria</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat['id']); ?>">
                                    <?= htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <img class="input-checker-icon" id="ingredient-category-input-icon" src="assets/icons/input-loader-22px.svg">
                    </div>
                    <div class="checkbox-wrapper">
                        <input class="checkbox-field" type="checkbox" id="has-allergens" name="has-allergens">
                        <label for="has-allergens">sisältää allergeenejä</label>
                    </div>
                </div>

                <!-- Ainesosat -->
                <div class="inputs">
                    <span class="input-label">Ainesosat</span>

                    <div class="input-wrapper ingredient-wrapper">
                        <input class="ingredient-name-field" type="text" name="ingredient-name[]" placeholder="Ainesosan nimi" required>
                        <input class="ingredient-quantity-field" type="number" name="ingredient-quantity[]" placeholder="0" min="0" step="any" required>
                        <select class="ingredient-unit-field" name="ingredient-unit[]" required>
                            <option value="ml" selected>ml</option>
                            <option value="cl">cl</option>
                            <option value="l">l</option>
                            <option value="kpl">kpl</option>
                            <option value="tl">tl</option>
                            <option value="rkl">rkl</option>
                        </select>
                        <img class="ingredient-delete-icon" src="assets/icons/remove-22px.svg" alt="Poista">
                    </div>

                    <button type="button" class="add-ingredient-button">
                        <span>Lisää ainesosa</span>
                    </button>
                </div>

                <!-- Valmistusohjeet -->
                <div class="inputs">
                    <span class="input-label">Ohjeet</span>
                    <textarea class="textarea-field" name="instructions" placeholder="Kirjoita ohjeet tänne" required></textarea>
                </div>

                <!-- Välitön hyväksyntä (vain ylläpitäjille) -->
                <?php if (getUserRank() === 2): ?>
                    <div class="checkbox-wrapper">
                        <input class="checkbox-field" type="checkbox" id="approve-instantly" name="approve-instantly">
                        <label for="approve-instantly">Hyväksy välittömästi</label>
                    </div>
                <?php endif; ?>

                <button type="submit">
                    <span class="button-text">Ehdota reseptiä</span>
                </button>
            </form>
        </div>
    </main>
</body>

</html>