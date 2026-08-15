<?php

require_once "../config/database.php";

$sql = "SELECT
            books.id,
            books.name,
            books.author,
            books.description,
            books.price,
            books.quantity,
            books.status,
            books.created_at,

            genres.name AS genre_name,
            categories.name AS category_name

        FROM books

        LEFT JOIN genres
            ON books.genre_id = genres.id

        LEFT JOIN categories
            ON genres.category_id = categories.id

        ORDER BY books.id DESC";

$stmt = $conn->query($sql);

$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <title>Quản lý sách</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 30px;
        }

        .container {
            max-width: 1400px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
        }

        h1 {
            margin-bottom: 20px;
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

        .active {
            color: green;
            font-weight: bold;
        }

        .inactive {
            color: red;
            font-weight: bold;
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

<div class="container">

    <h1>📚 Quản lý sách</h1>

    <a href="create.php" class="btn">
        + Thêm sách
    </a>

    <table>

        <tr>

            <th>ID</th>

            <th>Tên sách</th>

            <th>Danh mục</th>

            <th>Thể loại</th>

            <th>Tác giả</th>

            <th>Giá</th>

            <th>Số lượng</th>

            <th>Trạng thái</th>

            <th>Ngày tạo</th>

            <th>Thao tác</th>

        </tr>


        <?php foreach ($books as $book): ?>

            <tr>

                <td>
                    <?= $book['id'] ?>
                </td>

                <td>
                    <?= htmlspecialchars($book['name']) ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        $book['category_name'] ?? 'Chưa có'
                    ) ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        $book['genre_name'] ?? 'Chưa có'
                    ) ?>
                </td>

                <td>
                    <?= htmlspecialchars($book['author']) ?>
                </td>

                <td>
                    <?= number_format(
                        $book['price'],
                        0,
                        ',',
                        '.'
                    ) ?> VNĐ
                </td>

                <td>
                    <?= $book['quantity'] ?>
                </td>

                <td>

                    <?php if ($book['status'] == 1): ?>

                        <span class="active">
                            Đang hoạt động
                        </span>

                    <?php else: ?>

                        <span class="inactive">
                            Ngừng hoạt động
                        </span>

                    <?php endif; ?>

                </td>

                <td>
                    <?= $book['created_at'] ?>
                </td>

                <td>

                    <a
                        href="edit.php?id=<?= $book['id'] ?>"
                        class="edit"
                    >
                        Sửa
                    </a>

                    |

                    <a
                        href="delete.php?id=<?= $book['id'] ?>"
                        class="delete"
                        onclick="return confirm('Bạn có chắc muốn xóa sách này không?')"
                    >
                        Xóa
                    </a>

                </td>

            </tr>

        <?php endforeach; ?>

    </table>

</div>

</body>

</html>