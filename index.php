<?php
session_start();
include 'includes/db.php';

// temporary login user
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}

// ✅ ADD TO CART (SWAROOP SIMPLE)
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];

    $check = $conn->prepare("SELECT * FROM cart WHERE user_id=? AND product_id=?");
$check->execute([$_SESSION['user_id'], $product_id]);

if ($check->rowCount() > 0) {
    $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id=? AND product_id=?")
         ->execute([$_SESSION['user_id'], $product_id]);
} else {
    $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)")
         ->execute([$_SESSION['user_id'], $product_id]);
}
}

// FETCH PRODUCTS
$stmt = $conn->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
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
            <a href="pages/cart.php" class="cart-link">
                <img src="images/cart-icon.png" class="cart-icon"> Cart
            </a>
            <a href="pages/logout.php" class="logout-button">Logout</a>
        </nav>
    </div>
</header>

<div class="main-container">
<main>
<h2>Products</h2>

<div class="product-list">

<?php foreach ($products as $product): ?>
<div class="product">
    <h3><?= htmlspecialchars($product['name']); ?></h3>
    <p>Price: ₹<?= number_format($product['price'], 2); ?></p>
    <p><?= htmlspecialchars($product['description']); ?></p>

    <?php if (!empty($product['image'])): ?>
        <img src="images/<?= $product['image']; ?>" class="product-image">
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
        <button type="submit" name="add_to_cart" class="add-to-cart-button">
            Add to Cart
        </button>
    </form>
</div>
<?php endforeach; ?>

</div>
</main>
</div>

<footer>
<p>&copy; <?= date('Y'); ?> Online Store</p>
</footer>

</body>
</html>