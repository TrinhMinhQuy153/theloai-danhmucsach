<?php

require_once "../config/database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $category_id = $_POST["category_id"];
    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $status = $_POST["status"];

    if ($category_id == "" || $name == "") {

        $error = "Vui lòng nhập đầy đủ thông tin!";

    } else {

        $sql = "INSERT INTO genres
                (category_id, name, description, status)
                VALUES
                (:category_id, :name, :description, :status)";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            ":category_id" => $category_id,
            ":name" => $name,
            ":description" => $description,
            ":status" => $status
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

    <title>Thêm thể loại</title>

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

    <h1>➕ Thêm thể loại sách</h1>

    <?php if ($error): ?>

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

                <option value="<?= $category['id'] ?>">

                    <?= htmlspecialchars($category['name']) ?>

                </option>

            <?php endforeach; ?>

        </select>


        <label>Tên thể loại</label>

        <input
            type="text"
            name="name"
            placeholder="Ví dụ: Tiểu thuyết"
            required
        >


        <label>Mô tả</label>

        <textarea
            name="description"
            placeholder="Nhập mô tả thể loại..."
        ></textarea>


        <label>Trạng thái</label>

        <select name="status">

            <option value="1">
                Đang hoạt động
            </option>

            <option value="0">
                Không hoạt động
            </option>

        </select>


        <button type="submit">
            Thêm thể loại
        </button>

        <a href="index.php" class="back">
            Quay lại
        </a>

    </form>

</div>

</body>

</html>