<?php
include 'includes/header.php';
include 'includes/db_connect.php';

if (!isset($_SESSION['userId'])) {
    $_SESSION['message'] = "Please login to checkout."; //just in case they made it this far
    header("Location: login.php");
    exit();
} 

$userId = $_SESSION['userId'];
$totalCartValue = 0;
$cartItems = [];

// Fetching cart items to calculate total
$sql = "SELECT c.cartId, c.cartPrice FROM CART c WHERE c.userId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $totalCartValue += $row['cartPrice'];
        $cartItems[] = $row['cartId']; // Collect cartIds for order creation
    }
} else {
    $_SESSION['message'] = "Your cart is empty. Cannot proceed to checkout.";
    header("Location: cart.php");
    exit();
}
$stmt->close();

// For simplicity, assume a fixed delivery price
$deliveryPrice = 5.00;
$finalTotalPrice = $totalCartValue + $deliveryPrice;
?>

<h2 class="mb-4">Checkout</h2>

<?php
if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-info">' . htmlspecialchars($_SESSION['message']) . '</div>';
    unset($_SESSION['message']);
}
?>

<div class="row">
    <div class="col-md-8">
        <h4>Order Summary</h4>
        <ul class="list-group mb-3">
            <li class="list-group-item d-flex justify-content-between lh-sm">
                <div>
                    <h6 class="my-0">Subtotal</h6>
                </div>
                <span class="text-muted">R<?php echo number_format($totalCartValue, 2); ?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between lh-sm">
                <div>
                    <h6 class="my-0">Delivery Fee</h6>
                </div>
                <span class="text-muted">R<?php echo number_format($deliveryPrice, 2); ?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between">
                <span>Total (ZAR)</span>
                <strong>R<?php echo number_format($finalTotalPrice, 2); ?></strong>
            </li>
        </ul>

        <h4>Delivery Information</h4>
        <p>This is where you would typically have a form for delivery address, payment method selection, etc.</p>
        <p>For this example, we'll assume the user's registered address is used and payment is "on delivery".</p>

        <form action="php/process_order.php" method="POST">
            <input type="hidden" name="totalPrice" value="<?php echo $finalTotalPrice; ?>">
            <input type="hidden" name="deliveryPrice" value="<?php echo $deliveryPrice; ?>">
            <?php foreach ($cartItems as $cartId): ?>
                <input type="hidden" name="cartIds[]" value="<?php echo $cartId; ?>">
            <?php endforeach; ?>
            <button type="submit" class="btn btn-success btn-lg">Place Order</button>
        </form>
    </div>
</div>

<?php
$conn->close();
include 'includes/footer.php';
?>
