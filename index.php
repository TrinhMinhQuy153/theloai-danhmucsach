<?php

require_once "config/database.php";

/*
|--------------------------------------------------------------------------
| Thống kê danh mục
|--------------------------------------------------------------------------
*/

$stmt = $conn->query("SELECT COUNT(*) FROM categories");
$total_categories = $stmt->fetchColumn();


/*
|--------------------------------------------------------------------------
| Thống kê thể loại
|--------------------------------------------------------------------------
*/

$stmt = $conn->query("SELECT COUNT(*) FROM genres");
$total_genres = $stmt->fetchColumn();


/*
|--------------------------------------------------------------------------
| Thống kê sách
|--------------------------------------------------------------------------
*/

$stmt = $conn->query("SELECT COUNT(*) FROM books");
$total_books = $stmt->fetchColumn();


/*
|--------------------------------------------------------------------------
| Tổng số lượng sách
|--------------------------------------------------------------------------
*/

$stmt = $conn->query("SELECT COALESCE(SUM(quantity), 0) FROM books");
$total_quantity = $stmt->fetchColumn();

?>

<!DOCTYPE html>

<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Thư viện Mini - Trang chủ</title>

    <link rel="stylesheet" href="assets/style.css">

</head>

<body>


<?php include "includes/sidebar.php"; ?>


<div class="main">


    <!-- TOPBAR -->

    <div class="topbar">

        <h2>Trang chủ</h2>

        <div class="user">
            👤 Quản trị viên
        </div>

    </div>


    <!-- CONTENT -->

    <div class="content">


        <div class="page-header">

            <div>

                <h1>
                    📊 Tổng quan thư viện
                </h1>

                <p>
                    Chào mừng bạn đến với hệ thống quản lý thư viện mini.
                </p>

            </div>

        </div>


        <!-- DASHBOARD -->

        <div class="dashboard-grid">


            <!-- DANH MỤC -->

            <div class="dashboard-card">

                <div>
                    📁 Danh mục
                </div>

                <div class="number">

                    <?= $total_categories ?>

                </div>

                <div class="label">

                    Tổng số danh mục

                </div>

            </div>


            <!-- THỂ LOẠI -->

            <div class="dashboard-card">

                <div>
                    🏷️ Thể loại
                </div>

                <div class="number">

                    <?= $total_genres ?>

                </div>

                <div class="label">

                    Tổng số thể loại

                </div>

            </div>


            <!-- SÁCH -->

            <div class="dashboard-card">

                <div>
                    📖 Sách
                </div>

                <div class="number">

                    <?= $total_books ?>

                </div>

                <div class="label">

                    Tổng số đầu sách

                </div>

            </div>


            <!-- SỐ LƯỢNG -->

            <div class="dashboard-card">

                <div>
                    📚 Số lượng
                </div>

                <div class="number">

                    <?= $total_quantity ?>

                </div>

                <div class="label">

                    Tổng số quyển sách

                </div>

            </div>


        </div>


        <!-- HƯỚNG DẪN NHANH -->

        <div class="card" style="margin-top: 25px;">

            <h2>
                🚀 Quản lý nhanh
            </h2>

            <p style="margin-top: 10px; color: #64748b;">

                Bạn có thể sử dụng menu bên trái để quản lý hệ thống.

            </p>


            <div style="margin-top: 20px;">

                <a
                    href="categories/index.php"
                    class="btn"
                >
                    📁 Quản lý danh mục
                </a>


                <a
                    href="genres/index.php"
                    class="btn"
                    style="margin-left: 10px;"
                >
                    🏷️ Quản lý thể loại
                </a>


                <a
                    href="books/index.php"
                    class="btn"
                    style="margin-left: 10px;"
                >
                    📖 Quản lý sách
                </a>

            </div>

        </div>


    </div>

</div>


</body>

</html>