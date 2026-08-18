<<?php

require_once "../config/database.php";

$name = "";
$description = "";
$status = 1;

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // =====================================================
    // 1. NHẬN VÀ CHUẨN HÓA DỮ LIỆU
    // =====================================================

    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $status = isset($_POST["status"]) ? (int) $_POST["status"] : 0;


    // =====================================================
    // 2. VALIDATE TÊN DANH MỤC
    // =====================================================

    if ($name === "") {

        $errors["name"] = "Vui lòng nhập tên danh mục.";

    } elseif (mb_strlen($name) < 2) {

        $errors["name"] = "Tên danh mục phải có ít nhất 2 ký tự.";

    } elseif (mb_strlen($name) > 100) {

        $errors["name"] = "Tên danh mục không được vượt quá 100 ký tự.";
    }


    // =====================================================
    // 3. VALIDATE MÔ TẢ
    // =====================================================

    if (mb_strlen($description) > 500) {

        $errors["description"] =
            "Mô tả không được vượt quá 500 ký tự.";
    }


    // =====================================================
    // 4. VALIDATE TRẠNG THÁI
    // =====================================================

    if ($status !== 0 && $status !== 1) {

        $errors["status"] = "Trạng thái không hợp lệ.";
    }


    // =====================================================
    // 5. KIỂM TRA TÊN DANH MỤC ĐÃ TỒN TẠI
    // =====================================================

    if (!isset($errors["name"])) {

        $stmt = $conn->prepare(
            "SELECT COUNT(*) 
             FROM categories 
             WHERE name = :name"
        );

        $stmt->execute([
            ":name" => $name
        ]);

        if ($stmt->fetchColumn() > 0) {

            $errors["name"] =
                "Tên danh mục này đã tồn tại.";
        }
    }


    // =====================================================
    // 6. NẾU KHÔNG CÓ LỖI → INSERT DATABASE
    // =====================================================

    if (empty($errors)) {

        $sql = "INSERT INTO categories
                (name, description, status)
                VALUES
                (:name, :description, :status)";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            ":name" => $name,
            ":description" => $description,
            ":status" => $status
        ]);


        // Sau khi thêm thành công
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
            margin-top: 7px;
            font-size: 14px;
        }

        .input-error {
            border: 1px solid red;
        }

    </style>

</head>

<body>

<div class="container">

    <h1>➕ Thêm danh mục sách</h1>


    <form method="POST">

        <!-- ==============================
             TÊN DANH MỤC
        =============================== -->

        <label>Tên danh mục</label>

        <input
            type="text"
            name="name"
            placeholder="Ví dụ: Khoa học"
            value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>"
            class="<?= isset($errors["name"]) ? 'input-error' : '' ?>"
        >

        <?php if (isset($errors["name"])): ?>

            <div class="error">
                <?= htmlspecialchars($errors["name"], ENT_QUOTES, 'UTF-8') ?>
            </div>

        <?php endif; ?>


        <!-- ==============================
             MÔ TẢ
        =============================== -->

        <label>Mô tả</label>

        <textarea
            name="description"
            placeholder="Nhập mô tả danh mục..."
            class="<?= isset($errors["description"]) ? 'input-error' : '' ?>"
        ><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?></textarea>

        <?php if (isset($errors["description"])): ?>

            <div class="error">
                <?= htmlspecialchars($errors["description"], ENT_QUOTES, 'UTF-8') ?>
            </div>

        <?php endif; ?>


        <!-- ==============================
             TRẠNG THÁI
        =============================== -->

        <label>Trạng thái</label>

        <select
            name="status"
            class="<?= isset($errors["status"]) ? 'input-error' : '' ?>"
        >

            <option
                value="1"
                <?= $status === 1 ? "selected" : "" ?>
            >
                Đang hoạt động
            </option>

            <option
                value="0"
                <?= $status === 0 ? "selected" : "" ?>
            >
                Không hoạt động
            </option>

        </select>

        <?php if (isset($errors["status"])): ?>

            <div class="error">
                <?= htmlspecialchars($errors["status"], ENT_QUOTES, 'UTF-8') ?>
            </div>

        <?php endif; ?>


        <!-- ==============================
             SUBMIT
        =============================== -->

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