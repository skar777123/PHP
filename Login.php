<?php
session_start();
include "db.php";

if (isset($_POST["email"]) && isset($_POST["password"])) {
    $email = mysqli_real_escape_string($con, $_POST["email"]);
    $password = $_POST["password"];
    
    $sql = "SELECT * FROM users WHERE email = ? AND password = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $email, $password);
    mysqli_stmt_execute($stmt);
    $run_query = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($run_query) == 1) {
        $row = mysqli_fetch_array($run_query);
        $_SESSION["uid"] = $row["user_id"];
        $_SESSION["name"] = $row["name"];
        
        if (isset($_COOKIE["product_list"])) {
            $product_list = json_decode(stripslashes($_COOKIE["product_list"]), true);
            foreach ($product_list as $p_id) {
                $verify_cart = "SELECT id FROM carts WHERE user_id = ? AND product_id = ?";
                $stmt = mysqli_prepare($con, $verify_cart);
                mysqli_stmt_bind_param($stmt, "ii", $_SESSION["uid"], $p_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                if (mysqli_num_rows($result) < 1) {
                    $update_cart = "UPDATE carts SET user_id = ? WHERE ip_add = ? AND user_id = -1";
                    $stmt = mysqli_prepare($con, $update_cart);
                    mysqli_stmt_bind_param($stmt, "is", $_SESSION["uid"], $ip_add);
                    mysqli_stmt_execute($stmt);
                } else {
                    $delete_existing = "DELETE FROM carts WHERE user_id = -1 AND ip_add = ? AND product_id = ?";
                    $stmt = mysqli_prepare($con, $delete_existing);
                    mysqli_stmt_bind_param($stmt, "si", $ip_add, $p_id);
                    mysqli_stmt_execute($stmt);
                }
            }
            setcookie("product_list", "", time() - 3600, "/");
            echo "cart_login";
            exit();
        }
        
        echo "login_success";
        echo "<script>location.href='index.php';</script>";
        exit();
    }
    
    $password_hashed = md5($password);
    $sql = "SELECT * FROM admin WHERE email = ? AND password_hash = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $email, $password_hashed);
    mysqli_stmt_execute($stmt);
    $run_query = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($run_query) == 1) {
        $row = mysqli_fetch_array($run_query);
        $_SESSION["uid"] = $row["admin_id"];
        $_SESSION["name"] = $row["name"];
        echo "login_success";
        echo "<script>location.href='admin/add_products.php';</script>";
        exit();
    }
    
    echo "<span style='color:red;'>Invalid email or password!</span>";
    exit();
}
?>