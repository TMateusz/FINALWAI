<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie</title>
</head>
<body>
    <h2>Logowanie</h2>

    
    <hr>
    <?php
        // show flash messages from session (registration success or profile upload warning)
        $regSuccess = session_get('registration_success', null);
        $profileWarn = session_get('profile_upload_warning', null);
        if ($regSuccess) {
            echo '<p style="color: green;">✅ ' . htmlspecialchars($regSuccess) . '</p>';
            session_remove('registration_success');
        }
        if ($profileWarn) {
            echo '<p style="color: orange;">⚠️ ' . htmlspecialchars($profileWarn) . '</p>';
            session_remove('profile_upload_warning');
        }

    ?>

    <?php if (isset($error) && $error): ?>
        <p style="color: red;">❌ <?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    
    <form method="post">
        <label>Login: <input name="login" required></label><br>
        <label>Hasło: <input type="password" name="password1" required></label><br>
        <button type="submit" name="submit">Zaloguj</button>
    </form>
</body>
</html>
