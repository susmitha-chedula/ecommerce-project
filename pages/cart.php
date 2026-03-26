<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
    $stmt->execute([$user_id, $product_id]);
}
?>

$user_id = $_SESSION['user_id'];

// Handle Remove
if (isset($_POST['remove_from_cart'])) {
    $product_id = $_POST['product_id'];
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
}

// Handle Update Quantity
if (isset($_POST['update_quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$quantity, $user_id, $product_id]);
}

// Fetch cart items
$stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_cost = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Your Cart</title>
</head>
<body>

<h2>Your Cart</h2>

<?php if (empty($cart_items)): ?>
    <p>Your cart is empty.</p>
<?php else: ?>

<?php
$product_ids = array_column($cart_items, 'product_id');
$placeholders = implode(',', array_fill(0, count($product_ids), '?'));

$stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->execute($product_ids);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php foreach ($products as $product): ?>
    <?php
    $quantity = 0;
    foreach ($cart_items as $cart_item) {
        if ($cart_item['product_id'] == $product['id']) {
            $quantity = $cart_item['quantity'];
            break;
        }
    }
    $total_cost += $product['price'] * $quantity;
    ?>

    <div style="border:1px solid black; padding:10px; margin:10px;">
        <h3><?= $product['name']; ?></h3>
        <p>Price: ₹<?= $product['price']; ?></p>
        <p>Quantity: <?= $quantity; ?></p>

        <!-- Update -->
        <form method="POST">
            <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
            <input type="number" name="quantity" value="<?= $quantity; ?>" min="1">
            <button name="update_quantity">Update</button>
        </form>

        <!-- Remove -->
        <form method="POST">
            <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
            <button name="remove_from_cart">Remove</button>
        </form>
    </div>

<?php endforeach; ?>

<h3>Total: ₹<?= $total_cost; ?></h3>

<?php endif; ?>

<br>
<a href="../index.php">Back to Shop</a>

</body>
</html>