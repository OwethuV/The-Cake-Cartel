<?php
include 'includes/header.php';
include 'includes/db_connect.php';

if (!isset($_SESSION['userId'])) {
    $_SESSION['message'] = "Please login to view your cart.";
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['userId'];
$totalCartValue = 0;

// Fetch cart items for the logged-in user
$sql = "SELECT c.cartId, p.productId, p.productName, p.productImg, p.price AS unitPrice, c.quantity, c.cartPrice
        FROM CART c
        JOIN PRODUCTS p ON c.productId = p.productId
        WHERE c.userId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2 class="mb-4">Your Cart</h2>

<?php
if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-info">' . htmlspecialchars($_SESSION['message']) . '</div>';
    unset($_SESSION['message']);
}
?>

<?php if ($result->num_rows > 0): ?>
    <table class="table table-bordered align-middle">
        <thead>
            <tr>
                <th>Product</th>
                <th>Image</th>
                <th>Unit Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['productName']); ?></td>
                    <td><img src="<?php echo htmlspecialchars($row['productImg'] ?: 'img/placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($row['productName']); ?>" class="cart-item-img"></td>
                    <td>R<?php echo number_format($row['unitPrice'], 2); ?></td>
                    <td>
                        <form action="php/update_cart.php" method="POST" class="d-flex align-items-center">
                            <input type="hidden" name="cartId" value="<?php echo $row['cartId']; ?>">
                            <input type="hidden" name="productId" value="<?php echo $row['productId']; ?>">
                            <input type="hidden" name="unitPrice" value="<?php echo $row['unitPrice']; ?>">
                            <input type="number" name="quantity" value="<?php echo htmlspecialchars($row['quantity']); ?>" min="1" class="form-control w-50 me-2">
                            <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                        </form>
                    </td>
                    <td>R<?php echo number_format($row['cartPrice'], 2); ?></td>
                    <td>
                        <form action="php/remove_from_cart.php" method="POST">
                            <input type="hidden" name="cartId" value="<?php echo $row['cartId']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                        </form>
                    </td>
                </tr>
                <?php $totalCartValue += $row['cartPrice']; ?>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="text-end mt-4">
        <h4>Total Cart Value: R<?php echo number_format($totalCartValue, 2); ?></h4>
        <a href="checkout.php" class="btn btn-success btn-lg mt-3">Proceed to Checkout</a>
    </div>

<?php else: ?>
    <p>Your cart is empty. <a class="underline" href="products.php">Start shopping!</a></p>
<?php endif; ?>

<?php
$stmt->close();
$conn->close();
include 'includes/footer.php';
?>
