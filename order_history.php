<?php
include 'includes/header.php';
include 'includes/db_connect.php';

if (!isset($_SESSION['userId'])) {
    $_SESSION['message'] = "Please login to view your order history.";
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['userId'];


$stmt = $conn->prepare("SELECT o.orderId, o.deliveryPrice, o.totalPrice, o.status, o.createdAt,
                               oi.productId, p.productName, p.productImg, oi.quantity, oi.price AS itemTotalPrice
                        FROM ORDERS o
                        JOIN ORDER_ITEMS oi ON o.orderId = oi.orderId
                        JOIN PRODUCTS p ON oi.productId = p.productId
                        WHERE o.userId = ?
                        ORDER BY o.createdAt DESC");

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();


$orders = [];
while ($row = $result->fetch_assoc()) {
    $orderId = $row['orderId'];
    if (!isset($orders[$orderId])) {
        $orders[$orderId] = [
            'orderId' => $row['orderId'],
            'deliveryPrice' => $row['deliveryPrice'],
            'totalPrice' => $row['totalPrice'],
            'status' => $row['status'],
            'createdAt' => $row['createdAt'],
            'items' => []
        ];
    }
    $orders[$orderId]['items'][] = [
        'productId' => $row['productId'],
        'productName' => $row['productName'],
        'productImg' => $row['productImg'],
        'quantity' => $row['quantity'],
        'itemTotalPrice' => $row['itemTotalPrice']
    ];
}
?>

<h2 class="mb-4">Your Order History</h2>

<?php
if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-info">' . htmlspecialchars($_SESSION['message']) . '</div>';
    unset($_SESSION['message']);
}
?>

<?php if (!empty($orders)): ?>
    <?php foreach ($orders as $order): ?>
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Order #<?php echo htmlspecialchars($order['orderId']); ?>
                <span class="float-end">Status: <?php echo htmlspecialchars($order['status']); ?></span>
            </div>
            <div class="card-body">
                <p><strong>Order Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($order['createdAt'])); ?></p>
                <p><strong>Delivery Price:</strong> R<?php echo number_format($order['deliveryPrice'], 2); ?></p>
                <p><strong>Total Paid:</strong> R<?php echo number_format($order['totalPrice'], 2); ?></p>

                <h5>Items:</h5>
                <ul class="list-group mb-3">
                    <?php foreach ($order['items'] as $item): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <img src="<?php echo htmlspecialchars($item['productImg'] ?: 'img/placeholder.jpg'); ?>"
                                    alt="<?php echo htmlspecialchars($item['productName']); ?>"
                                    style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                <?php echo htmlspecialchars($item['productName']); ?>
                                (x<?php echo htmlspecialchars($item['quantity']); ?>)
                            </div>
                            <span>R<?php echo number_format($item['itemTotalPrice'], 2); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>You have no past orders.</p>
<?php endif; ?>

<?php
$stmt->close();
$conn->close();
include 'includes/footer.php';
?>