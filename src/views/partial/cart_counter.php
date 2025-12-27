<?php
/**
 * Partial View - licznik koszyka
 * Wyświetla ikonę koszyka z łączną liczbą elementów (suma ilości)
 */

// Oblicz sumę ilości (nie tylko liczbę różnych zdjęć)
$totalQuantity = 0;
$quantities = session_get('quantities', []);
if (is_array($quantities) && !empty($quantities)) {
    $totalQuantity = array_sum($quantities);
} else {
    $cart = session_get('cart', []);
    if (is_array($cart)) {
        $totalQuantity = count($cart);
    }
}
?>
<a href="/cart">🛒(<?= $totalQuantity ?>)</a>
