<?php
include "db.php";

// Set headers for JSON response
header('Content-Type: application/json');

// Function to send JSON response
function sendResponse($status, $data = [], $message = '') {
    echo json_encode([
        'status' => $status,
        'data' => $data,
        'message' => $message
    ]);
    exit();
}

// Get Categories
if (isset($_POST["categoryhome"])) {
    $sql = "SELECT c.cat_id, c.cat_title, COUNT(p.product_id) AS count_items 
            FROM categories c 
            LEFT JOIN products p ON p.product_cat = c.cat_id 
            GROUP BY c.cat_id";
    
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
    
    if (empty($categories)) {
        sendResponse('error', [], 'No categories found.');
    }
    
    $html = '<div class="panel-group" id="accordion">';
    foreach ($categories as $cat) {
        $html .= '<div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse' . $cat['cat_id'] . '">' 
                            . htmlspecialchars($cat['cat_title']) . ' (' . $cat['count_items'] . ')</a>
                        </h4>
                    </div>
                    <div id="collapse' . $cat['cat_id'] . '" class="panel-collapse collapse">
                        <div class="panel-body">' . htmlspecialchars($cat['cat_title']) . '</div>
                    </div>
                  </div>';
    }
    $html .= '</div>';
    
    sendResponse('success', ['html' => $html]);
}

// Get Products (with pagination and filtering)
if (isset($_POST["gethomeProduct"])) {
    $page = isset($_POST["pageNumber"]) ? (int)$_POST["pageNumber"] : 1;
    $limit = 12; // Increased limit for better display
    $offset = ($page - 1) * $limit;
    
    $where = [];
    $params = [];
    $types = '';
    
    // Filter by category
    if (isset($_POST["category_id"]) && !empty($_POST["category_id"])) {
        $where[] = "p.product_cat = ?";
        $params[] = (int)$_POST["category_id"];
        $types .= 'i';
    }
    
    // Filter by brand
    if (isset($_POST["brand_id"]) && !empty($_POST["brand_id"])) {
        $where[] = "p.product_brand = ?";
        $params[] = (int)$_POST["brand_id"];
        $types .= 'i';
    }
    
    // Search by keyword
    if (isset($_POST["search"]) && !empty($_POST["search"])) {
        $where[] = "p.product_keywords LIKE ?";
        $params[] = '%' . mysqli_real_escape_string($con, $_POST["search"]) . '%';
        $types .= 's';
    }
    
    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    // Get total products for pagination
    $countSql = "SELECT COUNT(p.product_id) AS total 
                 FROM products p 
                 JOIN categories c ON p.product_cat = c.cat_id 
                 $whereClause";
    $stmt = mysqli_prepare($con, $countSql);
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $countResult = mysqli_stmt_get_result($stmt);
    $totalProducts = mysqli_fetch_assoc($countResult)['total'];
    $totalPages = ceil($totalProducts / $limit);
    
    // Get products
    $sql = "SELECT p.*, c.cat_title 
            FROM products p 
            JOIN categories c ON p.product_cat = c.cat_id 
            $whereClause 
            LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($con, $sql);
    
    $types .= 'ii';
    $params[] = $limit;
    $params[] = $offset;
    
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    
    if (empty($products)) {
        sendResponse('error', [], 'No products found.');
    }
    
    $html = '<div class="row">';
    foreach ($products as $product) {
        $html .= '<div class="col-md-4 col-sm-6">
                    <div class="thumbnail">
                        <img src="product_images/' . htmlspecialchars($product['product_image']) . '" alt="' . htmlspecialchars($product['product_title']) . '">
                        <div class="caption">
                            <h4>' . htmlspecialchars($product['product_title']) . '</h4>
                            <p>Category: ' . htmlspecialchars($product['cat_title']) . '</p>
                            <p>Price: ₹' . number_format($product['product_price'], 2) . '</p>
                            <button class="btn btn-primary add-to-cart" data-product-id="' . $product['product_id'] . '">Add to Cart</button>
                        </div>
                    </div>
                  </div>';
    }
    $html .= '</div>';
    
    // Pagination controls
    $html .= '<nav aria-label="Page navigation">
                <ul class="pagination">';
    if ($page > 1) {
        $html .= '<li><a href="#" class="page-link" data-page="' . ($page - 1) . '">Previous</a></li>';
    }
    for ($i = 1; $i <= $totalPages; $i++) {
        $html .= '<li' . ($i == $page ? ' class="active"' : '') . '><a href="#" class="page-link" data-page="' . $i . '">' . $i . '</a></li>';
    }
    if ($page < $totalPages) {
        $html .= '<li><a href="#" class="page-link" data-page="' . ($page + 1) . '">Next</a></li>';
    }
    $html .= '</ul></nav>';
    
    sendResponse('success', ['html' => $html, 'totalPages' => $totalPages, 'currentPage' => $page]);
}

// Get Featured Products (limited to 6)
if (isset($_POST["getProducthome"])) {
    $sql = "SELECT p.*, c.cat_title 
            FROM products p 
            JOIN categories c ON p.product_cat = c.cat_id 
            ORDER BY p.created_at DESC 
            LIMIT 6";
    
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    
    if (empty($products)) {
        sendResponse('error', [], 'No featured products found.');
    }
    
    $html = '<div class="row">';
    foreach ($products as $product) {
        $html .= '<div class="col-md-4 col-sm-6">
                    <div class="thumbnail">
                        <img src="product_images/' . htmlspecialchars($product['product_image']) . '" alt="' . htmlspecialchars($product['product_title']) . '">
                        <div class="caption">
                            <h4>' . htmlspecialchars($product['product_title']) . '</h4>
                            <p>Category: ' . htmlspecialchars($product['cat_title']) . '</p>
                            <p>Price: ₹' . number_format($product['product_price'], 2) . '</p>
                            <button class="btn btn-primary add-to-cart" data-product-id="' . $product['product_id'] . '">Add to Cart</button>
                        </div>
                    </div>
                  </div>';
    }
    $html .= '</div>';
    
    sendResponse('success', ['html' => $html]);
}
?>