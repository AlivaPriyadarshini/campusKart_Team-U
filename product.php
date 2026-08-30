<?php
require_once "config/database.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {

    $stmt = $conn->prepare(
        "SELECT * FROM products WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $product = $stmt->get_result()->fetch_assoc();

    if (!$product) {
        die("Product not found");
    }

} else {

    $result = $conn->query(
        "SELECT * FROM products ORDER BY id DESC"
    );
}
?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>

        <?= $id > 0
            ? "Product Details"
            : "Products"
        ?>

        - CampusKart

    </title>

    <link
        rel="stylesheet"
        href="css/product.css"
    >

</head>


<body>


<!-- ==========================================
     NAVBAR
========================================== -->

<header class="navbar">

    <div class="logo">
        <img src="./image/WhatsApp Image 2026-08-29 at 12.40.25 PM.jpeg"
            alt="CampusKart Logo">
    </div>

    <nav>

        <a href="index.php">
            Home
        </a>

        <a href="product.php">
            Products
        </a>

        <a href="cart.php">
            Cart 🛒
        </a>

    </nav>

</header>


<?php if ($id > 0): ?>


<!-- ==========================================
     PRODUCT DETAILS
========================================== -->

<section class="product-detail-page">

    <div class="product-detail-container">

        <a
            href="product.php"
            class="back-link"
        >
            ← Back to Products
        </a>


        <div class="product-detail">


            <!-- PRODUCT IMAGE -->

            <div class="product-detail-image">

                <?php

                $image = trim(
                    $product['image'] ?? ''
                );

                ?>


                <?php if ($image !== ''): ?>

                    <img
                        src="<?= htmlspecialchars($image) ?>"
                        alt="<?= htmlspecialchars(
                            $product['name']
                        ) ?>"
                    >

                <?php else: ?>

                    <div class="no-image">

                        <span>🖼️</span>

                        No Image Available

                    </div>

                <?php endif; ?>

            </div>


            <!-- PRODUCT INFORMATION -->

            <div class="product-detail-info">


                <span class="category">

                    <?= htmlspecialchars(
                        $product['category']
                        ?? 'Uncategorized'
                    ) ?>

                </span>


                <h1>

                    <?= htmlspecialchars(
                        $product['name']
                    ) ?>

                </h1>


                <div class="product-rating">

                    ★★★★★

                    <span>
                        CampusKart Student Choice
                    </span>

                </div>


                <p class="price">

                    ₹<?= number_format(
                        (float)$product['price'],
                        2
                    ) ?>

                </p>


                <!-- DESCRIPTION -->

                <div class="product-description">

                    <h3>
                        Product Description
                    </h3>

                    <p>

                        <?= nl2br(
                            htmlspecialchars(
                                $product['description'] ?? ''
                            )
                        ) ?>

                    </p>

                </div>


                <!-- STOCK -->

                <div class="stock-info">

                    <?php if ((int)$product['stock'] > 0): ?>

                        <span class="stock-dot"></span>

                        <strong>
                            In Stock
                        </strong>

                        <span>
                            <?= (int)$product['stock'] ?>
                            items available
                        </span>

                    <?php else: ?>

                        <span class="stock-dot out"></span>

                        <strong>
                            Out of Stock
                        </strong>

                    <?php endif; ?>

                </div>


                <?php if ((int)$product['stock'] > 0): ?>


                    <!-- ADD TO CART -->

                    <form
                        action="cart.php"
                        method="POST"
                        class="add-cart-form"
                    >


                        <input
                            type="hidden"
                            name="product_id"
                            value="<?= (int)$product['id'] ?>"
                        >


                        <div class="quantity-box">

                            <label for="quantity">
                                Quantity
                            </label>

                            <input
                                id="quantity"
                                type="number"
                                name="quantity"
                                value="1"
                                min="1"
                                max="<?= (int)$product['stock'] ?>"
                                class="quantity"
                                required
                            >

                        </div>


                        <button
                            class="btn add-cart-btn"
                            type="submit"
                        >

                            🛒 Add to Cart

                        </button>


                    </form>


                <?php else: ?>


                    <div class="out-stock-box">

                        <p class="out-stock">
                            Out of Stock
                        </p>

                        <p>
                            This product is currently unavailable.
                        </p>

                    </div>


                <?php endif; ?>


                <!-- TRUST INFORMATION -->

                <div class="product-trust">

                    <div>

                        <span>🚚</span>

                        <div>

                            <strong>
                                Campus Delivery
                            </strong>

                            <p>
                                Easy delivery for students
                            </p>

                        </div>

                    </div>


                    <div>

                        <span>🔒</span>

                        <div>

                            <strong>
                                Secure Shopping
                            </strong>

                            <p>
                                Safe and reliable ordering
                            </p>

                        </div>

                    </div>


                    <div>

                        <span>🎓</span>

                        <div>

                            <strong>
                                Student Focused
                            </strong>

                            <p>
                                Products made for campus life
                            </p>

                        </div>

                    </div>

                </div>


            </div>

        </div>

    </div>

</section>


<!-- ==========================================
     PRODUCT BENEFITS
========================================== -->

