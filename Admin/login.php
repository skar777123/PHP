<?php
session_start();
include "../db.php";
// include "sidenav.php";
// include "topheader.php";
// include "activitity.php";
?>
<div class="content">
    <div class="container-fluid">
        <div class="panel-body">
            <?php
            if (isset($_POST['success'])) {
                echo "<div class='col-md-12 col-xs-12' id='product_msg'>
                    <div class='alert alert-success'>
                        <a href='#' class='close' data-dismiss='alert' aria-label='close'>×</a>
                        <b>Product Added!</b>
                    </div>
                </div>";
            }
            ?>
        </div>
        <div class="col-md-14">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title">Users List</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive ps">
                        <table class="table table-hover tablesorter">
                            <thead class="text-primary">
                                <tr>
                                    <th>ID</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>Password</th>
                                    <th>Contact</th>
                                    <th>Address</th>
                                    <th>City</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = mysqli_query($con, "SELECT * FROM users") or die("Query failed");
                                while ($row = mysqli_fetch_array($result)) {
                                    echo "<tr>
                                        <td>" . htmlspecialchars($row['user_id']) . "</td>
                                        <td>" . htmlspecialchars($row['name']) . "</td>
                                        <td>" . htmlspecialchars($row['name']) . "</td>
                                        <td>" . htmlspecialchars($row['email']) . "</td>
                                        <td>" . htmlspecialchars($row['phone']) . "</td>
                                        <td>" . htmlspecialchars($row['address']) . "</td>
                                        <td>" . htmlspecialchars($row['address']) . "</td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">Categories List</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive ps">
                            <table class="table table-hover tablesorter">
                                <thead class="text-primary">
                                    <tr>
                                        <th>ID</th>
                                        <th>Categories</th>
                                        <th>Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result = mysqli_query($con, "SELECT * FROM categories") or die("Query failed");
                                    $i = 1;
                                    while ($row = mysqli_fetch_array($result)) {
                                        $sql = "SELECT COUNT(*) AS count_items FROM products WHERE product_cat = ?";
                                        $stmt = mysqli_prepare($con, $sql);
                                        mysqli_stmt_bind_param($stmt, "i", $row['cat_id']);
                                        mysqli_stmt_execute($stmt);
                                        $row_count = mysqli_fetch_array(mysqli_stmt_get_result($stmt));
                                        $count = $row_count["count_items"];
                                        echo "<tr>
                                            <td>" . htmlspecialchars($row['cat_id']) . "</td>
                                            <td>" . htmlspecialchars($row['cat_title']) . "</td>
                                            <td>$count</td>
                                        </tr>";
                                        $i++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">Brands List</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive ps">
                            <table class="table table-hover tablesorter">
                                <thead class="text-primary">
                                    <tr>
                                        <th>ID</th>
                                        <th>Brands</th>
                                        <th>Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result = mysqli_query($con, "SELECT * FROM brands") or die("Query failed");
                                    $i = 1;
                                    while ($row = mysqli_fetch_array($result)) {
                                        $sql = "SELECT COUNT(*) AS count_items FROM products WHERE product_brand = ?";
                                        $stmt = mysqli_prepare($con, $sql);
                                        mysqli_stmt_bind_param($stmt, "i", $row['brand_id']);
                                        mysqli_stmt_execute($stmt);
                                        $row_count = mysqli_fetch_array(mysqli_stmt_get_result($stmt));
                                        $count = $row_count["count_items"];
                                        echo "<tr>
                                            <td>" . htmlspecialchars($row['brand_id']) . "</td>
                                            <td>" . htmlspecialchars($row['brand_title']) . "</td>
                                            <td>$count</td>
                                        </tr>";
                                        $i++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title">Subscribers</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive ps">
                        <table class="table table-hover tablesorter">
                            <thead class="text-primary">
                                <tr>
                                    <th>ID</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = mysqli_query($con, "SELECT * FROM email_info") or die("Query failed");
                                while ($row = mysqli_fetch_array($result)) {
                                    echo "<tr>
                                        <td>" . htmlspecialchars($row['email_id']) . "</td>
                                        <td>" . htmlspecialchars($row['email']) . "</td>
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
<?php
include "../footer.php";
?>