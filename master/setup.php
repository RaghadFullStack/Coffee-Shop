<?php
// Check if the database creation script has been run
$databaseExists = false;
$tableExists = false;

// Try to connect to check if database exists
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "coffee_machine";
$port = 3307;

try {
    $conn = @new mysqli();
    if ($conn) {
        $conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 3);
        $conn->options(MYSQLI_OPT_READ_TIMEOUT, 3);
        @$conn->real_connect($servername, $username, $password, $dbname, $port);
        
        if (!$conn->connect_error) {
            $databaseExists = true;
            
            // Check if Product table exists
            $result = $conn->query("SHOW TABLES LIKE 'Product'");
            if ($result && $result->num_rows > 0) {
                $tableExists = true;
            }
        }
        $conn->close();
    }
} catch (Exception $e) {
    // Connection failed
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - ShAGHAF</title>
    <link rel="stylesheet" href="assets/css/complete.css">
    <style>
        .setup-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .status-box {
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        
        .status-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .status-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 25px;
            margin: 10px 5px;
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
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }
        
        .instructions {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        
        .instructions h3 {
            margin-top: 0;
        }
        
        .instructions ol {
            padding-left: 20px;
        }
        
        .instructions li {
            margin-bottom: 10px;
        }
        
        .troubleshooting {
            background: #e9f7fe;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        
        .troubleshooting h3 {
            margin-top: 0;
            color: #0c5460;
        }
        
        .troubleshooting ul {
            padding-left: 20px;
        }
        
        .troubleshooting li {
            margin-bottom: 10px;
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
                    <div class="setup-container">
                        <h1>Database Setup</h1>
                        <p>This page helps you set up the database for your coffee machine application.</p>
                        
                        <div class="status-box <?php echo $databaseExists ? 'status-success' : 'status-warning'; ?>">
                            <h3>Database Status</h3>
                            <?php if ($databaseExists): ?>
                                <p>✓ Database "coffee_machine" exists</p>
                                <?php if ($tableExists): ?>
                                    <p>✓ Product table exists</p>
                                    <p class="status-success">✓ Database is ready to use!</p>
                                <?php else: ?>
                                    <p>⚠ Product table does not exist</p>
                                <?php endif; ?>
                            <?php else: ?>
                                <p>⚠ Database "coffee_machine" does not exist</p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="instructions">
                            <h3>Setup Options</h3>
                            <p>You can create the database using one of the following methods:</p>
                            <div style="text-align: center; margin: 20px 0;">
                                <?php if (!$databaseExists || !$tableExists): ?>
                                    <a href="create_database.php" class="btn btn-primary">Create Database (Advanced)</a>
                                    <a href="simple_create_db.php" class="btn btn-primary">Create Database (Simple)</a>
                                <?php else: ?>
                                    <a href="create_database.php" class="btn btn-warning">Recreate Database (Advanced)</a>
                                    <a href="simple_create_db.php" class="btn btn-warning">Recreate Database (Simple)</a>
                                <?php endif; ?>
                            </div>
                            <p><strong>Advanced Method:</strong> Uses robust error handling and multiple connection attempts</p>
                            <p><strong>Simple Method:</strong> Uses straightforward connection approach</p>
                        </div>
                        
                        <div style="text-align: center;">
                            <a href="test_page.php" class="btn btn-success">Test Database Connection</a>
                            <a href="test_db_connection.php" class="btn btn-secondary">Advanced Connection Test</a>
                            <a href="test_mysql_gone_away_fix.php" class="btn btn-secondary">Test 'Server Gone Away' Fix</a>
                            <a href="check_mysql_config.php" class="btn btn-secondary">Check MySQL Configuration</a>
                            <a href="find_mysql_port.php" class="btn btn-secondary">Find MySQL Port</a>
                            <a href="index.php" class="btn btn-secondary">Back to Homepage</a>
                        </div>
                        
                        <div class="troubleshooting">
                            <h3>Troubleshooting Database Connection Issues</h3>
                            <p>If you're having trouble with the database setup:</p>
                            <ul>
                                <li><strong>Make sure XAMPP MySQL service is running</strong></li>
                                <li>Check that no firewall is blocking the connection</li>
                                <li>Verify your XAMPP installation is working correctly</li>
                                <li>If you get "MySQL server has gone away" error:
                                    <ul>
                                        <li>Restart the MySQL service in XAMPP</li>
                                        <li>Try running the setup again</li>
                                        <li>Check if other applications are using MySQL</li>
                                    </ul>
                                </li>
                                <li>If connection still fails, try accessing phpMyAdmin directly to verify MySQL is working</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>
        </article>
    </main>
    
    <?php include('includes/footer.php'); ?>
</body>
</html>