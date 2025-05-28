<?php
// filepath: d:\XAMMPP\htdocs\FAST THRIFT WEBSITE (JAE PC)\FAST-THRIFT WEBSITE\index.php
session_start();
if (isset($_SESSION['user'])): ?>
<script>
localStorage.setItem("user", JSON.stringify({
    email: "<?php echo $_SESSION['user']['email']; ?>",
    first_name: "<?php echo $_SESSION['user']['first_name']; ?>",
    last_name: "<?php echo $_SESSION['user']['last_name']; ?>"
}));
</script>
<?php endif; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fast Thrift</title>
    <link rel="stylesheet" href="index.css">
    <link rel="icon" type="image/png" href="LOGOS/favicon.png" sizes="64x64">
</head> 
<body>  
    <?php if (isset($_SESSION['user'])): ?>
<script>
localStorage.setItem("user", JSON.stringify({
    email: "<?php echo $_SESSION['user']['email']; ?>",
    first_name: "<?php echo $_SESSION['user']['first_name']; ?>",
    last_name: "<?php echo $_SESSION['user']['last_name']; ?>"
}));
</script>
<?php endif; ?>
    <!-- Header Section -->
    <header id="mainHeader">
        <div class="top-bar">
            <img src="LOGOS/Fast-Thrift.png" class="logo" alt="Fast Thrift Logo">
            <div class="icons">
                <span id="userArea">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span style="font-weight:bold;">Hello, <?php echo htmlspecialchars($_SESSION['first_name']); ?></span>
                    <a href="order_history.php" style="margin-left:10px;color:#3498db;">Order History</a>
                    <a href="logout.php" style="margin-left:10px;color:#e74c3c;">Logout</a>
                <?php else: ?>
                    <span id="loginIcon" style="cursor:pointer;">
                        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4h-4Z" clip-rule="evenodd"/>
                        </svg>
                    </span>
                <?php endif; ?>
                </span>
                <a href="Cart.php">
                  <span style="position:relative;">
                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 4h1.5L9 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8.5-3h9.25L19 7H7.312"/>
                    </svg>                     
                    <span id="cart-count" style="position:absolute;top:0;right:0;background:#e74c3c;color:#fff;border-radius:50%;padding:2px 7px;font-size:13px;line-height:1;">0</span>
                  </span>
                </a>
            </div>
        </div>
    </header>
    <div class="divider"></div>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-text">
            <h2>THREADS OF THE <br>PAST, STYLED FOR<br>THE PRESENT.</h2>
            <ul>
                <li>&#10004; 90+ BRANDS</li>
                <li>&#10004; HANDPICKED VINTAGE</li>
                <li>&#10004; 100% AUTHENTIC</li>
            </ul>
            <button onclick="location.href='allitems.php'"><h1>SHOP NOW!</h1></button>
        </div>
        <div class="slideshow-container">
            <div class="slide fade">
                <img src="LOGOS/thrift-shop.jpg" alt="Thrift Shop 1">
            </div>
            <div class="slide fade">
                <img src="LOGOS/thrift-shop2.jpg" alt="Thrift Shop 2">
            </div>
            <div class="slide fade">
                <img src="LOGOS/thrift-shop3.jpg" alt="Thrift Shop 3">
            </div>
        </div>
    </section>

    <!-- Divider -->
    <section class="divider"></section>

    <!-- Brands Section -->
    <section class="brands">
        <h3>SHOP POPULAR VINTAGE BRANDS</h3>
        <div class="brand-logos">
            <img src="LOGOS/carhartt-logo.jpg" alt="Carhartt">
            <img src="LOGOS/Old_Nike_logo.jpg" alt="Nike">
            <img src="LOGOS/adidas.jpg" alt="Adidas">
            <img src="LOGOS/levi.jpg" alt="Levi's">
            <img src="LOGOS/championjpg.jpg" alt="Champion">
        </div>
    </section>

    <!-- Styles Section -->
    <section class="styles">
        <h3>SHOP BY STYLE</h3>
        <div class="style-options">
            <img src="STYLES/WORKWEAR.jpg" alt="Workwear">
            <img src="STYLES/blokecore.jpg" alt="Blokecore">
            <img src="STYLES/Y2K_460x.jpg" alt="Y2K">
            <img src="STYLES/skater.jpg" alt="Skater">
        </div>
    </section>

    <!-- Divider -->
    <div class="divider"></div>

    <!-- Men's and Women's Section -->
    <section class="mens-womens-space">
        <div class="mens-card">
            <a href="menswear.php"><img src="STYLES/mens.jpg" alt="Men's Vintage"></a>
            <div class="text">MENS</div>
            <button class="button" onclick="location.href='menswear.php'">SHOP NOW!</button>
        </div>
        <div class="womens-card">
            <a href="womenswear.php"> <img src="STYLES/WOMENS.jpg" alt="Women's Vintage"></a>
            <div class="text">WOMENS</div>
            <button class="button" onclick="location.href='womenswear.php'">SHOP NOW!</button>
        </div>
    </section>
 
    <!-- Divider -->
    <div class="divider"></div>

    <!-- Footer Section -->
    <footer>
        <h1>Welcome to FAST THRIFT!</h1>
        <h2>THREADS OF THE PAST, STYLED FOR THE PRESENT.</h2>
        <div class="space">
            <div class="socmed-logos">
                <img src="LOGOS/twitter.png" alt="Twitter">
                <img src="LOGOS/facebook.png" alt="Facebook">
                <img src="LOGOS/instagram.jpg" alt="Instagram">
                <img src="LOGOS/tiktok.jpg" alt="TikTok">
            </div>
            <div class="contacts">
                <h3>Contact Us</h3>
                <p>Email: Fasthrift.com<br>Phone: 091234567911<br>Address: Cambridge Village Class 31</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript for Slideshow, Modals, Cart Count -->
    <script>
        // AJAX registration
