<?php
// session_start();
include 'Header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fabrica - Store</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js    "></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</head>
<body>
    <div class="container">
        <h1>Shop</h1>
        <div class="row">
            <!-- Sidebar for Filters -->
            <div class="col-md-3">
                <div class="panel panel-default">
                    <div class="panel-heading">Categories</div>
                    <div class="panel-body" id="get_category_home"></div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">Search</div>
                    <div class="panel-body">
                        <input type="text" class="form-control" id="search" placeholder="Search products...">
                        <button class="btn btn-primary mt-2" id="search_btn">Search</button>
                    </div>
                </div>
            </div>
            <!-- Products Grid -->
            <div class="col-md-9">
                <div id="gethomeProduct"></div>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>

    <script>
        $(document).ready(function() {
            // Load categories
            $.ajax({
                url: "Homeaction.php",
                type: "POST",
                data: { categoryhome: 1 },
                success: function(response) {
                    if (response.status === 'success') {
                        $("#get_category_home").html(response.data.html);
                    } else {
                        $("#get_category_home").html('<p>' + response.message + '</p>');
                    }
                },
                error: function() {
                    $("#get_category_home").html('<p>Failed to load categories.</p>');
                }
            });

            // Load products with pagination and filtering
            function loadProducts(page, category_id = '', search = '') {
                $.ajax({
                    url: "Homeaction.php",
                    type: "POST",
                    data: { 
                        gethomeProduct: 1, 
                        pageNumber: page, 
                        category_id: category_id, 
                        search: search 
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            $("#gethomeProduct").html(response.data.html);
                            // Handle pagination clicks
                            $(".page-link").on("click", function(e) {
                                e.preventDefault();
                                var newPage = $(this).data("page");
                                loadProducts(newPage, category_id, search);
                            });
                        } else {
                            $("#gethomeProduct").html('<p>' + response.message + '</p>');
                        }
                    },
                    error: function() {
                        $("#gethomeProduct").html('<p>Failed to load products.</p>');
                    }
                });
            }
            loadProducts(1); // Initial load

            // Category filter click
            $("#get_category_home").on("click", "a", function(e) {
                e.preventDefault();
                var category_id = $(this).attr("href").replace("#collapse", "");
                loadProducts(1, category_id);
            });

            // Search button click
            $("#search_btn").on("click", function() {
                var search = $("#search").val();
                loadProducts(1, '', search);
            });

            // Add to cart
            $("#gethomeProduct").on("click", ".add-to-cart", function() {
                var product_id = $(this).data("product-id");
                $.ajax({
                    url: "Action.php",
                    type: "POST",
                    data: { addToCart: 1, proId: product_id },
                    success: function(response) {
                        alert(response === "success" ? "Added to cart!" : "Failed to add to cart: " + response);
                    },
                    error: function() {
                        alert("Failed to add to cart.");
                    }
                });
            });
        });
    </script>
</body>
</html>