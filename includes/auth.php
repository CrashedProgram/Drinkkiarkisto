<?php
declare(strict_types=1);

require_once __DIR__ . '/connection.php'; // tietokantayhteys (hieno)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Palauttaa käyttäjän roolin
 * 
 * @return int Käyttäjän rooli (0=vieras, 1=käyttäjä, 2=ylläpitäjä)
 */
function getUserRank(): int
{
    if (isset($_SESSION['user_id'])) {
        global $pdo;
        $stmt = $pdo->prepare('SELECT user_type FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $row = $stmt->fetch();
        if ($row) {
            $_SESSION['user_rank'] = (int)$row['user_type']; // päivitä istunto user_type-sarakkeen arvolla
            return (int)$row['user_type'];
        } else {
            // Käyttäjä ei ole enää tietokannassa, kirjaa käyttäjä ulos
            session_unset();
            session_destroy();
            if (!headers_sent()) {
                header('Location: login.php');
                exit;
            }
        }
    }
    return 0;
}

/**
 * Palauttaa käyttäjän ID:n tai null jos ei kirjautunut
 * 
 * @return int|null Käyttäjän ID tai null
 */
function getUserId(): ?int
{
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

/**
 * Tarkistaa käyttäjän käyttöoikeudet ja ohjaa tarvittaessa kirjautumissivulle
 * 
 * @param int $requiredType Vaadittu käyttöoikeustaso (0=vieras, 1=käyttäjä, 2=ylläpitäjä)
 * @return void
 */
function verifyAccess(int $requiredType = 1): void
{
    if ($requiredType === 0) {
        return;
    }
    if (empty($_SESSION['user_id']) || empty($_SESSION['user_rank'])) {
        header('Location: login.php');
        exit;
    }
    if ($requiredType === 2 && (int)$_SESSION['user_rank'] !== 2) {
        header('Location: index.php');
        exit;
    }
}
