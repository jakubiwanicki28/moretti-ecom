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

    // Custom Select Dropdowns - Premium styling
    function initCustomSelects() {
        const selects = document.querySelectorAll('.product-cart-form-custom .variations select, select.moretti-custom-select');
        
        selects.forEach(select => {
            if (select.parentElement.querySelector('.custom-select-wrapper')) return;

            const wrapper = document.createElement('div');
            wrapper.className = 'custom-select-wrapper relative w-full';
            
            const trigger = document.createElement('div');
            trigger.className = 'custom-select-trigger';
            trigger.style.cssText = 'height: 64px; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; padding-right: 45px; background: white; border: 1px solid #e5e7eb; cursor: pointer; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.15em; color: #2a2826; transition: all 0.2s;';
            
            trigger.innerHTML = `<span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${select.options[select.selectedIndex].text}</span><svg style="width: 14px; height: 14px; margin-left: 12px; flex-shrink: 0; transition: transform 0.2s;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>`;
            
            const optionsList = document.createElement('div');
            optionsList.className = 'custom-options-list';
            optionsList.style.cssText = 'display: none; position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #e5e7eb; border-top: none; max-height: 300px; overflow-y: auto; z-index: 1000;';
            
            Array.from(select.options).forEach((option, index) => {
                const opt = document.createElement('div');
                opt.className = 'custom-option';
                opt.style.cssText = `padding: 16px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.15em; cursor: pointer; transition: background 0.2s; ${index === select.selectedIndex ? 'background: #f9fafb;' : ''}`;
                opt.textContent = option.text;
                opt.dataset.value = option.value;
                
                opt.addEventListener('mouseenter', () => {
                    opt.style.background = '#f9fafb';
                });
                opt.addEventListener('mouseleave', () => {
                    if (select.value !== opt.dataset.value) {
                        opt.style.background = 'white';
                    }
                });
                
                opt.addEventListener('click', () => {
                    select.value = option.value;
                    
                    // Trigger native event
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                    
                    // IMPORTANT: Trigger jQuery change for WooCommerce variation script
                    if (typeof jQuery !== 'undefined') {
                        jQuery(select).trigger('change');
                    }
                    
                    if (select.onchange) {
                        select.onchange();
                    }

                    trigger.querySelector('span').textContent = option.text;
                    optionsList.style.display = 'none';
                    trigger.querySelector('svg').style.transform = 'rotate(0deg)';
                    
                    // Remove error state on selection
                    wrapper.classList.remove('error');
                    
                    optionsList.querySelectorAll('.custom-option').forEach(el => {
                        el.style.background = el === opt ? '#f9fafb' : 'white';
                    });
                });
                
                optionsList.appendChild(opt);
            });
            
            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                const isOpen = optionsList.style.display !== 'none';
                
                document.querySelectorAll('.custom-options-list').forEach(list => list.style.display = 'none');
                document.querySelectorAll('.custom-select-trigger svg').forEach(svg => svg.style.transform = 'rotate(0deg)');
                
                if (!isOpen) {
                    optionsList.style.display = 'block';
                    trigger.querySelector('svg').style.transform = 'rotate(180deg)';
                }
            });
            
            trigger.addEventListener('mouseenter', () => {
                trigger.style.borderColor = '#2a2826';
                trigger.style.background = '#f9fafb';
            });
            trigger.addEventListener('mouseleave', () => {
                trigger.style.borderColor = '#e5e7eb';
                trigger.style.background = 'white';
            });
            
            wrapper.appendChild(trigger);
            wrapper.appendChild(optionsList);
            
            select.style.display = 'none';
            select.parentElement.appendChild(wrapper);
            
            select.addEventListener('change', () => {
                trigger.querySelector('span').textContent = select.options[select.selectedIndex].text;
                optionsList.querySelectorAll('.custom-option').forEach(opt => {
                    opt.style.background = opt.dataset.value === select.value ? '#f9fafb' : 'white';
                });
            });
        });
    }

    document.addEventListener('click', () => {
        document.querySelectorAll('.custom-options-list').forEach(list => list.style.display = 'none');
        document.querySelectorAll('.custom-select-trigger svg').forEach(svg => svg.style.transform = 'rotate(0deg)');
    });

    initCustomSelects();

    // WooCommerce Variation Image Swap Fix
    if (typeof jQuery !== 'undefined') {
        jQuery('form.variations_form').on('found_variation', function(event, variation) {
            const mainImage = document.querySelector('.main-product-image img');
            const thumbnails = document.querySelectorAll('.thumbnail-item');
            
            if (variation && variation.image && variation.image.src) {
                // 1. Update main image
                if (mainImage) {
                    mainImage.src = variation.image.src;
                    mainImage.srcset = variation.image.srcset || '';
                    mainImage.alt = variation.image.alt || '';
                }
                
                // 2. Try to find and highlight matching thumbnail
                let foundMatch = false;
                thumbnails.forEach(thumb => {
                    const thumbImg = thumb.querySelector('img');
                    if (thumbImg && (thumbImg.src === variation.image.thumb_src || thumbImg.src === variation.image.src)) {
                        thumbnails.forEach(t => {
                            t.classList.remove('border-charcoal');
                            t.classList.add('border-gray-200');
                        });
                        thumb.classList.add('border-charcoal');
                        thumb.classList.remove('border-gray-200');
                        foundMatch = true;
                    }
                });
                
                // 3. If no matching thumbnail, clear highlights
                if (!foundMatch) {
                    thumbnails.forEach(t => {
                        t.classList.remove('border-charcoal');
                        t.classList.add('border-gray-200');
                    });
                }
            }
        });

        // Reset image when variation is cleared
        jQuery('form.variations_form').on('reset_data', function() {
            const mainImage = document.querySelector('.main-product-image img');
            const firstThumb = document.querySelector('.thumbnail-item');
            
            if (firstThumb) {
                firstThumb.click();
            }
        });
    }

    // Single Product Validation
    const cartForm = document.querySelector('form.cart');
    if (cartForm) {
        const addToCartBtn = cartForm.querySelector('.single_add_to_cart_button');
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', function(e) {
                const selects = cartForm.querySelectorAll('select');
                let hasError = false;

                selects.forEach(select => {
                    if (select.hasAttribute('required') || select.closest('.variations')) {
                        if (!select.value || select.value === '') {
                            const wrapper = select.parentElement.querySelector('.custom-select-wrapper');
                            if (wrapper) {
                                wrapper.classList.add('error');
                                hasError = true;
                            }
                        }
                    }
                });

                if (hasError) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Scroll to first error
                    const firstError = cartForm.querySelector('.custom-select-wrapper.error');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    return false;
                }
            });
        }
    }

    if (typeof jQuery !== 'undefined') {
        jQuery(document.body).on('woocommerce_variation_has_changed', function() {
            setTimeout(initCustomSelects, 50);
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
        images[currentIndex].classList.remove('active', 'opacity-100');
        images[currentIndex].classList.add('opacity-0');
        images[nextIndex].classList.add('active', 'opacity-100');
        images[nextIndex].classList.remove('opacity-0');
        
        // Update dots
        if (dots.length > 0) {
            dots[currentIndex].classList.remove('bg-charcoal', 'w-4');
            dots[currentIndex].classList.add('bg-charcoal/20');
            dots[nextIndex].classList.remove('bg-charcoal/20');
            dots[nextIndex].classList.add('bg-charcoal', 'w-4');
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
        images[currentIndex].classList.remove('active', 'opacity-100');
        images[currentIndex].classList.add('opacity-0');
        images[prevIndex].classList.add('active', 'opacity-100');
        images[prevIndex].classList.remove('opacity-0');
        
        // Update dots
        if (dots.length > 0) {
            dots[currentIndex].classList.remove('bg-charcoal', 'w-4');
            dots[currentIndex].classList.add('bg-charcoal/20');
            dots[prevIndex].classList.remove('bg-charcoal/20');
            dots[prevIndex].classList.add('bg-charcoal', 'w-4');
        }
    };

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

    // Debug log
    console.log('Adding to cart:', productId);

    fetch(morettiData.ajaxUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('Response:', data);
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
            console.error('Add to cart failed:', data);
            
            // If it's a variable product, redirect to product page
            if (data.data && data.data.redirect) {
                window.location.href = data.data.redirect;
                return;
            }
            
            alert(data.data && data.data.message ? data.data.message : 'Nie udało się dodać produktu do koszyka. Spróbuj odświeżyć stronę.');
            button.innerHTML = originalContent;
            button.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Wystąpił błąd połączenia. Spróbuj ponownie.');
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
                // Update all possible cart count indicators
                const cartCountElements = document.querySelectorAll('.cart-count, [data-cart-count], .absolute.top-1.right-1');
                cartCountElements.forEach(el => {
                    el.textContent = data.data.count;
                    el.style.display = data.data.count > 0 ? 'flex' : 'none';
                });
            }
        })
        .catch(error => console.error('Error updating cart count:', error));
}

