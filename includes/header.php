<?php
include_once __DIR__ . '/auth.php';
// (0 = vieras, 1 = käyttäjä, 2 = ylläpitäjä)
$permission = getUserRank();
$currentPage = basename($_SERVER['SCRIPT_NAME']);
?>

<header>
    <div class="header-container">
        <div class="header-branding">
            <div class="header-hamburger" id="menu-toggle">
                <img src="assets/icons/menu-22px.svg" alt="menu" class="hamburger-icon">
            </div>

            <a href="index.php" class="header-logo">
                <span class="brand-title">
                    drinkkiarkisto
                </span>
            </a>
        </div>

        <div class="nav-container">
            <nav>
                <?php switch ($permission):
                    case 1: // käyttäjä
                        ?>
                        <a href="search.php" <?php echo ($currentPage == 'search.php') ? 'class="active"' : ''; ?>>haku</a>
                        <a href="create.php" <?php echo ($currentPage == 'create.php') ? 'class="active"' : ''; ?>>lisää juoma</a>
                        <?php
                        break;
                    case 2: // ylläpitäjä
                        ?>
                        <a href="search.php" <?php echo ($currentPage == 'search.php') ? 'class="active"' : ''; ?>>haku</a>
                        <a href="create.php" <?php echo ($currentPage == 'create.php') ? 'class="active"' : ''; ?>>ehdota juomaa</a>
                        <a href="manage-drinks.php" <?php echo ($currentPage == 'manage-drinks.php') ? 'class="active"' : ''; ?>>ylläpitäjän hallintapaneeli</a>
                        <?php
                        break;
                    default: // vieras
                        ?>
                        <a href="login.php" <?php echo ($currentPage == 'login.php') ? 'class="active"' : ''; ?>>kirjaudu sisään</a>
                        <a href="register.php" <?php echo ($currentPage == 'register.php') ? 'class="active"' : ''; ?>>rekisteröidy</a>
                        <?php
                        break;
                endswitch; ?>
                <?php if ($permission === 1 || $permission === 2): ?>
                    <a href="logout.php"
                       class="<?php echo ($currentPage == 'logout.php') ? 'active' : ''; ?>">kirjaudu ulos
                    </a>
                <?php endif; ?>
            </nav>
        </div>

        <!-- Mobiilivalikko -->
        <div class="mobile-menu" id="mobile-menu">
            <?php switch ($permission):
                case 1: // käyttäjä
                    ?>
                    <a href="search.php" <?php echo ($currentPage == 'search.php') ? 'class="active"' : ''; ?>>haku</a>
                    <a href="create.php" <?php echo ($currentPage == 'create.php') ? 'class="active"' : ''; ?>>ehdota juoma</a>
                    <?php
                    break;
                case 2: // ylläpitäjä
                    ?>
                    <a href="search.php" <?php echo ($currentPage == 'search.php') ? 'class="active"' : ''; ?>>haku</a>
                    <a href="create.php" <?php echo ($currentPage == 'create.php') ? 'class="active"' : ''; ?>>lisää juoma</a>
                    <a href="manage-drinks.php" <?php echo ($currentPage == 'manage-drinks.php') ? 'class="active"' : ''; ?>>ylläpitäjän hallintapaneeli</a>
                    <?php
                    break;
                default: // vieras
                    ?>
                    <a href="login.php" <?php echo ($currentPage == 'login.php') ? 'class="active"' : ''; ?>>kirjaudu sisään</a>
                    <a href="register.php" <?php echo ($currentPage == 'register.php') ? 'class="active"' : ''; ?>>rekisteröidy</a>
                    <?php
                    break;
            endswitch; ?>
            <?php if ($permission === 1 || $permission === 2): ?>
                <a href="logout.php"
                   class="<?php echo ($currentPage == 'logout.php') ? 'active' : ''; ?>">kirjaudu ulos
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>
<!-- Lisää tämä JS-tiedoston sisällyttämiseksi, jos sitä ei jo ole -->
<script src="assets/js/header.js"></script>
