<?php
/**
 * Partial View - licznik koszyka
 * Wyświetla ikonę koszyka z łączną liczbą elementów (suma ilości)
 */

// Oblicz sumę ilości (nie tylko liczbę różnych zdjęć)
$totalQuantity = 0;
if (isset($_SESSION['quantities']) && is_array($_SESSION['quantities'])) {
    $totalQuantity = array_sum($_SESSION['quantities']);
} elseif (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    // Jeśli brak quantities, policz same pozycje
    $totalQuantity = count($_SESSION['cart']);
}
?>
<a href="/cart">🛒(<?= $totalQuantity ?>)</a>
