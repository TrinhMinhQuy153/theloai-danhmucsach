<?php
require_once "../config/database.php";

$id = $_GET["id"] ?? 0;

// Lấy danh mục theo ID
$sql = "SELECT * FROM categories WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute([":id" => $id]);

$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    die("Không tìm thấy danh mục!");
}

// Khi bấm nút Cập nhật
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $status = $_POST["status"];

    if ($name == "") {
        $error = "Vui lòng nhập tên danh mục!";
    } else {

        $sql = "UPDATE categories
                SET name = :name,
                    description = :description,
                    status = :status
                WHERE id = :id";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            ":name" => $name,
            ":description" => $description,
            ":status" => $status,
            ":id" => $id
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
    <title>Sửa danh mục</title>

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

    <h1>✏️ Sửa danh mục sách</h1>

    <?php if (isset($error)): ?>
        <p class="error">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form method="POST">

        <label>Tên danh mục</label>

        <input
            type="text"
            name="name"
            value="<?= htmlspecialchars($category['name']) ?>"
            required
        >

        <label>Mô tả</label>

        <textarea name="description"><?= htmlspecialchars($category['description']) ?></textarea>

        <label>Trạng thái</label>

        <select name="status">

            <option value="1"
                <?= $category['status'] == 1 ? 'selected' : '' ?>>
                Đang hoạt động
            </option>

            <option value="0"
                <?= $category['status'] == 0 ? 'selected' : '' ?>>
                Không hoạt động
            </option>

        </select>

        <button type="submit">
            Cập nhật danh mục
        </button>

        <a href="index.php" class="back">
            Quay lại
        </a>

    </form>

</div>

</body>
</html>