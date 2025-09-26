<?php
session_start();

// Check if user is admin
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

// Handle database connection with error handling
$database_available = false;
$conn = null;
$products = array();

try {
    include('includes/db_Connection.php');
    // Use the reconnect function to handle "MySQL server has gone away"
    if ($conn !== null && $conn instanceof mysqli && function_exists('checkAndReconnect')) {
        // Check and reconnect if needed
        if (checkAndReconnect($conn, "127.0.0.1", "root", "", "coffee_machine", 3306)) {
            $database_available = true;
            
            // Function to get all products from database with enhanced error handling
            function getProducts($conn) {
                $products = array();
                // Validate connection before query
                if (!$conn || !($conn instanceof mysqli)) {
                    return $products;
                }
                
                $sql = "SELECT * FROM Product ORDER BY id ASC";
                $result = $conn->query($sql);
                
                // Enhanced validation of query result
                if ($result && $result instanceof mysqli_result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $products[] = $row;
                    }
                }
                return $products;
            }
            
            // Fetch products from database
            $products = getProducts($conn);
        } else {
            $database_available = false;
        }
    } else if ($conn !== null && $conn instanceof mysqli) {
        // Fallback if reconnect function doesn't exist
        $database_available = true;
        
        // Function to get all products from database with enhanced error handling
        function getProducts($conn) {
            $products = array();
            // Validate connection before query
            if (!$conn || !($conn instanceof mysqli)) {
                return $products;
            }
            
            $sql = "SELECT * FROM Product ORDER BY id ASC";
            $result = $conn->query($sql);
            
            // Enhanced validation of query result
            if ($result && $result instanceof mysqli_result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $products[] = $row;
                }
            }
            return $products;
        }
        
        // Fetch products from database
        $products = getProducts($conn);
    }
} catch (Exception $e) {
    $database_available = false;
    $products = array(); // Empty array for fallback
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Coffee Machines - ShAGHAF</title>
    <link rel="stylesheet" href="assets/css/complete.css">
    <style>
        .product-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            padding: 25px 0;
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
        
        .page-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .actions {
            text-align: center;
            margin: 40px 0;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 25px;
            margin: 8px;
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
        
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background: #e0a800;
            transform: translateY(-2px);
        }
        
        .product-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
        }
        
        .card-content {
            padding: 20px;
        }
        
        .card-title {
            font-size: 1.3rem;
            margin: 15px 0 10px;
            color: #2c3e50;
        }
        
        .card-description {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        
        .price {
            font-size: 1.4rem;
            font-weight: bold;
            color: #28a745;
            margin: 10px 0;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin: 3px;
            background: #e9ecef;
        }
        
        .badge-info {
            background: #17a2b8;
            color: white;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .btn-group .btn {
            flex: 1;
            padding: 10px;
            font-size: 0.9rem;
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
        
        @media (max-width: 992px) {
            .product-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
            
            .page-title {
                font-size: 1.8rem;
            }
        }
        
        @media (max-width: 768px) {
            .product-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .page-header {
                padding: 20px;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .btn-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header class="header" data-header>
        <?php include('includes/header.php'); ?>
        <?php include('includes/menu.php'); ?>
    </header>
    
    <div class="container-fluid">
        <div class="ts-main-content">
            <div class="content-wrapper">
                <div class="row">
                    <div class="col-md-12">
                        <div class="page-header">
                            <h1 class="page-title">All Coffee Machines</h1>
                            <p>Browse our complete collection of premium coffee machines</p>
                            <?php if ($is_admin): ?>
                                <p style="margin-top: 15px; font-size: 1.2rem;">Administrator Mode - You can manage products</p>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!$database_available): ?>
                            <div class="db-status db-disconnected">
                                <strong>Database Status:</strong> Disconnected - Showing sample products (database not available)
                            </div>
                        <?php else: ?>
                            <div class="db-status db-connected">
                                <strong>Database Status:</strong> Connected - Showing live products from database
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($is_admin && $database_available): ?>
                            <div class="actions">
                                <a href="Add_prodect.php" class="btn btn-success">Add New Product</a>
                                <a href="table.php" class="btn btn-warning">Manage Products</a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="product-grid">
                            <?php if ($database_available && count($products) > 0): ?>
                                <?php foreach ($products as $product): ?>
                                    <div class="product-card">
                                        <div class="card-content">
                                            <img src="assets/images/<?php echo $product['Vimage']; ?>" alt="<?php echo $product['name']; ?>" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px 8px 0 0;">
                                            <h3 class="card-title"><?php echo $product['name']; ?></h3>
                                            <p class="card-description"><?php echo substr($product['ProductOverview'], 0, 100); ?>...</p>
                                            <p class="price"><?php echo number_format($product['Price'], 2); ?> SAR</p>
                                            <p>
                                                <span class="badge"><?php echo $product['size']; ?></span> 
                                                <span class="badge badge-info"><?php echo $product['Color']; ?></span>
                                            </p>
                                            <div class="btn-group">
                                                <form method="post" action="simple_add_handler.php" style="display: inline;">
                                                    <input type="hidden" name="add_to_cart_simple" value="1">
                                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                    <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                                                    <input type="hidden" name="product_price" value="<?php echo $product['Price']; ?>">
                                                    <input type="hidden" name="product_image" value="<?php echo $product['Vimage']; ?>">
                                                    <button type="submit" class="btn btn-primary">
                                                        Add to Cart
                                                    </button>
                                                </form>
                                                <form method="post" action="simple_add_handler.php" style="display: inline;">
                                                    <input type="hidden" name="add_to_favorites_simple" value="1">
                                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                    <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                                                    <input type="hidden" name="product_image" value="<?php echo $product['Vimage']; ?>">
                                                    <button type="submit" class="btn btn-danger">
                                                        &#9829; Favorite
                                                    </button>
                                                </form>
                                                <?php if ($is_admin): ?>
                                                    <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-warning">Edit</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <!-- Fallback to sample products when database is not available -->
                                <div class="product-card">
                                    <div class="card-content">
                                        <img src="assets/images/9.jpg" alt="OZTURKBAY ODC-10 12 Cups, Black" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px 8px 0 0;">
                                        <h3 class="card-title">OZTURKBAY ODC-10 12 Cups, Black</h3>
                                        <p class="card-description">Premium coffee maker with auto-pause feature. This 12-cup coffee maker features a glass carafe with easy-pour spout...</p>
                                        <p class="price">65.00 SAR</p>
                                        <p>
                                            <span class="badge">Large</span> 
                                            <span class="badge badge-info">Black</span>
                                        </p>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary" disabled>Add to Cart</button>
                                            <button type="button" class="btn btn-danger" disabled>&#9829; Favorite</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="product-card">
                                    <div class="card-content">
                                        <img src="assets/images/6.webp" alt="Glass Carafe Coffee Maker, 12 Cups" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px 8px 0 0;">
                                        <h3 class="card-title">Glass Carafe Coffee Maker, 12 Cups</h3>
                                        <p class="card-description">Coffee maker with pause and serve feature. Features a glass carafe with easy-pour spout, auto-pause and serve function...</p>
                                        <p class="price">55.00 SAR</p>
                                        <p>
                                            <span class="badge">Large</span> 
                                            <span class="badge badge-info">Black</span>
                                        </p>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary" disabled>Add to Cart</button>
                                            <button type="button" class="btn btn-danger" disabled>&#9829; Favorite</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="product-card">
                                    <div class="card-content">
                                        <img src="assets/images/2.webp" alt="Auto Pause Coffee Maker" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px 8px 0 0;">
                                        <h3 class="card-title">Auto Pause Coffee Maker</h3>
                                        <p class="card-description">Coffee Maker with Auto Pause and Glass Carafe. Features a glass carafe with easy-pour spout, auto-pause and serve function...</p>
                                        <p class="price">65.00 SAR</p>
                                        <p>
                                            <span class="badge">Medium</span> 
                                            <span class="badge badge-info">Black</span>
                                        </p>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary" disabled>Add to Cart</button>
                                            <button type="button" class="btn btn-danger" disabled>&#9829; Favorite</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include('includes/footer.php'); ?>
    
    <script src="assets/js/script.js"></script>
</body>
</html>