<?php
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

$sql = "SELECT c.cartId, c.cartPrice FROM CART c WHERE c.userId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $totalCartValue += $row['cartPrice'];
        $cartItems[] = $row['cartId'];
    }
} else {
    $_SESSION['message'] = "Your cart is empty.";
    header("Location: cart.php");
    exit();
}
$stmt->close();

$merchant_id = $_ENV['PAYFAST_MERCHANT_ID'];
$merchant_key = $_ENV['PAYFAST_MERCHANT_KEY'];
$return_url = $_ENV['PAYFAST_RETURN_URL'];
$cancel_url = $_ENV['PAYFAST_CANCEL_URL'];
$notify_url = $_ENV['PAYFAST_NOTIFY_URL'];

$deliveryFee = 0; // default pickup
$finalTotal = $totalCartValue;
?>

<h2 class="mb-4">Checkout</h2>

<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-info"><?= htmlspecialchars($_SESSION['message']) ?></div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <h4>Order Summary</h4>
        <ul class="list-group mb-3">
            <li class="list-group-item d-flex justify-content-between">
                <span>Subtotal</span>
                <strong>R<?= number_format($totalCartValue, 2) ?></strong>
            </li>
            <li class="list-group-item d-flex justify-content-between">
                <span>Delivery Fee</span>
                <strong id="delivery-fee">R<?= number_format($deliveryFee, 2) ?></strong>
            </li>
            <li class="list-group-item d-flex justify-content-between">
                <span>Total</span>
                <strong id="final-total">R<?= number_format($finalTotal, 2) ?></strong>
            </li>
        </ul>

        <h4>Choose Delivery Method</h4>
        <form action="https://www.payfast.co.za/eng/process" method="POST" id="payfastForm">
            <select name="delivery_method" id="delivery-method" class="form-control mb-3" required>
                <option value="pickup" selected>Pickup (Free)</option>
                <option value="delivery">Delivery (R5.00, Free over R700)</option>
            </select>

            <!-- PayFast Fields -->
            <input type="hidden" name="merchant_id" value="<?= htmlspecialchars($merchant_id) ?>">
            <input type="hidden" name="merchant_key" value="<?= htmlspecialchars($merchant_key) ?>">
            <input type="hidden" name="return_url" value="<?= htmlspecialchars($return_url) ?>">
            <input type="hidden" name="cancel_url" value="<?= htmlspecialchars($cancel_url) ?>">
            <input type="hidden" name="notify_url" value="<?= htmlspecialchars($notify_url) ?>">
            <input type="hidden" name="amount" id="amount" value="<?= number_format($finalTotal, 2, '.', '') ?>">
            <input type="hidden" name="item_name" value="Order from The Cake Cartel">

            <!-- Pass cart IDs -->
            <?php foreach ($cartItems as $cartId): ?>
                <input type="hidden" name="cartIds[]" value="<?= $cartId ?>">
            <?php endforeach; ?>

            <button type="submit" class="btn btn-success btn-lg">Pay Now</button>
        </form>
    </div>
</div>

<script>
    const deliveryMethod = document.getElementById('delivery-method');
    const deliveryFeeDisplay = document.getElementById('delivery-fee');
    const totalDisplay = document.getElementById('final-total');
    const amountInput = document.getElementById('amount');
    const subtotal = <?= number_format($totalCartValue, 2, '.', '') ?>;

    function updateTotals() {
        let fee = 0;
        if (deliveryMethod.value === 'delivery') {
            fee = (subtotal >= 700) ? 0 : 5;
        }
        const total = subtotal + fee;

        deliveryFeeDisplay.textContent = "R" + fee.toFixed(2);
        totalDisplay.textContent = "R" + total.toFixed(2);
        amountInput.value = total.toFixed(2);
    }

    deliveryMethod.addEventListener('change', updateTotals);

    // Run once on page load
    updateTotals();
</script>

<?php include 'includes/footer.php'; ?>
