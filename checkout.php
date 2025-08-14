<?php
include 'includes/header.php';
include 'includes/db_connect.php'; // This sets $mysqli

if (!isset($_SESSION['userId'])) {
    $_SESSION['message'] = "Please login to checkout.";
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['userId'];
$totalCartValue = 0;
$cartItems = [];

// Fetch cart data
$sql = "SELECT c.cartId, c.cartPrice FROM CART c WHERE c.userId = ?";
$stmt = $mysqli->prepare($sql);
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
?>

<h2 class="mb-4">Checkout</h2>

<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-info"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <form action="php/process_order.php" method="POST" id="checkoutForm">
            <h4>Delivery Method</h4>
            <select name="deliveryOption" id="deliveryMethod" class="form-control mb-3" required>
                <option value="">Select...</option>
                <option value="pickup">Pickup (Free)</option>
                <option value="delivery">Delivery (R5.00, Free over R700)</option>
            </select>

            <div id="addressSection" style="display: none;">
                <label for="address">Delivery Address</label>
                <textarea name="address" id="address" class="form-control mb-3" placeholder="Enter delivery address..."></textarea>
            </div>

            <input type="hidden" name="totalCartValue" value="<?php echo $totalCartValue; ?>">
            <?php foreach ($cartItems as $cartId): ?>
                <input type="hidden" name="cartIds[]" value="<?php echo $cartId; ?>">
            <?php endforeach; ?>

            <button type="submit" class="btn btn-success btn-lg">Place Order</button>
        </form>
    </div>
</div>

<script>
document.getElementById("deliveryMethod").addEventListener("change", function () {
    const method = this.value;
    const addressField = document.getElementById("addressSection");

    if (method === "delivery") {
        addressField.style.display = "block";
        document.getElementById("address").setAttribute("required", "required");
    } else {
        addressField.style.display = "none";
        document.getElementById("address").removeAttribute("required");
    }
});
</script>

<?php
$mysqli->close();
include 'includes/footer.php';
?>
