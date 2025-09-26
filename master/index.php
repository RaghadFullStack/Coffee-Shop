<?php
// Start session for cart functionality
session_start();

// Check if user is admin
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ShAGHAF - Coffee Shop</title>
  <link rel="stylesheet" href="./assets/css/complete.css">
  <style>
    .home-wrapper {
      background: url('./assets/images/7.jpg') no-repeat center center;
      background-size: cover;
      color: white;
      padding: 180px 30px;
      text-align: center;
      margin-bottom: 30px;
      border-radius: 8px;
      position: relative;
    }
    
    .home-wrapper::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(44, 62, 80, 0.8);
      border-radius: 8px;
    }
    
    .home-content {
      position: relative;
      z-index: 1;
      max-width: 800px;
      margin: 0 auto;
    }
    
    .page-title {
      color: white;
      margin: 0 0 15px 0;
      font-size: 4rem;
      text-shadow: 0 2px 4px rgba(0,0,0,0.5);
    }
    
    .home-wrapper p {
      font-size: 1.8rem;
      opacity: 0.9;
      max-width: 600px;
      margin: 0 auto 30px;
      text-shadow: 0 1px 3px rgba(0,0,0,0.5);
    }
    
    .feature-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 25px;
      padding: 20px;
    }
    
    .feature-card {
      background: white;
      border-radius: 8px;
      padding: 25px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
      border: 1px solid #eee;
      transition: all 0.3s ease;
      text-align: center;
    }
    
    .feature-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .feature-card h3 {
      color: #2c3e50;
      margin: 0 0 15px 0;
      font-size: 1.4rem;
    }
    
    .feature-card p {
      color: #666;
      line-height: 1.6;
      margin-bottom: 20px;
    }
    
    .btn-primary {
      display: inline-block;
      background: #007bff;
      color: white;
      padding: 12px 24px;
      text-decoration: none;
      border-radius: 4px;
      font-weight: 600;
      transition: all 0.3s ease;
      border: none;
      cursor: pointer;
      font-size: 16px;
    }
    
    .btn-primary:hover {
      background: #0056b3;
      transform: translateY(-2px);
      color: white;
    }
    
    @media (max-width: 992px) {
      .feature-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
      }
      
      .page-title {
        font-size: 3.2rem;
      }
      
      .home-wrapper {
        padding: 120px 30px;
      }
      
      .home-wrapper p {
        font-size: 1.5rem;
      }
    }
    
    @media (max-width: 768px) {
      .feature-grid {
        grid-template-columns: 1fr;
        gap: 15px;
      }
      
      .home-wrapper {
        padding: 80px 20px;
      }
      
      .page-title {
        font-size: 2.5rem;
      }
      
      .home-wrapper p {
        font-size: 1.3rem;
      }
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
      <div class="home-wrapper">
        <div class="home-content">
          <h1 class="page-title">WELCOME TO ShAGHAF Store</h1>
          <p>Your Premium Coffee Machine Destination</p>
          <?php if ($is_admin): ?>
            <p style="font-size: 1.5rem; margin-top: 20px;">You are logged in as Administrator</p>
          <?php endif; ?>
        </div>
      </div>
      
      <div class="welcome-content">
        <div class="feature-grid">
          <?php if ($is_admin): ?>
          <div class="feature-card">
            <h3>Add Products</h3>
            <p>Easily add new coffee machines to your inventory with our simple form.</p>
            <a href="Add_prodect.php" class="btn-primary">Add Product</a>
          </div>
          
          <div class="feature-card">
            <h3>Manage Inventory</h3>
            <p>View, edit, and manage all your coffee machines in an organized table.</p>
            <a href="table.php" class="btn-primary">View Products</a>
          </div>
          <?php endif; ?>
          
          <div class="feature-card">
            <h3>Products Showcase</h3>
            <p>Browse our collection of premium coffee machines.</p>
            <a href="products_showcase.php" class="btn-primary">View Products</a>
          </div>
          
          <div class="feature-card">
            <h3>Products Table</h3>
            <p>See all products in a detailed table format.</p>
            <a href="all_products_table.php" class="btn-primary">Products Table</a>
          </div>
        </div>
      </div>
    </article>
  </main>
  <?php include('includes/footer.php');?>
  
  <script src="./assets/js/script.js"></script>
</body>
</html>