// Function to update cart and favorites badge counts
function updateBadgeCounts() {
    // Get session data via AJAX
    fetch('get_session_counts.php')
        .then(response => response.json())
        .then(data => {
            // Update cart badge
            const cartBadges = document.querySelectorAll('[href="./cart.php"] .btn-badge');
            cartBadges.forEach(badge => {
                badge.textContent = data.cartCount;
                badge.setAttribute('value', data.cartCount);
            });
            
            // Update favorites badge
            const favoritesBadges = document.querySelectorAll('[href="./favorites.php"] .btn-badge');
            favoritesBadges.forEach(badge => {
                badge.textContent = data.favoritesCount;
                badge.setAttribute('value', data.favoritesCount);
            });
        })
        .catch(error => {
            console.error('Error updating badge counts:', error);
        });
}

// Update badge counts when page loads
document.addEventListener('DOMContentLoaded', function() {
    updateBadgeCounts();
});

// Also update badge counts periodically (every 30 seconds)
setInterval(updateBadgeCounts, 30000);