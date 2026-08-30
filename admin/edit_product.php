<?php

require_once "../config/database.php";


/* ==========================================
   CHECK ADMIN LOGIN
   ========================================== */

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}


/* ==========================================
   CHECK PRODUCT ID
   ========================================== */

if (
    !isset($_GET['id']) ||
    !is_numeric($_GET['id'])
) {
    header("Location: product.php");
    exit;
}

$id = intval($_GET['id']);


/* ==========================================
   GET PRODUCT
   ========================================== */

$stmt = $conn->prepare(
    "SELECT * FROM products WHERE id = ?"
);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    die(
        "Unable to fetch product: " .
        $stmt->error
    );
}

$product =
    $stmt->get_result()->fetch_assoc();

$stmt->close();


/* ==========================================
   CHECK PRODUCT EXISTS
   ========================================== */

if (!$product) {
    die("Product not found.");
}


/* ==========================================
   HANDLE UPDATE
   ========================================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name =
        trim($_POST['name'] ?? '');

    $description =
        trim($_POST['description'] ?? '');

    $price =
        floatval($_POST['price'] ?? 0);

    $imageUrl =
        trim($_POST['image_url'] ?? '');

    $category =
        trim($_POST['category'] ?? '');

    $stock =
        intval($_POST['stock'] ?? 0);


    /* ==========================================
       VALIDATION
       ========================================== */

    if ($name === '') {
        die("Product name is required.");
    }

    if ($description === '') {
        die("Product description is required.");
    }

    if ($category === '') {
        die("Category is required.");
    }

    if ($price < 0) {
        die("Price cannot be negative.");
    }

    if ($stock < 0) {
        die("Stock cannot be negative.");
    }


    /* ==========================================
       KEEP CURRENT IMAGE
       ========================================== */

    $imagePath =
        $product['image'];

    $newUploadedFilePath = "";

    $newImageUploaded = false;


    /* ==========================================
       CHECK NEW IMAGE
       ========================================== */

    $hasUploadedFile = (
        isset($_FILES['image']) &&
        $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
    );

    $hasImageUrl = ($imageUrl !== '');


    /*
     * If a new file is uploaded,
     * it gets priority over URL.
     */

    if ($hasUploadedFile) {


        /* ==========================================
           CHECK UPLOAD ERROR
           ========================================== */

        if (
            $_FILES['image']['error'] !==
            UPLOAD_ERR_OK
        ) {
            die(
                "There was an error uploading " .
                "the image."
            );
        }


        $image =
            $_FILES['image'];


        /* Maximum 5 MB */

        if (
            $image['size'] >
            5 * 1024 * 1024
        ) {
            die(
                "Image size must be less than 5 MB."
            );
        }


        /* Check actual image */

        $imageInfo =
            getimagesize(
                $image['tmp_name']
            );


        if ($imageInfo === false) {
            die(
                "Please upload a valid image."
            );
        }


        /* Allowed types */

        $allowedTypes = [

            'image/jpeg' => 'jpg',

            'image/png' => 'png',

            'image/webp' => 'webp',

            'image/gif' => 'gif'

        ];


        $mimeType =
            $imageInfo['mime'];


        if (
            !isset(
                $allowedTypes[$mimeType]
            )
        ) {
            die(
                "Only JPG, PNG, WEBP and GIF " .
                "images are allowed."
            );
        }


        /* ==========================================
           CREATE DIRECTORY
           ========================================== */

        $uploadDirectory =
            __DIR__ .
            "/../image/products/";


        if (!is_dir($uploadDirectory)) {

            if (
                !mkdir(
                    $uploadDirectory,
                    0755,
                    true
                )
            ) {
                die(
                    "Unable to create image directory."
                );
            }
        }


        /* ==========================================
           CREATE UNIQUE NAME
           ========================================== */

        $extension =
            $allowedTypes[$mimeType];


        $fileName =
            uniqid(
                "product_",
                true
            ) .
            "." .
            $extension;


        $newUploadedFilePath =
            $uploadDirectory .
            $fileName;


        /* ==========================================
           MOVE IMAGE
           ========================================== */

        if (
            !move_uploaded_file(
                $image['tmp_name'],
                $newUploadedFilePath
            )
        ) {
            die(
                "Unable to upload new image."
            );
        }


        $imagePath =
            "image/products/" .
            $fileName;


        $newImageUploaded = true;

    }


    /* ==========================================
       IMAGE URL
       ========================================== */

    elseif ($hasImageUrl) {

        if (
            !filter_var(
                $imageUrl,
                FILTER_VALIDATE_URL
            )
        ) {
            die(
                "Please enter a valid image URL."
            );
        }


        $imagePath =
            $imageUrl;
    }


    /*
     * If neither file nor URL is provided,
     * the old image remains unchanged.
     */


    /* ==========================================
       UPDATE DATABASE
       ========================================== */

    $stmt = $conn->prepare("
        UPDATE products
        SET
            name = ?,
            description = ?,
            price = ?,
            image = ?,
            category = ?,
            stock = ?
        WHERE id = ?
    ");


    if (!$stmt) {
        die(
            "Database error: " .
            $conn->error
        );
    }


    $stmt->bind_param(
        "ssdssii",
        $name,
        $description,
        $price,
        $imagePath,
        $category,
        $stock,
        $id
    );


    if (!$stmt->execute()) {


        /*
         * If database update fails,
         * remove newly uploaded image.
         */

        if (
            $newImageUploaded &&
            file_exists(
                $newUploadedFilePath
            )
        ) {

            unlink(
                $newUploadedFilePath
            );
        }


        die(
            "Product could not be updated: " .
            $stmt->error
        );
    }


    $stmt->close();


    /* ==========================================
       DELETE OLD LOCAL IMAGE
       ========================================== */

    if (
        $newImageUploaded &&
        !empty($product['image'])
    ) {

        $oldImagePath =
            __DIR__ .
            "/../" .
            $product['image'];


        $productsFolder =
            realpath(
                __DIR__ .
                "/../image/products/"
            );


        $oldRealPath =
            realpath(
                $oldImagePath
            );


        /*
         * Delete only images inside
         * image/products/
         */

        if (
            $productsFolder &&
            $oldRealPath &&
            strpos(
                $oldRealPath,
                $productsFolder
            ) === 0 &&
            file_exists(
                $oldRealPath
            )
        ) {

            unlink(
                $oldRealPath
            );
        }
    }


    /* ==========================================
       REDIRECT
       ========================================== */

    header(
        "Location: product.php"
    );

    exit;
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

    <title>Edit Product - CampusKart</title>

    <link
        rel="stylesheet"
        href="../css/edit.css"
    >

</head>


<body>


<!-- ==========================================
     ADMIN HEADER
     ========================================== -->

<header class="edit-product-header">

    <div class="admin-brand">

        <div class="admin-brand">
            <div class="admin-logo">
                <img src="../image/WhatsApp Image 2026-08-29 at 12.40.25 PM.jpeg"
                    alt="CampusKart Logo">
            </div>
        </div>

        <div>

            <h2>CampusKart</h2>

            <span>Admin Panel</span>

        </div>

    </div>


    <nav>

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="product.php" class="active">
            Products
        </a>

        <a href="add_product.php">
            Add Product
        </a>

        <a href="../index.php">
            Website
        </a>

        <a
            href="login.php?logout=1"
            class="logout"
        >
            Logout
        </a>

    </nav>

</header>



<!-- ==========================================
     EDIT PRODUCT SECTION
     ========================================== -->

<main class="edit-product-page">


    <div class="edit-product-heading">

        <div>

            <p class="heading-label">
                PRODUCT MANAGEMENT
            </p>

            <h1>
                Edit Product
            </h1>

            <p>
                Update the product information listed on CampusKart.
            </p>

        </div>


        <a
            href="product.php"
            class="back-btn"
        >
            ← Back to Products
        </a>

    </div>



    <!-- ======================================
         EDIT PRODUCT CARD
         ====================================== -->

    <div class="edit-product-card">


        <div class="form-heading">

            <div class="form-icon">
                ✎
            </div>

            <div>

                <h2>
                    Product Information
                </h2>

                <p>
                    Modify the details of this product below.
                </p>

            </div>

        </div>



        <form method="POST" enctype="multipart/form-data">


            <!-- Product Name -->

            <div class="form-group">

                <label for="name">
                    Product Name
                </label>

                <input
                    id="name"
                    type="text"
                    name="name"
                    value="<?= htmlspecialchars($product['name']) ?>"
                    placeholder="Product Name"
                    required
                >

            </div>



            <!-- Description -->

            <div class="form-group">

                <label for="description">
                    Product Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    placeholder="Product Description"
                    required
                ><?= htmlspecialchars($product['description']) ?></textarea>

            </div>



            <!-- Price and Stock -->

            <div class="form-row">


                <div class="form-group">

                    <label for="price">
                        Price
                    </label>

                    <div class="input-prefix">

                        <span>₹</span>

                        <input
                            id="price"
                            type="number"
                            name="price"
                            value="<?= htmlspecialchars($product['price']) ?>"
                            placeholder="Price"
                            step="0.01"
                            min="0"
                            required
                        >

                    </div>

                </div>



                <div class="form-group">

                    <label for="stock">
                        Stock Quantity
                    </label>

                    <input
                        id="stock"
                        type="number"
                        name="stock"
                        value="<?= htmlspecialchars($product['stock']) ?>"
                        placeholder="Stock"
                        min="0"
                        required
                    >

                </div>


            </div>



            <!-- Product Image -->

            <div class="form-group">

                <label>Current Product Image</label>

                <?php if (!empty($product['image'])): ?>
                    <img
                        src="<?= htmlspecialchars($product['image']) ?>"
                        alt="Current product image"
                        style="width:120px;height:120px;object-fit:cover;border-radius:10px;margin-bottom:10px;display:block;"
                    >
                <?php endif; ?>

                <label for="image">Replace With Local Image</label>

                <input
                    id="image"
                    type="file"
                    name="image"
                    accept="image/jpeg,image/png,image/webp,image/gif"
                >

                <small>
                    Leave empty to keep the current image. JPG, PNG, WEBP or GIF, maximum 5 MB.
                </small>

            </div>

            <div class="form-group">

                <label for="image_url">
                    Or Replace With Image URL
                </label>

                <input
                    id="image_url"
                    type="url"
                    name="image_url"
                    placeholder="https://example.com/product-image.jpg"
                >

                <small>
                    A newly uploaded file takes priority over the URL. Leave both empty to keep the current image.
                </small>

            </div>



            <!-- Category -->

            <div class="form-group">

                <label for="category">
                    Product Category
                </label>

                <input
                    id="category"
                    type="text"
                    name="category"
                    value="<?= htmlspecialchars($product['category']) ?>"
                    placeholder="Category"
                    required
                >

            </div>



            <!-- Form Actions -->

            <div class="form-actions">


                <a
                    href="product.php"
                    class="cancel-btn"
                >
                    Cancel
                </a>


                <button
                    class="update-btn"
                    type="submit"
                >
                    ✓ Update Product
                </button>


            </div>


        </form>


    </div>



    <!-- ======================================
         INFORMATION BOX
         ====================================== -->

    <div class="edit-product-info">

        <div class="info-icon">
            i
        </div>

        <div>

            <h3>
                Update Carefully
            </h3>

            <p>
                Check the product price, category and stock quantity
                before saving your changes.
            </p>

        </div>

    </div>


</main>



<!-- ==========================================
     FOOTER
     ========================================== -->

<footer class="admin-footer">

    <p>
        © 2026 CampusKart
    </p>

    <p>
        Student E-Commerce Platform
    </p>

</footer>


</body>

</html>