<section class="product-benefits">

    <div class="section-heading">

        <span class="section-label">
            WHY CAMPUSKART
        </span>

        <h2>
            Shopping Made For
            <span>Students</span>
        </h2>

        <p>
            CampusKart makes it easier for students
            to find useful products at affordable prices.
        </p>

    </div>


    <div class="benefit-grid">


        <div class="benefit-card">

            <div class="benefit-icon">
                🎓
            </div>

            <h3>
                Student Friendly
            </h3>

            <p>
                Products selected according to
                everyday college needs.
            </p>

        </div>


        <div class="benefit-card">

            <div class="benefit-icon">
                💰
            </div>

            <h3>
                Affordable Prices
            </h3>

            <p>
                Get useful campus products
                without spending too much.
            </p>

        </div>


        <div class="benefit-card">

            <div class="benefit-icon">
                🛒
            </div>

            <h3>
                Easy Shopping
            </h3>

            <p>
                Choose your product and add it
                to your cart in a few clicks.
            </p>

        </div>


        <div class="benefit-card">

            <div class="benefit-icon">
                ⭐
            </div>

            <h3>
                Quality Products
            </h3>

            <p>
                Find reliable products suitable
                for everyday student life.
            </p>

        </div>


    </div>

</section>


<?php else: ?>


<!-- ==========================================
     PRODUCT LIST
========================================== -->

<section class="products-page">


    <!-- PAGE HEADER -->

    <div class="products-header">

        <div>

            <span class="section-label">
                CAMPUSKART STORE
            </span>

            <h1>
                Everything You Need
                <span>On Campus</span>
            </h1>

            <p>
                Find useful products for your college life,
                from stationery and electronics to fashion
                and everyday essentials.
            </p>

        </div>


        <div class="product-count">

            <span>
                🛍️
            </span>

            <div>

                <strong>
                    Campus Essentials
                </strong>

                <small>
                    Made for students
                </small>

            </div>

        </div>

    </div>


    <!-- PRODUCT GRID -->

    <?php if ($result->num_rows > 0): ?>


        <div class="product-grid">


            <?php while (
                $product = $result->fetch_assoc()
            ): ?>


                <div class="product-card">


                    <!-- PRODUCT IMAGE -->

                    <div class="product-card-image">


                        <?php

                        $image = trim(
                            $product['image'] ?? ''
                        );

                        ?>


                        <?php if ($image !== ''): ?>


                            <img
                                src="<?= htmlspecialchars($image) ?>"
                                alt="<?= htmlspecialchars(
                                    $product['name']
                                ) ?>"
                            >


                        <?php else: ?>


                            <div class="no-image">

                                <span>
                                    🖼️
                                </span>

                                No Image Available

                            </div>


                        <?php endif; ?>


                    </div>


                    <!-- PRODUCT INFORMATION -->

                    <div class="product-info">


                        <span class="category">

                            <?= htmlspecialchars(
                                $product['category']
                                ?? 'Uncategorized'
                            ) ?>

                        </span>


                        <h3>

                            <?= htmlspecialchars(
                                $product['name']
                            ) ?>

                        </h3>


                        <p class="product-short-description">

                            <?= htmlspecialchars(
                                $product['description']
                            ) ?>

                        </p>


                        <div class="product-bottom">


                            <p class="price">

                                ₹<?= number_format(
                                    (float)$product['price'],
                                    2
                                ) ?>

                            </p>


                            <span class="student-tag">

                                STUDENT PICK

                            </span>


                        </div>


                        <a
                            href="product.php?id=<?= (int)$product['id'] ?>"
                            class="btn product-btn"
                        >

                            View Details →

                        </a>


                    </div>


                </div>


            <?php endwhile; ?>


        </div>


    <?php else: ?>


        <!-- EMPTY PRODUCTS -->

        <div class="empty-products">

            <div class="empty-icon">
                🛍️
            </div>

            <h2>
                No Products Available
            </h2>

            <p>
                We don't have any products available
                right now. Please check again later.
            </p>

        </div>


    <?php endif; ?>


</section>


<!-- ==========================================
     CAMPUSKART CTA
========================================== -->

<section class="products-cta">

    <div>

        <span class="section-label">
            CAMPUSKART
        </span>

        <h2>
            Everything For Your
            <span>College Life</span>
        </h2>

        <p>
            From notebooks and backpacks to electronics
            and fashion, CampusKart brings your everyday
            campus essentials together in one place.
        </p>

        <a
            href="cart.php"
            class="btn"
        >
            View My Cart →
        </a>

    </div>

</section>


<?php endif; ?>


<!-- ==========================================
     FOOTER
========================================== -->

<footer>


    <div class="footer-container">


        <!-- BRAND -->

        <div class="footer-brand">

            <h2>
                Campus<span>Kart</span>
            </h2>

            <p>
                Your student marketplace for affordable,
                useful and reliable campus essentials.
            </p>

        </div>


        <!-- QUICK LINKS -->

        <div class="footer-column">

            <h3>
                Quick Links
            </h3>

            <a href="index.php">
                Home
            </a>

            <a href="product.php">
                Products
            </a>

            <a href="cart.php">
                Cart
            </a>

        </div>


        <!-- CATEGORIES -->

        <div class="footer-column">

            <h3>
                Categories
            </h3>

            <p>
                Stationery
            </p>

            <p>
                Electronics
            </p>

            <p>
                Fashion
            </p>

            <p>
                Bags
            </p>

        </div>


        <!-- CAMPUSKART -->

        <div class="footer-column">

            <h3>
                CampusKart
            </h3>

            <p>
                Student Focused
            </p>

            <p>
                Affordable
            </p>

            <p>
                Easy Shopping
            </p>

            <p>
                Quality Products
            </p>

        </div>


    </div>


    <!-- FOOTER BOTTOM -->

    <div class="footer-bottom">

        <p>
            © 2026 CampusKart
        </p>

        <p>
            Student E-Commerce Platform
        </p>

    </div>


</footer>


</body>

</html>