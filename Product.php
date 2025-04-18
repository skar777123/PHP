<?php
include "header.php";
include "db.php";

$product_id = isset($_GET['p']) ? (int)$_GET['p'] : 0;
$sql = "SELECT * FROM products WHERE product_id = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $pro_title = htmlspecialchars($row['product_title']);
    $pro_price = $row['product_price'];
    $pro_image = htmlspecialchars($row['product_image']);
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $(".scroll").click(function(event) {
                event.preventDefault();
                $('html,body').animate({scrollTop: $(this.hash).offset().top}, 900);
            });
        });
    </script>
    <script>
        (function(global) {
            if (typeof global === "undefined") {
                throw new Error("window is undefined");
            }
            var _hash = "!";
            var noBackPlease = function() {
                global.location.href += "#";
                global.setTimeout(function() {
                    global.location.href += "!";
                }, 50);
            };
            global.onhashchange = function() {
                if (global.location.hash !== _hash) {
                    global.location.hash = _hash;
                }
            };
            global.onload = function() {
                noBackPlease();
                document.body.onkeydown = function(e) {
                    var elm = e.target.nodeName.toLowerCase();
                    if (e.which === 8 && (elm !== 'input' && elm !== 'textarea')) {
                        e.preventDefault();
                    }
                    e.stopPropagation();
                };
            };
        })(window);
    </script>
    <div class="section main main-raised">
        <div class="container">
            <div class="row">
                <div class="col-md-5 col-md-push-2">
                    <div id="product-main-img">
                        <div class="product-preview">
                            <img src="product_images/<?php echo $pro_image; ?>" alt="<?php echo $pro_title; ?>">
                        </div>
                        <div class="product-preview">
                            <img src="product_images/<?php echo $pro_image; ?>" alt="<?php echo $pro_title; ?>">
                        </div>
                        <div class="product-preview">
                            <img src="product_images/<?php echo $pro_image; ?>" alt="<?php echo $pro_title; ?>">
                        </div>
                        <div class="product-preview">
                            <img src="product_images/<?php echo $pro_image; ?>" alt="<?php echo $pro_title; ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-md-pull-5">
                    <div id="product-imgs">
                        <div class="product-preview">
                            <img src="product_images/<?php echo $pro_image; ?>" alt="<?php echo $pro_title; ?>">
                        </div>
                        <div class="product-preview">
                            <img src="product_images/<?php echo $pro_image; ?>" alt="<?php echo $pro_title; ?>">
                        </div>
                        <div class="product-preview">
                            <img src="product_images/<?php echo $pro_image; ?>" alt="<?php echo $pro_title; ?>">
                        </div>
                        <div class="product-preview">
                            <img src="product_images/<?php echo $pro_image; ?>" alt="<?php echo $pro_title; ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="product-details">
                        <h2 class="product-name"><?php echo $pro_title; ?></h2>
                        <div>
                            <div class="product-rating">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star-o"></i>
                            </div>
                            <a class="review-link" href="#review-form">10 Review(s) | Add your review</a>
                        </div>
                        <div>
                            <h3 class="product-price">$<?php echo $pro_price; ?> <del class="product-old-price">$990.00</del></h3>
                            <span class="product-available">In Stock</span>
                        </div>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                        <div class="product-options">
                            <label>
                                Size
                                <select class="input-select">
                                    <option value="0">X</option>
                                    <option value="1">S</option>
                                    <option value="2">M</option>
                                    <option value="3">L</option>
                                </select>
                            </label>
                            <label>
                                Color
                                <select class="input-select">
                                    <option value="0">Red</option>
                                    <option value="1">Blue</option>
                                    <option value="2">Black</option>
                                </select>
                            </label>
                        </div>
                        <div class="add-to-cart">
                            <div class="qty-label">
                                Qty
                                <div class="input-number">
                                    <input type="number" value="1" min="1">
                                    <span class="qty-up">+</span>
                                    <span class="qty-down">-</span>
                                </div>
                            </div>
                            <div class="btn-group" style="margin-left: 25px; margin-top: 15px">
                                <button class="add-to-cart-btn" pid="<?php echo $row['product_id']; ?>" id="product"><i class="fa fa-shopping-cart"></i> add to cart</button>
                            </div>
                        </div>
                        <ul class="product-btns">
                            <li><a href="#"><i class="fa fa-heart-o"></i> add to wishlist</a></li>
                            <li><a href="#"><i class="fa fa-exchange"></i> add to compare</a></li>
                        </ul>
                        <ul class="product-links">
                            <li>Category:</li>
                            <li><a href="#">Clothing</a></li>
                            <li><a href="#">Premium</a></li>
                        </ul>
                        <ul class="product-links">
                            <li>Share:</li>
                            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                            <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                            <li><a href="#"><i class="fa fa-envelope"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
} else {
    echo "<p>Product not found.</p>";
}
?>