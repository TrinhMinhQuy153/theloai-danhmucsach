<?php
require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $status = isset($_POST["status"]) ? 1 : 0;

    if ($name == "") {
        $error = "Vui lòng nhập tên danh mục!";
    } else {

        $sql = "INSERT INTO categories (name, description, status)
                VALUES (:name, :description, :status)";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            ":name" => $name,
            ":description" => $description,
            ":status" => $status
        ]);

        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thêm danh mục</title>

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
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>➕ Thêm danh mục sách</h1>

    <?php if (isset($error)): ?>
        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <label>Tên danh mục</label>

        <input
            type="text"
            name="name"
            placeholder="Ví dụ: Khoa học"
            required
        >

        <label>Mô tả</label>

        <textarea
            name="description"
            placeholder="Nhập mô tả danh mục..."
        ></textarea>

        <label>Trạng thái</label>

        <select name="status">
            <option value="1">Đang hoạt động</option>
            <option value="0">Không hoạt động</option>
        </select>

        <button type="submit">
            Thêm danh mục
        </button>

        <a href="index.php" class="back">
            Quay lại
        </a>

    </form>

</div>

</body>
</html>