<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Rejestracja</title>
</head>
<body>
    <h2>Rejestracja</h2>
    
    <hr>
    
    <?php if (isset($error) && $error): ?>
        <p style="color: red;">❌ <?= $error ?></p>
    <?php endif; ?>
    
    <?php if (isset($success) && $success): ?>
        <p style="color: green;">✅ <?= htmlspecialchars($success) ?></p>
    <?php endif; ?>
    
    <form method="post" enctype="multipart/form-data">
        <label>E-mail: <input type="email" name="address_email" required></label><br>
        <label>Login: <input type="text"  name="login" required></label><br>
        <label>Hasło: <input type="password" name="password1" required></label><br>
        <label>Powtórz hasło: <input type="password" name="password2" required></label><br>
        <label>Zdjęcie profilowe: <input type="file" name="fileToUpload" required></label><br>
        <button type="submit" name="submit">Zarejestruj</button>
    </form>
</body>
</html>
