<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    // Redirect to login page if not admin
    header("Location: Login.php");
    exit;
}

// Include database connection with error handling
$database_available = false;
$conn = null;
$products = array();
$message = "";

try {
    include('includes/db_Connection.php');
    // Enhanced validation: Check both not null and proper instance
    if ($conn !== null && $conn instanceof mysqli) {
        $database_available = true;
        
        // Function to get all products with enhanced error handling
        function getProducts($conn) {
            $products = array();
            // Validate connection before query
            if (!$conn || !($conn instanceof mysqli)) {
                return $products;
            }
            
            $sql = "SELECT * FROM Product ORDER BY created_at DESC";
            $result = $conn->query($sql);
            
            // Enhanced validation of query result
            if ($result && $result instanceof mysqli_result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $products[] = $row;
                }
            }
            return $products;
        }
        
        // Handle delete request with enhanced error handling
        if ($database_available && isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
            $product_id = intval($_GET['id']);
            // Validate connection before preparing statement
            if ($conn && $conn instanceof mysqli) {
                $stmt = $conn->prepare("DELETE FROM Product WHERE id = ?");
                if ($stmt) {
                    $stmt->bind_param("i", $product_id);
                    
                    if ($stmt->execute()) {
                        $message = "Product deleted successfully!";
                    } else {
                        // Safe access to error property with null check
                        $message = "Error deleting product: " . ($conn ? $conn->error : "Connection error");
                    }
                    $stmt->close();
                } else {
                    $message = "Error preparing statement: " . ($conn ? $conn->error : "Connection error");
                }
            } else {
                $message = "Database connection lost.";
            }
        }
        
        // Fetch products from database with validation
        if ($database_available && $conn && $conn instanceof mysqli) {
            $products = getProducts($conn);
        }
    }
} catch (Exception $e) {
    $database_available = false;
    $message = "Database connection failed. Please ensure XAMPP is running.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Table - ShAGHAF</title>
    <link rel="stylesheet" href="./assets/css/complete.css">
    <style>
        .btn-small {
            padding: 5px 10px;
            font-size: 14px;
            margin: 2px;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .product-table th, .product-table td {
            text-align: center;
        }
        
        .product-table th:first-child, .product-table td:first-child {
            text-align: center;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #c3e6cb;
            margin: 20px 0;
        }
        
        .actions {
            margin: 20px 0;
            text-align: center;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
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
    </style>
</head>
<body>
    <header class="header" data-header>
        <?php include('includes/header.php');?>
        <?php include('includes/menu.php');?>
    </header>
    
    <main>
        <article>
            <section class="section">
                <div class="container">
                    <h1>Product Management</h1>
                    
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-success"><?php echo $message; ?></div>
                    <?php endif; ?>
                    
                    <?php if (!$database_available): ?>
                        <div class="db-status db-disconnected">
                            <strong>Database Status:</strong> Disconnected - Database functionality not available
                        </div>
                    <?php else: ?>
                        <div class="db-status db-connected">
                            <strong>Database Status:</strong> Connected - Database functionality available
                        </div>
                    <?php endif; ?>
                    
                    <div class="actions">
                        <a href="Add_prodect.php" class="btn" <?php if (!$database_available) echo 'style="opacity: 0.5; pointer-events: none;"'; ?>>Add New Product</a>
                        <a href="index.php" class="btn btn-secondary">Back to Home</a>
                    </div>
                    
                    <?php if ($database_available && count($products) > 0): ?>
                        <table class="product-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Price (SAR)</th>
                                    <th>Size</th>
                                    <th>Color</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?php echo $product['id']; ?></td>
                                        <td><?php echo $product['name']; ?></td>
                                        <td><?php echo number_format($product['Price'], 2); ?></td>
                                        <td><?php echo $product['size']; ?></td>
                                        <td><?php echo $product['Color']; ?></td>
                                        <td>
                                            <form method="post" action="simple_add_handler.php" style="display: inline;">
                                                <input type="hidden" name="add_to_cart_simple" value="1">
                                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                                                <input type="hidden" name="product_price" value="<?php echo $product['Price']; ?>">
                                                <input type="hidden" name="product_image" value="<?php echo $product['Vimage']; ?>">
                                                <button type="submit" class="btn btn-small btn-success">
                                                    Add to Cart
                                                </button>
                                            </form>
                                            <form method="post" action="simple_add_handler.php" style="display: inline;">
                                                <input type="hidden" name="add_to_favorites_simple" value="1">
                                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                                                <input type="hidden" name="product_image" value="<?php echo $product['Vimage']; ?>">
                                                <button type="submit" class="btn btn-small btn-danger">
                                                    &#9829; Favorite
                                                </button>
                                            </form>
                                            <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn btn-small">View</a>
                                            <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-small btn-warning">Edit</a>
                                            <a href="table.php?action=delete&id=<?php echo $product['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php elseif ($database_available): ?>
                        <p>No products found in the database.</p>
                    <?php else: ?>
                        <p>Database connection unavailable. Please ensure XAMPP MySQL service is running.</p>
                    <?php endif; ?>
                </div>
            </section>
        </article>
    </main>
    
    <?php include('includes/footer.php');?>
    
    <script src="./assets/js/script.js"></script>
</body>
</html>