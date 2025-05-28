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
    <title>Menswear - Fast Thrift</title>
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

<!-- Add Menswear Banner -->
<div class="banner">
    <img src="LOGOS/menswear.jpg" alt="Menswear Banner" style="width: 100%; height: 500px;">
</div>
<div class="products">
    <?php
    $items = [
        [
            'id' => 'harley-davidson-sweatshirt',
            'title' => 'Harley Davidson Sweatshirt - Large Black Cotton Blend',
            'price' => '₱2,900.00',
            'image' => 'PRODUCTS/HarleyDavidsonSweatshirt.jpg',
            'thumbnails' => 'PRODUCTS/HarleyDavidsonSweatshirt.jpg,PRODUCTS/HarleyDavidson2.jpg,PRODUCTS/HarleyDavidson3.jpg',
            'desc' => 'Vintage black Harley Davidson sweatshirt, fits large.'
        ],
        [
            'id' => 'supreme-graphic-hoodie',
            'title' => 'Supreme Graphic Hoodie - XL Black Cotton',
            'price' => '₱33,100.00',
            'image' => 'PRODUCTS/supreme.png',
            'thumbnails' => 'PRODUCTS/supreme-graphic-hoodie.jpg,PRODUCTS/supreme-graphic-hoodie2.jpg,PRODUCTS/supreme-graphic-hoodie3.jpg',
            'desc' => 'Supreme Hoodie, fits X-Large. Supreme Lime Green Box Logo 2017, 100% Authentic, 100% Cotton, Made in Canada, 100% Original'
        ],
        [
            'id' => 'carhartt-jacket',
            'title' => 'Carhartt Jacket - 2XL Beige Cotton',
            'price' => '₱12,600.00',
            'image' => 'PRODUCTS/CarharttJacket1.jpg',
            'thumbnails' => 'PRODUCTS/CarharttJacket1.jpg,PRODUCTS/CarharttJacket2.jpg,PRODUCTS/CarharttJacket3.jpg',
            'desc' => 'Carhartt Jacket, fits Xx-Large. Visible Marks And Wear'
        ],
        [
            'id' => 'supreme-2018-hoodie',
            'title' => 'Supreme 2018 Supreme Hoodie - Medium Red Cotton',
            'price' => '₱10,700.00',
            'image' => 'PRODUCTS/Supreme2018Supreme.jpg',
            'thumbnails' => 'PRODUCTS/Supreme2018Supreme.jpg,PRODUCTS/Supreme2018Supreme2.jpg,PRODUCTS/Supreme2018Supreme3.jpg',
            'desc' => 'Supreme 2018 Supreme Hoodie, fits Medium. No Visible Marks Or Faults'
        ],
        [
            'id' => 'nike-sweatshirt-white',
            'title' => 'Nike Sweatshirt - 2XL White Cotton Blend',
            'price' => '₱3,700.00',
            'image' => 'PRODUCTS/nikesweat1.jpg',
            'thumbnails' => 'PRODUCTS/nikesweat1.jpg,PRODUCTS/nikesweat2.jpg,PRODUCTS/nikesweat3.jpg',
            'desc' => 'Nike Sweatshirt, fits Xx-Large. Slight Marks On Front'
        ],
        [
            'id' => 'nike-sweatshirt-brown',
            'title' => 'Made In Usa Nike Sweatshirt - Small Brown Cotton',
            'price' => '₱3,700.00',
            'image' => 'PRODUCTS/SweatshirtSmallBrownCotton1.jpg',
            'thumbnails' => 'PRODUCTS/SweatshirtSmallBrownCotton1.jpg,PRODUCTS/SweatshirtSmallBrownCotton2.jpg,PRODUCTS/SweatshirtSmallBrownCotton3.jpg',
            'desc' => 'Made In Usa Nike Sweatshirt, fits Small. No Visible Marks Or Faults'
        ],
        [
            'id' => 'versace-blazer',
            'title' => 'Versace Classic Blazer - Large Brown Wool Blend',
            'price' => '₱9,600.00',
            'image' => 'PRODUCTS/VersaceClassicBlazer1.jpg',
            'thumbnails' => 'PRODUCTS/VersaceClassicBlazer2.jpg,PRODUCTS/VersaceClassicBlazer1.jpg,PRODUCTS/VersaceClassicBlazer3.jpg',
            'desc' => 'Vintage brown Versace Classic blazer, fits large.'
        ],
        [
            'id' => 'puma-track-jacket',
            'title' => 'Puma Track Jacket - 2XL Blue Polyester',
            'price' => '₱2,700.00',
            'image' => 'PRODUCTS/PumaTrackJacket1.jpg',
            'thumbnails' => 'PRODUCTS/PumaTrackJacket1.jpg,PRODUCTS/PumaTrackJacket2.jpg,PRODUCTS/PumaTrackJacket3.jpg',
            'desc' => 'Vintage blue Puma track jacket, fits xx-large.'
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