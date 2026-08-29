<?php

require_once "config/database.php";

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


/* Add product */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $productId = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    if ($quantity < 1) {
        $quantity = 1;
    }

    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }

    header("Location: cart.php");
    exit;
}


/* Remove product */
if (isset($_GET['remove'])) {

    $id = intval($_GET['remove']);

    unset($_SESSION['cart'][$id]);

    header("Location: cart.php");
    exit;
}


$total = 0;
?>

<!DOCTYPE html>

<html>

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Cart - CampusKart</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<header class="navbar">

    <div class="logo">
        Campus<span>Kart</span>
    </div>

    <nav>
        <a href="index.php">Home</a>
        <a href="products.php">Products</a>
        <a href="cart.php">Cart 🛒</a>
    </nav>

</header>


<section class="section">

    <h1>Your Shopping Cart</h1>

    <?php if (empty($_SESSION['cart'])): ?>

        <div class="empty-cart">

            <h2>Your cart is empty 🛒</h2>

            <a href="products.php" class="btn">
                Start Shopping
            </a>

        </div>

    <?php else: ?>

        <div class="cart-container">

            <?php foreach ($_SESSION['cart'] as $id => $quantity): ?>

                <?php

                $stmt = $conn->prepare(
                    "SELECT * FROM products WHERE id = ?"
                );

                $stmt->bind_param("i", $id);
                $stmt->execute();

                $product = $stmt->get_result()->fetch_assoc();

                if (!$product) {
                    continue;
                }

                $subtotal = $product['price'] * $quantity;

                $total += $subtotal;

                ?>

                <div class="cart-item">

                    <img
                        src="<?= htmlspecialchars($product['image']) ?>"
                        alt=""
                    >

                    <div>

                        <h3>
                            <?= htmlspecialchars($product['name']) ?>
                        </h3>

                        <p>
                            ₹<?= number_format($product['price'], 2) ?>
                            × <?= $quantity ?>
                        </p>

                        <strong>
                            ₹<?= number_format($subtotal, 2) ?>
                        </strong>

                        <br>

                        <a
                            href="cart.php?remove=<?= $id ?>"
                            class="remove"
                        >
                            Remove
                        </a>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>


        <div class="cart-total">

            <h2>
                Total:
                ₹<?= number_format($total, 2) ?>
            </h2>

            <a href="checkout.php" class="btn">
                Proceed to Checkout
            </a>

        </div>

    <?php endif; ?>

</section>

</body>
</html>