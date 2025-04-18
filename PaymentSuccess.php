<?php
session_start();
include "db.php";

if (!isset($_SESSION["uid"])) {
    header("location: index.php");
    exit();
}

if (isset($_GET["st"]) && $_GET["st"] == "Completed") {
    $trx_id = $_GET["tx"];
    $amt = $_GET["amt"];
    $cc = $_GET["cc"];
    $cm_user_id = $_GET["cm"];
    $c_amt = $_COOKIE["ta"];
    
    $sql = "SELECT product_id, quantity FROM carts WHERE user_id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $cm_user_id);
    mysqli_stmt_execute($stmt);
    $query = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($query) > 0) {
        $product_ids = [];
        $quantities = [];
        while ($row = mysqli_fetch_array($query)) {
            $product_ids[] = $row["product_id"];
            $quantities[] = $row["quantity"];
        }
        
        for ($i = 0; $i < count($product_ids); $i++) {
            $sql = "INSERT INTO orders (user_id, product_id, qty, trx_id, p_status) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($con, $sql);
            $status = "Completed";
            mysqli_stmt_bind_param($stmt, "iiiss", $cm_user_id, $product_ids[$i], $quantities[$i], $trx_id, $status);
            mysqli_stmt_execute($stmt);
        }
        
        $sql = "DELETE FROM carts WHERE user_id = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $cm_user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            ?>
            <!DOCTYPE html>
            <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Fabrica</title>
                    <link rel="stylesheet" href="css/bootstrap.min.css"/>
                    <script src="js/jquery2.js"></script>
                    <script src="js/bootstrap.min.js"></script>
                    <script src="main.js"></script>
                    <style>
                        table tr td {padding: 10px;}
                    </style>
                </head>
                <body>
                    <div class="navbar navbar-inverse navbar-fixed-top">
                        <div class="container-fluid">
                            <div class="navbar-header">
                                <a href="#" class="navbar-brand">Fabrica</a>
                            </div>
                            <ul class="nav navbar-nav">
                                <li><a href="index.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>
                                <li><a href="profile.php"><span class="glyphicon glyphicon-modal-window"></span> Products</a></li>
                            </ul>
                        </div>
                    </div>
                    <p><br/></p>
                    <p><br/></p>
                    <p><br/></p>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-2"></div>
                            <div class="col-md-8">
                                <div class="panel panel-default">
                                    <div class="panel-heading"></div>
                                    <div class="panel-body">
                                        <h1>Thank You</h1>
                                        <hr/>
                                        <p>Hello <b><?php echo htmlspecialchars($_SESSION["name"]); ?></b>, Your payment process is successfully completed. Transaction ID: <b><?php echo htmlspecialchars($trx_id); ?></b><br/>
                                        You can continue shopping.</p>
                                        <a href="index.php" class="btn btn-success btn-lg">Continue Shopping</a>
                                    </div>
                                    <div class="panel-footer"></div>
                                </div>
                            </div>
                            <div class="col-md-2"></div>
                        </div>
                    </div>
                </body>
            </html>
            <?php
        }
    } else {
        header("location: index.php");
        exit();
    }
}
?>