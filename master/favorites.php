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

// Initialize favorites if not exists
if (!isset($_SESSION['favorites'])) {
    $_SESSION['favorites'] = array();
}

// Handle remove from favorites
if (isset($_POST['remove_from_favorites'])) {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    
    if ($product_id) {
        foreach ($_SESSION['favorites'] as $key => $item) {
            if ($item['id'] == $product_id) {
                unset($_SESSION['favorites'][$key]);
                $_SESSION['favorites'] = array_values($_SESSION['favorites']); // Re-index array
                break;
            }
        }
        $message = "Product removed from favorites!";
    } else {
        $error = "Invalid product ID!";
    }
}

// Handle clear favorites
if (isset($_POST['clear_favorites'])) {
    $_SESSION['favorites'] = array();
    $message = "Favorites cleared!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorites - ShAGHAF</title>
    <link rel="stylesheet" href="assets/css/complete.css">
    <style>
        .favorites-container {
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
        
        .favorites-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
        }
        
        .favorite-item {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .favorite-item:hover {
            transform: translateY(-5px);
        }
        
        .item-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .item-content {
            padding: 20px;
        }
        
        .item-title {
            font-size: 1.3rem;
            margin: 0 0 15px 0;
            color: #2c3e50;
        }
        
        .actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .btn {
            flex: 1;
            padding: 10px;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0056b3;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .empty-favorites {
            text-align: center;
            padding: 50px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .summary-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            margin: 30px 0;
            border: 1px solid #dee2e6;
        }
        
        .summary-title {
            font-size: 1.5rem;
            margin-top: 0;
            padding-bottom: 15px;
            border-bottom: 1px solid #dee2e6;
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
        
        .clear-favorites-btn {
            display: inline-block;
            padding: 12px 25px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .clear-favorites-btn:hover {
            background: #5a6268;
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
            .favorites-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <header class="header" data-header>
        <?php include('includes/header.php'); ?>
        <?php include('includes/menu.php'); ?>
    </header>
    
    <div class="favorites-container">
        <div class="page-header">
            <h1 class="page-title">My Favorites</h1>
            <p>Your saved coffee machines</p>
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
        
        <?php if (!empty($_SESSION['favorites'])): ?>
            <div class="summary-section">
                <h3 class="summary-title">Favorites Summary</h3>
                <p>You have <?php echo count($_SESSION['favorites']); ?> items in your favorites list.</p>
                <form method="post" style="display: inline;">
                    <button type="submit" name="clear_favorites" class="clear-favorites-btn" onclick="return confirm('Are you sure you want to clear all favorites?')">Clear All Favorites</button>
                </form>
                <a href="products_showcase.php" class="continue-shopping">Continue Shopping</a>
            </div>
        <?php endif; ?>
        
        <?php if (empty($_SESSION['favorites'])): ?>
            <div class="empty-favorites">
                <h3>No favorites yet</h3>
                <p>You haven't added any items to your favorites list.</p>
                <a href="products_showcase.php" class="continue-shopping">Start Shopping</a>
            </div>
        <?php else: ?>
            <div class="favorites-grid">
                <?php foreach ($_SESSION['favorites'] as $item): ?>
                    <div class="favorite-item">
                        <img src="assets/images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="item-image">
                        <div class="item-content">
                            <h3 class="item-title"><?php echo $item['name']; ?></h3>
                            <div class="actions">
                                <a href="product_details.php?id=<?php echo $item['id']; ?>" class="btn btn-primary">View Details</a>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" name="remove_from_favorites" class="btn btn-danger">Remove</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include('includes/footer.php'); ?>
</body>
</html>