document.addEventListener("DOMContentLoaded", function() {
    const registerForm = document.getElementById("registerForm");
    const registerError = document.getElementById("registerError");
    const registerSuccess = document.getElementById("registerSuccess");
    if (registerForm) {
        registerForm.addEventListener("submit", function(e) {
            e.preventDefault();
            registerError.textContent = "";
            registerSuccess.textContent = "";
            const formData = new FormData(registerForm);
            fetch("register.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    registerSuccess.textContent = data.message;
                    registerForm.reset();
                } else {
                    registerError.textContent = data.message;
                }
            })
            .catch(() => {
                registerError.textContent = "An error occurred. Please try again.";
            });
        });
    }
}); 
        // Slideshow
        let slideIndex = 0;
        function showSlides() {
            const slides = document.querySelectorAll(".slide");
            slides.forEach((slide) => {
                slide.style.display = "none";
            });
            slideIndex++;
            if (slideIndex > slides.length) {
                slideIndex = 1;
            }
            slides[slideIndex - 1].style.display = "block";
            setTimeout(showSlides, 3000);
        }
        document.addEventListener("DOMContentLoaded", showSlides);

        // Modal logic
        document.addEventListener("DOMContentLoaded", function() {
            const loginModal = document.getElementById("loginModal");
            const registerModal = document.getElementById("registerModal");
            const closeButtons = document.querySelectorAll(".modal .close");
            const openRegisterModal = document.getElementById("openRegisterModal");
            const openLoginModal = document.getElementById("openLoginModal");

            if(openRegisterModal && registerModal && loginModal) {
                openRegisterModal.addEventListener("click", (e) => {
                    e.preventDefault();
                    loginModal.style.display = "none";
                    registerModal.style.display = "block";
                });
            }
            if(openLoginModal && registerModal && loginModal) {
                openLoginModal.addEventListener("click", (e) => {
                    e.preventDefault();
                    registerModal.style.display = "none";
                    loginModal.style.display = "block";
                });
            }
            closeButtons.forEach((button) => {
                button.addEventListener("click", () => {
                    loginModal.style.display = "none";
                    registerModal.style.display = "none";
                });
            });
            window.addEventListener("click", (event) => {
                if (event.target === loginModal) {
                    loginModal.style.display = "none";
                }
                if (event.target === registerModal) {
                    registerModal.style.display = "none";
                }
            });
        });
