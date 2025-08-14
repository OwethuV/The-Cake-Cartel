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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart | The Cake Cartel</title>
    <!-- Google Fonts & Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;500;600&family=Quicksand:wght@500;600&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            background-color: #FFF9F6;
            font-family: 'Poppins', 'Quicksand', sans-serif;
        }
        .bakery-cart-wrapper {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding: 30px 20px;
        }
        .bakery-cart-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(251, 176, 166, 0.15);
            overflow: hidden;
            padding: 30px;
            width: 100%;
            max-width: 1200px;
        }
        .cart-header {
            margin-bottom: 30px;
            text-align: center;
        }
        .cart-main-title {
            font-family: 'Pacifico', cursive;
            color: #FF7E8A;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        .cart-subtitle {
            font-size: 1rem;
            color: #A38B82;
            max-width: 500px;
            margin: 0 auto 20px;
        }
        .cart-item-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #FFD6DD;
        }
        .table {
            border-collapse: separate;
            border-spacing: 0 15px;
        }
        .table thead th {
            border-bottom: none;
            background: #FFF9F6;
            color: #5A4A42;
            font-weight: 600;
            padding: 12px 15px;
        }
        .table tbody tr {
            background: #FFF9F6;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(251, 176, 166, 0.1);
            transition: all 0.3s;
        }
        .table tbody tr:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(251, 176, 166, 0.2);
        }
        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            border-top: none;
        }
        .form-control {
            border: 1px solid #F0E6E0;
            border-radius: 8px;
            padding: 8px 12px;
            background: white;
            font-size: 0.95rem;
            transition: all 0.3s;
            max-width: 80px;
        }
        .form-control:focus {
            border-color: #FFB6C1;
            box-shadow: 0 0 0 3px rgba(255, 182, 193, 0.2);
        }
        .btn-outline-primary {
            color: #FF7E8A;
            border-color: #FF7E8A;
        }
        .btn-outline-primary:hover {
            background-color: #FF7E8A;
            border-color: #FF7E8A;
            color: white;
        }
        .btn-danger {
            background-color: #FF7E8A;
            border-color: #FF7E8A;
        }
        .btn-danger:hover {
            background-color: #e66a76;
            border-color: #e66a76;
        }
        .btn-success {
            background: linear-gradient(135deg, #FF9A9E 0%, #FAD0C4 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(255, 154, 158, 0.3);
        }
        .btn-success:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 154, 158, 0.4);
        }
        .cart-total {
            background: #FFF9F6;
            padding: 20px;
            border-radius: 12px;
            margin-top: 30px;
        }
        .cart-total h4 {
            color: #5A4A42;
            font-weight: 600;
        }
        .alert-info {
            background-color: #FFD6DD;
            border-color: #FFB6C1;
            color: #5A4A42;
        }
        .empty-cart {
            text-align: center;
            padding: 50px 0;
        }
        .empty-cart p {
            font-size: 1.2rem;
            color: #5A4A42;
            margin-bottom: 20px;
        }
        .underline {
            color: #FF7E8A;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        .underline:hover {
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .cart-main-title {
                font-size: 2rem;
            }
            .table thead {
                display: none;
            }
            .table, .table tbody, .table tr, .table td {
                display: block;
                width: 100%;
            }
            .table tr {
                margin-bottom: 20px;
                position: relative;
            }
            .table td {
                padding-left: 50%;
                text-align: right;
                position: relative;
                border-bottom: 1px solid #F0E6E0;
            }
            .table td::before {
                content: attr(data-label);
                position: absolute;
                left: 15px;
                width: calc(50% - 15px);
                padding-right: 10px;
                text-align: left;
                font-weight: 600;
                color: #5A4A42;
            }
            .table td:last-child {
                border-bottom: none;
            }
            .cart-item-img {
                width: 60px;
                height: 60px;
            }
            .form-control {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="bakery-cart-wrapper">
        <div class="bakery-cart-card">
            <div class="cart-header">
                <h1 class="cart-main-title">My Shopping Cart</h1>
                <p class="cart-subtitle">Review your items before checkout</p>
            </div>

            <?php
            if (isset($_SESSION['message'])) {
                echo '<div class="alert alert-info mb-4">' . htmlspecialchars($_SESSION['message']) . '</div>';
                unset($_SESSION['message']);
            }
            ?>

            <?php if ($result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table">
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
                                    <td data-label="Product"><?php echo htmlspecialchars($row['productName']); ?></td>
                                    <td data-label="Image">
                                        <img src="<?php echo htmlspecialchars($row['productImg'] ?: 'img/placeholder.jpg'); ?>" 
                                             alt="<?php echo htmlspecialchars($row['productName']); ?>" 
                                             class="cart-item-img">
                                    </td>
                                    <td data-label="Unit Price">R<?php echo number_format($row['unitPrice'], 2); ?></td>
                                    <td data-label="Quantity">
                                        <form action="php/update_cart.php" method="POST" class="d-flex align-items-center">
                                            <input type="hidden" name="cartId" value="<?php echo $row['cartId']; ?>">
                                            <input type="hidden" name="productId" value="<?php echo $row['productId']; ?>">
                                            <input type="hidden" name="unitPrice" value="<?php echo $row['unitPrice']; ?>">
                                            <input type="number" name="quantity" value="<?php echo htmlspecialchars($row['quantity']); ?>" 
                                                   min="1" class="form-control me-2">
                                            <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                                        </form>
                                    </td>
                                    <td data-label="Subtotal">R<?php echo number_format($row['cartPrice'], 2); ?></td>
                                    <td data-label="Actions">
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
                </div>

                <div class="cart-total text-end">
                    <h4>Total Cart Value: R<?php echo number_format($totalCartValue, 2); ?></h4>
                    <a href="checkout.php" class="btn btn-success mt-3">
                        <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                    </a>
                </div>

            <?php else: ?>
                <div class="empty-cart">
                    <p>Your cart is empty. <a class="underline" href="products.php">Start shopping!</a></p>
                    <p>Or <a class="underline" href="index.php">return to the homepage</a>.</p>
                    
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
include 'includes/footer.php';
?>