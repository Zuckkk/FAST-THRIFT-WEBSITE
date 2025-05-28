<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Fast Thrift</title>
    <link rel="stylesheet" href="cart.css">
    <style>
        .promo-section {
            margin: 15px 0;
        }
        .receipt {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .items td, .items th {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .price { text-align: right; }
        .discount-text { color: #28a745; }
        .free-shipping { color: #007bff; }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
        }
        #modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
    </style>
</head>
<body>
    <header>
        <div class="top-bar">
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="Cart.php">Cart</a>
            </div>
        </div>
        <h1>Your Cart</h1>
    </header>

    <main>
        <div class="cart-container">
            <div class="cart-header">
                <span>Product</span>
                <span>Price</span>
                <span>Actions</span>
            </div>
            <div id="cart-items"></div>
            <div class="cart-footer">
                <h2>Total: <span id="cart-total">₱0.00</span></h2>
                <button onclick="startCheckout()" id="checkout-button">Checkout</button>
            </div>
        </div>
    </main>

    <div id="checkout-info-modal" class="modal">
        <div class="modal-content">
            <h3>Enter Delivery Address</h3>
            <input type="text" id="checkout-address" placeholder="Enter your delivery address">
            <div class="promo-section">
                <input type="text" id="promo-code" placeholder="Enter promo code (optional)">
                <button onclick="applyPromoCode()">Apply Code</button>
            </div>
            <p id="promo-message"></p>
            <button onclick="processCheckout()">Continue to Checkout</button>
            <button onclick="closeCheckoutModal()">Cancel</button>
        </div>
    </div>

    <div id="receipt-modal" class="modal">
        <div class="modal-content">
            <h2>Order Receipt</h2>
            <div id="receipt-content"></div>
            <button onclick="closeReceiptModal()" class="close-receipt">Close</button>
        </div>
    </div>

    <div id="modal-overlay"></div>

    <script>
        const SHIPPING_FEE = 100.00;
        const AUTO_DISCOUNT_THRESHOLD = 20000;  // Changed from 25000
        const AUTO_DISCOUNT_PERCENTAGE = 20;
        const PROMO_CODES = {
    'WELCOME10': { type: 'discount', value: 10 },
    'WELCOME20': { type: 'discount', value: 20 },
    'WELCOME30': { type: 'discount', value: 30 },
    'WELCOME40': { type: 'discount', value: 40 },
    'WELCOME50': { type: 'discount', value: 50 },
    'SUMMER25': { type: 'discount', value: 25 },
    'FREESHIP': { type: 'shipping', value: true },
    'THRIFTY15': { type: 'discount', value: 15 },
    'SAVE35': { type: 'discount', value: 35 },
    'KAEYLEPOGI': { type: 'discount', value: 100 }
};
        
        let appliedDiscount = 0;
        let freeShipping = false;

        function showCart() {
            const cartItemsDiv = document.getElementById("cart-items");
            const cartTotalSpan = document.getElementById("cart-total");
            let cart = [];
            let total = 0;

            try {
                const savedCart = localStorage.getItem("cart");
                if (savedCart) cart = JSON.parse(savedCart);
            } catch (e) {
                cart = [];
            }

            if (cart.length === 0) {
                cartItemsDiv.innerHTML = "<div style='text-align:center;padding:20px;'>Your cart is empty.</div>";
                cartTotalSpan.textContent = "₱0.00";
                document.getElementById("checkout-button").disabled = true;
                return;
            }

            let html = "";
            cart.forEach(item => {
                const price = parseFloat(item.price.toString().replace(/[^\d.]/g, ''));
                total += price;
                html += `
                    <div class="cart-row">
                        <span>${item.title}</span>
                        <span>₱${price.toLocaleString(undefined, {minimumFractionDigits:2})}</span>
                        <span><button onclick="removeFromCart('${item.id}')">Remove</button></span>
                    </div>
                `;
            });

            cartItemsDiv.innerHTML = html;
            cartTotalSpan.textContent = "₱" + total.toLocaleString(undefined, {minimumFractionDigits:2});
            document.getElementById("checkout-button").disabled = false;
        }

        function removeFromCart(id) {
            let cart = [];
            try {
                const savedCart = localStorage.getItem("cart");
                if (savedCart) cart = JSON.parse(savedCart);
            } catch (e) { cart = []; }
            
            cart = cart.filter(item => item.id != id);
            localStorage.setItem("cart", JSON.stringify(cart));
            showCart();
        }

        function applyPromoCode() {
            const promoCode = document.getElementById("promo-code").value.trim().toUpperCase();
            const promoMessage = document.getElementById("promo-message");
            
            let cart = [];
            try {
                const savedCart = localStorage.getItem("cart");
                if (savedCart) cart = JSON.parse(savedCart);
            } catch (e) { cart = []; }

            let subtotal = 0;
            cart.forEach(item => {
                const price = parseFloat(item.price.toString().replace(/[^\d.]/g, ''));
                subtotal += price;
            });

            // Show automatic discount message but allow promo code
            if (subtotal >= AUTO_DISCOUNT_THRESHOLD) {
                promoMessage.innerHTML = `✅ ${AUTO_DISCOUNT_PERCENTAGE}% automatic discount applied (Order above ₱20,000)`;
                promoMessage.style.color = 'green';
                appliedDiscount = AUTO_DISCOUNT_PERCENTAGE;
            }

            // Continue with promo code validation
            if (PROMO_CODES[promoCode]) {
                let user = null;
                try {
                    user = JSON.parse(localStorage.getItem("user"));
                } catch (e) { 
                    user = null; 
                }

                if (!user || !user.email) {
                    promoMessage.innerHTML = '❌ Please login to use promo codes';
                    promoMessage.style.color = 'red';
                    return;
                }

                fetch("check_promo.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        email: user.email,
                        promo_code: promoCode
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.can_use) {
                        const promo = PROMO_CODES[promoCode];
                        if (promo.type === 'discount') {
                            appliedDiscount = promo.value;
                            promoMessage.innerHTML = `✅ ${promo.value}% discount applied!`;
                            promoMessage.style.color = 'green';
                        } else if (promo.type === 'shipping') {
                            freeShipping = true;
                            promoMessage.innerHTML = '✅ Free shipping applied!';
                            promoMessage.style.color = 'green';
                        }
                    } else {
                        promoMessage.innerHTML = '❌ You have already used this promo code';
                        promoMessage.style.color = 'red';
                        appliedDiscount = 0;
                        freeShipping = false;
                    }
                })
                .catch(error => {
                    promoMessage.innerHTML = '❌ Error checking promo code';
                    promoMessage.style.color = 'red';
                });
            } else {
                promoMessage.innerHTML = '❌ Invalid promo code';
                promoMessage.style.color = 'red';
                appliedDiscount = 0;
                freeShipping = false;
            }
        }

        function startCheckout() {
            let user = null;
            try {
                user = JSON.parse(localStorage.getItem("user"));
            } catch (e) { 
                user = null; 
            }
              
            if (!user || !user.email) {
                alert("Please sign in first to checkout");
                localStorage.setItem("pendingCheckout", "true");
                window.location.href = "index.php?login=true";
                return;
            }

            document.getElementById("checkout-address").value = "";
            document.getElementById("promo-code").value = "";
            document.getElementById("promo-message").innerHTML = "";
            appliedDiscount = 0;
            freeShipping = false;
            document.getElementById("modal-overlay").style.display = "block";
            document.getElementById("checkout-info-modal").style.display = "block";
        }

        function closeCheckoutModal() {
            document.getElementById("modal-overlay").style.display = "none";
            document.getElementById("checkout-info-modal").style.display = "none";
        }

        function closeReceiptModal() {
            document.getElementById('receipt-modal').style.display = 'none';
            document.getElementById('modal-overlay').style.display = 'none';
            showCart();
        }

        function processCheckout() {
            let user = null;
            try {
                user = JSON.parse(localStorage.getItem("user"));
            } catch (e) { 
                user = null; 
            }

            if (!user || !user.email) {
                alert("Please sign in first to checkout");
                closeCheckoutModal();
                window.location.href = "index.php?login=true";
                return;
            }

            let address = document.getElementById("checkout-address").value.trim();
            if (!address) {
                alert("Please enter your delivery address");
                return;
            }

            let cart = [];
            try {
                const savedCart = localStorage.getItem("cart");
                if (savedCart) cart = JSON.parse(savedCart);
            } catch (e) {
                cart = [];
            }

            if (cart.length === 0) {
                alert("Your cart is empty!");
                closeCheckoutModal();
                return;
            }

            const promoCode = document.getElementById("promo-code").value.trim().toUpperCase();

            fetch("checkout.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    cart: cart,
                    user: {
                        email: user.email,
                        first_name: user.first_name,
                        last_name: user.last_name,
                        address: address
                    },
                    promo_code: promoCode,
                    discount: appliedDiscount,
                    freeShipping: freeShipping
                })
            })
            .then(res => res.json())
            .then(data => {
                closeCheckoutModal();
                if (data.success) {
                    const receiptHTML = generateReceipt(cart, user, address);
                    document.getElementById('receipt-content').innerHTML = receiptHTML;
                    document.getElementById('receipt-modal').style.display = 'block';
                    document.getElementById('modal-overlay').style.display = 'block';
                    localStorage.removeItem("cart");
                } else {
                    throw new Error(data.message || "Checkout failed");
                }
            })
            .catch(error => {
                alert(error.message || "An error occurred during checkout");
                closeCheckoutModal();
            });
        }

        function generateReceipt(cart, user, address) {
            const date = new Date().toLocaleString();
            let subtotal = 0;
            let itemsHtml = cart.map(item => {
                const price = parseFloat(item.price.toString().replace(/[^\d.]/g, ''));
                subtotal += price;
                return `
                    <tr>
                        <td>${item.title}</td>
                        <td class="price">₱${price.toLocaleString(undefined, {minimumFractionDigits:2})}</td>
                    </tr>
                `;
            }).join('');

            const shippingFee = freeShipping ? 0 : SHIPPING_FEE;
            let discount = 0;
            let discountPercentage = 0;
            
            // Check for automatic discount first
            if (subtotal >= AUTO_DISCOUNT_THRESHOLD) {
                discountPercentage = AUTO_DISCOUNT_PERCENTAGE;
                discount = (subtotal * discountPercentage) / 100;
            } else if (appliedDiscount > 0) {
                discountPercentage = appliedDiscount;
                discount = (subtotal * discountPercentage) / 100;
            }

            const total = subtotal - discount + shippingFee;

            let html = `
                <div class="receipt">
                    <div class="receipt-header">
                        <h3>Fast Thrift</h3>
                        <p>Date: ${date}</p>
                    </div>
                    <div class="customer-info">
                        <p>Name: ${user.first_name} ${user.last_name}</p>
                        <p>Email: ${user.email}</p>
                        <p>Delivery Address: ${address}</p>
                    </div>
                    <table class="items">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>${itemsHtml}</tbody>
                        <tfoot>
                            <tr>
                                <td>Subtotal</td>
                                <td class="price">₱${subtotal.toLocaleString(undefined, {minimumFractionDigits:2})}</td>
                            </tr>`;

            // Show both automatic and promo discounts
            if (subtotal >= AUTO_DISCOUNT_THRESHOLD) {
                html += `
                <tr>
                    <td class="discount-text">Automatic Discount (Order above ₱20,000)</td>
                    <td class="price">-₱${(subtotal * AUTO_DISCOUNT_PERCENTAGE / 100).toLocaleString(undefined, {minimumFractionDigits:2})}</td>
                </tr>`;
            }
            
            if (appliedDiscount > 0) {
                html += `
                <tr>
                    <td class="discount-text">Additional Discount (${appliedDiscount}% off)</td>
                    <td class="price">-₱${(subtotal * appliedDiscount / 100).toLocaleString(undefined, {minimumFractionDigits:2})}</td>
                </tr>`;
            }

            html += `
                            <tr>
                                <td>
                                    Shipping Fee
                                    ${freeShipping ? '<span class="free-shipping">(FREE)</span>' : ''}
                                </td>
                                <td class="price">₱${shippingFee.toLocaleString(undefined, {minimumFractionDigits:2})}</td>
                            </tr>
                            <tr>
                                <td><strong>Total</strong></td>
                                <td class="price"><strong>₱${total.toLocaleString(undefined, {minimumFractionDigits:2})}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="receipt-footer">
                        <p>Thank you for shopping at Fast Thrift!</p>
                    </div>
                </div>
            `;

            return html;
        }

        // Initialize cart on page load
        document.addEventListener("DOMContentLoaded", showCart);
    </script>
</body>
</html>