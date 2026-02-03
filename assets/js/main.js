/**
 * Moretti Theme Main JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Moretti Theme loaded!');

    // Mobile menu toggle - Handled by inline onclick in header.php for better reliability
    /*
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
    */

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    // Filter toggle
    const filterToggle = document.getElementById('filter-toggle');
    const filtersPanel = document.getElementById('filters-panel');
    
    if (filterToggle && filtersPanel) {
        filterToggle.addEventListener('click', function() {
            filtersPanel.classList.toggle('hidden');
            
            // Rotate icon
            const icon = this.querySelector('svg');
            if (icon) {
                icon.classList.toggle('rotate-180');
            }
        });
    }

    // Product Image Slider Functions
    window.morettiSliderNext = function(button) {
        const slider = button.closest('.product-image-slider');
        const images = slider.querySelectorAll('.slider-image');
        const dots = slider.querySelectorAll('.slider-dot');
        let currentIndex = 0;
        
        // Find current active image
        images.forEach((img, index) => {
            if (img.classList.contains('active')) {
                currentIndex = index;
            }
        });
        
        // Calculate next index
        const nextIndex = (currentIndex + 1) % images.length;
        
        // Update images
        images[currentIndex].classList.remove('active');
        images[nextIndex].classList.add('active');
        
        // Update dots
        if (dots.length > 0) {
            dots[currentIndex].classList.remove('bg-white', 'w-4');
            dots[currentIndex].classList.add('bg-white/60');
            dots[nextIndex].classList.remove('bg-white/60');
            dots[nextIndex].classList.add('bg-white', 'w-4');
        }
    };

    window.morettiSliderPrev = function(button) {
        const slider = button.closest('.product-image-slider');
        const images = slider.querySelectorAll('.slider-image');
        const dots = slider.querySelectorAll('.slider-dot');
        let currentIndex = 0;
        
        // Find current active image
        images.forEach((img, index) => {
            if (img.classList.contains('active')) {
                currentIndex = index;
            }
        });
        
        // Calculate previous index
        const prevIndex = (currentIndex - 1 + images.length) % images.length;
        
        // Update images
        images[currentIndex].classList.remove('active');
        images[prevIndex].classList.add('active');
        
        // Update dots
        if (dots.length > 0) {
            dots[currentIndex].classList.remove('bg-white', 'w-4');
            dots[currentIndex].classList.add('bg-white/60');
            dots[prevIndex].classList.remove('bg-white/60');
            dots[prevIndex].classList.add('bg-white', 'w-4');
        }
    };

    // Product details accordions
    document.querySelectorAll('.product-accordion details').forEach(details => {
        details.addEventListener('toggle', function() {
            const icon = this.querySelector('summary svg');
            if (this.open) {
                icon.style.transform = 'rotate(180deg)';
            } else {
                icon.style.transform = 'rotate(0deg)';
            }
        });
    });
});

// Quick Add to Cart functionality
function morettiQuickAddToCart(productId) {
    // Show loading state
    const button = document.querySelector(`button[data-product-id="${productId}"]`);
    const originalContent = button.innerHTML;
    
    button.innerHTML = '<svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    button.disabled = true;

    // AJAX add to cart
    const formData = new FormData();
    formData.append('action', 'moretti_quick_add_to_cart');
    formData.append('product_id', productId);
    formData.append('quantity', 1);
    formData.append('nonce', morettiData.nonce);

    fetch(morettiData.ajaxUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count
            updateCartCount();
            
            // Show success state
            button.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
            
            // Reset after 2 seconds
            setTimeout(() => {
                button.innerHTML = originalContent;
                button.disabled = false;
            }, 2000);
        } else {
            // Show error
            alert(data.data.message || 'Error adding to cart');
            button.innerHTML = originalContent;
            button.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding to cart');
        button.innerHTML = originalContent;
        button.disabled = false;
    });
}

// Update cart count
function updateCartCount() {
    fetch(morettiData.ajaxUrl + '?action=moretti_get_cart_count')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cartCountElements = document.querySelectorAll('.cart-count, [data-cart-count]');
                cartCountElements.forEach(el => {
                    el.textContent = data.data.count;
                });
            }
        })
        .catch(error => console.error('Error updating cart count:', error));
}

