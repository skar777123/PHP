<?php
// session_start();
include 'Header.php';
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION["uid"])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION["uid"];

// Fetch user details
$sql = "SELECT name, email, phone, address FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_array($result);

if (!$user) {
    echo "<div class='container'><p>Error: User not found.</p></div>";
    exit();
}

// Handle form submission for profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($con, trim($_POST["name"]));
    $phone = !empty($_POST["phone"]) ? mysqli_real_escape_string($con, trim($_POST["phone"])) : NULL;
    $address = !empty($_POST["address"]) ? mysqli_real_escape_string($con, trim($_POST["address"])) : NULL;

    if (empty($name)) {
        $error = "Name is required.";
    } else {
        $sql = "UPDATE users SET name = ?, phone = ?, address = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "sssi", $name, $phone, $address, $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION["name"] = $name; // Update session name
            $success = "Profile updated successfully.";
            // Refresh user data
            $sql = "SELECT name, email, phone, address FROM users WHERE user_id = ?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_array($result);
        } else {
            $error = "Failed to update profile: " . mysqli_error($con);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fabrica - My Profile</title>
    <script src="js/jquery2.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</head>
<body>
    <div class="container">
        <h1>My Profile</h1>
        <?php
        if (isset($success)) {
            echo "<div class='alert alert-success'>$success</div>";
        }
        if (isset($error)) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
        ?>
        <form method="post" action="myprofile.php">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <textarea class="form-control" id="address" name="address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
        <div class="mt-3">
            <a href="cart.php" class="btn btn-info">View Cart</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</body>
</html>