// Premium Shopping Cart & Interaction Logic - Mie Ayam Wengi 57

document.addEventListener('DOMContentLoaded', () => {
    // Initialize Cart from LocalStorage
    let cart = JSON.parse(localStorage.getItem('wengi_cart')) || [];
    
    // UI Selectors
    const cartBadge = document.getElementById('cart-badge');
    const cartItemsContainer = document.getElementById('cart-items-container');
    const cartTotalAmount = document.getElementById('cart-total-amount');
    const cartEmptyState = document.getElementById('cart-empty-state');
    const cartCheckoutSection = document.getElementById('cart-checkout-section');
    const checkoutCartItemsInput = document.getElementById('checkout-cart-items-input');
    const checkoutTotalAmountInput = document.getElementById('checkout-total-amount-input');
    const checkoutForm = document.getElementById('checkout-form');
    const checkoutModalElement = document.getElementById('checkoutModal');
    
    let checkoutModal = null;
    if (checkoutModalElement) {
        checkoutModal = new bootstrap.Modal(checkoutModalElement);
    }

    // Dynamic Payment Info Box Handler
    const paymentMethodSelect = document.getElementById('payment_method');
    const paymentInfoText = document.getElementById('payment-info-text');
    if (paymentMethodSelect && paymentInfoText) {
        paymentMethodSelect.addEventListener('change', () => {
            if (paymentMethodSelect.value === 'QRIS') {
                paymentInfoText.innerHTML = 'Silakan scan kode QRIS Mie Ayam Wengi 57 yang berada di kasir atau di meja Anda, lalu lakukan pembayaran.';
            } else {
                paymentInfoText.innerHTML = 'Silakan tunjukkan Invoice pesanan digital yang muncul setelah pemesanan dikonfirmasi ke kasir untuk proses pembayaran Tunai.';
            }
        });
    }

    // Change navbar styling on scroll
    const navbar = document.querySelector('.navbar-wengi');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 30) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // Add to Cart Event Handlers
    document.querySelectorAll('.btn-add-to-cart').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const id = parseInt(button.dataset.id);
            const name = button.dataset.name;
            const price = parseFloat(button.dataset.price);
            const image = button.dataset.image;
            
            addToCart(id, name, price, image);
            
            // Subtle button click micro-animation
            button.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Ditambahkan';
            button.classList.remove('btn-wengi-primary');
            button.classList.add('btn-success');
            
            setTimeout(() => {
                button.innerHTML = '<i class="bi bi-cart-plus me-1"></i> Tambah ke Keranjang';
                button.classList.remove('btn-success');
                button.classList.add('btn-wengi-primary');
            }, 1000);
        });
    });

    // Save and Render Helper
    function saveAndRender() {
        localStorage.setItem('wengi_cart', JSON.stringify(cart));
        renderCart();
    }

    // Core Cart Functions
    function addToCart(id, name, price, image) {
        const existingItem = cart.find(item => item.id === id);
        
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({
                id: id,
                name: name,
                price: price,
                image: image,
                quantity: 1
            });
        }
        saveAndRender();
        
        // Auto open cart drawer for instant feedback
        const cartDrawerElement = document.getElementById('cartDrawer');
        if (cartDrawerElement) {
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(cartDrawerElement) || new bootstrap.Offcanvas(cartDrawerElement);
            bsOffcanvas.show();
        }
    }

    window.updateQty = function(id, change) {
        const item = cart.find(item => item.id === id);
        if (item) {
            item.quantity += change;
            if (item.quantity <= 0) {
                cart = cart.filter(i => i.id !== id);
            }
            saveAndRender();
        }
    }

    window.removeItem = function(id) {
        cart = cart.filter(item => item.id !== id);
        saveAndRender();
    }

    // Format currency to IDR
    function formatIDR(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }

    // Render Cart HTML
    function renderCart() {
        // Update Floating Badges
        const totalItemsCount = cart.reduce((acc, item) => acc + item.quantity, 0);
        if (cartBadge) {
            cartBadge.textContent = totalItemsCount;
            cartBadge.style.display = totalItemsCount > 0 ? 'flex' : 'none';
        }
        
        if (!cartItemsContainer) return; // Not on the home page

        cartItemsContainer.innerHTML = '';
        
        if (cart.length === 0) {
            cartEmptyState.classList.remove('d-none');
            cartCheckoutSection.classList.add('d-none');
            return;
        }

        cartEmptyState.classList.add('d-none');
        cartCheckoutSection.classList.remove('d-none');

        let grandTotal = 0;

        cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            grandTotal += itemTotal;

            const itemHtml = `
                <div class="cart-item">
                    <img src="${item.image}" alt="${item.name}" class="cart-item-img">
                    <div class="cart-item-details">
                        <div class="cart-item-title">${item.name}</div>
                        <div class="cart-item-price">${formatIDR(item.price)}</div>
                    </div>
                    <div class="d-flex flex-column align-items-end gap-2">
                        <button class="cart-item-remove" onclick="removeItem(${item.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                        <div class="cart-qty-control">
                            <button class="cart-qty-btn" onclick="updateQty(${item.id}, -1)">-</button>
                            <span class="cart-qty-val">${item.quantity}</span>
                            <button class="cart-qty-btn" onclick="updateQty(${item.id}, 1)">+</button>
                        </div>
                    </div>
                </div>
            `;
            cartItemsContainer.insertAdjacentHTML('beforeend', itemHtml);
        });

        cartTotalAmount.textContent = formatIDR(grandTotal);
        
        // Put data into hidden inputs for safety if using standard submit, 
        // but we'll use AJAX for high-fidelity response.
        if (checkoutCartItemsInput) {
            checkoutCartItemsInput.value = JSON.stringify(cart);
        }
        if (checkoutTotalAmountInput) {
            checkoutTotalAmountInput.value = grandTotal;
        }
    }

    // Checkout Form Form Submission via AJAX
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            if (cart.length === 0) {
                alert('Keranjang Anda kosong!');
                return;
            }

            const submitBtn = checkoutForm.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            
            // Set loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...';

            const formData = new FormData(checkoutForm);
            
            // Append cart data manually since it's in localStorage
            formData.append('cart_items', JSON.stringify(cart));
            formData.append('total_amount', cart.reduce((acc, item) => acc + (item.price * item.quantity), 0));

            fetch('checkout-process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close drawers and checkout modal
                    if (checkoutModal) checkoutModal.hide();
                    
                    const cartDrawerElement = document.getElementById('cartDrawer');
                    if (cartDrawerElement) {
                        const bsOffcanvas = bootstrap.Offcanvas.getInstance(cartDrawerElement);
                        if (bsOffcanvas) bsOffcanvas.hide();
                    }

                    // Reset cart
                    cart = [];
                    saveAndRender();

                    // Show success alert modal
                    const successModalHtml = `
                        <div class="modal fade" id="orderSuccessModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content card-wengi" style="border-color: #10b981;">
                                    <div class="modal-body text-center p-5">
                                        <div class="mb-4" style="font-size: 4rem; color: #10b981; animation: fadeIn 0.5s ease;">
                                            <i class="bi bi-patch-check-fill"></i>
                                        </div>
                                        <h3 class="fw-bold mb-2">Pesanan Diterima!</h3>
                                        <p class="text-muted mb-4">Terima kasih atas pesanan Anda. Pesanan dengan ID <strong>#${data.order_id}</strong> sedang disiapkan di dapur Wengi.</p>
                                        
                                        <div class="p-3 bg-secondary rounded-3 text-start mb-4 border border-color" style="font-size: 0.9rem;">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Pemesan:</span>
                                                <span class="fw-semibold text-white">${data.customer_name}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Nomor Meja:</span>
                                                <span class="fw-semibold text-white">Meja #${data.table_number}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Metode Pembayaran:</span>
                                                <span class="fw-bold text-warning">${data.payment_method}</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Total Bayar:</span>
                                                <span class="fw-bold text-gradient">${formatIDR(data.total_amount)}</span>
                                            </div>
                                        </div>

                                        <button class="btn btn-wengi-primary w-100" data-bs-dismiss="modal">
                                            Kembali Jelajah Menu
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    document.body.insertAdjacentHTML('beforeend', successModalHtml);
                    const orderSuccessModal = new bootstrap.Modal(document.getElementById('orderSuccessModal'));
                    orderSuccessModal.show();

                    // Cleanup success modal on hide
                    document.getElementById('orderSuccessModal').addEventListener('hidden.bs.modal', function () {
                        this.remove();
                    });
                } else {
                    alert('Terjadi kesalahan: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error during checkout:', error);
                alert('Gagal memproses pesanan. Silakan hubungi kasir.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        });
    }

    // Run initial rendering of the cart
    renderCart();
});
