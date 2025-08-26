<?php
require_once __DIR__ . '/sanitize.php';
require_once __DIR__ . '/auth.php';

// 1) CSRF-tarkistus
verifyCsrfToken($_POST['csrf_token'] ?? null);

require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/validation_functions.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 2) Puhdista käyttäjän syötteet
    $username       = sanitize($_POST["username"]);
    $email          = sanitize($_POST["email"]);
    $password       = sanitize($_POST["password"]);
    $passwordConfirm= sanitize($_POST["password-confirm"]);

    // 3) Validoi syötteet
    $usernameValid = isUsernameValid($username);
    $emailValid    = isEmailValid($email);
    $passwordValid = isPasswordValid($password, $passwordConfirm);

    if ($usernameValid && $emailValid && $passwordValid) {
        // 4) Salaa salasana
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // 5) Lisää käyttäjä tietokantaan
        $stmt = $pdo->prepare(
            "INSERT INTO users (username, password_hash, email) VALUES (?, ?, ?)"
        );
        $stmt->execute([$username, $hash, $email]);

        if ($stmt->rowCount() > 0) {
            // 6) Hae uuden käyttäjän rooli ja aloita istunto
            $userId = $pdo->lastInsertId();
            $stmt2  = $pdo->prepare("SELECT user_type FROM users WHERE id = ?");
            $stmt2->execute([$userId]);
            $user = $stmt2->fetch(PDO::FETCH_ASSOC);

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user_id']   = $userId;
            $_SESSION['user_rank'] = (int)($user['user_type'] ?? 1);

            // 7) Ohjaa etusivulle
            header("Location: index.php");
            exit();
        } else {
            // Lisäys epäonnistui
            echo "Virhe: Käyttäjän rekisteröinti epäonnistui.";
        }
    }
}
