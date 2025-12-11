<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie</title>
</head>
<body>
    <h2>Logowanie</h2>

    
    <hr>
    
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
