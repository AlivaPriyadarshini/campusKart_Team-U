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
        href="css/style.css"
    >

</head>


<body>


<header class="navbar">

    <div class="logo">
        Campus<span>Kart</span>
    </div>


    <nav>

        <a href="index.php">
            Home
        </a>


        <!-- Correct filename -->
        <a href="product.php">
            Products
        </a>


        <a href="cart.php">
            Cart 🛒
        </a>

    </nav>

</header>


<section class="section">


    <h1>Your Shopping Cart</h1>


    <?php if (empty($_SESSION['cart'])): ?>


        <div class="empty-cart">

            <h2>
                Your cart is empty 🛒
            </h2>


            <!-- Correct filename -->
            <a
                href="product.php"
                class="btn"
            >
                Start Shopping
            </a>

        </div>


    <?php else: ?>


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


                <div class="cart-item">


                    <img
                        src="<?= htmlspecialchars(
                            $product['image'] ?? ''
                        ) ?>"
                        alt="<?= htmlspecialchars(
                            $product['name']
                        ) ?>"
                    >


                    <div>


                        <h3>
                            <?= htmlspecialchars(
                                $product['name']
                            ) ?>
                        </h3>


                        <p>

                            ₹<?= number_format(
                                (float)$product['price'],
                                2
                            ) ?>

                            ×

                            <?= $quantity ?>

                        </p>


                        <strong>

                            ₹<?= number_format(
                                $subtotal,
                                2
                            ) ?>

                        </strong>


                        <br>


                        <a
                            href="cart.php?remove=<?= (int)$id ?>"
                            class="remove"
                            onclick="return confirm('Remove this product from cart?')"
                        >
                            Remove
                        </a>


                    </div>


                </div>


            <?php endforeach; ?>


        </div>


        <?php if ($total > 0): ?>


            <div class="cart-total">


                <h2>

                    Total:

                    ₹<?= number_format(
                        $total,
                        2
                    ) ?>

                </h2>


                <a
                    href="checkout.php"
                    class="btn"
                >
                    Proceed to Checkout
                </a>


            </div>


        <?php else: ?>


            <div class="empty-cart">

                <h2>
                    Your cart is empty 🛒
                </h2>


                <a
                    href="product.php"
                    class="btn"
                >
                    Start Shopping
                </a>

            </div>


        <?php endif; ?>


    <?php endif; ?>


</section>


</body>

</html>