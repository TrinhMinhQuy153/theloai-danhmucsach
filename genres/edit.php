<?php

require_once "../config/database.php";

$id = $_GET["id"] ?? 0;

// Lấy thông tin thể loại
$sql = "SELECT * FROM genres WHERE id = :id";

$stmt = $conn->prepare($sql);

$stmt->execute([
    ":id" => $id
]);

$genre = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$genre) {
    die("Không tìm thấy thể loại!");
}


// Khi bấm nút Cập nhật
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $category_id = $_POST["category_id"];
    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $status = $_POST["status"];


    if ($category_id == "" || $name == "") {

        $error = "Vui lòng nhập đầy đủ thông tin!";

    } else {

        $sql = "UPDATE genres
                SET category_id = :category_id,
                    name = :name,
                    description = :description,
                    status = :status
                WHERE id = :id";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            ":category_id" => $category_id,
            ":name" => $name,
            ":description" => $description,
            ":status" => $status,
            ":id" => $id
        ]);

        header("Location: index.php");

        exit;
    }
}


// Lấy danh sách danh mục
$sql = "SELECT * FROM categories
        WHERE status = 1
        ORDER BY name ASC";

$stmt = $conn->query($sql);

$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html lang="vi">

<head>

    <meta charset="UTF-8">

    <title>Sửa thể loại</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 40px;
        }

        .container {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
        }

        h1 {
            color: #222;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            box-sizing: border-box;
        }

        textarea {
            height: 120px;
        }

        button {
            margin-top: 20px;
            padding: 12px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .back {
            margin-left: 10px;
        }

        .error {
            color: red;
        }

    </style>

</head>

<body>

<div class="container">

    <h1>✏️ Sửa thể loại sách</h1>


    <?php if (isset($error)): ?>

        <p class="error">
            <?= htmlspecialchars($error) ?>
        </p>

    <?php endif; ?>


    <form method="POST">


        <label>Danh mục</label>

        <select name="category_id" required>

            <option value="">
                -- Chọn danh mục --
            </option>


            <?php foreach ($categories as $category): ?>

                <option
                    value="<?= $category['id'] ?>"
                    <?= $genre['category_id'] == $category['id'] ? 'selected' : '' ?>
                >

                    <?= htmlspecialchars($category['name']) ?>

                </option>

            <?php endforeach; ?>

        </select>


        <label>Tên thể loại</label>

        <input
            type="text"
            name="name"
            value="<?= htmlspecialchars($genre['name']) ?>"
            required
        >


        <label>Mô tả</label>

        <textarea name="description"><?= htmlspecialchars($genre['description'] ?? '') ?></textarea>


        <label>Trạng thái</label>

        <select name="status">

            <option
                value="1"
                <?= $genre['status'] == 1 ? 'selected' : '' ?>
            >
                Đang hoạt động
            </option>


            <option
                value="0"
                <?= $genre['status'] == 0 ? 'selected' : '' ?>
            >
                Không hoạt động
            </option>

        </select>


        <button type="submit">
            Cập nhật thể loại
        </button>


        <a href="index.php" class="back">
            Quay lại
        </a>

    </form>

</div>

</body>

</html>