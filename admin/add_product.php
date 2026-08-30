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
   HANDLE FORM SUBMISSION
   ========================================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $imageUrl = trim($_POST['image_url'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $stock = intval($_POST['stock'] ?? 0);

    $imagePath = "";
    $uploadedFilePath = "";


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
       CHECK IMAGE
       ========================================== */

    $hasUploadedFile = (
        isset($_FILES['image']) &&
        $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
    );

    $hasImageUrl = ($imageUrl !== '');


    if (!$hasUploadedFile && !$hasImageUrl) {
        die("Please upload an image or enter an image URL.");
    }


    /* ==========================================
       OPTION 1: UPLOAD LOCAL IMAGE
       ========================================== */

    if ($hasUploadedFile) {

        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            die("There was an error uploading the image.");
        }


        $image = $_FILES['image'];


        /* Maximum 5 MB */
        if ($image['size'] > 5 * 1024 * 1024) {
            die("Image size must be less than 5 MB.");
        }


        /* Check actual image */
        $imageInfo = getimagesize($image['tmp_name']);

        if ($imageInfo === false) {
            die("Please upload a valid image.");
        }


        /* Allowed image types */
        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif'
        ];


        $mimeType = $imageInfo['mime'];


        if (!isset($allowedTypes[$mimeType])) {
            die("Only JPG, PNG, WEBP and GIF images are allowed.");
        }


        /* ==========================================
           CREATE PRODUCT IMAGE DIRECTORY
           ========================================== */

        $uploadDirectory = __DIR__ . "/../image/products/";


        if (!is_dir($uploadDirectory)) {

            if (!mkdir($uploadDirectory, 0755, true)) {
                die("Unable to create image directory.");
            }
        }


        /* ==========================================
           CREATE UNIQUE FILE NAME
           ========================================== */

        $extension = $allowedTypes[$mimeType];

        $fileName =
            uniqid("product_", true) .
            "." .
            $extension;


        $uploadedFilePath =
            $uploadDirectory .
            $fileName;


        /* ==========================================
           MOVE IMAGE
           ========================================== */

        if (!move_uploaded_file(
            $image['tmp_name'],
            $uploadedFilePath
        )) {
            die("Unable to upload image.");
        }


        /* Database path */
        $imagePath = "image/products/" . $fileName;

    }


    /* ==========================================
       OPTION 2: IMAGE URL
       ========================================== */

    elseif ($hasImageUrl) {

        if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            die("Please enter a valid image URL.");
        }


        /*
         * Store the URL directly in the database.
         */

        $imagePath = $imageUrl;
    }


    /* ==========================================
       INSERT PRODUCT
       ========================================== */

    $stmt = $conn->prepare("
        INSERT INTO products
        (name, description, price, image, category, stock)
        VALUES (?, ?, ?, ?, ?, ?)
    ");


    if (!$stmt) {
        die("Database error: " . $conn->error);
    }


    $stmt->bind_param(
        "ssdssi",
        $name,
        $description,
        $price,
        $imagePath,
        $category,
        $stock
    );


    if (!$stmt->execute()) {

        /*
         * If database insertion fails,
         * remove the uploaded image.
         */

        if (
            $uploadedFilePath !== "" &&
            file_exists($uploadedFilePath)
        ) {
            unlink($uploadedFilePath);
        }

        die(
            "Product could not be added: " .
            $stmt->error
        );
    }


    $stmt->close();


    /* ==========================================
       REDIRECT
       ========================================== */

    header("Location: product.php");
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

    <title>Add Product - CampusKart</title>

    <link
        rel="stylesheet"
        href="../css/add_product.css"
    >

</head>


<body>


<!-- ==========================================
     ADMIN HEADER
     ========================================== -->

<header class="add-product-header">

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

        <a href="product.php">
            Products
        </a>

        <a href="add_product.php" class="active">
            Add Product
        </a>

        <a href="../index.php">
            Website
        </a>

        <a href="login.php?logout=1" class="logout">
            Logout
        </a>

    </nav>

</header>



<!-- ==========================================
     ADD PRODUCT SECTION
     ========================================== -->

<main class="add-product-page">


    <div class="add-product-heading">

        <div>

            <p class="heading-label">
                PRODUCT MANAGEMENT
            </p>

            <h1>
                Add New Product
            </h1>

            <p>
                Add a new product to the CampusKart student marketplace.
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
         PRODUCT FORM
         ====================================== -->

    <div class="add-product-card">


        <div class="form-heading">

            <div class="form-icon">
                +
            </div>

            <div>

                <h2>
                    Product Information
                </h2>

                <p>
                    Enter the details of the product below.
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
                    placeholder="Example: Campus Backpack"
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
                    placeholder="Write a short description about the product..."
                    required
                ></textarea>

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
                            placeholder="899"
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
                        placeholder="20"
                        min="0"
                        required
                    >

                </div>


            </div>



            <!-- Product Image -->

            <div class="form-group">

                <label for="image">
                    Product Image
                </label>

                <input
                    id="image"
                    type="file"
                    name="image"
                    accept="image/jpeg,image/png,image/webp,image/gif"
                >

                <small>
                    Upload JPG, PNG, WEBP or GIF (maximum 5 MB).
                </small>

            </div>

            <div class="form-group">

                <label for="image_url">
                    Or Product Image URL
                </label>

                <input
                    id="image_url"
                    type="url"
                    name="image_url"
                    placeholder="https://example.com/product-image.jpg"
                >

                <small>
                    Use either a local image upload or a direct image URL.
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
                    placeholder="Example: Electronics, Fashion, Stationery"
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
                    class="add-btn"
                    type="submit"
                >
                    + Add Product
                </button>


            </div>


        </form>


    </div>



    <!-- ======================================
         INFORMATION BOX
         ====================================== -->

    <div class="add-product-info">

        <div class="info-icon">
            i
        </div>

        <div>

            <h3>
                Product Tip
            </h3>

            <p>
                Make sure the product name, price, category and stock
                information are correct before adding the product.
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