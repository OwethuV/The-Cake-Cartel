<?php
session_start();

echo "<h1>Payment Cancelled</h1>";
echo "<p>You cancelled the payment. Your order has not been processed.</p>";
echo "<a href='/cart.php'>Return to cart</a>";
