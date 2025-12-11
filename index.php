<?php
/**
 * Index.php - automatyczne przekierowanie do galerii
 * Zapobiega wyświetlaniu listingu katalogów przez Apache
 */
header('Location: front_controller.php?action=/gallery');
exit;
