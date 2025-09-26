<?php
// Enhanced database creation script with better error handling

// Include the database connection
include('includes/db_Connection.php');

// Function to create database directly
function createDatabaseDirect($server, $username, $password, $port) {
    echo "<h2>Creating Database Directly</h2>";
    
    try {
        // Connect without specifying database
        $conn = @new mysqli($server, $username, $password, null, $port);
        
        if ($conn->connect_error) {
            echo "<p style='color: red;'>Connection failed: " . $conn->connect_error . "</p>";
            return false;
        }
        
        // Create database
        $dbname = "coffee_machine";
        $sql = "CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        
        if ($conn->query($sql) === TRUE) {
            echo "<p style='color: green;'>Database '$dbname' created successfully or already exists!</p>";
            
            // Select the database
            if ($conn->select_db($dbname)) {
                echo "<p style='color: green;'>Database selected successfully!</p>";
                
                // Create tables
                createTables($conn);
            } else {
                echo "<p style='color: red;'>Error selecting database: " . $conn->error . "</p>";
            }
        } else {
            echo "<p style='color: red;'>Error creating database: " . $conn->error . "</p>";
        }
        
        $conn->close();
        return true;
    } catch (Exception $e) {
        echo "<p style='color: red;'>Exception: " . $e->getMessage() . "</p>";
        return false;
    }
}

// Function to create tables
function createTables($conn) {
    echo "<h3>Creating Tables</h3>";
    
    // Create products table
    $sql = "CREATE TABLE IF NOT EXISTS `products` (
        `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `description` TEXT,
        `price` DECIMAL(10,2) NOT NULL,
        `image` VARCHAR(255),
        `category` VARCHAR(100),
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>Products table created successfully or already exists!</p>";
    } else {
        echo "<p style='color: red;'>Error creating products table: " . $conn->error . "</p>";
    }
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS `users` (
        `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `email` VARCHAR(100),
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>Users table created successfully or already exists!</p>";
    } else {
        echo "<p style='color: red;'>Error creating users table: " . $conn->error . "</p>";
    }
    
    // Insert sample data
    insertSampleData($conn);
}

// Function to insert sample data
function insertSampleData($conn) {
    echo "<h3>Inserting Sample Data</h3>";
    
    // Check if we already have data
    $result = $conn->query("SELECT COUNT(*) as count FROM products");
    $row = $result->fetch_assoc();
    
    if ($row['count'] == 0) {
        // Insert sample products
        $products = [
            ['name' => 'Espresso Machine', 'description' => 'Professional espresso machine with steam wand', 'price' => 299.99, 'image' => 'espresso.jpg', 'category' => 'Machines'],
            ['name' => 'Coffee Grinder', 'description' => 'Burr grinder for perfect coffee beans', 'price' => 149.99, 'image' => 'grinder.jpg', 'category' => 'Accessories'],
            ['name' => 'French Press', 'description' => 'Classic French press coffee maker', 'price' => 39.99, 'image' => 'frenchpress.jpg', 'category' => 'Brewing'],
            ['name' => 'Coffee Beans', 'description' => 'Premium Arabica coffee beans', 'price' => 19.99, 'image' => 'beans.jpg', 'category' => 'Beans']
        ];
        
        foreach ($products as $product) {
            $stmt = $conn->prepare("INSERT INTO products (name, description, price, image, category) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdss", $product['name'], $product['description'], $product['price'], $product['image'], $product['category']);
            
            if ($stmt->execute()) {
                echo "<p style='color: green;'>Inserted: " . $product['name'] . "</p>";
            } else {
                echo "<p style='color: red;'>Error inserting " . $product['name'] . ": " . $stmt->error . "</p>";
            }
            $stmt->close();
        }
    } else {
        echo "<p>Sample data already exists, skipping insertion.</p>";
    }
}

echo "<h1>Database Setup</h1>";

// Try to create database using direct connection
$server = "127.0.0.1";
$username = "root";
$password = "";
$port = 3306; // Changed from 3307 to 3306

createDatabaseDirect($server, $username, $password, $port);

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Try accessing your website again</li>";
echo "<li>If database connection still fails, check XAMPP Control Panel</li>";
echo "<li>Try restarting both Apache and MySQL services</li>";
echo "</ol>";

echo "<p><a href='setup.php'>Back to Setup Page</a> | <a href='index.php'>Back to Homepage</a></p>";
?>