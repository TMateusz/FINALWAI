<?php
// Use unified session helper
if (!function_exists('session_get')){
    include_once __DIR__ . '/../Core/session_helpers.php';
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Galeria zdjęć WAI2 - przeglądaj, wyszukuj i zarządzaj swoimi ulubionymi zdjęciami">
    <title><?= isset($title) ? htmlspecialchars($title) : 'Galeria' ?></title>
    <link rel="stylesheet" href="/src/static/css/styles.css">
</head>
<body>
    <header>
        <h1>Galeria Zdjęć - Projekt WAI2</h1>
    </header>
    
    <nav>
        <a href="/gallery">STRONA GŁÓWNA</a>
        <a href="/search">WYSZUKIWANIE</a>
        <a href="/upload">UPLOAD</a>

        <?php require __DIR__ . '/partial/cart_counter.php'; ?>

        <?php if (session_get('login') !== null): ?>
            <span class="user-links">
                <?php $loginEsc = htmlspecialchars(session_get('login')); ?>
                <img src="/static/profileimages/<?= $loginEsc ?>.jpg" alt="Avatar" width="30" height="30"
                     onerror="(function(img){var exts=['jpg','png'];var base='/static/profileimages/<?= $loginEsc ?>.';var i=1;img.onerror=function(){ if(i>=exts.length){img.onerror=null; img.src='/static/profileimages/default-avatar.png'; return;} img.src=base+exts[i++]; }; img.src=base+exts[0]; })(this)">
                <a href="/logout">WYLOGUJ (<?= $loginEsc ?>)</a>
            </span>
        <?php else: ?>
            <span class="user-links">
                <a href="/register">REJESTRACJA</a>
                <a href="/login">LOGOWANIE</a>
            </span>
        <?php endif; ?>
    </nav>
    
    <main>
        <!-- Tutaj zostanie wstawiona treść z widoku -->
        <?= $content ?>
    </main>
    
    <footer>
        <p>&copy; 2025 WAI2 - Projekt MVC obiektowy. Wszystkie prawa zastrzeżone.</p>
    </footer>
</body>
</html>
