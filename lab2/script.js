// E-commerce Website JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart functionality
    initializeCart();
    
    // Initialize product functionality
    initializeProducts();
    
    // Initialize forms
    initializeForms();
    
    // Initialize search
    initializeSearch();
    
    // Initialize accessibility features
    initializeAccessibility();
});

// Cart Management
let cart = JSON.parse(localStorage.getItem('techmart-cart')) || [];

function initializeCart() {
    updateCartCount();
    
    // Add to cart buttons
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const productId = this.dataset.productId;
            const productName = this.dataset.productName;
            const productPrice = parseFloat(this.dataset.productPrice);
            
            addToCart({
                id: productId,
                name: productName,
                price: productPrice,
                quantity: 1
            });
            
            // Visual feedback
            this.textContent = 'Added!';
            this.style.background = '#059669';
            
            setTimeout(() => {
                this.textContent = 'Add to Cart';
                this.style.background = '#4f46e5';
            }, 2000);
        });
    });
    
    // Quantity controls
    document.querySelectorAll('.qty-increase').forEach(button => {
        button.addEventListener('click', function() {
            const input = document.getElementById(this.dataset.target);
            input.value = Math.min(parseInt(input.value) + 1, 10);
            updateCartItem(input);
        });
    });
    
    document.querySelectorAll('.qty-decrease').forEach(button => {
        button.addEventListener('click', function() {
            const input = document.getElementById(this.dataset.target);
            input.value = Math.max(parseInt(input.value) - 1, 1);
            updateCartItem(input);
        });
    });
    
    // Remove item buttons
    document.querySelectorAll('.remove-item').forEach(button => {
        button.addEventListener('click', function() {
            const cartItem = this.closest('.cart-item');
            const productId = cartItem.dataset.productId;
            
            if (confirm('Remove this item from your cart?')) {
                removeFromCart(productId);
                cartItem.remove();
                updateCartSummary();
            }
        });
    });
}

function addToCart(product) {
    const existingItem = cart.find(item => item.id === product.id);
    
    if (existingItem) {
        existingItem.quantity += product.quantity;
    } else {
        cart.push(product);
    }
    
    localStorage.setItem('techmart-cart', JSON.stringify(cart));
    updateCartCount();
    
    // Show success message
    showNotification(`${product.name} added to cart!`, 'success');
}

function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    localStorage.setItem('techmart-cart', JSON.stringify(cart));
    updateCartCount();
}

function updateCartItem(input) {
    const productId = input.closest('.cart-item').dataset.productId;
    const newQuantity = parseInt(input.value);
    
    const cartItem = cart.find(item => item.id === productId);
    if (cartItem) {
        cartItem.quantity = newQuantity;
        localStorage.setItem('techmart-cart', JSON.stringify(cart));
        updateCartSummary();
    }
}

function updateCartCount() {
    const countElement = document.getElementById('cart-count');
    if (countElement) {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        countElement.textContent = totalItems;
    }
}

function updateCartSummary() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const tax = subtotal * 0.08; // 8% tax
    const total = subtotal + tax;
    
    // Update price breakdown if on cart page
    const subtotalElement = document.querySelector('.price-breakdown dd:nth-of-type(1)');
    const taxElement = document.querySelector('.price-breakdown dd:nth-of-type(3)');
    const totalElement = document.querySelector('.price-breakdown dd.total');
    
    if (subtotalElement) subtotalElement.textContent = `$${subtotal.toFixed(2)}`;
    if (taxElement) taxElement.textContent = `$${tax.toFixed(2)}`;
    if (totalElement) totalElement.textContent = `$${total.toFixed(2)}`;
    
    // Update checkout button
    const checkoutPrice = document.querySelector('.checkout-price');
    if (checkoutPrice) checkoutPrice.textContent = `$${total.toFixed(2)}`;
}

