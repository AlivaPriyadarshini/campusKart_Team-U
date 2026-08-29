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

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        <?= $id > 0 ? "Product Details" : "Products" ?> - CampusKart
    </title>

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


<?php if ($id > 0): ?>

<section class="section">

    <div class="product-detail">

        <img
            src="<?= htmlspecialchars($product['image']) ?>"
            alt="<?= htmlspecialchars($product['name']) ?>"
        >

        <div>

            <span class="category">
                <?= htmlspecialchars($product['category']) ?>
            </span>

            <h1>
                <?= htmlspecialchars($product['name']) ?>
            </h1>

            <p class="price">
                ₹<?= number_format($product['price'], 2) ?>
            </p>

            <p>
                <?= htmlspecialchars($product['description']) ?>
            </p>

            <p>
                <strong>
                    Stock:
                </strong>

                <?= $product['stock'] ?>
            </p>

            <?php if ($product['stock'] > 0): ?>

                <form action="cart.php" method="POST">

                    <input
                        type="hidden"
                        name="product_id"
                        value="<?= $product['id'] ?>"
                    >

                    <label>Quantity</label>

                    <input
                        type="number"
                        name="quantity"
                        value="1"
                        min="1"
                        max="<?= $product['stock'] ?>"
                        class="quantity"
                    >

                    <button class="btn" type="submit">
                        Add to Cart
                    </button>

                </form>

            <?php else: ?>

                <p class="out-stock">
                    Out of Stock
                </p>

            <?php endif; ?>

        </div>

    </div>

</section>


<?php else: ?>

<section class="section">

    <h1>CampusKart Products</h1>

    <div class="product-grid">

        <?php while ($product = $result->fetch_assoc()): ?>

            <div class="product-card">

                <img
                    src="<?= htmlspecialchars($product['image']) ?>"
                    alt="<?= htmlspecialchars($product['name']) ?>"
                >

                <div class="product-info">

                    <span class="category">
                        <?= htmlspecialchars($product['category']) ?>
                    </span>

                    <h3>
                        <?= htmlspecialchars($product['name']) ?>
                    </h3>

                    <p class="price">
                        ₹<?= number_format($product['price'], 2) ?>
                    </p>

                    <a
                        href="products.php?id=<?= $product['id'] ?>"
                        class="btn"
                    >
                        View Details
                    </a>

                </div>

            </div>

        <?php endwhile; ?>

    </div>

</section>

<?php endif; ?>


<footer>
    <p>© 2026 CampusKart</p>
</footer>

</body>
</html>