<?php

require_once "config/database.php";


/* ==========================================
   INITIALIZE CART
   ========================================== */

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


/* ==========================================
   ADD PRODUCT TO CART
   ========================================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $productId = intval($_POST['product_id'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 1);


    if ($productId <= 0) {
        header("Location: product.php");
        exit;
    }


    if ($quantity < 1) {
        $quantity = 1;
    }


    /* Get product from database */

    $stmt = $conn->prepare(
        "SELECT id, stock FROM products WHERE id = ?"
    );


    if (!$stmt) {
        die("Database error: " . $conn->error);
    }


    $stmt->bind_param("i", $productId);


    if (!$stmt->execute()) {
        die("Unable to check product: " . $stmt->error);
    }


    $product = $stmt->get_result()->fetch_assoc();

    $stmt->close();


    /* Product doesn't exist */

    if (!$product) {
        die("Product not found.");
    }


    /* Check stock */

    $currentQuantity =
        $_SESSION['cart'][$productId] ?? 0;

    $newQuantity =
        $currentQuantity + $quantity;


    if ($newQuantity > (int)$product['stock']) {
        die(
            "Sorry, only " .
            (int)$product['stock'] .
            " item(s) available in stock."
        );
    }


    /* Add to cart */

    $_SESSION['cart'][$productId] = $newQuantity;


    header("Location: cart.php");
    exit;
}


/* ==========================================
   REMOVE PRODUCT
   ========================================== */

if (isset($_GET['remove'])) {

    $id = intval($_GET['remove']);

    unset($_SESSION['cart'][$id]);

    header("Location: cart.php");
    exit;
}


/* ==========================================
   CALCULATE TOTAL
   ========================================== */

$total = 0;

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Cart - CampusKart</title>

    <link
        rel="stylesheet"
        href="css/cart.css"
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

        <a href="cart.php" class="active">
            Cart 🛒
        </a>

    </nav>

</header>



<!-- ==========================================
     CART PAGE
========================================== -->

