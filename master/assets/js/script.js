'use strict';

// Ensure page loads immediately, don't wait for any external resources
document.addEventListener('DOMContentLoaded', function() {
  // Force page visibility immediately
  document.body.style.visibility = 'visible';
  document.body.style.opacity = '1';
  document.body.style.display = 'block';
  
  // Update badge counts on page load
  updateCartCount();
  updateFavoritesCount();
});

/**
 * Update cart count (non-blocking)
 */
function updateCartCount() {
  console.log('Updating cart count');
  // Don't block page loading for cart count
  setTimeout(function() {
    try {
      fetch('cart_handler.php?get_cart_count=true', {
        method: 'GET',
        cache: 'no-cache',
        timeout: 2000 // 2 second timeout
      })
      .then(response => {
        console.log('Cart count response:', response);
        if (response.ok) {
          return response.json();
        }
        throw new Error('Cart handler not available');
      })
      .then(data => {
        console.log('Cart count data:', data);
        const cartBadges = document.querySelectorAll('.header-action-btn[aria-label="Open shopping cart"] .btn-badge');
        cartBadges.forEach(badge => {
          badge.textContent = data.count || '0';
        });
      })
      .catch(error => {
        console.log('Cart count update skipped:', error.message);
        // Silently fail - don't block page
        const cartBadges = document.querySelectorAll('.header-action-btn[aria-label="Open shopping cart"] .btn-badge');
        cartBadges.forEach(badge => {
          badge.textContent = '0';
        });
      });
    } catch (e) {
      console.log('Cart update failed:', e.message);
    }
  }, 500); // Delay cart update so it doesn't block page load
}

/**
 * Update favorites count (non-blocking)
 */
function updateFavoritesCount() {
  console.log('Updating favorites count');
  // Don't block page loading for favorites count
  setTimeout(function() {
    try {
      fetch('favorites_handler.php?get_favorites_count=true', {
        method: 'GET',
        cache: 'no-cache',
        timeout: 2000 // 2 second timeout
      })
      .then(response => {
        console.log('Favorites count response:', response);
        if (response.ok) {
          return response.json();
        }
        throw new Error('Favorites handler not available');
      })
      .then(data => {
        console.log('Favorites count data:', data);
        const favoritesBadges = document.querySelectorAll('.header-action-btn[aria-label="Open wishlist"] .btn-badge');
        favoritesBadges.forEach(badge => {
          badge.textContent = data.count || '0';
        });
      })
      .catch(error => {
        console.log('Favorites count update skipped:', error.message);
        // Silently fail - don't block page
        const favoritesBadges = document.querySelectorAll('.header-action-btn[aria-label="Open wishlist"] .btn-badge');
        favoritesBadges.forEach(badge => {
          badge.textContent = '0';
        });
      });
    } catch (e) {
      console.log('Favorites update failed:', e.message);
    }
  }, 500); // Delay favorites update so it doesn't block page load
}

// Add to cart functionality
function addToCart(productId, productName, productPrice, productImage) {
  console.log('Adding to cart:', productId, productName, productPrice, productImage);
  fetch('cart_handler.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'add_to_cart=true&product_id=' + productId + '&product_name=' + encodeURIComponent(productName) + '&product_price=' + productPrice + '&product_image=' + encodeURIComponent(productImage)
  })
  .then(response => {
    console.log('Add to cart response:', response);
    return response.json();
  })
  .then(data => {
    console.log('Add to cart data:', data);
    if (data.success) {
      alert(data.message);
      updateCartCount();
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(error => {
    console.log('Error adding to cart:', error);
    alert('Error adding to cart: ' + error.message);
  });
}

// Add to favorites functionality
function addToFavorites(productId, productName, productImage) {
  console.log('Adding to favorites:', productId, productName, productImage);
  fetch('favorites_handler.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'add_to_favorites=true&product_id=' + productId + '&product_name=' + encodeURIComponent(productName) + '&product_image=' + encodeURIComponent(productImage)
  })
  .then(response => {
    console.log('Add to favorites response:', response);
    return response.json();
  })
  .then(data => {
    console.log('Add to favorites data:', data);
    if (data.success) {
      alert(data.message);
      updateFavoritesCount();
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(error => {
    console.log('Error adding to favorites:', error);
    alert('Error adding to favorites: ' + error.message);
  });
}