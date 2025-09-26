<?php
// Initialize session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Initialize session arrays if they don't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

if (!isset($_SESSION['favorites'])) {
    $_SESSION['favorites'] = array();
}

// Check if user is logged in as admin
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

// Calculate badge counts
$cartCount = 0;
foreach ($_SESSION['cart'] as $item) {
    $cartCount += $item['quantity'];
}

$favoritesCount = count($_SESSION['favorites']);
?>

<div class="nav-wrapper">
  <div class="container">
    <nav class="navbar" data-navbar>
      <ul class="navbar-list">
        <li>
          <a href="./index.php" class="navbar-link">Home</a>
        </li>
        <li>
          <a href="./products_showcase.php" class="navbar-link">Products</a>
        </li>
        <?php if ($is_admin): ?>
        <li>
          <a href="./Add_prodect.php" class="navbar-link">Add Products</a>
        </li>
        <li>
          <a href="./table.php" class="navbar-link">Manage Products</a>
        </li>
        <li>
          <a href="./logout.php" class="navbar-link">Logout</a>
        </li>
        <?php else: ?>
        <li>
          <a href="./Login.php" class="navbar-link">Admin Login</a>
        </li>
        <?php endif; ?>
      </ul>
    </nav>

    <div class="header-action">
      <a href="./favorites.php" class="header-action-btn" aria-label="Open wishlist">
        <span style="font-size: 20px;">&#9829;</span>
        <data class="btn-badge" value="<?php echo $favoritesCount; ?>"><?php echo $favoritesCount; ?></data>
      </a>
      <a href="./cart.php" class="header-action-btn" aria-label="Open shopping cart">
        <span style="font-size: 20px;">&#128722;</span>
        <data class="btn-badge" value="<?php echo $cartCount; ?>"><?php echo $cartCount; ?></data>
      </a>
    </div>
    <script src="./assets/js/cart-favorites.js"></script>
  </div>
</div>

<style>
  .nav-wrapper {
    background: #f8f9fa;
    padding: 15px 0;
    border-bottom: 1px solid #dee2e6;
  }
  
  .nav-wrapper .container {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .navbar-list {
    display: flex;
    list-style: none;
  }
  
  .navbar-link {
    text-decoration: none;
    padding: 10px 15px;
    color: #2c3e50;
    font-weight: 600;
    font-size: 16px;
    border-radius: 4px;
    transition: all 0.3s ease;
    margin: 0 5px;
  }
  
  .navbar-link:hover {
    color: #fff;
    background: #2c3e50;
  }
  
  .header-action {
    display: flex;
    align-items: center;
  }
  
  .header-action-btn {
    background: white;
    border: 1px solid #ddd;
    font-size: 1.2rem;
    margin-left: 15px;
    cursor: pointer;
    padding: 10px;
    border-radius: 50%;
    transition: all 0.3s ease;
    position: relative;
    text-decoration: none;
    color: inherit;
  }
  
  .header-action-btn:hover {
    background-color: #2c3e50;
    color: white;
  }
  
  .btn-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #c0392b;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
  }
  
  /* Ensure consistent container styling */
  .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 15px;
    position: relative;
  }
  
  /* Responsive adjustments */
  @media (max-width: 768px) {
    .navbar-list {
      flex-direction: column;
    }
    
    .navbar-link {
      margin: 5px 0;
      padding: 12px 18px;
    }
  }
</style>