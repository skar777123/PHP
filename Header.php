<?php
session_start();
include "db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fabrica</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="css/bootstrap.min.css"/>
    <link type="text/css" rel="stylesheet" href="css/slick.css"/>
    <link type="text/css" rel="stylesheet" href="css/slick-theme.css"/>
    <link type="text/css" rel="stylesheet" href="css/nouislider.min.css"/>
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link type="text/css" rel="stylesheet" href="css/style.css"/>
    <link type="text/css" rel="stylesheet" href="css/accountbtn.css"/>
    <style>
        #navigation {
            background: linear-gradient(to right, #F9D423, #FF4E50);
        }
        #header {
            background: linear-gradient(to right, #061161, #780206);
        }
        #top-header {
            background: linear-gradient(to right, #190A05, #870000);
        }
        #footer, #bottom-footer {
            background: linear-gradient(to right, #348AC7, #7474BF);
            color: #1E1F29;
        }
        .footer-links li a {
            color: #1E1F29;
        }
        .mainn-raised {
            margin: -7px 0px 0px;
            border-radius: 6px;
            box-shadow: 0 16px 24px 2px rgba(0, 0, 0, 0.14), 0 6px 30px 5px rgba(0, 0, 0, 0.12), 0 8px 10px -5px rgba(0, 0, 0, 0.2);
        }
        .glyphicon {
            display: inline-block;
            font: normal normal normal 14px/1 FontAwesome;
            font-size: inherit;
            text-rendering: auto;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .glyphicon-chevron-left:before { content: "\f053"; }
        .glyphicon-chevron-right:before { content: "\f054"; }
    </style>
</head>
<body>
    <header>
        <div id="top-header">
            <div class="container">
                <ul class="header-links pull-left">
                    <li><a href="#"><i class="fa fa-phone"></i> +91-9535688928</a></li>
                    <li><a href="#"><i class="fa fa-envelope-o"></i> support@fabrica.com</a></li>
                    <li><a href="#"><i class="fa fa-map-marker"></i> Bangalore</a></li>
                </ul>
                <ul class="header-links pull-right">
                    <li><a href="#"><i class="fa fa-inr"></i> INR</a></li>
                    <li>
                        <?php
                        if (isset($_SESSION["uid"])) {
                            $sql = "SELECT name FROM users WHERE user_id = ?";
                            $stmt = mysqli_prepare($con, $sql);
                            mysqli_stmt_bind_param($stmt, "i", $_SESSION["uid"]);
                            mysqli_stmt_execute($stmt);
                            $row = mysqli_fetch_array(mysqli_stmt_get_result($stmt));
                            echo '
                                <div class="dropdownn">
                                    <a href="#" class="dropdownn" data-toggle="modal" data-target="#myModal"><i class="fa fa-user-o"></i> Hi ' . htmlspecialchars($row["name"]) . '</a>
                                    <div class="dropdownn-content">
                                        <a href="myprofile.php" data-toggle="modal" data-target="#profile"><i class="fa fa-user-circle"></i> My Profile</a>
                                        <a href="logout.php"><i class="fa fa-sign-in"></i> Log out</a>
                                    </div>
                                </div>';
                        } else {
                            echo '
                                <div class="dropdownn">
                                    <a href="#" class="dropdownn" data-toggle="modal" data-target="#myModal"><i class="fa fa-user-o"></i> My Account</a>
                                    <div class="dropdownn-content">
                                        <a href="admin/login.php"><i class="fa fa-user"></i> Admin</a>
                                        <a href="login_form.php" data-toggle="modal" data-target="#Modal_login"><i class="fa fa-sign-in"></i> Login</a>
                                        <a href="register_form.php" data-toggle="modal" data-target="#Modal_register"><i class="fa fa-user-plus"></i> Register</a>
                                    </div>
                                </div>';
                        }
                        ?>
                    </li>
                </ul>
            </div>
        </div>
        <div id="header">
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        <div class="header-logo">
                            <a href="index.php" class="logo">
                                <font style="font-style: normal; font-size: 33px; color: aliceblue; font-family: serif">Fabrica</font>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="header-search">
                            <form>
                                <select class="input-select">
                                    <option value="0">All Categories</option>
                                    <option value="1">Men</option>
                                    <option value="2">Women</option>
                                </select>
                                <input class="input" id="search" type="text" placeholder="Search here">
                                <button type="submit" id="search_btn" class="search-btn">Search</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-3 clearfix">
                        <div class="header-ctn">
                            <div>
                                <a href="https://github.com/puneethreddyhc">
                                    <i class="fa fa-github"></i>
                                    <span>Github</span>
                                </a>
                            </div>
                            <div class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-shopping-cart"></i>
                                    <span>Your Cart</span>
                                    <div class="badge qty">0</div>
                                </a>
                                <div class="cart-dropdown">
                                    <div class="cart-list" id="cart_product"></div>
                                    <div class="cart-btns">
                                        <a href="cart.php" style="width:100%;"><i class="fa fa-edit"></i> edit cart</a>
                                    </div>
                                </div>
                            </div>
                            <div class="menu-toggle">
                                <a href="#">
                                    <i class="fa fa-bars"></i>
                                    <span>Menu</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <nav id='navigation'>
        <div class="container" id="get_category_home"></div>
    </nav>
    