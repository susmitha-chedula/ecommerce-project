<?php
session_start();
include 'includes/db.php';

// ✅ Add to Cart logic
if (isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: pages/login.php");
        exit();
    }

    $product_id = $_POST['product_id'];
    $user_id = $_SESSION['user_id'];

    // Check if already exists
    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Increase quantity
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
    } else {
        // Insert new
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
        $stmt->execute([$user_id, $product_id]);
    }
}

// Fetch products
$stmt = $conn->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Store</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <div class="header-container">
        <h1>Welcome to Our Store</h1>
        <nav>
            <a href="pages/login.php">Login</a>
            <a href="pages/register.php">Register</a>
            <a href="pages/cart.php">Cart</a>
        </nav>
    </div>
</header>

<div class="main-container">
    <main>
        <h2>Products</h2>

        <div class="product-list">
            <?php if (empty($products)) : ?>
                <p>No products available.</p>
            <?php else : ?>
                <?php foreach ($products as $product) : ?>

                    <div class="product">
                        <h3><?= htmlspecialchars($product['name']); ?></h3>
                        <p>Price: ₹<?= number_format($product['price'], 2); ?></p>
                        <p><?= htmlspecialchars($product['description']); ?></p>

                        <?php if (!empty($product['image'])) : ?>
                            <img src="images/<?= htmlspecialchars($product['image']); ?>" class="product-image">
                        <?php endif; ?>

                        <!-- ✅ ADD TO CART BUTTON -->
                        <form method="POST">
                            <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                            <button type="submit" name="add_to_cart" class="add-to-cart-button">Add to Cart</button>
                        </form>

                    </div>

                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</div>

<footer>
    <p>&copy; <?= date('Y'); ?> Online Store</p>
</footer>

</body>
</html>