// Product Functionality
function initializeProducts() {
    // Product filtering
    const filterForm = document.querySelector('.filter-form');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            filterProducts();
        });
        
        filterForm.addEventListener('reset', function() {
            setTimeout(() => filterProducts(), 0);
        });
    }
    
    // Product sorting
    const sortSelect = document.getElementById('sort-by');
    if (sortSelect) {
        sortSelect.addEventListener('change', sortProducts);
    }
    
    // Wishlist functionality
    document.querySelectorAll('.add-to-wishlist').forEach(button => {
        button.addEventListener('click', function() {
            const productName = this.closest('.product-card').querySelector('h3').textContent;
            
            if (this.style.color === 'red') {
                this.style.color = '';
                showNotification(`${productName} removed from wishlist`, 'info');
            } else {
                this.style.color = 'red';
                showNotification(`${productName} added to wishlist!`, 'success');
            }
        });
    });
    
    // Product comparison
    let compareList = [];
    document.querySelectorAll('.compare').forEach(button => {
        button.addEventListener('click', function() {
            const productCard = this.closest('.product-card');
            const productName = productCard.querySelector('h3').textContent;
            
            if (compareList.includes(productName)) {
                compareList = compareList.filter(name => name !== productName);
                this.style.background = '';
                showNotification(`${productName} removed from comparison`, 'info');
            } else {
                if (compareList.length >= 3) {
                    showNotification('You can only compare up to 3 products', 'warning');
                    return;
                }
                compareList.push(productName);
                this.style.background = '#fbbf24';
                showNotification(`${productName} added to comparison`, 'success');
            }
            
            updateCompareButton();
        });
    });
}

function filterProducts() {
    const formData = new FormData(document.querySelector('.filter-form'));
    const categories = formData.getAll('category');
    const minPrice = parseFloat(formData.get('min-price')) || 0;
    const maxPrice = parseFloat(formData.get('max-price')) || Infinity;
    const brand = formData.get('brand');
    
    document.querySelectorAll('.product-card').forEach(card => {
        let show = true;
        
        // Category filter
        if (categories.length > 0) {
            const productCategory = card.dataset.category;
            if (!categories.includes(productCategory)) {
                show = false;
            }
        }
        
        // Price filter
        const priceElement = card.querySelector('.current-price');
        if (priceElement) {
            const price = parseFloat(priceElement.textContent.replace('$', '').replace(',', ''));
            if (price < minPrice || price > maxPrice) {
                show = false;
            }
        }
        
        // Brand filter
        if (brand && brand !== '') {
            const productBrand = card.dataset.brand;
            if (productBrand !== brand) {
                show = false;
            }
        }
        
        card.style.display = show ? 'block' : 'none';
    });
}

function sortProducts() {
    const sortBy = document.getElementById('sort-by').value;
    const productList = document.querySelector('.product-list');
    const products = Array.from(productList.querySelectorAll('.product-card'));
    
    products.sort((a, b) => {
        switch (sortBy) {
            case 'price-low':
                const priceA = parseFloat(a.querySelector('.current-price').textContent.replace(/[$,]/g, ''));
                const priceB = parseFloat(b.querySelector('.current-price').textContent.replace(/[$,]/g, ''));
                return priceA - priceB;
            
            case 'price-high':
                const priceA2 = parseFloat(a.querySelector('.current-price').textContent.replace(/[$,]/g, ''));
                const priceB2 = parseFloat(b.querySelector('.current-price').textContent.replace(/[$,]/g, ''));
                return priceB2 - priceA2;
            
            case 'rating':
                const ratingA = parseFloat(a.querySelector('.rating-number').textContent.match(/\d\.\d/)[0]);
                const ratingB = parseFloat(b.querySelector('.rating-number').textContent.match(/\d\.\d/)[0]);
                return ratingB - ratingA;
            
            default:
                return 0;
        }
    });
    
    products.forEach(product => productList.appendChild(product));
}

function updateCompareButton() {
    let compareButton = document.querySelector('.compare-button');
    if (!compareButton && compareList.length > 0) {
        compareButton = document.createElement('div');
        compareButton.className = 'compare-button';
        compareButton.innerHTML = `
            <button onclick="openComparison()">
                Compare Products (${compareList.length})
            </button>
        `;
        document.body.appendChild(compareButton);
    } else if (compareButton) {
        if (compareList.length === 0) {
            compareButton.remove();
        } else {
            compareButton.querySelector('button').textContent = `Compare Products (${compareList.length})`;
        }
    }
}

