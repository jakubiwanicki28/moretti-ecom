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

    // Custom Select Dropdowns
    function initCustomSelects() {
        const selects = document.querySelectorAll('.product-cart-form-custom .variations select');
        
        selects.forEach(select => {
            if (select.parentElement.querySelector('.custom-select-wrapper')) return;

            const wrapper = document.createElement('div');
            wrapper.className = 'custom-select-wrapper relative w-full';
            
            const trigger = document.createElement('div');
            trigger.className = 'custom-select-trigger w-full px-4 py-3 border border-gray-300 text-sm bg-white cursor-pointer flex items-center justify-between select-none rounded-none';
            trigger.innerHTML = `<span>${select.options[select.selectedIndex].text}</span><svg class="w-4 h-4 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"></path></svg>`;
            
            const optionsList = document.createElement('div');
            optionsList.className = 'custom-options-list hidden absolute top-full left-0 w-full bg-white border border-gray-300 border-t-0 z-[100] shadow-lg rounded-none';
            
            Array.from(select.options).forEach((option, index) => {
                const opt = document.createElement('div');
                opt.className = `custom-option px-4 py-3 text-sm hover:bg-sand-50 cursor-pointer transition-colors ${index === select.selectedIndex ? 'bg-sand-50 font-semibold' : ''}`;
                opt.textContent = option.text;
                opt.dataset.value = option.value;
                
                opt.addEventListener('click', () => {
                    select.value = option.value;
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                    trigger.querySelector('span').textContent = option.text;
                    optionsList.classList.add('hidden');
                    trigger.querySelector('svg').classList.remove('rotate-180');
                    
                    // Update active state in list
                    optionsList.querySelectorAll('.custom-option').forEach(el => el.classList.remove('bg-sand-50', 'font-semibold'));
                    opt.classList.add('bg-sand-50', 'font-semibold');
                });
                
                optionsList.appendChild(opt);
            });
            
            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                const isOpen = !optionsList.classList.contains('hidden');
                
                // Close all other custom selects
                document.querySelectorAll('.custom-options-list').forEach(list => list.classList.add('hidden'));
                document.querySelectorAll('.custom-select-trigger svg').forEach(svg => svg.classList.remove('rotate-180'));
                
                if (!isOpen) {
                    optionsList.classList.remove('hidden');
                    trigger.querySelector('svg').classList.add('rotate-180');
                }
            });
            
            wrapper.appendChild(trigger);
            wrapper.appendChild(optionsList);
            
            // Hide original select but keep it for WooCommerce logic
            select.style.display = 'none';
            select.parentElement.appendChild(wrapper);
            
            // Sync back if select changes externally (e.g. reset variations)
            select.addEventListener('change', () => {
                trigger.querySelector('span').textContent = select.options[select.selectedIndex].text;
                optionsList.querySelectorAll('.custom-option').forEach(opt => {
                    opt.classList.toggle('bg-sand-50', opt.dataset.value === select.value);
                    opt.classList.toggle('font-semibold', opt.dataset.value === select.value);
                });
            });
        });
    }

    // Close dropdowns on outside click
    document.addEventListener('click', () => {
        document.querySelectorAll('.custom-options-list').forEach(list => list.classList.add('hidden'));
        document.querySelectorAll('.custom-select-trigger svg').forEach(svg => svg.classList.remove('rotate-180'));
    });

    initCustomSelects();

    // Re-init when WooCommerce updates variations (e.g. AJAX)
    jQuery(document.body).on('woocommerce_variation_has_changed', function() {
        // Small delay to ensure DOM is updated
        setTimeout(initCustomSelects, 50);
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

