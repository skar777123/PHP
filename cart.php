<?php
// session_start();
include 'Header.php';
include 'db.php';

// Get user ID or IP address
$user_id = isset($_SESSION["uid"]) ? $_SESSION["uid"] : -1;
$ip_add = $_SERVER["REMOTE_ADDR"];

// Fetch cart items
$sql = "SELECT p.product_id, p.product_title, p.product_price, p.product_image, c.id AS cart_id, c.quantity 
        FROM products p 
        JOIN carts c ON p.product_id = c.product_id 
        WHERE c.user_id = ? OR (c.user_id = -1 AND c.ip_add = ?)";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "is", $user_id, $ip_add);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$cart_items = [];
$total = 0;
while ($row = mysqli_fetch_array($result)) {
    $cart_items[] = $row;
    $total += $row['product_price'] * $row['quantity'];
}

$count = 0;
$cart_id = null;
if($count > 0){
    $rsql = "DELETE c.product_id
             FROM carts c
             WHERE c.product_id = $cart_id";
    $rstmt = mysqli_prepare($con, $rsql);
    mysqli_stmt_bind_param($rstmt, "i", $cart_id);
    mysqli_stmt_execute($rstmt);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fabrica - Shopping Cart</title>
    <script src="js/jquery2.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</head>
<body>
    <div class="container">
        <h1>Shopping Cart</h1>
        <?php if (empty($cart_items)) { ?>
            <p>Your cart is empty. <a href="store.php">Browse products</a>.</p>
        <?php } else { ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Image</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_title']); ?></td>
                            <td><img src="product_images/<?php echo htmlspecialchars($item['product_image']); ?>" alt="Product Image" style="width: 50px;"></td>
                            <td>₹<?php echo number_format($item['product_price'], 2); ?></td>
                            <td>
                                <input type="number" class="form-control qty-input" data-cart-id="<?php echo $item['cart_id']; ?>" value="<?php echo $item['quantity']; ?>" min="1" style="width: 80px;">
                            </td>
                            <td>₹<?php echo number_format($item['product_price'] * $item['quantity'], 2); ?></td>
                            <td>
                                <button onclick="()=>{
                                $cart_id = <?php echo $item['cart_id']; ?>;
                                $count+=1;
                                }" class="btn btn-danger remove-cart" data-cart-id="<?php echo $item['cart_id']; ?>">Remove</button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div class="text-right">
                <h3>Total: ₹<?php echo number_format($total, 2); ?></h3>
                <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
            </div>
        <?php } ?>
    </div>

    <script>
        $(document).ready(function() {
            // Update quantity
            $('.qty-input').on('change', function() {
                var cart_id = $(this).data('cart-id');
                var quantity = $(this).val();
                
                $.ajax({
                    url: 'Action.php',
                    type: 'POST',
                    data: { updateCart: 1, cart_id: cart_id, quantity: quantity },
                    success: function(response) {
                        if (response == 'success') {
                            location.reload(); // Refresh to update total
                        } else {
                            alert('Failed to update quantity');
                        }
                    }
                });
            });

            // Remove item
            $('.remove-cart').on('click', function() {
                var cart_id = $(this).data('cart-id');
                
                $.ajax({
                    url: 'Action.php',
                    type: 'POST',
                    data: { deleteCart: 1, cart_id: cart_id },
                    success: function(response) {
                        if (response == 'success') {
                            location.reload(); // Refresh to update cart
                        } else {
                            alert('Failed to remove item');
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>