<?php
session_start();

// Handle database connection with error handling
$database_available = false;
$conn = null;
$product = null;
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Function to get product by ID
function getProductById($conn, $id) {
    // Check connection before query
    if ($conn && $conn->ping() === false) {
        // Try to reconnect
        $conn->close();
        $conn = @new mysqli();
        if ($conn) {
            $conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);
            $conn->options(MYSQLI_OPT_READ_TIMEOUT, 10);
            @$conn->real_connect("127.0.0.1", "root", "", "coffee_machine", 3307);
        }
    }
    
    if ($conn && $conn->ping() !== false) {
        $stmt = $conn->prepare("SELECT * FROM Product WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }
    return null;
}

try {
    include('includes/db_Connection.php');
    // Check if we need to reconnect
    if ($conn !== null) {
        // Use the reconnect function from db_Connection.php
        if (function_exists('checkAndReconnect')) {
            checkAndReconnect($conn, "127.0.0.1", "root", "", "coffee_machine", 3307);
        }
        
        if ($conn !== null) {
            $database_available = true;
            
            // Fetch product from database
            if ($product_id > 0) {
                $product = getProductById($conn, $product_id);
            }
        }
    }
} catch (Exception $e) {
    $database_available = false;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product ? $product['name'] : 'Product Not Found'; ?> - ShAGHAF</title>
    <link rel="stylesheet" href="assets/css/complete.css">
    <style>
        .product-details {
            display: flex;
            gap: 30px;
            margin: 30px 0;
            flex-wrap: wrap;
        }
        
        .product-image-large {
            flex: 1;
            min-width: 300px;
            max-width: 500px;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .product-info {
            flex: 2;
            min-width: 300px;
        }
        
        .price {
            font-size: 2rem;
            font-weight: bold;
            color: #28a745;
            margin: 20px 0;
        }
        
        .size, .color {
            margin: 10px 0;
            font-size: 1.1rem;
        }
        
        .description {
            margin: 20px 0;
            line-height: 1.6;
            color: #555;
        }
        
        .actions {
            margin: 30px 0;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #f5c6cb;
            margin: 20px 0;
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
            .product-details {
                flex-direction: column;
            }
            
            .product-image-large {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <header class="header" data-header>
        <?php include('includes/header.php'); ?>
        <?php include('includes/menu.php'); ?>
    </header>
    
    <main>
        <article>
            <section class="section">
                <div class="container">
                    <?php if (!$database_available): ?>
                        <div class="db-status db-disconnected">
                            <strong>Database Status:</strong> Disconnected - Product details not available
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($product): ?>
                        <h1><?php echo $product['name']; ?></h1>
                        
                        <div class="product-details">
                            <?php if (!empty($product['Vimage'])): ?>
                                <?php 
                                // Check if image exists in uploads folder, otherwise use assets/images
                                $image_path = "uploads/" . $product['Vimage'];
                                if (!file_exists($image_path)) {
                                    $image_path = "assets/images/" . $product['Vimage'];
                                }
                                ?>
                                <img src="<?php echo $image_path; ?>" alt="<?php echo $product['name']; ?>" class="product-image-large">
                            <?php endif; ?>
                            
                            <div class="product-info">
                                <p class="price"><?php echo number_format($product['Price'], 2); ?> SAR</p>
                                <p class="size"><strong>Size:</strong> <?php echo $product['size']; ?></p>
                                <p class="color"><strong>Color:</strong> <?php echo $product['Color']; ?></p>
                                <p class="description"><?php echo $product['ProductOverview']; ?></p>
                                
                                <div class="actions">
                                    <form method="post" action="simple_add_handler.php" style="display: inline;">
                                        <input type="hidden" name="add_to_cart_simple" value="1">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                                        <input type="hidden" name="product_price" value="<?php echo $product['Price']; ?>">
                                        <input type="hidden" name="product_image" value="<?php echo $product['Vimage']; ?>">
                                        <button type="submit" class="btn btn-success">
                                            Add to Cart
                                        </button>
                                    </form>
                                    <form method="post" action="simple_add_handler.php" style="display: inline;">
                                        <input type="hidden" name="add_to_favorites_simple" value="1">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                                        <input type="hidden" name="product_image" value="<?php echo $product['Vimage']; ?>">
                                        <button type="submit" class="btn btn-danger">
                                            &#9829; Add to Favorites
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php elseif ($database_available): ?>
                        <h1>Product Not Found</h1>
                        <p class="alert alert-error">The product you're looking for doesn't exist or has been removed.</p>
                    <?php else: ?>
                        <h1>Product Details Unavailable</h1>
                        <p class="alert alert-error">Product details are unavailable because the database is not connected.</p>
                    <?php endif; ?>
                    
                    <div class="actions">
                        <a href="products_showcase.php" class="btn">Back to Products</a>
                        <a href="index.php" class="btn btn-secondary">Back to Home</a>
                    </div>
                </div>
            </section>
        </article>
    </main>
    
    <?php include('includes/footer.php'); ?>
    
    <script src="assets/js/script.js"></script>
</body>
</html>