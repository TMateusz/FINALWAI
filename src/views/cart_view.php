<?php $title = 'Koszyk - Ulubione zdjęcia'; ?>

<h2>Koszyk ulubionych zdjęć</h2>

<?php if (!empty($cartImages)): ?>
    <p><strong>Liczba zdjęć w koszyku:</strong> <?= count($cartImages) ?></p>

    <form method="post" action="/cart/remove">
        <p><button type="submit">Zaktualizuj koszyk</button></p>
        <?php foreach ($cartImages as $image): ?>
            <div class="image-item">
                <a href="<?= htmlspecialchars($image['path']) ?>" target="_blank">
                    <img src="<?= htmlspecialchars($image['miniature']) ?>" alt="Miniatura">
                </a>
                
                <p>
                    <strong>Tytuł:</strong> <?= htmlspecialchars($image['tytul']) ?><br>
                    <strong>Autor:</strong> <?= htmlspecialchars($image['autor']) ?><br>
                    <label>
                        <strong>Ilość:</strong> 
                        <input type="number" name="quantity[]"  value="<?= $image['quantity'] ?>"  min="1" style="width: 60px;" aria-label="Ilość <?= htmlspecialchars($image['tytul']) ?>">
                    </label>
                    <input type="hidden" name="imageFile[]" value="<?= htmlspecialchars($image['plik']) ?>">
                    <br>
                    <label>
                        <input type="checkbox" 
                               name="toRemove[]" 
                               value="<?= htmlspecialchars($image['plik']) ?>">
                        Usuń z koszyka
                    </label>
                </p>
            </div>
        <?php endforeach; ?>
    </form>
<?php else: ?>
    <p>Koszyk jest pusty. <a href="/gallery">Przejdź do galerii</a>, aby dodać zdjęcia.</p>
<?php endif; ?>
