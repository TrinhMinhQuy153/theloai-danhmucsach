<?php

require_once "../config/database.php";

$keyword = trim($_GET["keyword"] ?? "");
$status = $_GET["status"] ?? "";
$category_id = $_GET["category_id"] ?? "";


// Lấy danh sách danh mục
$sql = "SELECT * FROM categories
        WHERE status = 1
        ORDER BY name ASC";

$stmt = $conn->query($sql);

$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Tìm kiếm thể loại
$sql = "SELECT
            genres.id,
            genres.name,
            genres.description,
            genres.status,
            genres.create_at,
            categories.name AS category_name

        FROM genres

        LEFT JOIN categories
            ON genres.category_id = categories.id

        WHERE 1=1";

$params = [];


// Tìm theo tên thể loại
if ($keyword != "") {

    $sql .= " AND genres.name LIKE :keyword";

    $params[":keyword"] = "%" . $keyword . "%";
}


// Lọc trạng thái
if ($status !== "") {

    $sql .= " AND genres.status = :status";

    $params[":status"] = $status;
}


// Lọc theo danh mục
if ($category_id !== "") {

    $sql .= " AND genres.category_id = :category_id";

    $params[":category_id"] = $category_id;
}


$sql .= " ORDER BY genres.id DESC";


$stmt = $conn->prepare($sql);

$stmt->execute($params);

$genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý thể loại sách</title>
    <link rel="stylesheet" href="../assets/style.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 30px;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
        }

        h1 {
            color: #222;
        }

        .btn {
            display: inline-block;
            padding: 12px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background: #eee;
        }

        .edit {
            color: blue;
        }

        .delete {
            color: red;
        }
    </style>
</head>

<body>

<div class="sidebar">

    <div class="logo">
        📚 THƯ VIỆN MINI
    </div>

    <div class="menu-title">
        QUẢN LÝ
    </div>

    <a href="../index.php">
        🏠 Trang chủ
    </a>

    <a href="../categories/index.php">
        📁 Danh mục
    </a>

    <a href="index.php">
        📚 Thể loại
    </a>

    <div class="menu-title">
        HỆ THỐNG
    </div>

    <a href="#">
        ⚙️ Cài đặt
    </a>

</div>


<div class="main">

    <div class="topbar">

        <h2>Quản lý thể loại</h2>

        <div class="user">
            👤 Quản trị viên
        </div>

    </div>


    <div class="content">

        <div class="page-header">
            <div class="card">

    <form method="GET" class="search-box">

        <input
            type="text"
            name="keyword"
            placeholder="🔍 Tìm kiếm thể loại..."
            value="<?= htmlspecialchars($keyword) ?>"
        >


        <select
            name="category_id"
            class="form-control"
            style="max-width: 220px;"
        >

            <option value="">
                Tất cả danh mục
            </option>


            <?php foreach ($categories as $category): ?>

                <option
                    value="<?= $category['id'] ?>"
                    <?= $category_id == $category['id'] ? "selected" : "" ?>
                >

                    <?= htmlspecialchars($category['name']) ?>

                </option>

            <?php endforeach; ?>

        </select>


        <select
            name="status"
            class="form-control"
            style="max-width: 200px;"
        >

            <option value="">
                Tất cả trạng thái
            </option>

            <option
                value="1"
                <?= $status === "1" ? "selected" : "" ?>
            >
                Đang hoạt động
            </option>

            <option
                value="0"
                <?= $status === "0" ? "selected" : "" ?>
            >
                Ngừng hoạt động
            </option>

        </select>


        <button type="submit" class="btn">
            Tìm kiếm
        </button>


        <a
            href="index.php"
            class="btn btn-danger"
        >
            Xóa lọc
        </a>

    </form>

</div>

<br>

            <div>

                <h1>Thể loại sách</h1>

                <p>
                    Quản lý các thể loại thuộc danh mục sách
                </p>

            </div>

            <a href="create.php" class="btn">
                + Thêm thể loại
            </a>

        </div>


        <div class="card">

            <table>

                <tr>

                    <th>ID</th>

                    <th>Tên thể loại</th>

                    <th>Danh mục</th>

                    <th>Mô tả</th>

                    <th>Trạng thái</th>

                    <th>Ngày tạo</th>

                    <th>Thao tác</th>

                </tr>


                <?php foreach ($genres as $genre): ?>

                    <tr>

                        <td>
                            <?= $genre['id'] ?>
                        </td>


                        <td>
                            <?= htmlspecialchars($genre['name']) ?>
                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $genre['category_name'] ?? 'Chưa có'
                            ) ?>
                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $genre['description'] ?? ''
                            ) ?>
                        </td>


                        <td>

                            <?php if ($genre['status'] == 1): ?>

                                <span class="status status-active">
                                    Đang hoạt động
                                </span>

                            <?php else: ?>

                                <span class="status status-inactive">
                                    Ngừng hoạt động
                                </span>

                            <?php endif; ?>

                        </td>


                        <td>
                            <?= $genre['create_at'] ?>
                        </td>


                        <td>

                            <a
                                href="edit.php?id=<?= $genre['id'] ?>"
                                class="btn-edit"
                            >
                                Sửa
                            </a>


                            <a
                                href="delete.php?id=<?= $genre['id'] ?>"
                                class="btn-delete"
                                onclick="return confirm('Bạn có chắc muốn xóa thể loại này không?')"
                            >
                                Xóa
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </table>

        </div>

    </div>

</div>

</body>

</html>