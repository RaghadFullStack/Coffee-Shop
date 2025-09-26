<?php
session_start();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Simple validation (in a real application, you would check against a database)
    // For now, we'll keep it simple with admin/password as the admin credentials
    if ($username == "admin" && $password == "password") {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['is_admin'] = true; // Set admin flag
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ShAGHAF</title>
    <link rel="stylesheet" href="./assets/css/complete.css">
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
                    <h1>Admin Login</h1>
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-error"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="post" class="login-form">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" name="username" id="username" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Login</button>
                            <a href="index.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </section>
        </article>
    </main>
    
    <?php include('includes/footer.php');?>
</body>
</html>