<?php
include 'includes/header.php';
include 'includes/db_connect.php';

// Fetch all products from the database
$sql = "SELECT * FROM PRODUCTS ORDER BY productName";
$result = $conn->query($sql);
?>

<h2 class="mb-4 text-center">All Our Delicious Desserts</h2>

<div class="row">
    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($row['productImg'] ?: 'img/placeholder.jpg'); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['productName']);?>" width="150" height="400">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['productName']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars(substr($row['description'], 0, 70)) . (strlen($row['description']) > 70 ? '...' : ''); ?></p>
                        <p class="price">R<?php echo number_format($row['price'], 2); ?></p>
                        <div class="btn-group mt-auto" role="group">
                            <a href="product_detail.php?id=<?php echo $row['productId']; ?>" class="btn btn-outline-secondary">View Details</a>
                            <form action="php/add_to_cart.php" method="POST" class="d-inline">
                                <input type="hidden" name="productId" value="<?php echo $row['productId']; ?>">
                                <input type="hidden" name="price" value="<?php echo $row['price']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-primary">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        echo "<p class='text-center'>No products found.</p>";
    }
    $conn->close();
    ?>
</div>

<?php include 'includes/footer.php'; ?>
