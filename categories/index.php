<?php

require_once "../config/database.php";

$keyword = trim($_GET["keyword"] ?? "");
$status = $_GET["status"] ?? "";

$sql = "SELECT * FROM categories WHERE 1=1";

$params = [];

if ($keyword != "") {

    $sql .= " AND name LIKE :keyword";

    $params[":keyword"] = "%" . $keyword . "%";
}

if ($status !== "") {

    $sql .= " AND status = :status";

    $params[":status"] = $status;
}

$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);

$stmt->execute($params);

$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý danh mục sách</title>
    <link rel="stylesheet" href="../assets/style.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background: #f5f5f5;
        }

        h1 {
            color: #333;
        }

        .container {
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
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
    </style>

</head>

<<body>

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

    <a href="index.php">
        📁 Danh mục
    </a>

    <a href="../genres/index.php">
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

        <h2>Quản lý danh mục</h2>

        <div class="user">
            👤 Quản trị viên
        </div>

    </div>


    <div class="content">

        <div class="page-header">

            <div>
                <h1>Danh mục sách</h1>
                <p>Quản lý các danh mục trong thư viện</p>
            </div>

            <a href="create.php" class="btn">
                + Thêm danh mục
            </a>

        </div>
<div class="card">

    <form method="GET" class="search-box">

        <input
            type="text"
            name="keyword"
            placeholder="🔍 Tìm kiếm danh mục..."
            value="<?= htmlspecialchars($keyword) ?>"
        >

        <select name="status" class="form-control" style="max-width: 200px;">

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

    </form>

</div>

        <div class="card">

            <table>

                <tr>
                    <th>ID</th>
                    <th>Tên danh mục</th>
                    <th>Mô tả</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>


                <?php foreach ($categories as $category): ?>

                    <tr>

                        <td>
                            <?= $category['id'] ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($category['name']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($category['description']) ?>
                        </td>

                        <td>

                            <?php if ($category['status'] == 1): ?>

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
                            <?= $category['created_at'] ?>
                        </td>

                        <td>

                            <a
                                href="edit.php?id=<?= $category['id'] ?>"
                                class="btn-edit"
                            >
                                Sửa
                            </a>

                            <a
                                href="delete.php?id=<?= $category['id'] ?>"
                                class="btn-delete"
                                onclick="return confirm('Bạn có chắc muốn xóa danh mục này không?')"
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