// AJAX login
document.addEventListener("DOMContentLoaded", function() {
    const loginForm = document.getElementById("loginForm");
    const loginError = document.getElementById("loginError");
    if (loginForm) {
        loginForm.addEventListener("submit", function(e) {
            e.preventDefault();
            loginError.textContent = "";
            const formData = new FormData(loginForm);
            fetch("login.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    loginError.textContent = data.message;
                }
            })
            .catch(() => {
                loginError.textContent = "An error occurred. Please try again.";
            });
        });
    }
});
        // Cart count
        function updateCartCount() {
            const cart = JSON.parse(localStorage.getItem("cart")) || [];
            const cartCount = cart.reduce((total, item) => total + item.quantity, 0);
            const cartCountElem = document.getElementById("cart-count");
            if(cartCountElem) cartCountElem.textContent = cartCount;
        }
        document.addEventListener("DOMContentLoaded", updateCartCount);

        // Show login modal on icon click (if not logged in)
        document.addEventListener("DOMContentLoaded", function() {
            const loginIcon = document.getElementById("loginIcon");
            const loginModal = document.getElementById("loginModal");
            if (loginIcon && loginModal) {
                loginIcon.onclick = function() {
                    loginModal.style.display = "block";
                };
            }
        });
    </script>

<!-- Login Modal -->
<?php if (!isset($_SESSION['user_id'])): ?>
<div id="loginModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>LOGIN</h2>
        <div id="loginError" style="color:red;margin-bottom:10px;"></div>
        <p>Don't have an account? <a href="#" id="openRegisterModal">Register.</a></p>
        <form id="loginForm" method="POST">
            <label for="email">EMAIL ADDRESS</label>
            <input type="email" id="email" name="email" required>
            <label for="password">PASSWORD</label>
            <input type="password" id="password" name="password" required>
            <button type="submit" class="login-button">LOGIN</button>
        </form>
        <p style="text-align: center;"><a href="admin.php" id="">Admin Login</a></p>
    </div>
    
</div>
<?php endif; ?>

<!-- Registration Modal -->
<div id="registerModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>CREATE ACCOUNT</h2>
        <div id="registerError" style="color:red;margin-bottom:10px;"></div>
        <div id="registerSuccess" style="color:green;margin-bottom:10px;"></div>
        <p>Already a member? <a href="#" id="openLoginModal">Login</a></p>
        <form id="registerForm" method="POST">
            <label for="reg-email">EMAIL ADDRESS</label>
            <input type="email" id="reg-email" name="email" required>
            <label for="reg-password">PASSWORD</label>
            <input type="password" id="reg-password" name="password" required>
            <label for="confirm-password">RE-ENTER PASSWORD</label>
            <input type="password" id="confirm-password" name="confirm-password" required>
            <label for="first-name">FIRST NAME</label>
            <input type="text" id="first-name" name="first_name" required>
            <label for="last-name">LAST NAME</label>
            <input type="text" id="last-name" name="last_name" required>
            <button type="submit" class="register-button">CREATE ACCOUNT</button>
        </form>
    </div>
</div>
<script>
  let lastScrollTop = 0;
  const header = document.getElementById("mainHeader");

  window.addEventListener("scroll", function () {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    if (scrollTop > lastScrollTop) {
      // Scrolling down
      header.classList.add("hidden");
    } else {
      // Scrolling up
      header.classList.remove("hidden");
    }

    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
  });
</script>
</body>
</html>