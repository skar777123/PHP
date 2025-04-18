<?php
// session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $name = mysqli_real_escape_string($con, trim($_POST["name"]));
    $email = mysqli_real_escape_string($con, trim($_POST["email"]));
    $password = trim($_POST["password"]);
    $phone = !empty($_POST["phone"]) ? mysqli_real_escape_string($con, trim($_POST["phone"])) : NULL;
    $address = !empty($_POST["address"]) ? mysqli_real_escape_string($con, trim($_POST["address"])) : NULL;

    // Basic validation
    if (empty($name) || empty($email) || empty($password)) {
        echo "<span style='color:red;'>Please fill all required fields!</span>";
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<span style='color:red;'>Invalid email format!</span>";
        exit();
    }

    // Check for duplicate email
    $sql = "SELECT user_id FROM users WHERE email = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        echo "<span style='color:red;'>Email already exists!</span>";
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $sql = "INSERT INTO users (name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssss", $name, $email, $hashed_password, $phone, $address);
    
    if (mysqli_stmt_execute($stmt)) {
        // Optionally log the user in
        $sql = "SELECT user_id, name FROM users WHERE email = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_array($result);
        
        $_SESSION["uid"] = $row["user_id"];
        $_SESSION["name"] = $row["name"];
        
        echo "register_success";
        echo "<script>location.href='index.php';</script>";
        exit();
    } else {
        echo "<span style='color:red;'>Registration failed: " . mysqli_error($con) . "</span>";
        exit();
    }
} else {
    echo "<span style='color:red;'>Invalid request method!</span>";
    exit();
}
?>