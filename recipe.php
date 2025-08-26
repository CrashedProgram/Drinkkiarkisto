<?php
// Sisällytetään autentikointi
include_once __DIR__ . '/includes/auth.php';
// Varmistetaan, että käyttäjä on kirjautunut (käyttäjätaso vähintään 1)
verifyAccess(1);
// Ladataan juoman tiedot ja ainesosat
include_once __DIR__ . '/recipe_logic.php';
?>
<!DOCTYPE html>
<html lang="fi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hae drinkkejä</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/recipe.css">
</head>

<body>

    <?php include "includes/header.php"; ?>

    <main>
        <div class="recipe-container">
            <!-- Palaa hakuun -painike -->
            <section class="return-section">
                <div class="return-container">
                    <a href="search.php" class="return-button">
                        <img src="assets/icons/chevron-left-22px-green.svg" alt="palaa hakuun">
                        <a href="search.php">palaa hakuun</a>
                    </a>
                </div>

            </section>
            <!-- Juoman perustiedot -->
            <section class="info-section">
                <span class="name"><?php echo htmlspecialchars($drink['name']); ?></span>
                <span class="general-info"><?php echo htmlspecialchars($drink['category']); ?></span>
            </section>
            <!-- Arviointi (tykkää/en tykkää) -->
            <section class="rating-section">
                <button class="like-button" data-drink-id="<?php echo $drink['id']; ?>"
                    <?php if ($drink['user_vote'] === 1) echo 'disabled'; ?>
                    aria-pressed="<?php echo ($drink['user_vote'] === 1) ? 'true' : 'false'; ?>"
                    aria-label="Tykkää">
                    <img src="assets/icons/like-22px.svg" alt="like">
                </button>
                <span class="rating">
                    <?php echo $drink['likes_count'] . '/' . $drink['dislikes_count']; ?>
                </span>
                <button class="dislike-button" data-drink-id="<?php echo $drink['id']; ?>"
                    <?php if ($drink['user_vote'] === 0) echo 'disabled'; ?>
                    aria-pressed="<?php echo ($drink['user_vote'] === 0) ? 'true' : 'false'; ?>"
                    aria-label="En tykkää">
                    <img src="assets/icons/dislike-22px.svg" alt="dislike">
                </button>
            </section>
            <!-- Ainesosat -->
            <section class="ingredients-section">
                <span>ainesosat</span>
                <div class="ingredients-list">
                    <?php foreach ($ingredients as $ing): ?>
                        <span>
                            <?php echo htmlspecialchars($ing['name'] . ' ' . $ing['amount'] . $ing['unit']); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </section>
            <!-- Valmistusohjeet -->
            <section class="instructions-section">
                <span>ohjeet</span>

                <p class="instructions"><?php echo nl2br(htmlspecialchars($drink['recipe_notes'])); ?></p>

            </section>
        </div>
    </main>
    <script src="assets/js/recipe.js"></script>
</body>

</html>