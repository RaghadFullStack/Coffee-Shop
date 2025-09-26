<?php
session_start();

// Initialize session arrays if they don't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

if (!isset($_SESSION['favorites'])) {
    $_SESSION['favorites'] = array();
}

// Handle add to cart
if (isset($_POST['add_to_cart_simple'])) {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $product_name = filter_input(INPUT_POST, 'product_name', FILTER_SANITIZE_STRING);
    $product_price = filter_input(INPUT_POST, 'product_price', FILTER_VALIDATE_FLOAT);
    $product_image = filter_input(INPUT_POST, 'product_image', FILTER_SANITIZE_STRING);
    
    if ($product_id && $product_name && $product_price !== false && $product_image) {
        // Check if item already exists in cart
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $product_id) {
                $item['quantity']++;
                $found = true;
                break;
            }
        }
        
        // If not found, add new item
        if (!$found) {
            $_SESSION['cart'][] = array(
                'id' => $product_id,
                'name' => $product_name,
                'price' => $product_price,
                'image' => $product_image,
                'quantity' => 1
            );
        }
        
        // Set success message
        $_SESSION['message'] = 'Product added to cart successfully!';
    } else {
        $_SESSION['error'] = 'Invalid product data!';
    }
    
    // Redirect back to the referring page
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

// Handle add to favorites
if (isset($_POST['add_to_favorites_simple'])) {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $product_name = filter_input(INPUT_POST, 'product_name', FILTER_SANITIZE_STRING);
    $product_image = filter_input(INPUT_POST, 'product_image', FILTER_SANITIZE_STRING);
    
    if ($product_id && $product_name && $product_image) {
        // Check if item already exists in favorites
        $found = false;
        foreach ($_SESSION['favorites'] as $item) {
            if ($item['id'] == $product_id) {
                $found = true;
                break;
            }
        }
        
        // If not found, add new item
        if (!$found) {
            $_SESSION['favorites'][] = array(
                'id' => $product_id,
                'name' => $product_name,
                'image' => $product_image
            );
            $_SESSION['message'] = 'Product added to favorites successfully!';
        } else {
            $_SESSION['error'] = 'Product already in favorites!';
        }
    } else {
        $_SESSION['error'] = 'Invalid product data!';
    }
    
    // Redirect back to the referring page
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}
?>