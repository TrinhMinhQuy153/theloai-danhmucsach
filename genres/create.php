<?php

require_once "../config/database.php";

$category_id = "";
$name = "";
$description = "";
$status = 1;

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // =====================================================
    // 1. NHẬN VÀ CHUẨN HÓA DỮ LIỆU
    // =====================================================

    $category_id = trim($_POST["category_id"] ?? "");
    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $status = isset($_POST["status"]) ? (int) $_POST["status"] : 0;


    // =====================================================
    // 2. VALIDATE DANH MỤC
    // =====================================================

    if ($category_id === "") {

        $errors["category_id"] = "Vui lòng chọn danh mục.";

    } else {

        // Kiểm tra category_id có tồn tại không
        $stmt = $conn->prepare(
            "SELECT COUNT(*) 
             FROM categories 
             WHERE id = :id AND status = 1"
        );

        $stmt->execute([
            ":id" => $category_id
        ]);

        if ($stmt->fetchColumn() == 0) {

            $errors["category_id"] = "Danh mục không hợp lệ.";
        }
    }


    // =====================================================
    // 3. VALIDATE TÊN THỂ LOẠI
    // =====================================================

    if ($name === "") {

        $errors["name"] = "Vui lòng nhập tên thể loại.";

    } elseif (mb_strlen($name) < 2) {

        $errors["name"] =
            "Tên thể loại phải có ít nhất 2 ký tự.";

    } elseif (mb_strlen($name) > 100) {

        $errors["name"] =
            "Tên thể loại không được vượt quá 100 ký tự.";
    }


    // =====================================================
    // 4. VALIDATE MÔ TẢ
    // =====================================================

    if (mb_strlen($description) > 500) {

        $errors["description"] =
            "Mô tả không được vượt quá 500 ký tự.";
    }


    // =====================================================
    // 5. VALIDATE TRẠNG THÁI
    // =====================================================

    if ($status !== 0 && $status !== 1) {

        $errors["status"] =
            "Trạng thái không hợp lệ.";
    }


    // =====================================================
    // 6. KIỂM TRA THỂ LOẠI ĐÃ TỒN TẠI
    // =====================================================

    if (!isset($errors["category_id"]) &&
        !isset($errors["name"])) {

        $stmt = $conn->prepare(
            "SELECT COUNT(*)
             FROM genres
             WHERE category_id = :category_id
             AND name = :name"
        );

        $stmt->execute([
            ":category_id" => $category_id,
            ":name" => $name
        ]);

        if ($stmt->fetchColumn() > 0) {

            $errors["name"] =
                "Thể loại này đã tồn tại trong danh mục đã chọn.";
        }
    }


    // =====================================================
    // 7. NẾU KHÔNG CÓ LỖI → INSERT
    // =====================================================

    if (empty($errors)) {

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


// =====================================================
// LẤY DANH SÁCH DANH MỤC
// =====================================================

$sql = "SELECT *
        FROM categories
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
            margin-top: 6px;
            font-size: 14px;
        }

        .input-error {
            border: 1px solid red;
        }

    </style>

</head>

<body>

<div class="container">

    <h1>➕ Thêm thể loại sách</h1>


    <form method="POST">


        <!-- ==============================
             DANH MỤC
        =============================== -->

        <label>Danh mục</label>

        <select
            name="category_id"
            class="<?= isset($errors["category_id"]) ? 'input-error' : '' ?>"
        >

            <option value="">
                -- Chọn danh mục --
            </option>

            <?php foreach ($categories as $category): ?>

                <option
                    value="<?= htmlspecialchars($category['id'], ENT_QUOTES, 'UTF-8') ?>"
                    <?= (string)$category_id === (string)$category['id'] ? "selected" : "" ?>
                >

                    <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?>

                </option>

            <?php endforeach; ?>

        </select>

        <?php if (isset($errors["category_id"])): ?>

            <div class="error">
                <?= htmlspecialchars($errors["category_id"], ENT_QUOTES, 'UTF-8') ?>
            </div>

        <?php endif; ?>


        <!-- ==============================
             TÊN THỂ LOẠI
        =============================== -->

        <label>Tên thể loại</label>

        <input
            type="text"
            name="name"
            placeholder="Ví dụ: Tiểu thuyết"
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
            placeholder="Nhập mô tả thể loại..."
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
             NÚT
        =============================== -->

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