// Form Functionality
function initializeForms() {
    // Newsletter form
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            
            // Simulate API call
            setTimeout(() => {
                showNotification('Successfully subscribed to newsletter!', 'success');
                this.reset();
            }, 1000);
        });
    }
    
    // Contact form
    const contactForm = document.querySelector('.contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (validateContactForm()) {
                const submitBtn = this.querySelector('.submit-btn');
                const spinner = submitBtn.querySelector('.loading-spinner');
                
                submitBtn.disabled = true;
                spinner.style.display = 'inline';
                
                // Simulate form submission
                setTimeout(() => {
                    showNotification('Message sent successfully! We\'ll get back to you soon.', 'success');
                    this.reset();
                    submitBtn.disabled = false;
                    spinner.style.display = 'none';
                }, 2000);
            }
        });
        
        // Real-time validation
        contactForm.addEventListener('blur', function(e) {
            if (e.target.matches('input, select, textarea')) {
                validateField(e.target);
            }
        }, true);
    }
    
    // Promo code form
    const promoForm = document.querySelector('.promo-form');
    if (promoForm) {
        promoForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const code = this.querySelector('input').value.toUpperCase();
            
            // Mock promo codes
            const promoCodes = {
                'SAVE10': { discount: 10, type: 'percentage' },
                'WELCOME': { discount: 25, type: 'fixed' },
                'FIRST50': { discount: 50, type: 'fixed' }
            };
            
            if (promoCodes[code]) {
                const discount = promoCodes[code];
                showNotification(`Promo code applied! You saved $${discount.discount}`, 'success');
                // Update cart total here
            } else {
                showNotification('Invalid promo code', 'error');
            }
        });
    }
}

function validateContactForm() {
    let isValid = true;
    const requiredFields = document.querySelectorAll('.contact-form [required]');
    
    requiredFields.forEach(field => {
        if (!validateField(field)) {
            isValid = false;
        }
    });
    
    return isValid;
}

function validateField(field) {
    const errorElement = document.getElementById(field.name + '-error') || 
                        document.getElementById(field.id + '-error');
    let isValid = true;
    let errorMessage = '';
    
    // Required field validation
    if (field.required && !field.value.trim()) {
        errorMessage = `${field.labels[0].textContent.replace('*', '').trim()} is required`;
        isValid = false;
    }
    
    // Email validation
    if (field.type === 'email' && field.value) {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(field.value)) {
            errorMessage = 'Please enter a valid email address';
            isValid = false;
        }
    }
    
    // Phone validation
    if (field.type === 'tel' && field.value) {
        const phonePattern = /^\(\d{3}\)\s\d{3}-\d{4}$/;
        if (!phonePattern.test(field.value)) {
            errorMessage = 'Please enter a valid phone number: (123) 456-7890';
            isValid = false;
        }
    }
    
    // Update error display
    if (errorElement) {
        errorElement.textContent = errorMessage;
        errorElement.style.display = errorMessage ? 'block' : 'none';
    }
    
    // Update field styling
    field.style.borderColor = isValid ? '#d1d5db' : '#ef4444';
    
    return isValid;
}

// Search Functionality
function initializeSearch() {
    const searchForm = document.querySelector('form[role="search"]');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const query = this.querySelector('input[type="search"]').value.trim();
            
            if (query.length >= 2) {
                performSearch(query);
            } else {
                showNotification('Please enter at least 2 characters to search', 'warning');
            }
        });
        
        // Search suggestions (mock data)
        const searchInput = searchForm.querySelector('input[type="search"]');
        if (searchInput) {
            let suggestionTimeout;
            
            searchInput.addEventListener('input', function() {
                clearTimeout(suggestionTimeout);
                const query = this.value.trim();
                
                if (query.length >= 2) {
                    suggestionTimeout = setTimeout(() => showSearchSuggestions(query), 300);
                } else {
                    hideSearchSuggestions();
                }
            });
            
            searchInput.addEventListener('blur', function() {
                setTimeout(hideSearchSuggestions, 200);
            });
        }
    }
}

