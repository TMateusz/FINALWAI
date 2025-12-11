<?php
// Tylko wyniki (bez layoutu) - dla AJAX
?>
<?php if (!empty($results)): ?>
    <h3>Znalezione zdjęcia:</h3>
    
    <?php foreach ($results as $row): ?>
        <div class="image-item">
            <a href="<?= htmlspecialchars($row['path']) ?>" target="_blank">
                <img src="<?= htmlspecialchars($row['miniature']) ?>" alt="Miniatura">
            </a>
            
            <p>
                <strong>Tytuł:</strong> <?= htmlspecialchars($row['tytul']) ?><br>
                <strong>Autor:</strong> <?= htmlspecialchars($row['autor']) ?>
                
                <?php if ($row['publiczny'] == 0): ?>
                    <em>(Prywatne)</em>
                <?php endif; ?>
            </p>
        </div>
    <?php endforeach; ?>
    
<?php else: ?>
    <p>Brak wyników wyszukiwania.</p>
<?php endif; ?>
