<?php
// Check if user is admin
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    // Redirect to login page if not admin
    header("Location: Login.php");
    exit;
}

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
    $message = "Database connection failed. Please ensure XAMPP is running.";
}

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Initialize variables
$product = null;
$message = '';

// Function to get product by ID
function getProductById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM Product WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

// Function to update product
function updateProduct($conn, $id, $name, $price, $size, $color, $overview, $image_name) {
    $stmt = $conn->prepare("UPDATE Product SET name = ?, Price = ?, size = ?, Color = ?, ProductOverview = ?, Vimage = ? WHERE id = ?");
    $stmt->bind_param("sdssssi", $name, $price, $size, $color, $overview, $image_name, $id);
    
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

// Fetch product from database if connection is available
if ($database_available && $product_id > 0) {
    $product = getProductById($conn, $product_id);
}

// Handle form submission
if ($database_available && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $size = $_POST['size'];
    $color = $_POST['color'];
    $overview = $_POST['overview'];
    $image_name = $product['Vimage']; // Keep existing image by default
    
    // Handle file upload if a new image is provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
        
        if (in_array($file_extension, $allowed_extensions)) {
            $image_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $target_dir . $image_name;
            
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $message = "Error uploading image.";
            }
        } else {
            $message = "Invalid file type. Please upload JPG, JPEG, PNG, or GIF.";
        }
    }
    
    // Update product in database if no upload errors
    if (empty($message)) {
        if (updateProduct($conn, $product_id, $name, $price, $size, $color, $overview, $image_name)) {
            $message = "Product updated successfully!";
            // Refresh product data
            $product = getProductById($conn, $product_id);
        } else {
            $message = "Error updating product: " . ($conn ? $conn->error : "Database connection unavailable");
        }
    }
}

// Handle delete request
if ($database_available && isset($_GET['action']) && $_GET['action'] == 'delete' && $product_id > 0) {
    if ($conn !== null) {
        $stmt = $conn->prepare("DELETE FROM Product WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        
        if ($stmt->execute()) {
            header("Location: table.php?message=Product deleted successfully");
            exit;
        } else {
            $message = "Error deleting product: " . $conn->error;
        }
    } else {
        $message = "Database connection unavailable. Cannot delete product.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - ShAGHAF</title>
    <link rel="stylesheet" href="./assets/css/complete.css">
    <style>
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
                    <h1>Edit Product</h1>
                    
                    <?php if (!empty($message)): ?>
                        <div class="alert"><?php echo $message; ?></div>
                    <?php endif; ?>
                    
                    <?php if (!$database_available): ?>
                        <div class="db-status db-disconnected">
                            <strong>Database Status:</strong> Disconnected - Database functionality not available
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($product): ?>
                        <form method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="name">Product Name:</label>
                                <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required <?php if (!$database_available) echo 'disabled'; ?>>
                            </div>
                            
                            <div class="form-group">
                                <label for="price">Price (SAR):</label>
                                <input type="number" name="price" id="price" class="form-control" step="0.01" min="0" value="<?php echo $product['Price']; ?>" required <?php if (!$database_available) echo 'disabled'; ?>>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="size">Size:</label>
                                    <select name="size" id="size" class="form-control" required <?php if (!$database_available) echo 'disabled'; ?>>
                                        <option value="">Choose Size</option>
                                        <option value="Small" <?php echo ($product['size'] == 'Small') ? 'selected' : ''; ?>>Small</option>
                                        <option value="Medium" <?php echo ($product['size'] == 'Medium') ? 'selected' : ''; ?>>Medium</option>
                                        <option value="Large" <?php echo ($product['size'] == 'Large') ? 'selected' : ''; ?>>Large</option>
                                        <option value="XL" <?php echo ($product['size'] == 'XL') ? 'selected' : ''; ?>>XL</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="color">Color:</label>
                                    <select name="color" id="color" class="form-control" required <?php if (!$database_available) echo 'disabled'; ?>>
                                        <option value="">Choose Color</option>
                                        <option value="Red" <?php echo ($product['Color'] == 'Red') ? 'selected' : ''; ?>>Red</option>
                                        <option value="Blue" <?php echo ($product['Color'] == 'Blue') ? 'selected' : ''; ?>>Blue</option>
                                        <option value="Green" <?php echo ($product['Color'] == 'Green') ? 'selected' : ''; ?>>Green</option>
                                        <option value="Black" <?php echo ($product['Color'] == 'Black') ? 'selected' : ''; ?>>Black</option>
                                        <option value="White" <?php echo ($product['Color'] == 'White') ? 'selected' : ''; ?>>White</option>
                                        <option value="Brown" <?php echo ($product['Color'] == 'Brown') ? 'selected' : ''; ?>>Brown</option>
                                        <option value="Silver" <?php echo ($product['Color'] == 'Silver') ? 'selected' : ''; ?>>Silver</option>
                                        <option value="Gold" <?php echo ($product['Color'] == 'Gold') ? 'selected' : ''; ?>>Gold</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="overview">Product Overview:</label>
                                <textarea name="overview" id="overview" class="form-control" rows="4" required <?php if (!$database_available) echo 'disabled'; ?>><?php echo htmlspecialchars($product['ProductOverview']); ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="image">Product Image (leave blank to keep current image):</label>
                                <input type="file" name="image" id="image" class="form-control" accept="image/*" <?php if (!$database_available) echo 'disabled'; ?>>
                                <?php if (!empty($product['Vimage'])): ?>
                                    <div class="mt-2">
                                        <p>Current image:</p>
                                        <?php 
                                        // Check if image exists in uploads folder, otherwise use assets/images
                                        $image_path = "uploads/" . $product['Vimage'];
                                        if (!file_exists($image_path)) {
                                            $image_path = "assets/images/" . $product['Vimage'];
                                        }
                                        ?>
                                        <img src="<?php echo $image_path; ?>" alt="<?php echo $product['name']; ?>" style="max-width: 200px; height: auto;">
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" name="update_product" class="btn" <?php if (!$database_available) echo 'disabled'; ?>>Update Product</button>
                                <a href="table.php" class="btn btn-secondary">Cancel</a>
                                <?php if ($database_available): ?>
                                    <a href="edit_product.php?id=<?php echo $product_id; ?>&action=delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete Product</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    <?php elseif ($database_available): ?>
                        <p>Product not found.</p>
                        <a href="table.php" class="btn">Back to Products</a>
                    <?php else: ?>
                        <p>Product details unavailable because the database is not connected.</p>
                        <a href="table.php" class="btn">Back to Products</a>
                    <?php endif; ?>
                </div>
            </section>
        </article>
    </main>
    
    <?php include('includes/footer.php');?>
</body>
</html>