<section class="cart-page">


    <!-- PAGE HEADER -->

    <div class="cart-header">

        <div>

            <span class="section-label">
                CAMPUSKART
            </span>

            <h1>
                Your Shopping Cart
            </h1>

            <p>
                Review your selected products before
                completing your order.
            </p>

        </div>


        <div class="cart-icon">
            🛒
        </div>

    </div>



    <?php if (empty($_SESSION['cart'])): ?>


        <!-- ==========================================
             EMPTY CART
        ========================================== -->

        <div class="empty-cart">

            <div class="empty-cart-icon">
                🛒
            </div>

            <h2>
                Your Cart is Empty
            </h2>

            <p>
                Looks like you haven't added anything yet.
                Explore our products and find something
                useful for your campus life.
            </p>

            <a
                href="product.php"
                class="btn"
            >
                Start Shopping →
            </a>

        </div>


    <?php else: ?>


        <!-- ==========================================
             CART CONTENT
        ========================================== -->

        <div class="cart-layout">


            <!-- ======================================
                 CART ITEMS
            ====================================== -->

            <div class="cart-products">


                <div class="cart-products-header">

                    <h2>
                        Cart Items
                    </h2>

                    <span>
                        Your Selected Products
                    </span>

                </div>



                <div class="cart-container">


                    <?php foreach ($_SESSION['cart'] as $id => $quantity): ?>


                        <?php

                        $id = intval($id);
                        $quantity = intval($quantity);


                        /* Get product */

                        $stmt = $conn->prepare(
                            "SELECT * FROM products WHERE id = ?"
                        );


                        if (!$stmt) {
                            die("Database error: " . $conn->error);
                        }


                        $stmt->bind_param(
                            "i",
                            $id
                        );


                        if (!$stmt->execute()) {
                            die("Unable to load product: " . $stmt->error);
                        }


                        $product =
                            $stmt->get_result()->fetch_assoc();

                        $stmt->close();


                        /* Remove deleted products */

                        if (!$product) {

                            unset($_SESSION['cart'][$id]);

                            continue;
                        }


                        /* Make sure quantity is valid */

                        if ($quantity < 1) {

                            unset($_SESSION['cart'][$id]);

                            continue;
                        }


                        /* Prevent cart from exceeding stock */

                        if ($quantity > (int)$product['stock']) {

                            $quantity = (int)$product['stock'];

                            $_SESSION['cart'][$id] = $quantity;
                        }


                        /* If stock is zero, remove item */

                        if ($quantity <= 0) {

                            unset($_SESSION['cart'][$id]);

                            continue;
                        }


                        $subtotal =
                            (float)$product['price'] * $quantity;


                        $total += $subtotal;

                        ?>


                        <!-- ==================================
                             CART ITEM
                        ================================== -->

                        <div class="cart-item">


                            <!-- PRODUCT IMAGE -->

                            <div class="cart-item-image">

                                <img
                                    src="<?= htmlspecialchars(
                                        $product['image'] ?? ''
                                    ) ?>"
                                    alt="<?= htmlspecialchars(
                                        $product['name']
                                    ) ?>"
                                >

                            </div>



                            <!-- PRODUCT INFORMATION -->

                            <div class="cart-item-info">


                                <span class="category">

                                    <?= htmlspecialchars(
                                        $product['category']
                                        ?? 'Campus Essential'
                                    ) ?>

                                </span>


                                <h3>

                                    <?= htmlspecialchars(
                                        $product['name']
                                    ) ?>

                                </h3>


                                <p class="item-price">

                                    ₹<?= number_format(
                                        (float)$product['price'],
                                        2
                                    ) ?>

                                    per item

                                </p>


                                <div class="item-quantity">

                                    <span>
                                        Quantity:
                                    </span>

                                    <strong>
                                        <?= $quantity ?>
                                    </strong>

                                </div>


                                <a
                                    href="cart.php?remove=<?= (int)$id ?>"
                                    class="remove"
                                    onclick="return confirm('Remove this product from cart?')"
                                >
                                    🗑 Remove
                                </a>


                            </div>



                            <!-- SUBTOTAL -->

                            <div class="item-subtotal">

                                <span>
                                    Subtotal
                                </span>

                                <strong>

                                    ₹<?= number_format(
                                        $subtotal,
                                        2
                                    ) ?>

                                </strong>

                            </div>


                        </div>


                    <?php endforeach; ?>


                </div>


                <!-- CONTINUE SHOPPING -->

                <a
                    href="product.php"
                    class="continue-shopping"
                >
                    ← Continue Shopping
                </a>


            </div>



            <!-- ======================================
                 ORDER SUMMARY
            ====================================== -->

            <?php if ($total > 0): ?>


                <div class="cart-summary">


                    <div class="summary-header">

                        <span class="section-label">
                            ORDER SUMMARY
                        </span>

                        <h2>
                            Your Order
                        </h2>

                    </div>


                    <div class="summary-line">

                        <span>
                            Products
                        </span>

                        <span>
                            Included
                        </span>

                    </div>


                    <div class="summary-line">

                        <span>
                            Delivery
                        </span>

                        <span class="free">
                            Student Friendly
                        </span>

                    </div>


                    <div class="summary-line">

                        <span>
                            Platform
                        </span>

                        <span>
                            CampusKart
                        </span>

                    </div>


                    <div class="summary-divider"></div>


                    <div class="summary-total">

                        <span>
                            Total
                        </span>

                        <strong>

                            ₹<?= number_format(
                                $total,
                                2
                            ) ?>

                        </strong>

                    </div>


                    <a
                        href="checkout.php"
                        class="btn checkout-btn"
                    >
                        Proceed to Checkout →
                    </a>


                    <div class="secure-checkout">

                        🔒

                        <span>
                            Secure and simple checkout
                        </span>

                    </div>


                </div>


            <?php else: ?>


                <!-- ==================================
                     CART BECAME EMPTY
                ================================== -->

                <div class="empty-cart">

                    <div class="empty-cart-icon">
                        🛒
                    </div>

                    <h2>
                        Your Cart is Empty
                    </h2>

                    <p>
                        The products in your cart are
                        no longer available.
                    </p>

                    <a
                        href="product.php"
                        class="btn"
                    >
                        Start Shopping →
                    </a>

                </div>


            <?php endif; ?>


        </div>


    <?php endif; ?>


</section>



<!-- ==========================================
     WHY CAMPUSKART
========================================== -->

<section class="cart-benefits">


    <div class="cart-benefits-heading">

        <span class="section-label">
            SHOP WITH CONFIDENCE
        </span>

        <h2>
            Why Students Choose
            <span>CampusKart</span>
        </h2>

    </div>


    <div class="benefit-grid">


        <div class="benefit-card">

            <div class="benefit-icon">
                🎓
            </div>

            <h3>
                Made For Students
            </h3>

            <p>
                Products selected with everyday
                college life in mind.
            </p>

        </div>


        <div class="benefit-card">

            <div class="benefit-icon">
                💰
            </div>

            <h3>
                Affordable
            </h3>

            <p>
                Get useful campus essentials
                at student-friendly prices.
            </p>

        </div>


        <div class="benefit-card">

            <div class="benefit-icon">
                🚚
            </div>

            <h3>
                Easy Shopping
            </h3>

            <p>
                Select your products and complete
                your order with ease.
            </p>

        </div>


        <div class="benefit-card">

            <div class="benefit-icon">
                🔒
            </div>

            <h3>
                Simple & Secure
            </h3>

            <p>
                A straightforward shopping
                experience for campus life.
            </p>

        </div>


    </div>


</section>



<!-- ==========================================
     FOOTER
========================================== -->

<footer>


    <div class="footer-container">


        <div class="footer-brand">

            <h2>
                Campus<span>Kart</span>
            </h2>

            <p>
                Your student marketplace for affordable,
                useful and reliable campus essentials.
            </p>

        </div>


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