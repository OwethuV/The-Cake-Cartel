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

<style>
    .product-detail-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding: 40px 20px;
        background-color: #fff9f6;
        font-family: 'Poppins', 'Quicksand', sans-serif;
    }

    .product-detail-container {
        width: 100%;
        max-width: 1200px;
    }

    .product-detail-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(251, 176, 166, 0.15);
        overflow: hidden;
        padding: 40px;
    }

    .product-header {
        margin-bottom: 20px;
    }

    .product-title {
        font-family: 'Pacifico', cursive;
        color: #ff7e8a;
        font-size: 2.5rem;
        margin-bottom: 10px;
    }

    .product-price {
        font-size: 1.8rem;
        color: #5a4a42;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .product-description {
        color: #8a7369;
        line-height: 1.6;
        margin-bottom: 20px;
        font-size: 1rem;
    }

    .product-flavor {
        background: #fff9f6;
        padding: 10px 15px;
        border-radius: 8px;
        display: inline-block;
        margin-bottom: 20px;
    }

    .product-flavor strong {
        color: #5a4a42;
        font-weight: 600;
    }

    .product-flavor span {
        color: #ff7e8a;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-weight: 500;
        color: #5a4a42;
        font-size: 0.95rem;
    }

    .form-control {
        border: 1px solid #f0e6e0;
        border-radius: 8px;
        padding: 10px 12px;
        width: 100%;
        transition: all 0.3s;
        background: #fff9f6;
        font-size: 0.95rem;
    }

    .form-control:focus {
        border-color: #ffb6c1;
        box-shadow: 0 0 0 3px rgba(255, 182, 193, 0.2);
        background: white;
    }

    .btn-add-to-cart {
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
        font-size: 0.9rem;
        cursor: pointer;
        margin-top: 15px;
    }

    .btn-add-to-cart:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(255, 154, 158, 0.4);
    }

    .product-image {
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        width: 100%;
        height: auto;
        object-fit: cover;
    }

    .alert-warning {
        background-color: #fff3cd;
        color: #856404;
        border-color: #ffeeba;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        max-width: 600px;
        margin: 40px auto;
        font-family: 'Poppins', sans-serif;
    }

    @media (max-width: 768px) {
        .product-detail-card {
            padding: 25px;
        }
        
        .product-title {
            font-size: 2rem;
        }
        
        .product-price {
            font-size: 1.5rem;
        }
    }
</style>

<div class="product-detail-wrapper">
    <div class="product-detail-container">
        <?php if ($product): ?>
            <div class="product-detail-card">
                <div class="row">
                    <div class="col-md-6">
                        <img src="<?php echo htmlspecialchars($product['productImg'] ?: 'img/placeholder.jpg'); ?>" 
                             class="product-image" 
                             alt="<?php echo htmlspecialchars($product['productName']); ?>">
                    </div>
                    <div class="col-md-6">
                        <div class="product-header">
                            <h1 class="product-title"><?php echo htmlspecialchars($product['productName']); ?></h1>
                            <p class="product-price">R<?php echo number_format($product['price'], 2); ?></p>
                        </div>
                        
                        <p class="product-description"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                        
                        <?php if (!empty($product['flavor'])): ?>
                            <div class="product-flavor">
                                <strong>Flavor:</strong> <span><?php echo htmlspecialchars($product['flavor']); ?></span>
                            </div>
                        <?php endif; ?>

                        <form action="php/add_to_cart.php" method="POST">
                            <input type="hidden" name="productId" value="<?php echo $product['productId']; ?>">
                            <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                            <div class="form-group">
                                <label for="quantity">Quantity:</label>
                                <input type="number" id="quantity" name="quantity" value="1" min="1" class="form-control w-25" style="width: 80px;">
                            </div>
                            <button type="submit" class="btn btn-add-to-cart">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center" role="alert">
                <i class="fas fa-exclamation-circle"></i> Product not found.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$conn->close();
include 'includes/footer.php';
?>
