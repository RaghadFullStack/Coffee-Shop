<?php
// Database configuration for XAMPP - using port 3306 as confirmed by logs
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "coffee_machine";
$port = 3306; // MySQL is actually running on port 3306, not 3307

// Create connection with error handling
$conn = null;
$connection_error = null;

// Function to create a new connection with proper timeout handling
function createMySQLConnection($server, $user, $pass, $database, $port) {
    $connection = null;
    try {
        // Create connection with timeout settings
        $connection = @new mysqli();
        if ($connection && $connection instanceof mysqli) {
            // Set connection options
            $connection->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);     // 5 second connection timeout
            $connection->options(MYSQLI_OPT_READ_TIMEOUT, 5);        // 5 second read timeout
            
            // Attempt connection
            @$connection->real_connect($server, $user, $pass, $database, $port);
            
            // Check if connection was successful
            if ($connection->connect_error) {
                $error = $connection->connect_error;
                $connection->close();
                return ['connection' => null, 'error' => $error];
            }
            
            // Set charset
            $connection->set_charset("utf8mb4");
            
            return ['connection' => $connection, 'error' => null];
        }
    } catch (Exception $e) {
        if ($connection && $connection instanceof mysqli) {
            $connection->close();
        }
        return ['connection' => null, 'error' => $e->getMessage()];
    }
    
    return ['connection' => null, 'error' => 'Failed to create mysqli object'];
}

// Try to connect
$result = createMySQLConnection($servername, $username, $password, $dbname, $port);

if ($result['connection'] && $result['connection'] instanceof mysqli) {
    $conn = $result['connection'];
    $connection_error = null;
} else {
    $connection_error = "Connection failed: " . $result['error'];
    $conn = null; // Explicitly set to null if connection failed
}

// Function to check and reconnect if needed
function checkAndReconnect(&$conn, $servername, $username, $password, $dbname, $port) {
    // If connection is null, try to create a new one
    if ($conn === null) {
        $result = createMySQLConnection($servername, $username, $password, $dbname, $port);
        if ($result['connection'] && $result['connection'] instanceof mysqli) {
            $conn = $result['connection'];
            return true;
        }
        return false;
    }
    
    // Check if existing connection is still alive
    if ($conn && $conn instanceof mysqli) {
        // First try ping
        if ($conn->ping() === false) {
            // Connection lost, try to reconnect
            $conn->close();
            $result = createMySQLConnection($servername, $username, $password, $dbname, $port);
            if ($result['connection'] && $result['connection'] instanceof mysqli) {
                $conn = $result['connection'];
                return true;
            }
            $conn = null;
            return false;
        }
        return true; // Connection is still alive
    }
    
    return $conn !== null && $conn instanceof mysqli;
}

// If connection still failed, the calling script will need to handle it
if ($conn === null) {
    // Don't throw an exception, just leave $conn as null
    // The calling script will check if $conn is null and handle accordingly
}
?>