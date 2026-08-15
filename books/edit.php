<?php

require_once "../config/database.php";

$id = $_GET["id"] ?? 0;

/* Lấy thông tin sách */
$sql = "SELECT * FROM books WHERE id = :id";

$stmt = $conn->prepare($sql);

$stmt->execute([
    ":id" => $id
]);

$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    die("Không tìm thấy sách!");
}


/* Lấy danh sách danh mục */
$sql = "SELECT *
        FROM categories
        WHERE status = 1
        ORDER BY name ASC";

$stmt = $conn->query($sql);

$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* Khi bấm Cập nhật */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $genre_id = $_POST["genre_id"];
    $name = trim($_POST["name"]);
    $author = trim($_POST["author"]);
    $description = trim($_POST["description"]);
    $price = $_POST["price"];
    $quantity = $_POST["quantity"];
    $status = $_POST["status"];


    if ($genre_id == "" || $name == "" || $author == "") {

        $error = "Vui lòng nhập đầy đủ thông tin bắt buộc!";

    } else {

        $sql = "UPDATE books SET

                    genre_id = :genre_id,
                    name = :name,
                    author = :author,
                    description = :description,
                    price = :price,
                    quantity = :quantity,
                    status = :status

                WHERE id = :id";


        $stmt = $conn->prepare($sql);


        $stmt->execute([

            ":genre_id" => $genre_id,
            ":name" => $name,
            ":author" => $author,
            ":description" => $description,
            ":price" => $price,
            ":quantity" => $quantity,
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

    <title>Sửa sách</title>

    <style>

        body {
            font-family: Arial;
            background: #f5f5f5;
            padding: 30px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
        }

        h1 {
            margin-bottom: 30px;
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

    <h1>✏️ Sửa sách</h1>


    <?php if (isset($error)): ?>

        <p class="error">
            <?= htmlspecialchars($error) ?>
        </p>

    <?php endif; ?>


    <form method="POST">


        <!-- DANH MỤC -->

        <label>Danh mục</label>

        <select
            id="category_id"
            name="category_id"
            required
        >

            <option value="">
                -- Chọn danh mục --
            </option>


            <?php foreach ($categories as $category): ?>

                <option
                    value="<?= $category['id'] ?>"
                >

                    <?= htmlspecialchars($category['name']) ?>

                </option>

            <?php endforeach; ?>

        </select>


        <!-- THỂ LOẠI -->

        <label>Thể loại</label>

        <select
            id="genre_id"
            name="genre_id"
            required
        >

            <option value="">
                -- Chọn thể loại --
            </option>

        </select>


        <!-- TÊN SÁCH -->

        <label>Tên sách</label>

        <input
            type="text"
            name="name"
            value="<?= htmlspecialchars($book['name']) ?>"
            required
        >


        <!-- TÁC GIẢ -->

        <label>Tác giả</label>

        <input
            type="text"
            name="author"
            value="<?= htmlspecialchars($book['author']) ?>"
            required
        >


        <!-- MÔ TẢ -->

        <label>Mô tả</label>

        <textarea name="description"><?= htmlspecialchars($book['description'] ?? '') ?></textarea>


        <!-- GIÁ -->

        <label>Giá</label>

        <input
            type="number"
            name="price"
            value="<?= $book['price'] ?>"
            min="0"
        >


        <!-- SỐ LƯỢNG -->

        <label>Số lượng</label>

        <input
            type="number"
            name="quantity"
            value="<?= $book['quantity'] ?>"
            min="0"
        >


        <!-- TRẠNG THÁI -->

        <label>Trạng thái</label>

        <select name="status">

            <option
                value="1"
                <?= $book['status'] == 1 ? 'selected' : '' ?>
            >
                Đang hoạt động
            </option>

            <option
                value="0"
                <?= $book['status'] == 0 ? 'selected' : '' ?>
            >
                Ngừng hoạt động
            </option>

        </select>


        <button type="submit">
            Cập nhật sách
        </button>


        <a
            href="index.php"
            class="back"
        >
            Quay lại
        </a>


    </form>

</div>


<script>

const categorySelect =
    document.getElementById("category_id");

const genreSelect =
    document.getElementById("genre_id");

const currentGenreId =
    <?= (int)$book['genre_id'] ?>;


categorySelect.addEventListener("change", function () {

    const categoryId = this.value;


    genreSelect.innerHTML =
        '<option value="">-- Đang tải thể loại --</option>';


    if (!categoryId) {

        genreSelect.innerHTML =
            '<option value="">-- Chọn thể loại --</option>';

        return;
    }


    fetch(
        "../genres/get_genres.php?category_id="
        + categoryId
    )

    .then(response => response.json())

    .then(data => {

        genreSelect.innerHTML =
            '<option value="">-- Chọn thể loại --</option>';


        data.forEach(function (genre) {

            const option =
                document.createElement("option");


            option.value = genre.id;

            option.textContent = genre.name;


            if (genre.id == currentGenreId) {

                option.selected = true;

            }


            genreSelect.appendChild(option);

        });

    });

});

</script>

</body>

</html>