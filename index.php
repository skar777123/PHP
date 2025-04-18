<?php include 'Header.php'; ?>
<div class="container">
    <h1>Welcome to Fabrica</h1>
    <div id="get_category_home"></div>
    <div id="gethomeProduct"></div>
</div>
<?php include 'footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js    "></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
<script>
    $(document).ready(function() {
        // Load categories
        $.ajax({
            url: "./Homeaction.php",
            type: "POST",
            data: { categoryhome: 1 },
            success: function(response) {
                if (response.status === 'success') {
                    $("#get_category_home").html(response.data.html);
                } else {
                    $("#get_category_home").html('<p>' + response.message + '</p>');
                }
            }
        });

        // Load products with pagination
        function loadProducts(page) {
            $.ajax({
                url: "Homeaction.php",
                type: "POST",
                data: { gethomeProduct: 1, pageNumber: page },
                success: function(response) {
                    if (response.status === 'success') {
                        $("#gethomeProduct").html(response.data.html);
                        // Handle pagination clicks
                        $(".page-link").on("click", function(e) {
                            e.preventDefault();
                            var newPage = $(this).data("page");
                            loadProducts(newPage);
                        });
                    } else {
                        $("#gethomeProduct").html('<p>' + response.message + '</p>');
                    }
                }
            });
        }
        loadProducts(1); // Initial load

        $("#gethomeProduct").on("click", ".add-to-cart", function() {
            var product_id = $(this).data("product-id");
            $.ajax({
                url: "Action.php",
                type: "POST",
                data: { addToCart: 1, proId: product_id },
                success: function(response) {
                    alert(response === "success" ? "Added to cart!" : "Failed to add to cart.");
                }
            });
        });
    });
</script>
</body>
</html>