<?php $title = 'Galeria zdjęć'; ?>

<h2>Galeria zdjęć</h2>

<form method="post" action="/cart/save">
    <p><button type="submit">Zapisz do koszyka</button></p>

<?php if (!empty($images)): ?>
    <?php foreach ($images as $image): ?>
        <div class="image-item">
            <a href="<?= htmlspecialchars($image['path']) ?>" target="_blank">
                <img src="<?= htmlspecialchars($image['miniature']) ?>" alt="Miniatura">
            </a>
            
            <p>
                <strong>Tytuł:</strong> <?= htmlspecialchars($image['tytul']) ?><br>
                <strong>Autor:</strong> <?= htmlspecialchars($image['autor']) ?>
                
                <?php if ($image['publiczny'] == 0): ?>
                    <em>(Prywatne)</em>
                <?php endif; ?>
                
                <label>
                    <input type="checkbox" 
                           name="cart[]" 
                           value="<?= htmlspecialchars($image['plik']) ?>"
                           <?= in_array($image['plik'], $cart) ? 'checked' : '' ?>>
                    Do koszyka
                </label>
            </p>
        </div>
    <?php endforeach; ?>
    
    
<?php else: ?>
    <p>Brak zdjęć do wyświetlenia.</p>
<?php endif; ?>

</form>

<script>
// Persist checkbox state via AJAX so selections survive paging
(function(){
    function postUpdate(file, checked){
        try{
            var fd = new FormData();
            fd.append('file', file);
            fd.append('checked', checked ? '1' : '0');
            fetch('/cart/update', {method: 'POST', body: fd, credentials: 'same-origin'}).catch(function(e){console.warn(e)});
        }catch(e){console.warn(e)}
    }

    document.querySelectorAll('input[name="cart[]"]').forEach(function(cb){
        cb.addEventListener('change', function(e){
            postUpdate(cb.value, cb.checked);
        });
    });
})();
</script>

<!-- Paginacja -->
<?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php if ($currentPage > 1): ?>
            <a href="?page=<?= $currentPage - 1 ?>">&laquo; Poprzednia</a>
        <?php endif; ?>
        
        <?php for ($page = 1; $page <= $totalPages; $page++): ?>
            <?php if ($page == $currentPage): ?>
                <strong><?= $page ?></strong>
            <?php else: ?>
                <a href="?page=<?= $page ?>"><?= $page ?></a>
            <?php endif; ?>
        <?php endfor; ?>
        
        <?php if ($currentPage < $totalPages): ?>
            <a href="?page=<?= $currentPage + 1 ?>">Następna &raquo;</a>
        <?php endif; ?>
    </div>
<?php endif; ?>
