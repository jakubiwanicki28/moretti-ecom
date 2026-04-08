/**
 * Moretti Theme Main JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Moretti Theme loaded!');

    const WISHLIST_COOKIE_NAME = 'moretti_wishlist';
    const WISHLIST_COOKIE_DAYS = 365;

    function readWishlistCookie() {
        const cookieRow = document.cookie
            .split('; ')
            .find((row) => row.startsWith(`${WISHLIST_COOKIE_NAME}=`));

        if (!cookieRow) return [];

        const cookieValue = cookieRow.split('=')[1] || '';
        try {
            const parsed = JSON.parse(decodeURIComponent(cookieValue));
            if (!Array.isArray(parsed)) return [];
            return parsed
                .map((id) => parseInt(id, 10))
                .filter((id) => Number.isInteger(id) && id > 0);
        } catch (error) {
            return [];
        }
    }

    function writeWishlistCookie(ids) {
        const uniqueIds = Array.from(new Set(ids))
            .map((id) => parseInt(id, 10))
            .filter((id) => Number.isInteger(id) && id > 0);

        const maxAge = WISHLIST_COOKIE_DAYS * 24 * 60 * 60;
        document.cookie = `${WISHLIST_COOKIE_NAME}=${encodeURIComponent(JSON.stringify(uniqueIds))}; path=/; max-age=${maxAge}; SameSite=Lax`;
        return uniqueIds;
    }

    function updateWishlistUi() {
        const wishlistIds = readWishlistCookie();
        const wishlistSet = new Set(wishlistIds);

        document.querySelectorAll('.wishlist-toggle[data-product-id]').forEach((button) => {
            const id = parseInt(button.dataset.productId || '0', 10);
            const isActive = wishlistSet.has(id);

            button.classList.toggle('is-active', isActive);
            button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            button.setAttribute('aria-label', isActive ? 'Usuń z ulubionych' : 'Dodaj do ulubionych');
        });

        const count = wishlistIds.length;
        document.querySelectorAll('[data-wishlist-count]').forEach((badge) => {
            badge.textContent = String(count);
            if (count > 0) {
                badge.classList.remove('hidden');
                badge.classList.add('flex');
            } else {
                badge.classList.remove('flex');
                badge.classList.add('hidden');
            }
        });
    }

    function toggleWishlist(productId) {
        const wishlistIds = readWishlistCookie();
        const id = parseInt(productId, 10);
        if (!Number.isInteger(id) || id <= 0) return;

        const exists = wishlistIds.includes(id);
        const updated = exists
            ? wishlistIds.filter((entry) => entry !== id)
            : wishlistIds.concat(id);

        writeWishlistCookie(updated);
        updateWishlistUi();
    }

    document.addEventListener('click', function(event) {
        const button = event.target.closest('.wishlist-toggle[data-product-id]');
        if (!button) return;

        event.preventDefault();
        event.stopPropagation();
        toggleWishlist(button.dataset.productId);
    });

    // Public API for potential future integrations.
    window.morettiWishlist = {
        getItems: readWishlistCookie,
        setItems: writeWishlistCookie,
        refreshUi: updateWishlistUi,
        toggle: toggleWishlist,
    };

    updateWishlistUi();

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
                            t.classList.remove('is-active');
                        });
                        thumb.classList.add('is-active');
                        foundMatch = true;
                    }
                });
                
                // 3. If no matching thumbnail, clear highlights
                if (!foundMatch) {
                    thumbnails.forEach(t => {
                        t.classList.remove('is-active');
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

    // Product cards: hover on color dot shows that variant's first image (grid + homepage)
    // Event delegation so it works for initial DOM and for infinite-scroll loaded cards
    var morettiDotDebug = typeof window !== 'undefined' && /[?&]moretti_debug_dots=1/.test(window.location.search);
    function morettiDotLog(msg, detail) {
        if (!morettiDotDebug) return;
        var line = msg + (detail !== undefined ? ' ' + JSON.stringify(detail) : '');
        if (typeof console !== 'undefined' && console.log) console.log('[moretti-dots]', line);
        var el = document.getElementById('moretti-dots-debug');
        if (el) {
            el.innerHTML = '<strong>' + new Date().toLocaleTimeString() + '</strong> ' + msg + (detail !== undefined ? ' <code>' + String(detail).substring(0, 80) + '</code>' : '');
            el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }
    if (morettiDotDebug) {
        var wrap = document.createElement('div');
        wrap.id = 'moretti-dots-debug';
        wrap.setAttribute('style', 'position:fixed !important; left:auto !important; right:0 !important; top:80px !important; bottom:20px !important; width:min(360px,90vw) !important; z-index:999999; background:#1a1a1a; color:#0f0; font:12px/1.4 monospace; padding:8px 12px; overflow:auto; border-left:2px solid #0f0; border-radius:4px 0 0 4px; box-shadow:-4px 0 12px rgba(0,0,0,0.2);');
        wrap.innerHTML = '<strong>Moretti dots debug (?)</strong> Najechanie na kropkę zaktualizuje ten komunikat.';
        document.body.appendChild(wrap);
        var dotsCount = document.querySelectorAll('.sku-color-dot[data-first-image-url]').length;
        var dotsAny = document.querySelectorAll('.sku-color-dot').length;
        morettiDotLog('Strona załadowana. Kropek z data-first-image-url: ' + dotsCount + ', wszystkich .sku-color-dot: ' + dotsAny);
    }
    function getCardFirstImg(card) {
        const slide = card.querySelector('.product-image-slide[data-index="0"]') || card.querySelector('.product-image-slide');
        return slide ? slide.querySelector('img') : null;
    }
    function applyDotPreview(dot) {
        const url = dot.getAttribute('data-first-image-url');
        if (!url) {
            morettiDotLog('applyDotPreview: brak data-first-image-url na kropce');
            return;
        }
        const card = dot.closest('.product-card');
        const firstSlide = card && (card.querySelector('.product-image-slide[data-index="0"]') || card.querySelector('.product-image-slide'));
        const img = firstSlide ? firstSlide.querySelector('img') : null;
        if (!card) {
            morettiDotLog('applyDotPreview: nie znaleziono .product-card');
            return;
        }
        if (!img) {
            morettiDotLog('applyDotPreview: nie znaleziono img w pierwszym slajdzie', { slide: !!card.querySelector('.product-image-slide') });
            return;
        }
        if (!card.dataset.originalFirstImageSrc) {
            card.dataset.originalFirstImageSrc = img.currentSrc || img.getAttribute('src') || img.src;
            card.dataset.originalSrcset = img.getAttribute('srcset') || '';
            card.dataset.originalSizes = img.getAttribute('sizes') || '';
        }
        img.removeAttribute('srcset');
        img.removeAttribute('sizes');
        img.src = url;
        if (firstSlide) {
            firstSlide.style.setProperty('opacity', '1', 'important');
            firstSlide.style.setProperty('visibility', 'visible', 'important');
        }
        var secondSlide = card.querySelector('.product-image-slide[data-index="1"]');
        if (secondSlide) {
            secondSlide.style.setProperty('opacity', '0', 'important');
            secondSlide.style.setProperty('visibility', 'hidden', 'important');
        }
        card.classList.add('is-showing-variant-preview');
        morettiDotLog('Podmiana zdjęcia (src+bez srcset)', url.substring(0, 50) + '…');
    }
    function clearDotPreview(dot) {
        const card = dot.closest('.product-card');
        const img = card && getCardFirstImg(card);
        if (!card || !img) return;
        if (card.dataset.originalFirstImageSrc) {
            img.src = card.dataset.originalFirstImageSrc;
            if (card.dataset.originalSrcset) img.setAttribute('srcset', card.dataset.originalSrcset);
            else img.removeAttribute('srcset');
            if (card.dataset.originalSizes) img.setAttribute('sizes', card.dataset.originalSizes);
            else img.removeAttribute('sizes');
        }
        card.querySelectorAll('.product-image-slide').forEach(function(slide) {
            slide.style.removeProperty('opacity');
            slide.style.removeProperty('visibility');
        });
        card.classList.remove('is-showing-variant-preview');
        morettiDotLog('Przywrócono oryginalne zdjęcie');
    }
    document.addEventListener('mouseover', function(e) {
        const dot = e.target.closest && e.target.closest('.sku-color-dot[data-first-image-url]');
        if (dot) {
            morettiDotLog('mouseover na kropce z data-first-image-url');
            applyDotPreview(dot);
        } else if (e.target.closest && e.target.closest('.sku-color-dot')) {
            morettiDotLog('mouseover na kropce BEZ data-first-image-url – sprawdź PHP/warianty');
        }
    });
    document.addEventListener('mouseout', function(e) {
        const dot = e.target.closest && e.target.closest('.sku-color-dot[data-first-image-url]');
        if (!dot) return;
        const card = dot.closest('.product-card');
        const variants = card && card.querySelector('.sku-color-variants');
        const stillInsideVariants = variants && variants.contains(e.relatedTarget);
        if (!stillInsideVariants) {
            morettiDotLog('mouseout z kropki – przywracam zdjęcie');
            clearDotPreview(dot);
        }
    });

    // Single product page: hover on color dot shows that variant's first image
    var singleMainImg = document.querySelector('.single-product-main .main-product-image-el, #moretti-main-img');
    if (singleMainImg) {
        document.addEventListener('mouseover', function(e) {
            var singleDot = e.target.closest && e.target.closest('.single-color-dot[data-first-image-url]');
            if (!singleDot) return;
            var url = singleDot.getAttribute('data-first-image-url');
            if (!url) return;
            var img = document.querySelector('.single-product-main .main-product-image-el, #moretti-main-img');
            if (!img) return;
            if (!img.dataset.morettiOriginalSrc) {
                img.dataset.morettiOriginalSrc = img.currentSrc || img.getAttribute('src') || img.src;
                img.dataset.morettiOriginalSrcset = img.getAttribute('srcset') || '';
                img.dataset.morettiOriginalSizes = img.getAttribute('sizes') || '';
            }
            img.removeAttribute('srcset');
            img.removeAttribute('sizes');
            img.src = url;
            singleDot.classList.add('is-previewing');
        });
        document.addEventListener('mouseout', function(e) {
            var singleDot = e.target.closest && e.target.closest('.single-color-dot[data-first-image-url]');
            if (!singleDot) return;
            var container = singleDot.closest('.single-color-variants');
            var stillInside = container && container.contains(e.relatedTarget);
            if (stillInside) return;
            singleDot.classList.remove('is-previewing');
            var img = document.querySelector('.single-product-main .main-product-image-el, #moretti-main-img');
            if (!img || !img.dataset.morettiOriginalSrc) return;
            img.src = img.dataset.morettiOriginalSrc;
            if (img.dataset.morettiOriginalSrcset) img.setAttribute('srcset', img.dataset.morettiOriginalSrcset);
            else img.removeAttribute('srcset');
            if (img.dataset.morettiOriginalSizes) img.setAttribute('sizes', img.dataset.morettiOriginalSizes);
            else img.removeAttribute('sizes');
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

    function getSliderActiveIndex(images) {
        let activeIndex = 0;
        images.forEach((img, index) => {
            if (img.classList.contains('active')) {
                activeIndex = index;
            }
        });
        return activeIndex;
    }

    function goToSliderIndex(slider, targetIndex) {
        const images = slider.querySelectorAll('.slider-image');
        const dots = slider.querySelectorAll('.slider-dot');
        if (!images.length) return;

        const currentIndex = getSliderActiveIndex(images);
        const boundedTarget = ((targetIndex % images.length) + images.length) % images.length;

        images[currentIndex].classList.remove('active', 'opacity-100');
        images[currentIndex].classList.add('opacity-0');
        images[boundedTarget].classList.add('active', 'opacity-100');
        images[boundedTarget].classList.remove('opacity-0');

        if (dots.length > 0 && dots[currentIndex] && dots[boundedTarget]) {
            dots[currentIndex].classList.remove('bg-charcoal', 'w-4');
            dots[currentIndex].classList.add('bg-charcoal/20');
            dots[boundedTarget].classList.remove('bg-charcoal/20');
            dots[boundedTarget].classList.add('bg-charcoal', 'w-4');
        }
    }

    function initProductImageSliders() {
        const sliders = document.querySelectorAll('.product-image-slider');
        sliders.forEach((slider) => {
            if (slider.dataset.sliderInitialized === 'true') return;
            slider.style.touchAction = 'pan-y';

            const images = slider.querySelectorAll('.slider-image');
            const dots = slider.querySelectorAll('.slider-dot');
            const prevBtn = slider.querySelector('.slider-prev');
            const nextBtn = slider.querySelector('.slider-next');

            if (images.length <= 1) {
                slider.dataset.sliderInitialized = 'true';
                return;
            }

            if (prevBtn) {
                prevBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    goToSliderIndex(slider, getSliderActiveIndex(images) - 1);
                });
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    goToSliderIndex(slider, getSliderActiveIndex(images) + 1);
                });
            }

            dots.forEach((dot, index) => {
                dot.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    goToSliderIndex(slider, index);
                });
            });

            let touchStartX = 0;
            let touchStartY = 0;
            let touchDeltaX = 0;
            let touchDeltaY = 0;
            let didSwipe = false;
            const imageLink = slider.querySelector('.slider-images-wrapper');

            slider.addEventListener('touchstart', function(event) {
                if (event.touches.length !== 1) return;
                touchStartX = event.touches[0].clientX;
                touchStartY = event.touches[0].clientY;
                touchDeltaX = 0;
                touchDeltaY = 0;
                didSwipe = false;
            }, { passive: true });

            slider.addEventListener('touchmove', function(event) {
                if (event.touches.length !== 1) return;
                touchDeltaX = event.touches[0].clientX - touchStartX;
                touchDeltaY = event.touches[0].clientY - touchStartY;
                if (Math.abs(touchDeltaX) > Math.abs(touchDeltaY)) {
                    event.preventDefault();
                }
            }, { passive: false });

            slider.addEventListener('touchend', function() {
                const swipeThreshold = 35;
                if (Math.abs(touchDeltaX) > Math.abs(touchDeltaY) && Math.abs(touchDeltaX) > swipeThreshold) {
                    didSwipe = true;
                    if (touchDeltaX < 0) {
                        goToSliderIndex(slider, getSliderActiveIndex(images) + 1);
                    } else {
                        goToSliderIndex(slider, getSliderActiveIndex(images) - 1);
                    }
                }
            });

            if (imageLink) {
                imageLink.addEventListener('click', function(event) {
                    if (didSwipe) {
                        event.preventDefault();
                        event.stopPropagation();
                        didSwipe = false;
                    }
                });
            }

            slider.dataset.sliderInitialized = 'true';
        });
    }

    // Product Image Slider Functions
    window.morettiSliderNext = function(button) {
        const slider = button.closest('.product-image-slider');
        if (!slider) return;
        const images = slider.querySelectorAll('.slider-image');
        goToSliderIndex(slider, getSliderActiveIndex(images) + 1);
    };

    window.morettiSliderPrev = function(button) {
        const slider = button.closest('.product-image-slider');
        if (!slider) return;
        const images = slider.querySelectorAll('.slider-image');
        goToSliderIndex(slider, getSliderActiveIndex(images) - 1);
    };

    initProductImageSliders();

    // =============================================
    // Product Image Inline Zoom (CSS hover handles scale, JS tracks cursor for origin)
    // =============================================
    function initProductZoom() {
        const wrapper = document.getElementById('moretti-img-wrapper');
        const img     = document.getElementById('moretti-main-img');
        if (!wrapper || !img) return;

        wrapper.addEventListener('mousemove', function(e) {
            const wRect = wrapper.getBoundingClientRect();
            const cw = wRect.width;
            const ch = wRect.height;

            // Compute rendered image bounds (object-fit: contain, object-position: center)
            const nw = img.naturalWidth  || cw;
            const nh = img.naturalHeight || ch;
            const imgRatio       = nw / nh;
            const containerRatio = cw / ch;

            var renderedW, renderedH, offsetX, offsetY;
            if (imgRatio > containerRatio) {
                renderedW = cw;
                renderedH = cw / imgRatio;
                offsetX   = 0;
                offsetY   = (ch - renderedH) / 2;
            } else {
                renderedH = ch;
                renderedW = ch * imgRatio;
                offsetX   = (cw - renderedW) / 2;
                offsetY   = 0;
            }

            // Clamp cursor to rendered image bounds (in element coords)
            var cx = Math.max(offsetX, Math.min(e.clientX - wRect.left, offsetX + renderedW));
            var cy = Math.max(offsetY, Math.min(e.clientY - wRect.top,  offsetY + renderedH));

            // transform-origin as % of element box
            var x = (cx / cw) * 100;
            var y = (cy / ch) * 100;

            img.style.transformOrigin = x + '% ' + y + '%';
        });

        wrapper.addEventListener('mouseleave', function() {
            img.style.transformOrigin = '';
        });
    }

    initProductZoom();

});

// Quick Add to Cart functionality (native WooCommerce AJAX endpoint)
function morettiQuickAddToCart(productId, triggerButton = null) {
    const button = triggerButton || document.querySelector(`button[data-product-id="${productId}"]`);
    if (!button) return;

    if (button.dataset.productType === 'variable' && button.dataset.productUrl) {
        window.location.href = button.dataset.productUrl;
        return;
    }

    const wcAjaxTemplate =
        (window.wc_add_to_cart_params && window.wc_add_to_cart_params.wc_ajax_url) ||
        (window.morettiData && window.morettiData.wcAjaxUrl);
    if (!wcAjaxTemplate) {
        alert('Brak endpointu WooCommerce AJAX. Odśwież stronę i spróbuj ponownie.');
        return;
    }

    const endpointUrl = wcAjaxTemplate.replace('%%endpoint%%', 'add_to_cart');
    const originalContent = button.innerHTML;
    button.innerHTML = '<svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    button.disabled = true;

    const payload = new URLSearchParams();
    payload.append('product_id', String(productId));
    payload.append('quantity', '1');

    fetch(endpointUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: payload.toString(),
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`Network response was not ok (${response.status})`);
            }
            return response.json();
        })
        .then((data) => {
            if (data && data.error && data.product_url) {
                window.location.href = data.product_url;
                return;
            }

            if (data && data.fragments && typeof data.fragments === 'object') {
                Object.entries(data.fragments).forEach(([selector, html]) => {
                    document.querySelectorAll(selector).forEach((element) => {
                        element.innerHTML = html;
                    });
                });
            }

            updateCartCount();
            button.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';

            if (typeof jQuery !== 'undefined') {
                jQuery(document.body).trigger('added_to_cart', [data.fragments || {}, data.cart_hash || '', jQuery(button)]);
            }

            setTimeout(() => {
                button.innerHTML = originalContent;
                button.disabled = false;
            }, 2000);
        })
        .catch((error) => {
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

