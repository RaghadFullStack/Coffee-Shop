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
$message = "";

try {
    include('includes/db_Connection.php');
    // Enhanced validation: Check both not null and proper instance
    if ($conn !== null && $conn instanceof mysqli) {
        $database_available = true;
    }
} catch (Exception $e) {
    $database_available = false;
    $message = "Database connection failed. Please ensure XAMPP is running.";
}

// Handle form submission only if database is available
if ($database_available && $_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $size = $_POST['size'];
    $color = $_POST['color'];
    $overview = $_POST['overview'];
    $image_name = "";
    
    // Handle file upload
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
    
    // Insert into database if no upload errors
    if (empty($message) && $database_available && $conn !== null && $conn instanceof mysqli) {
        $stmt = $conn->prepare("INSERT INTO Product (name, Price, size, Color, ProductOverview, Vimage) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sdssss", $name, $price, $size, $color, $overview, $image_name);
            
            if ($stmt->execute()) {
                $message = "Product added successfully!";
            } else {
                // Safe access to error property with null check
                $message = "Error: " . ($stmt ? $stmt->error : "Statement error");
            }
            $stmt->close();
        } else {
            $message = "Error preparing statement: " . ($conn ? $conn->error : "Connection error");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - ShAGHAF</title>
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
        .form-container {
            max-width: 600px;
            margin: 0 auto;
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
                    <h1>Add New Product</h1>
                    
                    <?php if (!empty($message)): ?>
                        <div class="alert"><?php echo $message; ?></div>
                    <?php endif; ?>
                    
                    <?php if (!$database_available): ?>
                        <div class="db-status db-disconnected">
                            <strong>Database Status:</strong> Disconnected - Please ensure XAMPP MySQL service is running to add products
                        </div>
                    <?php else: ?>
                        <div class="db-status db-connected">
                            <strong>Database Status:</strong> Connected - Ready to add products
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-container">
                        <form method="post" enctype="multipart/form-data" <?php if (!$database_available) echo 'disabled'; ?>>
                            <div class="form-group">
                                <label for="name">Product Name:</label>
                                <input type="text" name="name" id="name" class="form-control" required <?php if (!$database_available) echo 'disabled'; ?>>
                            </div>
                            
                            <div class="form-group">
                                <label for="price">Price (SAR):</label>
                                <input type="number" name="price" id="price" class="form-control" step="0.01" min="0" required <?php if (!$database_available) echo 'disabled'; ?>>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="size">Size:</label>
                                    <select name="size" id="size" class="form-control" required <?php if (!$database_available) echo 'disabled'; ?>>
                                        <option value="">Choose Size</option>
                                        <option value="Small">Small</option>
                                        <option value="Medium">Medium</option>
                                        <option value="Large">Large</option>
                                        <option value="XL">XL</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="color">Color:</label>
                                    <select name="color" id="color" class="form-control" required <?php if (!$database_available) echo 'disabled'; ?>>
                                        <option value="">Choose Color</option>
                                        <option value="Red">Red</option>
                                        <option value="Blue">Blue</option>
                                        <option value="Green">Green</option>
                                        <option value="Black">Black</option>
                                        <option value="White">White</option>
                                        <option value="Brown">Brown</option>
                                        <option value="Silver">Silver</option>
                                        <option value="Gold">Gold</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="overview">Product Overview:</label>
                                <textarea name="overview" id="overview" class="form-control" rows="4" required <?php if (!$database_available) echo 'disabled'; ?>></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="image">Product Image:</label>
                                <input type="file" name="image" id="image" class="form-control" accept="image/*" required <?php if (!$database_available) echo 'disabled'; ?>>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn" <?php if (!$database_available) echo 'disabled'; ?>>Save Product</button>
                                <a href="index.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </article>
    </main>
    
    <?php include('includes/footer.php');?>
</body>
</html>