<?php
session_start();
include "db.php";

$ip_add = getenv("REMOTE_ADDR");

if (isset($_POST["category"])) {
    $category_query = "SELECT * FROM categories";
    $run_query = mysqli_query($con, $category_query) or die(mysqli_error($con));
    
    echo "<div class='aside'><h3 class='aside-title'>Categories</h3><div class='btn-group-vertical'>";
    $i = 1;
    while ($row = mysqli_fetch_array($run_query)) {
        $cid = $row["cat_id"];
        $cat_name = htmlspecialchars($row["cat_title"]);
        $sql = "SELECT COUNT(*) AS count_items FROM products WHERE product_cat = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $cid);
        mysqli_stmt_execute($stmt);
        $row_count = mysqli_fetch_array(mysqli_stmt_get_result($stmt));
        $count = $row_count["count_items"];
        echo "<div type='button' class='btn navbar-btn category' cid='$cid'><a href='#'><span></span>$cat_name <small class='qty'>($count)</small></a></div>";
        $i++;
    }
    echo "</div></div>";
}

if (isset($_POST["brand"])) {
    $brand_query = "SELECT * FROM brands";
    $run_query = mysqli_query($con, $brand_query);
    
    echo "<div class='aside'><h3 class='aside-title'>Brands</h3><div class='btn-group-vertical'>";
    $i = 1;
    while ($row = mysqli_fetch_array($run_query)) {
        $bid = $row["brand_id"];
        $brand_name = htmlspecialchars($row["brand_title"]);
        $sql = "SELECT COUNT(*) AS count_items FROM products WHERE product_brand = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $bid);
        mysqli_stmt_execute($stmt);
        $row_count = mysqli_fetch_array(mysqli_stmt_get_result($stmt));
        $count = $row_count["count_items"];
        echo "<div type='button' class='btn navbar-btn selectBrand' bid='$bid'><a href='#'><span></span>$brand_name <small>($count)</small></a></div>";
        $i++;
    }
    echo "</div></div>";
}

if (isset($_POST["page"])) {
    $sql = "SELECT COUNT(*) FROM products";
    $run_query = mysqli_query($con, $sql);
    $count = mysqli_num_rows($run_query);
    $pageno = ceil($count / 9);
    
    for ($i = 1; $i <= $pageno; $i++) {
        echo "<li><a href='#product-row' page='$i' id='page' class='active'>$i</a></li>";
    }
}

if (isset($_POST["getProduct"])) {
    $limit = 9;
    $start = isset($_POST["setPage"]) ? (($_POST["pageNumber"] * $limit) - $limit) : 0;
    
    $product_query = "SELECT * FROM products p JOIN categories c ON p.product_cat = c.cat_id LIMIT ?, ?";
    $stmt = mysqli_prepare($con, $product_query);
    mysqli_stmt_bind_param($stmt, "ii", $start, $limit);
    mysqli_stmt_execute($stmt);
    $run_query = mysqli_stmt_get_result($stmt);
    
    while ($row = mysqli_fetch_array($run_query)) {
        $pro_id = $row['product_id'];
        $pro_title = htmlspecialchars($row['product_title']);
        $pro_price = $row['product_price'];
        $pro_image = htmlspecialchars($row['product_image']);
        $cat_name = htmlspecialchars($row["cat_title"]);
        
        echo "
            <div class='col-md-4 col-xs-6'>
                <a href='product.php?p=$pro_id'>
                    <div class='product'>
                        <div class='product-img'>
                            <img src='product_images/$pro_image' style='max-height: 170px;' alt='$pro_title'>
                            <div class='product-label'>
                                <span class='sale'>-30%</span>
                                <span class='new'>NEW</span>
                            </div>
                        </div>
                    </a>
                    <div class='product-body'>
                        <p class='product-category'>$cat_name</p>
                        <h3 class='product-name header-cart-item-name'><a href='product.php?p=$pro_id'>$pro_title</a></h3>
                        <h4 class='product-price header-cart-item-info'>$$pro_price <del class='product-old-price'>$990.00</del></h4>
                        <div class='product-rating'>
                            <i class='fa fa-star'></i>
                            <i class='fa fa-star'></i>
                            <i class='fa fa-star'></i>
                            <i class='fa fa-star'></i>
                            <i class='fa fa-star'></i>
                        </div>
                        <div class='product-btns'>
                            <button class='add-to-wishlist'><i class='fa fa-heart-o'></i><span class='tooltipp'>add to wishlist</span></button>
                            <button class='add-to-compare'><i class='fa fa-exchange'></i><span class='tooltipp'>add to compare</span></button>
                            <button class='quick-view'><i class='fa fa-eye'></i><span class='tooltipp'>quick view</span></button>
                        </div>
                    </div>
                    <div class='add-to-cart'>
                        <button pid='$pro_id' id='product' class='add-to-cart-btn block2-btn-towishlist'><i class='fa fa-shopping-cart'></i> add to cart</button>
                    </div>
                </div>
            </div>";
    }
}

