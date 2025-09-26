<?php
session_start();

// Check if user is admin
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

// Handle database connection with error handling
$database_available = false;
$conn = null;
$products = array();

// Function to get all products
function getProducts($conn) {
    // Check connection before query
    if ($conn && $conn->ping() === false) {
        // Try to reconnect
        $conn->close();
        $conn = @new mysqli();
        if ($conn) {
            $conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);
            $conn->options(MYSQLI_OPT_READ_TIMEOUT, 10);
            @$conn->real_connect("127.0.0.1", "root", "", "coffee_machine", 3306);
        }
    }
    
    if ($conn && $conn->ping() !== false) {
        $products = array();
        $sql = "SELECT * FROM Product ORDER BY id ASC";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
        return $products;
    }
    return array();
}

// Try to connect to database
try {
    include('includes/db_Connection.php');
    // Check if we need to reconnect
    if ($conn !== null) {
        // Use the reconnect function from db_Connection.php
        if (function_exists('checkAndReconnect')) {
            checkAndReconnect($conn, "127.0.0.1", "root", "", "coffee_machine", 3306);
        }
        
        if ($conn !== null) {
            $database_available = true;
            
            // Fetch products from database
            $products = getProducts($conn);
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
    <title>All Products Table - ShAGHAF</title>
    <link rel="stylesheet" href="assets/css/complete.css">
    <style>
        .table-container {
            overflow-x: auto;
            margin: 20px 0;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .products-table {
            width: 100%;
            border-collapse: collapse;
        }
        .products-table th, .products-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .products-table th {
            background: linear-gradient(to right, var(--emerald), #27ae60);
            color: white;
            font-weight: 600;
        }
        .products-table tr:hover {
            background-color: #f8f9fa;
        }
        .thumbnail {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        .price {
            font-weight: bold;
            color: var(--accent);
        }
        .size, .color {
            background-color: var(--light);
            padding: 3px 8px;
            border-radius: 15px;
            font-size: 0.85em;
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
        .actions {
            text-align: center;
            margin: 30px 0;
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
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-success:hover {
            background: #218838;
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
    </style>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/menu.php'); ?>
    
    <main>
        <article>
            <section class="section">
                <div class="container">
                    <h1>Complete Product Catalog</h1>
                    <p style="text-align: center; margin-bottom: 30px;">All coffee machines with their details</p>
                    
                    <?php if (!$database_available): ?>
                        <div class="db-status db-disconnected">
                            <strong>Database Status:</strong> Disconnected - Showing sample data (database not available)
                        </div>
                    <?php else: ?>
                        <div class="db-status db-connected">
                            <strong>Database Status:</strong> Connected - Showing live data from database
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($is_admin && $database_available): ?>
                        <div class="actions">
                            <a href="Add_prodect.php" class="btn btn-success">Add New Product</a>
                            <a href="table.php" class="btn btn-warning">Manage Products</a>
                        </div>
                    <?php endif; ?>
                    
                    <div class="table-container">
                        <table class="products-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Product Name</th>
                                    <th>Price</th>
                                    <th>Size</th>
                                    <th>Color</th>
                                    <th>Description</th>
                                    <?php if ($is_admin): ?>
                                        <th>Actions</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($database_available && count($products) > 0): ?>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td><?php echo $product['id']; ?></td>
                                            <td><img src="assets/images/<?php echo $product['Vimage']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="thumbnail"></td>
                                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                                            <td class="price"><?php echo number_format($product['Price'], 2); ?> SAR</td>
                                            <td><span class="size"><?php echo $product['size']; ?></span></td>
                                            <td><span class="color"><?php echo $product['Color']; ?></span></td>
                                            <td><?php echo htmlspecialchars($product['description'] ?? $product['ProductOverview']); ?></td>
                                            <?php if ($is_admin): ?>
                                                <td>
                                                    <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-warning">Edit</a>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <!-- Fallback to hardcoded data when database is not available -->
                                    <tr>
                                        <td>1</td>
                                        <td><img src="assets/images/9.jpg" alt="OZTURKBAY ODC-10 12 Cups, Black" class="thumbnail"></td>
                                        <td>OZTURKBAY ODC-10 12 Cups, Black</td>
                                        <td class="price">65.00 SAR</td>
                                        <td><span class="size">Large</span></td>
                                        <td><span class="color">Black</span></td>
                                        <td>Premium coffee maker with auto-pause feature. This 12-cup coffee maker features a glass carafe with easy-pour spout, auto-pause and serve function, and programmable timer.</td>
                                        <?php if ($is_admin): ?>
                                            <td>
                                                <span class="btn btn-secondary" disabled>Edit</span>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td><img src="assets/images/6.webp" alt="Glass Carafe Coffee Maker, 12 Cups" class="thumbnail"></td>
                                        <td>Glass Carafe Coffee Maker, 12 Cups</td>
                                        <td class="price">55.00 SAR</td>
                                        <td><span class="size">Large</span></td>
                                        <td><span class="color">Black</span></td>
                                        <td>Coffee maker with pause and serve feature. Features a glass carafe with easy-pour spout, auto-pause and serve function, and programmable timer.</td>
                                        <?php if ($is_admin): ?>
                                            <td>
                                                <span class="btn btn-secondary" disabled>Edit</span>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td><img src="assets/images/2.webp" alt="Auto Pause Coffee Maker" class="thumbnail"></td>
                                        <td>Auto Pause Coffee Maker</td>
                                        <td class="price">65.00 SAR</td>
                                        <td><span class="size">Medium</span></td>
                                        <td><span class="color">Black</span></td>
                                        <td>Coffee Maker with Auto Pause and Glass Carafe. Features a glass carafe with easy-pour spout, auto-pause and serve function, and programmable timer.</td>
                                        <?php if ($is_admin): ?>
                                            <td>
                                                <span class="btn btn-secondary" disabled>Edit</span>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td><img src="assets/images/1.jpg" alt="Programmable 10-Cup Coffee Maker" class="thumbnail"></td>
                                        <td>Programmable 10-Cup Coffee Maker</td>
                                        <td class="price">49.99 SAR</td>
                                        <td><span class="size">Medium</span></td>
                                        <td><span class="color">Silver</span></td>
                                        <td>Wake up to fresh coffee with this programmable coffee maker. Features a 10-cup glass carafe, programmable timer, auto shut-off, and brew strength selector.</td>
                                        <?php if ($is_admin): ?>
                                            <td>
                                                <span class="btn btn-secondary" disabled>Edit</span>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td><img src="assets/images/3.jpg" alt="Drip Coffee Maker with Thermal Carafe" class="thumbnail"></td>
                                        <td>Drip Coffee Maker with Thermal Carafe</td>
                                        <td class="price">79.99 SAR</td>
                                        <td><span class="size">Large</span></td>
                                        <td><span class="color">Stainless Steel</span></td>
                                        <td>Keep your coffee hot for hours with the thermal carafe. Features a 12-cup capacity, programmable timer, auto-pause and serve function, and brew strength selector.</td>
                                        <?php if ($is_admin): ?>
                                            <td>
                                                <span class="btn btn-secondary" disabled>Edit</span>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                    <tr>
                                        <td>6</td>
                                        <td><img src="assets/images/4.jpg" alt="Compact 4-Cup Coffee Maker" class="thumbnail"></td>
                                        <td>Compact 4-Cup Coffee Maker</td>
                                        <td class="price">29.99 SAR</td>
                                        <td><span class="size">Small</span></td>
                                        <td><span class="color">White</span></td>
                                        <td>Perfect for singles or small households. This compact coffee maker features a 4-cup glass carafe, auto shut-off, and brew strength selector.</td>
                                        <?php if ($is_admin): ?>
                                            <td>
                                                <span class="btn btn-secondary" disabled>Edit</span>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                    <tr>
                                        <td>7</td>
                                        <td><img src="assets/images/7.jpg" alt="Espresso Machine with Milk Frother" class="thumbnail"></td>
                                        <td>Espresso Machine with Milk Frother</td>
                                        <td class="price">129.99 SAR</td>
                                        <td><span class="size">Medium</span></td>
                                        <td><span class="color">Black</span></td>
                                        <td>Make café-quality espresso drinks at home. Features a 15-bar pump pressure, steam wand for milk frothing, removable water tank, and compact design.</td>
                                        <?php if ($is_admin): ?>
                                            <td>
                                                <span class="btn btn-secondary" disabled>Edit</span>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                    <tr>
                                        <td>8</td>
                                        <td><img src="assets/images/8.webp" alt="8-Cup Coffee Maker with Grinder" class="thumbnail"></td>
                                        <td>8-Cup Coffee Maker with Grinder</td>
                                        <td class="price">89.99 SAR</td>
                                        <td><span class="size">Medium</span></td>
                                        <td><span class="color">Black</span></td>
                                        <td>Fresh ground coffee with every brew. Features a built-in conical burr grinder, 8-cup glass carafe, programmable timer, auto-pause and serve function, and brew strength selector.</td>
                                        <?php if ($is_admin): ?>
                                            <td>
                                                <span class="btn btn-secondary" disabled>Edit</span>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                    <tr>
                                        <td>9</td>
                                        <td><img src="assets/images/11.jpg" alt="Single Serve Coffee Maker" class="thumbnail"></td>
                                        <td>Single Serve Coffee Maker</td>
                                        <td class="price">39.99 SAR</td>
                                        <td><span class="size">Small</span></td>
                                        <td><span class="color">Red</span></td>
                                        <td>Brew your favorite single-serve coffee pods or ground coffee. Features a 10-oz water reservoir, brew strength selector, and auto shut-off.</td>
                                        <?php if ($is_admin): ?>
                                            <td>
                                                <span class="btn btn-secondary" disabled>Edit</span>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="actions">
                        <a href="index.php" class="btn">Back to Home</a>
                        <a href="products_showcase.php" class="btn btn-secondary">View Product Gallery</a>
                        <?php if ($is_admin): ?>
                            <a href="Add_prodect.php" class="btn btn-success">Add New Product</a>
                            <a href="table.php" class="btn btn-warning">Manage Products</a>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </article>
    </main>
    
    <?php include('includes/footer.php'); ?>
</body>
</html>