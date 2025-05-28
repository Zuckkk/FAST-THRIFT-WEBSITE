<?php
session_start();
$conn = new mysqli("localhost", "root", "", "fast_thrift");
$quantities = [];
if (!$conn->connect_error) {
    $result = $conn->query("SELECT id, quantity FROM products");
    while ($row = $result->fetch_assoc()) {
        $quantities[$row['id']] = (int)$row['quantity'];
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Womenswear - Fast Thrift</title>
    <link rel="stylesheet" href="allitems.css">
    <link rel="icon" type="image/png" href="LOGOS/favicon.png" sizes="64x64">
</head>
<body>
<header>
    <div class="top-bar">
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="Cart.php">Cart</a>
        </div>
    </div>
</header>

<!-- Add Womenswear Banner -->
<div class="banner">
    <img src="LOGOS/womens.jpg" alt="Womenswear Banner" style="width: 100%; height: 500px;">
</div>


<div class="products">
    <?php
    $items = [
        [
            'id' => 'diesel-jeans',
            'title' => 'Diesel Jeans - 30W UK 6 Blue Cotton',
            'price' => '₱3,300.00',
            'image' => 'PRODUCTS/DieselJeans.jpg',
            'thumbnails' => 'PRODUCTS/DieselJeans.jpg,PRODUCTS/DieselJeans2.jpg,PRODUCTS/DieselJeans3.jpg',
            'desc' => 'Vintage Diesel blue jeans, fit a UK size 6 - low waisted with a 30 waist.'
        ],
        [
            'id' => 'riley-true-religion',
            'title' => 'Riley True Religion Denim Shorts - 28W UK 8 Blue Cotton',
            'price' => '₱2,700.00',
            'image' => 'PRODUCTS/RileyTrueReligionDenim.jpg',
            'thumbnails' => 'PRODUCTS/RileyTrueReligionDenim.jpg,PRODUCTS/RileyTrueReligionDenim2.jpg,PRODUCTS/RileyTrueReligionDenim3.jpg',
            'desc' => 'Vintage True Religion Made in USA Joey Big T blue denim shorts, fit a UK size 10 - low waisted with a 32 waist'
        ],
        [
            'id' => '3-suisses-top',
            'title' => '3 Suisses Long Sleeve Top - XS Pink Viscose Blend',
            'price' => '₱500.00',
            'image' => 'PRODUCTS/3SuissesLongSleeveTop1.jpg',
            'thumbnails' => 'PRODUCTS/3SuissesLongSleeveTop1.jpg,PRODUCTS/3SuissesLongSleeveTop2.jpg,PRODUCTS/3SuissesLongSleeveTop3.jpg',
            'desc' => 'Vintage pink 3 Suisses long sleeve top, fits x-small.'
        ],
        [
            'id' => 'levis-denim-shorts',
            'title' => '311 Levis Denim Shorts - 31W UK 12 Blue Cotton',
            'price' => '₱900.00',
            'image' => 'PRODUCTS/311LevisDenimShorts1.jpg',
            'thumbnails' => 'PRODUCTS/311LevisDenimShorts.jpg,PRODUCTS/311LevisDenimShorts1.jpg,PRODUCTS/311LevisDenimShorts3.jpg',
            'desc' => 'Vintage Levis 311 blue denim shorts, fit a UK size 12 - mid rise with a 31 waist.'
        ],
        [
            'id' => '501-levis-jeans',
            'title' => '501 Levis Jeans - 28W UK 8 Blue Cotton',
            'price' => '₱900.00',
            'image' => 'PRODUCTS/501LevisJeans.jpg',
            'thumbnails' => 'PRODUCTS/501LevisJeans.jpg,PRODUCTS/501LevisJeans2.jpg,PRODUCTS/501LevisJeans3.jpg',
            'desc' => 'Vintage Levis 501 blue jeans, fit a UK size 8 - mid rise with a 28 waist.'
        ],
        [
            'id' => 'divided-cropped-top',
            'title' => 'Divided Cropped Top - Small Pink Polyester Blend',
            'price' => '₱200.00',
            'image' => 'PRODUCTS/Divided1.jpg',
            'thumbnails' => 'PRODUCTS/divided1.jpg,PRODUCTS/divided2.jpg,PRODUCTS/divided3.jpg',
            'desc' => 'Vintage pink Divided top, fits small.'
        ]
    ];

    foreach ($items as $item) {
        $qty = $quantities[$item['id']] ?? 1;
        $soldOut = $qty <= 0 ? ' sold-out' : '';
        echo '<div class="product-card' . $soldOut . '"'
            . ' data-id="' . htmlspecialchars($item['id']) . '"'
            . ' data-title="' . htmlspecialchars($item['title']) . '"'
            . ' data-price="' . htmlspecialchars($item['price']) . '"'
            . ' data-quantity="' . $qty . '"'
            . ' data-image="' . htmlspecialchars($item['image']) . '"'
            . ' data-thumbnails="' . htmlspecialchars($item['thumbnails']) . '"'
            . ' data-description="' . htmlspecialchars($item['desc']) . '">';
        echo '<img src="' . htmlspecialchars($item['image']) . '" alt="">';
        echo '<p>' . htmlspecialchars($item['title']) . '</p>';
        echo '<p class="price">' . htmlspecialchars($item['price']) . '</p>';
        echo '<p class="quantity">Quantity: ' . $qty . '</p>';
        if ($qty <= 0) {
            echo '<div class="sold-out-label">Sold Out</div>';
        }
        echo '<button class="add-to-cart"' . ($qty <= 0 ? ' disabled' : '') . '>Add to Cart</button>';
        echo '</div>';
    }
    ?>
</div>

<!-- Modal for Product Details -->
<div id="productModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <div class="product-details">
            <div class="product-images">
                <img src="" alt="Product Image" id="mainImage">
                <div class="thumbnail-container" id="thumbnails"></div>
            </div>
            <div class="product-info">
                <h2 id="productTitle"></h2>
                <p id="productPrice"></p>
                <ul class="product-description" id="productDesc"></ul>
                <button id="modalAddToCart" class="add-to-cart">Add to Cart</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Add to Cart from card
    document.querySelectorAll(".product-card .add-to-cart").forEach(button => {
        button.addEventListener("click", function(e) {
            e.stopPropagation();
            const card = this.closest(".product-card");
            const id = card.getAttribute("data-id");
            const title = card.getAttribute("data-title");
            const price = card.getAttribute("data-price");
            const image = card.getAttribute("data-image");

            // 1. Add to cart in localStorage
            let cart = JSON.parse(localStorage.getItem("cart") || "[]");
            let existing = cart.find(item => item.id === id);
            if (existing) {
                existing.quantity = (existing.quantity || 1) + 1;
            } else {
                cart.push({ id, title, price, image, quantity: 1 });
            }
            localStorage.setItem("cart", JSON.stringify(cart));

            // 2. Show popup message
            alert(title + " has been added to your cart!");
        });
    });

    // Modal logic
    const modal = document.getElementById("productModal");
    const closeModal = document.getElementById("closeModal");
    const mainImage = document.getElementById("mainImage");
    const thumbnailsDiv = document.getElementById("thumbnails");
    const productTitle = document.getElementById("productTitle");
    const productPrice = document.getElementById("productPrice");
    const productDesc = document.getElementById("productDesc");
    const modalAddToCart = document.getElementById("modalAddToCart");
    let modalProduct = null;

    document.querySelectorAll(".product-card").forEach(card => {
        card.addEventListener("click", function(e) {
            if (e.target.classList.contains("add-to-cart")) return;

            modalProduct = {
                id: card.getAttribute("data-id"),
                title: card.getAttribute("data-title"),
                price: card.getAttribute("data-price"),
                image: card.getAttribute("data-image"),
                thumbnails: card.getAttribute("data-thumbnails"),
                description: card.getAttribute("data-description"),
                quantity: card.getAttribute("data-quantity")
            };

            mainImage.src = modalProduct.image;
            mainImage.alt = modalProduct.title;
            productTitle.textContent = modalProduct.title;
            productPrice.textContent = modalProduct.price;
            productDesc.innerHTML = "";
            if (modalProduct.description) {
                const li = document.createElement("li");
                li.textContent = modalProduct.description;
                productDesc.appendChild(li);
            }

            // Thumbnails
            thumbnailsDiv.innerHTML = "";
            if (modalProduct.thumbnails) {
                modalProduct.thumbnails.split(",").forEach((src, idx) => {
                    const thumb = document.createElement("img");
                    thumb.src = src.trim();
                    thumb.className = idx === 0 ? "selected" : "";
                    thumb.onclick = () => {
                        mainImage.src = thumb.src;
                        thumbnailsDiv.querySelectorAll("img").forEach(img => img.classList.remove("selected"));
                        thumb.classList.add("selected");
                    };
                    thumbnailsDiv.appendChild(thumb);
                });
            }

            // Sold out logic for modal
            if (parseInt(modalProduct.quantity) <= 0) {
                modalAddToCart.disabled = true;
                modalAddToCart.textContent = "Sold Out";
            } else {
                modalAddToCart.disabled = false;
                modalAddToCart.textContent = "Add to Cart";
            }

            modal.style.display = "block";
        });
    });

    modalAddToCart.addEventListener("click", function() {
        if (!modalProduct) return;

        // 1. Add to cart in localStorage
        let cart = JSON.parse(localStorage.getItem("cart") || "[]");
        let existing = cart.find(item => item.id === modalProduct.id);
        if (existing) {
            existing.quantity = (existing.quantity || 1) + 1;
        } else {
            cart.push({
                id: modalProduct.id,
                title: modalProduct.title,
                price: modalProduct.price,
                image: modalProduct.image,
                quantity: 1
            });
        }
        localStorage.setItem("cart", JSON.stringify(cart));

        // 2. Show popup message
        alert(modalProduct.title + " has been added to your cart!");
        // Optionally close the modal:
        // modal.style.display = "none";
    });

    closeModal.onclick = () => { modal.style.display = "none"; };
    window.onclick = e => { if (e.target === modal) modal.style.display = "none"; };
});
</script>   
</body>
</html>