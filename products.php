<?php
session_start();
include 'includes/header.php';
include 'includes/db_connect.php';


$sql = "SELECT * FROM PRODUCTS ORDER BY productName";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Menu | The Cake Cartel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --pink: #ff7e8a;
            --light-pink: #ffd6dd;
            --peach: #fad0c4;
            --tan: #5a4a42;
            --light-tan: #8a7369;
            --bg: #fff9f6;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg);
            color: var(--tan);
        }

        .menu-header {
            text-align: center;
            padding: 60px 20px 40px;
            background-color: white;
            margin-bottom: 40px;
            box-shadow: 0 5px 15px rgba(251, 176, 166, 0.1);
        }

        .menu-title {
            font-family: 'Pacifico', cursive;
            color: var(--pink);
            font-size: 3rem;
            margin-bottom: 15px;
        }

        .menu-subtitle {
            color: var(--light-tan);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .product-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px 60px;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
        }

        .product-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(251, 176, 166, 0.15);
            transition: all 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(251, 176, 166, 0.25);
        }

        .product-image {
            height: 250px;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .product-card:hover .product-image img {
            transform: scale(1.05);
        }

        .product-body {
            padding: 25px;
        }

        .product-category {
            display: inline-block;
            background: var(--light-pink);
            color: var(--pink);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .product-name {
            font-size: 1.5rem;
            color: var(--tan);
            margin-bottom: 10px;
            font-weight: 600;
        }

        .product-description {
            color: var(--light-tan);
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .product-price {
            color: var(--pink);
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .product-actions {
            display: flex;
            gap: 10px;
        }

        .btn-details {
            background: white;
            border: 2px solid var(--pink);
            color: var(--pink);
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            flex: 1;
            text-align: center;
        }

        .btn-details:hover {
            background: var(--pink);
            color: white;
        }

        .btn-cart {
            background: linear-gradient(135deg, var(--pink) 0%, var(--peach) 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
            flex: 1;
            cursor: pointer;
        }

        .btn-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 126, 138, 0.4);
        }

        @media (max-width: 768px) {
            .menu-title {
                font-size: 2.5rem;
            }
            
            .product-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body>
    <div class="menu-header">
        <h1 class="menu-title">Our Sweet Menu</h1>
        <p class="menu-subtitle">Indulge in our handcrafted desserts made with love and the finest ingredients</p>
    </div>

    <div class="product-container">
        <div class="product-grid">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    
                    $description = htmlspecialchars($row['description']);
                    $truncated_description = (strlen($description) > 70) ? substr($description, 0, 70) . '...' : $description;
                    ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?php echo htmlspecialchars($row['productImg'] ?: 'img/placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($row['productName']); ?>">
                        </div>
                        <div class="product-body">
                            <h3 class="product-name"><?php echo htmlspecialchars($row['productName']); ?></h3>
                            <p class="product-description"><?php echo $truncated_description; ?></p>
                            <p class="product-price">R<?php echo number_format($row['price'], 2); ?></p>
                            <div class="product-actions">
                                <a href="product_detail.php?id=<?php echo $row['productId']; ?>" class="btn-details">Details</a>
                                <form action="php/add_to_cart.php" method="POST" style="display: contents;">
                                    <input type="hidden" name="productId" value="<?php echo $row['productId']; ?>">
                                    <input type="hidden" name="price" value="<?php echo $row['price']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn-cart">Add to Cart</button>
                                </form>
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
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
