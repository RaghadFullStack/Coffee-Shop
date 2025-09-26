<?php
session_start();

// Handle database connection with error handling
$database_available = false;
$conn = null;

try {
    include('includes/db_Connection.php');
    if ($conn !== null) {
        $database_available = true;
    }
} catch (Exception $e) {
    $database_available = false;
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Handle remove from cart
if (isset($_POST['remove_from_cart'])) {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    
    if ($product_id) {
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id'] == $product_id) {
                unset($_SESSION['cart'][$key]);
                $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
                break;
            }
        }
        $message = "Product removed from cart!";
    } else {
        $error = "Invalid product ID!";
    }
}

// Handle clear cart
if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = array();
    $message = "Cart cleared!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - ShAGHAF</title>
    <link rel="stylesheet" href="assets/css/complete.css">
    <style>
        .cart-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .page-header {
            background: #2c3e50;
            color: white;
            padding: 30px;
            text-align: center;
            margin-bottom: 35px;
            border-radius: 8px;
        }
        
        .page-title {
            color: white;
            margin: 0 0 10px 0;
            font-size: 2.2rem;
        }
        
        .cart-content {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }
        
        .cart-items {
            flex: 3;
            min-width: 300px;
        }
        
        .cart-summary {
            flex: 1;
            min-width: 300px;
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        
        .cart-item {
            display: flex;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #eee;
            background: white;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .item-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 6px;
            margin-right: 20px;
        }
        
        .item-details {
            flex: 1;
        }
        
        .item-title {
            font-size: 1.2rem;
            margin: 0 0 10px 0;
            color: #2c3e50;
        }
        
        .item-price {
            color: #28a745;
            font-weight: bold;
            margin: 5px 0;
        }
        
        .item-quantity {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }
        
        .quantity-btn {
            background: #e9ecef;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .quantity-value {
            margin: 0 10px;
            min-width: 30px;
            text-align: center;
        }
        
        .item-total {
            font-weight: bold;
            font-size: 1.1rem;
            margin: 10px 0;
        }
        
        .remove-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .remove-btn:hover {
            background: #c82333;
        }
        
        .summary-title {
            font-size: 1.5rem;
            margin-top: 0;
            padding-bottom: 15px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 15px 0;
        }
        
        .summary-total {
            font-size: 1.3rem;
            font-weight: bold;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
            margin-top: 15px;
        }
        
        .checkout-btn {
            display: block;
            width: 100%;
            padding: 15px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            margin: 20px 0;
            transition: background 0.3s;
        }
        
        .checkout-btn:hover {
            background: #218838;
        }
        
        .clear-cart-btn {
            display: block;
            width: 100%;
            padding: 12px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .clear-cart-btn:hover {
            background: #5a6268;
        }
        
        .empty-cart {
            text-align: center;
            padding: 50px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .continue-shopping {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 25px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s;
        }
        
        .continue-shopping:hover {
            background: #0056b3;
        }
        
        .db-status {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            text-align: center;
        }
        .db-connected {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .db-disconnected {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        @media (max-width: 768px) {
            .cart-content {
                flex-direction: column;
            }
            
            .cart-item {
                flex-direction: column;
                text-align: center;
            }
            
            .item-image {
                margin-right: 0;
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>
    <header class="header" data-header>
        <?php include('includes/header.php'); ?>
        <?php include('includes/menu.php'); ?>
    </header>
    
    <div class="cart-container">
        <div class="page-header">
            <h1 class="page-title">Shopping Cart</h1>
            <p>Review and manage your selected items</p>
        </div>
        
        <?php if (!$database_available): ?>
            <div class="db-status db-disconnected">
                <strong>Database Status:</strong> Disconnected - Some features may be limited
            </div>
        <?php endif; ?>
        
        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="cart-content">
            <div class="cart-items">
                <?php if (empty($_SESSION['cart'])): ?>
                    <div class="empty-cart">
                        <h3>Your cart is empty</h3>
                        <p>Looks like you haven't added any items to your cart yet.</p>
                        <a href="products_showcase.php" class="continue-shopping">Continue Shopping</a>
                    </div>
                <?php else: ?>
                    <?php 
                    $total = 0;
                    foreach ($_SESSION['cart'] as $item): 
                        $itemTotal = $item['price'] * $item['quantity'];
                        $total += $itemTotal;
                    ?>
                        <div class="cart-item">
                            <img src="assets/images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="item-image">
                            <div class="item-details">
                                <h3 class="item-title"><?php echo $item['name']; ?></h3>
                                <p class="item-price"><?php echo number_format($item['price'], 2); ?> SAR</p>
                                <div class="item-quantity">
                                    <span>Quantity: <?php echo $item['quantity']; ?></span>
                                </div>
                                <p class="item-total">Total: <?php echo number_format($itemTotal, 2); ?> SAR</p>
                            </div>
                            <form method="post" style="margin-left: 20px;">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" name="remove_from_cart" class="remove-btn">Remove</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($_SESSION['cart'])): ?>
                <div class="cart-summary">
                    <h3 class="summary-title">Order Summary</h3>
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span><?php echo number_format($total, 2); ?> SAR</span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span>Free</span>
                    </div>
                    <div class="summary-row summary-total">
                        <span>Total</span>
                        <span><?php echo number_format($total, 2); ?> SAR</span>
                    </div>
                    
                    <button class="checkout-btn" onclick="checkout()">Proceed to Checkout</button>
                    
                    <form method="post">
                        <button type="submit" name="clear_cart" class="clear-cart-btn" onclick="return confirm('Are you sure you want to clear your cart?')">Clear Cart</button>
                    </form>
                    
                    <a href="products_showcase.php" class="continue-shopping" style="text-align: center; display: block; margin-top: 20px;">Continue Shopping</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include('includes/footer.php'); ?>
    
    <script>
        function checkout() {
            alert('Checkout functionality would be implemented here');
        }
    </script>
</body>
</html>