if (isset($_POST["getting_seleted_Category"]) || isset($_POST["selectBrand"]) || isset($_POST["search"])) {
    if (isset($_POST["getting_seleted_Category"])) {
        $id = $_POST["cat_id"];
        $sql = "SELECT * FROM products p JOIN categories c ON p.product_cat = c.cat_id WHERE p.product_cat = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
    } elseif (isset($_POST["selectBrand"])) {
        $id = $_POST["brand_id"];
        $sql = "SELECT * FROM products p JOIN categories c ON p.product_cat = c.cat_id WHERE p.product_brand = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
    } else {
        $keyword = "%" . $_POST["keyword"] . "%";
        $sql = "SELECT * FROM products p JOIN categories c ON p.product_cat = c.cat_id WHERE p.product_keywords LIKE ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $keyword);
    }
    
    mysqli_stmt_execute($stmt);
    $run_query = mysqli_stmt_get_result($stmt);
    
    while ($row = mysqli_fetch_array($run_query)) {
        $pro_id = $row['product_id'];
        $pro_title = htmlspecialchars($row['product_title']);
        $pro_price = $row['product_price'];
        $pro_image = htmlspecialchars($row['product_image']);
        $cat_name = htmlspecialchars($row["cat_title"]);
        
        echo "
            <div class='col-md-4 col-xs-6'>
                <a href='product.php?p=$pro_id'>
                    <div class='product'>
                        <div class='product-img'>
                            <img src='product_images/$pro_image' style='max-height: 170px;' alt='$pro_title'>
                            <div class='product-label'>
                                <span class='sale'>-30%</span>
                                <span class='new'>NEW</span>
                            </div>
                        </div>
                    </a>
                    <div class='product-body'>
                        <p class='product-category'>$cat_name</p>
                        <h3 class='product-name header-cart-item-name'><a href='product.php?p=$pro_id'>$pro_title</a></h3>
                        <h4 class='product-price header-cart-item-info'>$$pro_price <del class='product-old-price'>$990.00</del></h4>
                        <div class='product-rating'>
                            <i class='fa fa-star'></i>
                            <i class='fa fa-star'></i>
                            <i class='fa fa-star'></i>
                            <i class='fa fa-star'></i>
                            <i class='fa fa-star'></i>
                        </div>
                        <div class='product-btns'>
                            <button class='add-to-wishlist'><i class='fa fa-heart-o'></i><span class='tooltipp'>add to wishlist</span></button>
                            <button class='add-to-compare'><i class='fa fa-exchange'></i><span class='tooltipp'>add to compare</span></button>
                            <button class='quick-view'><i class='fa fa-eye'></i><span class='tooltipp'>quick view</span></button>
                        </div>
                    </div>
                    <div class='add-to-cart'>
                        <button pid='$pro_id' id='product' class='add-to-cart-btn'><i class='fa fa-shopping-cart'></i> add to cart</button>
                    </div>
                </div>
            </div>";
    }
}

if (isset($_POST["addToCart"])) {
    $p_id = mysqli_real_escape_string($con, $_POST["proId"]);
    $user_id = isset($_SESSION["uid"]) ? $_SESSION["uid"] : -1;
    $ip_add = $_SERVER["REMOTE_ADDR"];
    
    $sql = "SELECT id FROM carts WHERE product_id = ? AND (user_id = ? OR (user_id = -1 AND ip_add = ?))";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iis", $p_id, $user_id, $ip_add);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        echo "Product already in cart!";
    } else {
        $sql = "INSERT INTO carts (product_id, user_id, ip_add, quantity) VALUES (?, ?, ?, 1)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "iis", $p_id, $user_id, $ip_add);
        if (mysqli_stmt_execute($stmt)) {
            echo "success";
        } else {
            echo "error";
        }
    }
    exit();
}

