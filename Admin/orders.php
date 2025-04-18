<?php
session_start();
include "../db.php";
error_reporting(0);

if (isset($_GET['action']) && $_GET['action'] == "delete") {
    $order_id = (int)$_GET['order_id'];
    $sql = "DELETE FROM orders WHERE order_id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt) or die("Delete query failed");
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page1 = ($page == 1) ? 0 : ($page * 10) - 10;

?>
<div class="content">
    <div class="container-fluid">
        <div class="col-md-14">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title">Orders / Page <?php echo $page; ?></h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive ps">
                        <table class="table table-hover tablesorter">
                            <thead class="text-primary">
                                <tr>
                                    <th>Customer Name</th>
                                    <th>Products</th>
                                    <th>Contact | Email</th>
                                    <th>Address</th>
                                    <th>Details</th>
                                    <th>Shipping</th>
                                    <th>Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT o.order_id, p.product_title, u.name, u.phone, u.email, u.address, o.qty, o.created_at
                                        FROM orders o
                                        JOIN products p ON o.product_id = p.product_id
                                        JOIN users u ON o.user_id = u.user_id
                                        LIMIT ?, 10";
                                $stmt = mysqli_prepare($con, $sql);
                                mysqli_stmt_bind_param($stmt, "i", $page1);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                
                                while ($row = mysqli_fetch_array($result)) {
                                    echo "<tr>
                                        <td>" . htmlspecialchars($row['name']) . "</td>
                                        <td>" . htmlspecialchars($row['product_title']) . "</td>
                                        <td>" . htmlspecialchars($row['email']) . "<br>" . htmlspecialchars($row['phone']) . "</td>
                                        <td>" . htmlspecialchars($row['address']) . "</td>
                                        <td>Order Details</td>
                                        <td>" . $row['qty'] . "</td>
                                        <td>" . htmlspecialchars($row['created_at']) . "</td>
                                        <td>
                                            <a class='btn btn-danger' href='orders.php?order_id=" . $row['order_id'] . "&action=delete'>Delete</a>
                                        </td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include "footer.php";
?>