function performSearch(query) {
    // Mock search implementation
    const searchResults = [
        'iPhone 15 Pro',
        'MacBook Pro 14-inch',
        'AirPods Pro',
        'iPad Air',
        'Apple Watch Series 9'
    ].filter(item => item.toLowerCase().includes(query.toLowerCase()));
    
    if (searchResults.length > 0) {
        showNotification(`Found ${searchResults.length} results for "${query}"`, 'success');
        // Redirect to products page with search results
        window.location.href = `products.html?search=${encodeURIComponent(query)}`;
    } else {
        showNotification(`No results found for "${query}"`, 'info');
    }
}

function showSearchSuggestions(query) {
    const suggestions = [
        'iPhone', 'MacBook', 'AirPods', 'iPad', 'Apple Watch',
        'Samsung Galaxy', 'Dell XPS', 'HP Laptop', 'Sony Headphones'
    ].filter(item => item.toLowerCase().includes(query.toLowerCase()));
    
    let suggestionList = document.querySelector('.search-suggestions');
    if (!suggestionList) {
        suggestionList = document.createElement('div');
        suggestionList.className = 'search-suggestions';
        document.querySelector('.search-container').appendChild(suggestionList);
    }
    
    if (suggestions.length > 0) {
        suggestionList.innerHTML = suggestions
            .slice(0, 5)
            .map(suggestion => `<div class="suggestion-item" onclick="selectSuggestion('${suggestion}')">${suggestion}</div>`)
            .join('');
        suggestionList.style.display = 'block';
    }
}

function hideSearchSuggestions() {
    const suggestionList = document.querySelector('.search-suggestions');
    if (suggestionList) {
        suggestionList.style.display = 'none';
    }
}

function selectSuggestion(suggestion) {
    const searchInput = document.querySelector('input[type="search"]');
    if (searchInput) {
        searchInput.value = suggestion;
        performSearch(suggestion);
    }
    hideSearchSuggestions();
}

// Accessibility Features
function initializeAccessibility() {
    // Keyboard navigation for custom components
    document.addEventListener('keydown', function(e) {
        // Handle Enter/Space for buttons without proper semantics
        if ((e.key === 'Enter' || e.key === ' ') && e.target.matches('.add-to-cart, .cta-button, .category-link')) {
            e.preventDefault();
            e.target.click();
        }
        
        // Escape key to close modals/dropdowns
        if (e.key === 'Escape') {
            hideSearchSuggestions();
            closeModals();
        }
    });
    
    // Focus management for dynamic content
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                // Set focus to first focusable element in new content
                const focusableElements = mutation.addedNodes[0]?.querySelectorAll?.(
                    'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
                );
                if (focusableElements && focusableElements.length > 0) {
                    focusableElements[0].focus();
                }
            }
        });
    });
    
    observer.observe(document.body, { childList: true, subtree: true });
    
    // Announce dynamic changes to screen readers
    const announcer = document.createElement('div');
    announcer.setAttribute('aria-live', 'polite');
    announcer.setAttribute('aria-atomic', 'true');
    announcer.className = 'sr-only';
    document.body.appendChild(announcer);
    
    window.announceToScreenReader = function(message) {
        announcer.textContent = message;
    };
}

// Utility Functions
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        color: white;
        z-index: 10000;
        max-width: 400px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        animation: slideIn 0.3s ease;
    `;
    
    const colors = {
        success: '#059669',
        error: '#dc2626',
        warning: '#d97706',
        info: '#2563eb'
    };
    
    notification.style.backgroundColor = colors[type] || colors.info;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Announce to screen readers
    if (window.announceToScreenReader) {
        window.announceToScreenReader(message);
    }
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 4000);
}

function closeModals() {
    // Close any open modals/overlays
    document.querySelectorAll('.modal, .overlay, .dropdown-menu').forEach(element => {
        element.style.display = 'none';
    });
}

function openLiveChat() {
    showNotification('Live chat feature coming soon!', 'info');
}

function openComparison() {
    showNotification('Product comparison feature coming soon!', 'info');
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    
    .search-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        display: none;
    }
    
    .suggestion-item {
        padding: 0.75rem 1rem;
        cursor: pointer;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .suggestion-item:hover {
        background: #f3f4f6;
    }
    
    .suggestion-item:last-child {
        border-bottom: none;
    }
    
    .compare-button {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
    }
    
    .compare-button button {
        background: #fbbf24;
        color: #1f2937;
        border: none;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }
`;

document.head.appendChild(style);