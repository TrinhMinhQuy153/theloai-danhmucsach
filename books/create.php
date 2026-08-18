<?php

require_once "../config/database.php";

$genre_id = "";
$name = "";
$author = "";
$description = "";
$price = 0;
$quantity = 0;
$status = 1;

$errors = [];


// =====================================================
// LẤY DANH SÁCH DANH MỤC
// =====================================================

$sql = "SELECT *
        FROM categories
        WHERE status = 1
        ORDER BY name";

$stmt = $conn->query($sql);

$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);


// =====================================================
// XỬ LÝ KHI BẤM THÊM SÁCH
// =====================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // =================================================
    // 1. NHẬN VÀ CHUẨN HÓA DỮ LIỆU
    // =================================================

    $genre_id = trim($_POST["genre_id"] ?? "");
    $name = trim($_POST["name"] ?? "");
    $author = trim($_POST["author"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = $_POST["price"] ?? 0;
    $quantity = $_POST["quantity"] ?? 0;
    $status = isset($_POST["status"]) ? (int) $_POST["status"] : 0;


    // =================================================
    // 2. VALIDATE THỂ LOẠI
    // =================================================

    if ($genre_id === "") {

        $errors["genre_id"] = "Vui lòng chọn thể loại.";

    } elseif (!filter_var($genre_id, FILTER_VALIDATE_INT) ||
              $genre_id <= 0) {

        $errors["genre_id"] = "Thể loại không hợp lệ.";

    } else {

        // Kiểm tra thể loại có tồn tại không
        $stmt = $conn->prepare(
            "SELECT COUNT(*)
             FROM genres
             WHERE id = :id
             AND status = 1"
        );

        $stmt->execute([
            ":id" => $genre_id
        ]);

        if ($stmt->fetchColumn() == 0) {

            $errors["genre_id"] =
                "Thể loại không tồn tại hoặc đã ngừng hoạt động.";
        }
    }


    // =================================================
    // 3. VALIDATE TÊN SÁCH
    // =================================================

    if ($name === "") {

        $errors["name"] =
            "Vui lòng nhập tên sách.";

    } elseif (mb_strlen($name) < 2) {

        $errors["name"] =
            "Tên sách phải có ít nhất 2 ký tự.";

    } elseif (mb_strlen($name) > 200) {

        $errors["name"] =
            "Tên sách không được vượt quá 200 ký tự.";
    }


    // =================================================
    // 4. VALIDATE TÁC GIẢ
    // =================================================

    if ($author === "") {

        $errors["author"] =
            "Vui lòng nhập tên tác giả.";

    } elseif (mb_strlen($author) < 2) {

        $errors["author"] =
            "Tên tác giả phải có ít nhất 2 ký tự.";

    } elseif (mb_strlen($author) > 150) {

        $errors["author"] =
            "Tên tác giả không được vượt quá 150 ký tự.";
    }


    // =================================================
    // 5. VALIDATE MÔ TẢ
    // =================================================

    if (mb_strlen($description) > 1000) {

        $errors["description"] =
            "Mô tả không được vượt quá 1000 ký tự.";
    }


    // =================================================
    // 6. VALIDATE GIÁ
    // =================================================

    if (!is_numeric($price)) {

        $errors["price"] =
            "Giá sách phải là số.";

    } elseif ($price < 0) {

        $errors["price"] =
            "Giá sách không được nhỏ hơn 0.";
    }


    // =================================================
    // 7. VALIDATE SỐ LƯỢNG
    // =================================================

    if (
        filter_var($quantity, FILTER_VALIDATE_INT) === false
        || $quantity < 0
    ) {

        $errors["quantity"] =
            "Số lượng phải là số nguyên lớn hơn hoặc bằng 0.";
    }


    // =================================================
    // 8. VALIDATE TRẠNG THÁI
    // =================================================

    if ($status !== 0 && $status !== 1) {

        $errors["status"] =
            "Trạng thái không hợp lệ.";
    }


    // =================================================
    // 9. KIỂM TRA TÊN SÁCH ĐÃ TỒN TẠI
    // =================================================

    if (!isset($errors["genre_id"]) &&
        !isset($errors["name"])) {

        $stmt = $conn->prepare(
            "SELECT COUNT(*)
             FROM books
             WHERE genre_id = :genre_id
             AND name = :name"
        );

        $stmt->execute([
            ":genre_id" => $genre_id,
            ":name" => $name
        ]);

        if ($stmt->fetchColumn() > 0) {

            $errors["name"] =
                "Tên sách này đã tồn tại trong thể loại đã chọn.";
        }
    }


    // =================================================
    // 10. NẾU KHÔNG CÓ LỖI → INSERT DATABASE
    // =================================================

    if (empty($errors)) {

        $sql = "INSERT INTO books
                (
                    genre_id,
                    name,
                    author,
                    description,
                    price,
                    quantity,
                    status
                )
                VALUES
                (
                    :genre_id,
                    :name,
                    :author,
                    :description,
                    :price,
                    :quantity,
                    :status
                )";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            ":genre_id" => $genre_id,
            ":name" => $name,
            ":author" => $author,
            ":description" => $description,
            ":price" => $price,
            ":quantity" => $quantity,
            ":status" => $status
        ]);


        // Thêm thành công
        header("Location: index.php");
        exit;
    }
}

?>

<!DOCTYPE html>