if (isset($_POST["count_item"])) {
    if (isset($_SESSION["uid"])) {
        $sql = "SELECT COUNT(*) AS count_item FROM carts WHERE user_id = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $_SESSION["uid"]);
    } else {
        $sql = "SELECT COUNT(*) AS count_item FROM carts WHERE ip_add = ? AND user_id < 0";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $ip_add);
    }
    
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_array(mysqli_stmt_get_result($stmt));
    echo $row["count_item"];
    exit();
}

if (isset($_POST["Common"])) {
    if (isset($_SESSION["uid"])) {
        $sql = "SELECT p.product_id, p.product_title, p.product_price, p.product_image, c.id, c.quantity 
                FROM products p JOIN carts c ON p.product_id = c.product_id WHERE c.user_id = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $_SESSION["uid"]);
    } else {
        $sql = "SELECT p.product_id, p.product_title, p.product_price, p.product_image, c.id, c.quantity 
                FROM products p JOIN carts c ON p.product_id = c.product_id WHERE c.ip_add = ? AND c.user_id < 0";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $ip_add);
    }
    
    mysqli_stmt_execute($stmt);
    $query = mysqli_stmt_get_result($stmt);
    
    if (isset($_POST["getCartItem"])) {
        if (mysqli_num_rows($query) > 0) {
            $n = 0;
            $total_price = 0;
            while ($row = mysqli_fetch_array($query)) {
                $n++;
                $product_id = $row["product_id"];
                $product_title = htmlspecialchars($row["product_title"]);
                $product_price = $row["product_price"];
                $product_image = htmlspecialchars($row["product_image"]);
                $cart_item_id = $row["id"];
                $qty = $row["quantity"];
                $total_price += $product_price * $qty;
                
                echo "
                    <div class='product-widget'>
                        <div class='product-img'>
                            <img src='product_images/$product_image' alt='$product_title'>
                        </div>
                        <div class='product-body'>
                            <h3 class='product-name'><a href='#'>$product_title</a></h3>
                            <h4 class='product-price'><span class='qty'>$n</span>$$product_price</h4>
                        </div>
                    </div>";
            }
            
            echo "<div class='cart-summary'>
                    <small class='qty'>$n Item(s) selected</small>
                    <h5>$$total_price</h5>
                </div>";
            exit();
        }
    }
    
    if (isset($_POST["checkOutDetails"])) {
        if (mysqli_num_rows($query) > 0) {
            echo '<div class="main">
                <div class="table-responsive">
                    <form method="post" action="login_form.php">
                        <table id="cart" class="table table-hover table-condensed">
                            <thead>
                                <tr>
                                    <th style="width:50%">Product</th>
                                    <th style="width:10%">Price</th>
                                    <th style="width:8%">Quantity</th>
                                    <th style="width:7%" class="text-center">Subtotal</th>
                                    <th style="width:10%"></th>
                                </tr>
                            </thead>
                            <tbody>';
            $n = 0;
            $total_price = 0;
            while ($row = mysqli_fetch_array($query)) {
                $n++;
                $product_id = $row["product_id"];
                $product_title = htmlspecialchars($row["product_title"]);
                $product_price = $row["product_price"];
                $product_image = htmlspecialchars($row["product_image"]);
                $cart_item_id = $row["id"];
                $qty = $row["quantity"];
                $subtotal = $product_price * $qty;
                $total_price += $subtotal;
                
                echo "
                    <tr>
                        <td data-th='Product'>
                            <div class='row'>
                                <div class='col-sm-4'>
                                    <img src='product_images/$product_image' style='height: 70px; width: 75px;'/>
                                    <h4 class='nomargin product-name header-cart-item-name'><a href='product.php?p=$product_id'>$product_title</a></h4>
                                </div>
                                <div class='col-sm-6'>
                                    <div style='max-width: 50px;'>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <input type='hidden' name='product_id[]' value='$product_id'/>
                        <input type='hidden' name='cart_item_id' value='$cart_item_id'/>
                        <td data-th='Price'><input type='text' class='form-control price' value='$product_price' readonly='readonly'></td>
                        <td data-th='Quantity'>
                            <input type='text' class='form-control qty' value='$qty'></td>
                        <td data-th='Subtotal' class='text-center'><input type='text' class='form-control total' value='$subtotal' readonly='readonly'></td>
                        <td class='actions' data-th=''>
                            <div class='btn-group'>
                                <a href='#' class='btn btn-info btn-sm update' update_id='$product_id'><i class='fa fa-refresh'></i></a>
                                <a href='#' class='btn btn-danger btn-sm remove' remove_id='$product_id'><i class='fa fa-trash-o'></i></a>
                            </div>
                        </td>
                    </tr>";
            }
            
            echo "</tbody>
                <tfoot>
                    <tr>
                        <td><a href='store.php' class='btn btn-warning'><i class='fa fa-angle-left'></i> Continue Shopping</a></td>
                        <td colspan='2' class='hidden-xs'></td>
                        <td class='hidden-xs text-center'><b class='net_total'>$$total_price</b></td>
                        <td>";
            
            if (!isset($_SESSION["uid"])) {
                echo "<a href='' data-toggle='modal' data-target='#Modal_register' class='btn btn-success'>Ready to Checkout</a>";
            } else {
                echo "</form>
                    <form action='checkout.php' method='post'>
                        <input type='hidden' name='cmd' value='_cart'>
                        <input type='hidden' name='business' value='shoppingcart@fabrica.com'>
                        <input type='hidden' name='upload' value='1'>";
                
                $x = 0;
                $sql = "SELECT p.product_id, p.product_title, p.product_price, c.quantity 
                        FROM products p JOIN carts c ON p.product_id = c.product_id WHERE c.user_id = ?";
                $stmt = mysqli_prepare($con, $sql);
                mysqli_stmt_bind_param($stmt, "i", $_SESSION["uid"]);
                mysqli_stmt_execute($stmt);
                $query = mysqli_stmt_get_result($stmt);
                
                while ($row = mysqli_fetch_array($query)) {
                    $x++;
                    echo "
                        <input type='hidden' name='total_count' value='$x'>
                        <input type='hidden' name='item_name_$x' value='" . htmlspecialchars($row["product_title"]) . "'>
                        <input type='hidden' name='item_number_$x' value='$x'>
                        <input type='hidden' name='amount_$x' value='" . $row["product_price"] . "'>
                        <input type='hidden' name='quantity_$x' value='" . $row["quantity"] . "'>";
                }
                
                echo "
                        <input type='hidden' name='return' value='http://localhost/fabrica/payment_success.php'/>
                        <input type='hidden' name='notify_url' value='http://localhost/fabrica/payment_success.php'>
                        <input type='hidden' name='cancel_return' value='http://localhost/fabrica/cancel.php'/>
                        <input type='hidden' name='currency_code' value='USD'/>
                        <input type='hidden' name='custom' value='" . $_SESSION["uid"] . "'/>
                        <input type='submit' id='submit' name='login_user_with_product' class='btn btn-success' value='Ready to Checkout'>
                    </form>";
            }
            
            echo "</td></tr></tfoot></table></div></div>";
        }
    }
}

if (isset($_POST["removeItemFromCart"])) {
    $remove_id = (int)$_POST["rid"];
    
    if (isset($_SESSION["uid"])) {
        $sql = "DELETE FROM carts WHERE product_id = ? AND user_id = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $remove_id, $_SESSION["uid"]);
    } else {
        $sql = "DELETE FROM carts WHERE product_id = ? AND ip_add = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "is", $remove_id, $ip_add);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>×</a><b>Product removed from cart</b></div>";
        exit();
    }
}

if (isset($_POST["updateCartItem"])) {
    $update_id = (int)$_POST["update_id"];
    $qty = (int)$_POST["qty"];
    
    if (isset($_SESSION["uid"])) {
        $sql = "UPDATE carts SET quantity = ? WHERE product_id = ? AND user_id = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $qty, $update_id, $_SESSION["uid"]);
    } else {
        $sql = "UPDATE carts SET quantity = ? WHERE product_id = ? AND ip_add = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "iis", $qty, $update_id, $ip_add);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<div class='alert alert-info'><a href='#' class='close' data-dismiss='alert' aria-label='close'>×</a><b>Product updated</b></div>";
        exit();
    }
}
?>