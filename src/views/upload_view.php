<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Upload</title>
</head>
<body>
    <h2>Prześlij zdjęcie</h2>
    
    
    <hr>
    
    <?php if (isset($error) && $error): ?>
        <p style="color: red;">❌ <?= $error ?></p>
    <?php endif; ?>
    
    <?php if (isset($success) && $success): ?>
        <p style="color: green;">✅ <?= htmlspecialchars($success) ?></p>
    <?php endif; ?>
    
    <form method="post" enctype="multipart/form-data">
        <label>Plik: <input type="file" name="fileToUpload" required></label><br>
        <label>Tytuł: <input name="title" required></label><br>
        <label>Autor: <input name="author" value="<?= htmlspecialchars($author) ?>" <?php if (!empty($author)) echo 'readonly'; ?> required></label><br>
        
        <fieldset>
            <legend>Widoczność:</legend>
            <label><input type="radio" name="visibility" value="1" checked> Publiczne</label>
              <label><input type="radio" name="visibility" value="0" <?php if (empty($author)) echo 'disabled'; ?>> Prywatne</label>
        </fieldset><br>
        
        <button type="submit" name="submit">Wyślij</button>
    </form>
</body>
</html>
