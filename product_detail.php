<?php
include 'includes/header.php';
include 'includes/db_connect.php';

$product = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $productId = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM PRODUCTS WHERE productId = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    }
    $stmt->close();
}
?>

<?php if ($product): ?>
    <div class="row">
        <div class="col-md-6">
            <img src="<?php echo htmlspecialchars($product['productImg']); ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($product['productName']); ?>" width="1000" length="1000">
        </div>
        <div class="col-md-6">
            <h1 class="mb-3"><?php echo htmlspecialchars($product['productName']); ?></h1>
            <p class="lead price">R<?php echo number_format($product['price'], 2); ?></p>
            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            <?php if (!empty($product['flavor'])): ?>
                <p><strong>Flavor:</strong> <?php echo htmlspecialchars($product['flavor']); ?></p>
            <?php endif; ?>

            <form action="php/add_to_cart.php" method="POST" class="mt-4">
                <input type="hidden" name="productId" value="<?php echo $product['productId']; ?>">
                <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                <div class="mb-3 d-flex align-items-center">
                    <label for="quantity" class="form-label me-3">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" class="form-control w-25">
                </div>
                <button type="submit" class="btn btn-primary btn-lg">Add to Cart</button>
            </form>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-warning text-center" role="alert">
        Product not found.
    </div>
<?php endif; ?>

<?php
$conn->close();
include 'includes/footer.php';
?>