<html lang="vi">

<head>

    <meta charset="UTF-8">

    <title>Thêm sách</title>

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

    <h1>➕ Thêm sách</h1>


    <form method="POST">


        <!-- =========================================
             DANH MỤC
        ========================================== -->

        <label>Danh mục</label>

        <select id="category_id">

            <option value="">
                -- Chọn danh mục --
            </option>

            <?php foreach ($categories as $category): ?>

                <option
                    value="<?= htmlspecialchars(
                        $category['id'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >

                    <?= htmlspecialchars(
                        $category['name'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </option>

            <?php endforeach; ?>

        </select>


        <!-- =========================================
             THỂ LOẠI
        ========================================== -->

        <label>Thể loại</label>

        <select
            name="genre_id"
            id="genre_id"
            class="<?= isset($errors["genre_id"])
                ? 'input-error'
                : '' ?>"
        >

            <option value="">
                -- Chọn thể loại --
            </option>

        </select>

        <?php if (isset($errors["genre_id"])): ?>

            <div class="error">

                <?= htmlspecialchars(
                    $errors["genre_id"],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        <?php endif; ?>


        <!-- =========================================
             TÊN SÁCH
        ========================================== -->

        <label>Tên sách</label>

        <input
            type="text"
            name="name"
            value="<?= htmlspecialchars(
                $name,
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            class="<?= isset($errors["name"])
                ? 'input-error'
                : '' ?>"
            placeholder="Nhập tên sách..."
        >

        <?php if (isset($errors["name"])): ?>

            <div class="error">

                <?= htmlspecialchars(
                    $errors["name"],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        <?php endif; ?>


        <!-- =========================================
             TÁC GIẢ
        ========================================== -->

        <label>Tác giả</label>

        <input
            type="text"
            name="author"
            value="<?= htmlspecialchars(
                $author,
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            class="<?= isset($errors["author"])
                ? 'input-error'
                : '' ?>"
            placeholder="Nhập tên tác giả..."
        >

        <?php if (isset($errors["author"])): ?>

            <div class="error">

                <?= htmlspecialchars(
                    $errors["author"],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        <?php endif; ?>


        <!-- =========================================
             MÔ TẢ
        ========================================== -->

        <label>Mô tả</label>

        <textarea
            name="description"
            class="<?= isset($errors["description"])
                ? 'input-error'
                : '' ?>"
            placeholder="Nhập mô tả..."
        ><?= htmlspecialchars(
            $description,
            ENT_QUOTES,
            'UTF-8'
        ) ?></textarea>

        <?php if (isset($errors["description"])): ?>

            <div class="error">

                <?= htmlspecialchars(
                    $errors["description"],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        <?php endif; ?>


        <!-- =========================================
             GIÁ
        ========================================== -->

        <label>Giá sách</label>

        <input
            type="number"
            name="price"
            min="0"
            step="0.01"
            value="<?= htmlspecialchars(
                $price,
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            class="<?= isset($errors["price"])
                ? 'input-error'
                : '' ?>"
        >

        <?php if (isset($errors["price"])): ?>

            <div class="error">

                <?= htmlspecialchars(
                    $errors["price"],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        <?php endif; ?>


        <!-- =========================================
             SỐ LƯỢNG
        ========================================== -->

        <label>Số lượng</label>

        <input
            type="number"
            name="quantity"
            min="0"
            step="1"
            value="<?= htmlspecialchars(
                $quantity,
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            class="<?= isset($errors["quantity"])
                ? 'input-error'
                : '' ?>"
        >

        <?php if (isset($errors["quantity"])): ?>

            <div class="error">

                <?= htmlspecialchars(
                    $errors["quantity"],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        <?php endif; ?>


        <!-- =========================================
             TRẠNG THÁI
        ========================================== -->

        <label>Trạng thái</label>

        <select
            name="status"
            class="<?= isset($errors["status"])
                ? 'input-error'
                : '' ?>"
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
                Ngừng hoạt động
            </option>

        </select>

        <?php if (isset($errors["status"])): ?>

            <div class="error">

                <?= htmlspecialchars(
                    $errors["status"],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        <?php endif; ?>


        <!-- =========================================
             NÚT
        ========================================== -->

        <button type="submit">
            Thêm sách
        </button>

        <a href="index.php" class="back">
            Quay lại
        </a>

    </form>

</div>


<script>

document
    .getElementById("category_id")
    .addEventListener("change", function() {

        let categoryId = this.value;

        let genreSelect =
            document.getElementById("genre_id");

        genreSelect.innerHTML =
            '<option value="">-- Đang tải thể loại --</option>';


        if (categoryId === "") {

            genreSelect.innerHTML =
                '<option value="">-- Chọn thể loại --</option>';

            return;
        }


        fetch(
            "../genres/get_genres.php?category_id="
            + encodeURIComponent(categoryId)
        )

        .then(response => response.json())

        .then(data => {

            genreSelect.innerHTML =
                '<option value="">-- Chọn thể loại --</option>';


            data.forEach(function(genre) {

                let option =
                    document.createElement("option");

                option.value = genre.id;

                option.textContent = genre.name;

                genreSelect.appendChild(option);

            });

        })

        .catch(error => {

            genreSelect.innerHTML =
                '<option value="">-- Không tải được thể loại --</option>';

        });

    });

</script>

</body>

</html>