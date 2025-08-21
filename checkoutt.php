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
    header("Location: products.php");
    exit();
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | The Cake Cartel</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;500;600&family=Quicksand:wght@500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            background-color: #fff9f6;
            font-family: 'Poppins', 'Quicksand', sans-serif;
            color: #5a4a42;
        }
        
        .bakery-checkout-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .bakery-checkout-container {
            width: 100%;
            max-width: 900px;
        }
        
        .bakery-checkout-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(251, 176, 166, 0.15);
            overflow: hidden;
            padding: 30px;
            margin: 0 auto;
        }
        
        .checkout-header {
            margin-bottom: 30px;
            text-align: center;
        }
        
        .checkout-main-title {
            font-family: 'Pacifico', cursive;
            color: #ff7e8a;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .checkout-subtitle {
            font-size: 1rem;
            color: #a38b82;
            max-width: 500px;
            margin: 0 auto 20px;
        }
        
        .alert {
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }
        
        .alert-info {
            background-color: #ffd6dd;
            color: #ff7e8a;
            border: 1px solid #ffb6c1;
        }
        
        .checkout-content-wrapper {
            margin-top: 20px;
        }
        
        .form-title {
            color: #5a4a42;
            font-size: 1.6rem;
            margin-bottom: 20px;
            font-weight: 600;
            font-family: 'Quicksand', sans-serif;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #5a4a42;
            font-size: 0.95rem;
        }
        
        .form-control {
            border: 1px solid #f0e6e0;
            border-radius: 8px;
            padding: 12px 15px;
            width: 100%;
            transition: all 0.3s;
            background: #fff9f6;
            font-size: 0.95rem;
            font-family: 'Poppins', sans-serif;
        }
        
        .form-control:focus {
            border-color: #ffb6c1;
            box-shadow: 0 0 0 3px rgba(255, 182, 193, 0.2);
            background: white;
            outline: none;
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        
        .btn-checkout {
            background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(255, 154, 158, 0.3);
            font-size: 1rem;
            cursor: pointer;
            display: block;
            width: 100%;
            margin-top: 20px;
        }
        
        .btn-checkout:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 154, 158, 0.4);
        }
        
        .order-summary {
            background: #fff9f6;
            border-radius: 12px;
            padding: 20px;
            margin-top: 30px;
        }
        
        .summary-title {
            font-size: 1.2rem;
            color: #5a4a42;
            margin-bottom: 15px;
            font-weight: 600;
            border-bottom: 1px solid #f0e6e0;
            padding-bottom: 10px;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.95rem;
        }
        
        .summary-total {
            display: flex;
            justify-content: space-between;
            font-weight: 600;
            font-size: 1.1rem;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #f0e6e0;
            color: #ff7e8a;
        }
        
        @media (max-width: 768px) {
            .checkout-main-title {
                font-size: 2rem;
            }
            
            .bakery-checkout-wrapper {
                padding: 30px 15px;
            }
            
            .bakery-checkout-card {
                padding: 25px;
            }
        }
    </style>
</head>

<body>
    <div class="bakery-checkout-wrapper">
        <div class="bakery-checkout-container">
            <div class="bakery-checkout-card">
                <div class="checkout-header">
                    <h1 class="checkout-main-title">Checkout</h1>
                    <p class="checkout-subtitle">Complete your order with our secure checkout process</p>
                </div>

                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-info"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></div>
                <?php endif; ?>

                <div class="checkout-content-wrapper">
                    <form action="php/process_order.php" method="POST" id="checkoutForm">
                        <h2 class="form-title">Delivery Information</h2>
                        
                        <div class="form-group">
                            <label for="deliveryMethod">Delivery Method</label>
                            <select name="deliveryMethod" id="deliveryMethod" class="form-control" required>
                                <option value="">Select delivery method...</option>
                                <option value="pickup">Pickup (Free)</option>
                                <option value="delivery">Delivery (R100.00, Free over R700)</option>
                            </select>
                        </div>

                        <div id="addressSection" style="display: none;">
                            <div class="form-group">
                                <label for="address">Delivery Address</label>
                                <textarea name="address" id="address" class="form-control" placeholder="Enter your complete delivery address..."></textarea>
                            </div>
                        </div>

                        <div class="order-summary">
                            <h3 class="summary-title">Order Summary</h3>
                            <div class="summary-item">
                                <span>Subtotal:</span>
                                <span>R<?php echo number_format($totalCartValue, 2); ?></span>
                            </div>
                            <div class="summary-item" id="deliveryFeeItem">
                                <span>Delivery Fee:</span>
                                <!-- if the option selected is delivery, a R100 delivery fee must be added but keep it R0.00 if not -->
                                <span>R<?php echo ($totalCartValue >= 700) ? '0.00' : '100.00'; ?></span>
                            </div>
                            <div class="summary-total">
                                <span>Total:</span>
                                <span id="totalAmount">R<?php echo number_format($totalCartValue, 2); ?></span>
                            </div>
                        </div>

                        <input type="hidden" name="totalCartValue" value="<?php echo htmlspecialchars($totalCartValue); ?>">
                        <?php foreach ($cartItems as $cartId): ?>
                            <input type="hidden" name="cartIds[]" value="<?php echo htmlspecialchars($cartId); ?>">
                        <?php endforeach; ?>

                        <button type="submit" class="btn-checkout">Place Order</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById("deliveryMethod").addEventListener("change", function () {
            const method = this.value;
            const addressField = document.getElementById("addressSection");
            const deliveryFeeItem = document.getElementById("deliveryFeeItem");
            const totalAmount = document.getElementById("totalAmount");
            const cartValue = <?php echo $totalCartValue; ?>;
            
            if (method === "delivery") {
                addressField.style.display = "block";
                document.getElementById("address").setAttribute("required", "required");
                
                let deliveryFee = 100.00;
                if (cartValue > 700) {
                    deliveryFee = 0.00;
                }
                
                deliveryFeeItem.innerHTML = `<span>Delivery Fee:</span><span>R${deliveryFee.toFixed(2)}</span>`;
                
                const total = cartValue + deliveryFee;
                totalAmount.textContent = `R${total.toFixed(2)}`;
            } else {
                addressField.style.display = "none";
                document.getElementById("address").removeAttribute("required");
                
                deliveryFeeItem.innerHTML = `<span>Delivery Fee:</span><span>R0.00</span>`;
                totalAmount.textContent = `R${cartValue.toFixed(2)}`;
            }
        });
    </script>

    <?php
    $conn->close();
    include 'includes/footer.php';
    ?>
</body>

</html>