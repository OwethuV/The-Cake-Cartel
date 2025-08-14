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

// Fetch cart items
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
    $_SESSION['message'] = "Your cart is empty. Cannot proceed to checkout.";
    header("Location: cart.php");
    exit();
}
$stmt->close();

$deliveryPrice = 0.00;
$deliveryMethod = $_POST['deliveryMethod'] ?? 'pickup';

if ($deliveryMethod === 'delivery' && $totalCartValue < 700) {
    $deliveryPrice = 5.00;
}

$finalTotalPrice = $totalCartValue + $deliveryPrice;

// PayFast credentials (from .env)
$merchant_id = getenv('PAYFAST_MERCHANT_ID');
$merchant_key = getenv('PAYFAST_MERCHANT_KEY');
$passphrase = getenv('PAYFAST_PASSPHRASE');
$payfast_url = 'https://sandbox.payfast.co.za/eng/process';

$item_name = 'Cake Order';
$return_url = 'http://localhost/success.php';
$cancel_url = 'http://localhost/cancel.php';
$notify_url = 'http://localhost/ipn.php';

$data = array(
    'merchant_id' => $merchant_id,
    'merchant_key' => $merchant_key,
    'return_url' => $return_url,
    'cancel_url' => $cancel_url,
    'notify_url' => $notify_url,
    'name_first' => $_SESSION['userName'] ?? 'Customer',
    'email_address' => $_SESSION['userEmail'] ?? 'test@example.com',
    'amount' => number_format($finalTotalPrice, 2, '.', ''),
    'item_name' => $item_name,
);

// Add passphrase for signature if set
$signature_data = [];
foreach ($data as $key => $val) {
    $signature_data[] = "$key=" . urlencode($val);
}
if (!empty($passphrase)) {
    $signature_data[] = "passphrase=" . urlencode($passphrase);
}
$data['signature'] = md5(implode("&", $signature_data));
?>

<h2 class="mb-4">Checkout</h2>

<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-info"><?= htmlspecialchars($_SESSION['message']) ?></div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<form method="POST" action="">
    <div class="form-group mb-3">
        <label for="deliveryMethod">Choose Delivery Option:</label>
        <select name="deliveryMethod" id="deliveryMethod" class="form-control" onchange="this.form.submit()">
            <option value="pickup" <?= ($deliveryMethod === 'pickup') ? 'selected' : '' ?>>Pickup (Free)</option>
            <option value="delivery" <?= ($deliveryMethod === 'delivery') ? 'selected' : '' ?>>Delivery (R5 unless total > R700)</option>
        </select>
    </div>
</form>

<div class="row">
    <div class="col-md-8">
        <h4>Order Summary</h4>
        <ul class="list-group mb-3">
            <li class="list-group-item d-flex justify-content-between lh-sm">
                <div><h6 class="my-0">Subtotal</h6></div>
                <span class="text-muted">R<?= number_format($totalCartValue, 2) ?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between lh-sm">
                <div><h6 class="my-0">Delivery Fee</h6></div>
                <span class="text-muted">R<?= number_format($deliveryPrice, 2) ?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between">
                <strong>Total (ZAR)</strong>
                <strong>R<?= number_format($finalTotalPrice, 2) ?></strong>
            </li>
        </ul>

        <form action="<?= htmlspecialchars($payfast_url) ?>" method="POST">
            <?php foreach ($data as $key => $value): ?>
                <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
            <?php endforeach; ?>
            <?php foreach ($cartItems as $cartId): ?>
                <input type="hidden" name="cartIds[]" value="<?= $cartId ?>">
            <?php endforeach; ?>
            <input type="hidden" name="deliveryPrice" value="<?= $deliveryPrice ?>">
            <input type="hidden" name="deliveryMethod" value="<?= $deliveryMethod ?>">
            <button type="submit" class="btn btn-success btn-lg">Pay with PayFast</button>
        </form>
    </div>
</div>

<?php
$conn->close();
include 'includes/footer.php';
?>
