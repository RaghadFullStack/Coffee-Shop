<?php
session_start();

// Initialize session arrays if they don't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

if (!isset($_SESSION['favorites'])) {
    $_SESSION['favorites'] = array();
}

// Calculate counts
$cartCount = 0;
foreach ($_SESSION['cart'] as $item) {
    $cartCount += $item['quantity'];
}

$favoritesCount = count($_SESSION['favorites']);

// Return JSON response
header('Content-Type: application/json');
echo json_encode(array(
    'cartCount' => $cartCount,
    'favoritesCount' => $favoritesCount
));
?>