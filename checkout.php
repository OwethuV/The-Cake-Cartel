<?php
session_start();
include 'includes/header.php';
include 'includes/db_connect.php';

if (!isset($_SESSION['userId'])) {
    $_SESSION['message'] = "Please login to checkout.";
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['userId'];
$totalCartValue = 0;
$cartItems = [];

// Fetch cart items
$sql = "SELECT c.cartId, c.cartPrice FROM CART c WHERE c.userId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['message'] = "Your cart is empty. Cannot proceed to checkout.";
    header("Location: cart.php");
    exit();
}

while ($row = $result->fetch_assoc()) {
    $totalCartValue += $row['cartPrice'];
    $cartItems[] = $row['cartId'];
}
$stmt->close();

// Default delivery cost
$deliveryPrice = ($totalCartValue <= 700) ? 5.00 : 0.00;
$finalTotalPrice = $totalCartValue + $deliveryPrice;
?>

<div class="container mt-5">
    <h2 class="mb-4">Checkout</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info">
            <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <h4>Order Summary</h4>
            <ul class="list-group mb-3">
                <li class="list-group-item d-flex justify-content-between lh-sm">
                    <div><h6 class="my-0">Subtotal</h6></div>
                    <span class="text-muted">R<?php echo number_format($totalCartValue, 2); ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between lh-sm">
                    <div>
                        <h6 class="my-0">Delivery Fee</h6>
                        <?php if ($deliveryPrice == 0): ?>
                            <small class="text-success">Free (Order over R700)</small>
                        <?php endif; ?>
                    </div>
                    <span class="text-muted">R<?php echo number_format($deliveryPrice, 2); ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Total (ZAR)</span>
                    <strong>R<?php echo number_format($finalTotalPrice, 2); ?></strong>
                </li>
            </ul>

            <form action="php/process_order.php" method="POST">
                <input type="hidden" name="totalCartValue" value="<?php echo $totalCartValue; ?>">
                <?php foreach ($cartItems as $cartId): ?>
                    <input type="hidden" name="cartIds[]" value="<?php echo $cartId; ?>">
                <?php endforeach; ?>

                <div class="mb-3">
                    <label for="deliveryOption" class="form-label">Delivery Option</label>
                    <select class="form-control" name="deliveryOption" required>
                        <option value="pickup">Pickup (Free)</option>
                        <option value="delivery">Delivery (R5.00 / Free if order > R700)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success btn-lg w-100">Place Order & Pay</button>
            </form>
        </div>
    </div>
</div>

<?php
$conn->close();
include 'includes/footer.php';
?>
