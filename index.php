<?php

$name = "";
$email = "";
$subject = "";
$content = "";

$errors = [
    "name" => "",
    "email" => "",
    "subject" => "",
    "content" => "",
    "avatar" => ""
];

$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // =====================================================
    // 1. ĐỌC VÀ CHUẨN HÓA DỮ LIỆU
    // =====================================================

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $subject = trim($_POST["subject"] ?? "");
    $content = trim($_POST["content"] ?? "");


    // =====================================================
    // 2. VALIDATE HỌ TÊN
    // =====================================================

    if ($name === "") {

        $errors["name"] = "Họ tên không được để trống.";

    } elseif (mb_strlen($name) < 2 || mb_strlen($name) > 50) {

        $errors["name"] = "Họ tên phải từ 2 đến 50 ký tự.";
    }


    // =====================================================
    // 3. VALIDATE EMAIL
    // =====================================================

    if ($email === "") {

        $errors["email"] = "Email không được để trống.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $errors["email"] = "Email không đúng định dạng.";
    }


    // =====================================================
    // 4. VALIDATE CHỦ ĐỀ
    // =====================================================

    $allowedSubjects = [
        "Hỗ trợ kỹ thuật",
        "Góp ý",
        "Tư vấn"
    ];

    if (!in_array($subject, $allowedSubjects, true)) {

        $errors["subject"] = "Chủ đề không hợp lệ.";
    }


    // =====================================================
    // 5. VALIDATE NỘI DUNG
    // =====================================================

    if ($content === "") {

        $errors["content"] = "Nội dung không được để trống.";

    } elseif (mb_strlen($content) < 10) {

        $errors["content"] = "Nội dung phải từ 10 đến 500 ký tự.";

    } elseif (mb_strlen($content) > 500) {

        $errors["content"] = "Nội dung phải từ 10 đến 500 ký tự.";
    }


    // =====================================================
    // 6. VALIDATE ẢNH
    // =====================================================

    if (
        isset($_FILES["avatar"]) &&
        $_FILES["avatar"]["error"] !== UPLOAD_ERR_NO_FILE
    ) {

        // Kiểm tra upload có lỗi không
        if ($_FILES["avatar"]["error"] !== UPLOAD_ERR_OK) {

            $errors["avatar"] = "Không thể tải ảnh lên.";

        } else {

            $tmpName = $_FILES["avatar"]["tmp_name"];
            $fileSize = $_FILES["avatar"]["size"];
            $fileName = $_FILES["avatar"]["name"];

            // Giới hạn dung lượng 2MB
            if ($fileSize > 2 * 1024 * 1024) {

                $errors["avatar"] = "Ảnh không được vượt quá 2MB.";

            } else {

                // Kiểm tra ảnh thực sự có phải là ảnh không
                $imageInfo = getimagesize($tmpName);

                if ($imageInfo === false) {

                    $errors["avatar"] = "File tải lên không phải là ảnh hợp lệ.";

                } else {

                    // Kiểm tra MIME type
                    $allowedMimeTypes = [
                        "image/jpeg",
                        "image/png",
                        "image/gif"
                    ];

                    if (!in_array($imageInfo["mime"], $allowedMimeTypes, true)) {

                        $errors["avatar"] =
                            "Ảnh chỉ được phép có định dạng JPG, JPEG, PNG hoặc GIF.";
                    }
                }
            }
        }
    }


    // =====================================================
    // 7. NẾU KHÔNG CÓ LỖI
    // =====================================================

    $hasErrors = false;

    foreach ($errors as $error) {

        if ($error !== "") {
            $hasErrors = true;
            break;
        }
    }

    if (!$hasErrors) {

        /*
         * Bài 3 chưa yêu cầu lưu dữ liệu vào Database.
         * Vì vậy chỉ thông báo thành công.
         */

        $success = true;

        // Xóa dữ liệu sau khi gửi thành công
        $name = "";
        $email = "";
        $subject = "";
        $content = "";
    }
}

?>

<!DOCTYPE html>

<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Liên hệ</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #eef4fb;
            color: #1f2937;
            min-height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 700px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .form-box {
            background: white;
            padding: 40px 45px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        h1 {
            text-align: center;
            font-size: 36px;
            margin-bottom: 10px;
            color: #1f2937;
        }

        .description {
            text-align: center;
            color: #6b7280;
            margin-bottom: 35px;
        }

        .form-group {
            margin-bottom: 22px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #374151;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            font-size: 16px;
            outline: none;
            transition: 0.2s;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
        }

        textarea {
            height: 170px;
            resize: vertical;
        }

        .note {
            margin-top: 8px;
            font-size: 14px;
            color: #6b7280;
        }

        .field-error {
            margin-top: 7px;
            color: #dc2626;
            font-size: 14px;
        }

        .input-error {
            border-color: #dc2626;
        }

        button {
            width: 100%;
            padding: 15px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 7px;
            font-size: 17px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
        }

        button:hover {
            background: #1d4ed8;
        }

        .success-box {
            background: #dcfce7;
            color: #15803d;
            padding: 15px;
            border-radius: 7px;
            margin-bottom: 20px;
        }

        .footer {
            text-align: center;
            margin-top: 25px;
            color: #6b7280;
            font-size: 14px;
        }

        @media (max-width: 600px) {

            .container {
                margin: 20px auto;
            }

            .form-box {
                padding: 25px 20px;
            }

            h1 {
                font-size: 30px;
            }
        }

    </style>

</head>

<body>

<div class="container">

    <div class="form-box">

        <h1>Liên hệ</h1>

        <p class="description">
            Vui lòng nhập đầy đủ thông tin bên dưới.
        </p>


        <!-- =================================================
             THÔNG BÁO THÀNH CÔNG
        ================================================== -->

        <?php if ($success): ?>

            <div class="success-box">
                Gửi liên hệ thành công!
            </div>

        <?php endif; ?>


        <form method="POST" enctype="multipart/form-data">


            <!-- =================================================
                 HỌ TÊN
            ================================================== -->

            <div class="form-group">

                <label>Họ tên</label>

                <input
                    type="text"
                    name="name"
                    placeholder="Nhập họ tên..."
                    value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>"
                    class="<?= $errors["name"] !== "" ? 'input-error' : '' ?>"
                >

                <?php if ($errors["name"] !== ""): ?>

                    <div class="field-error">
                        <?= htmlspecialchars($errors["name"], ENT_QUOTES, 'UTF-8') ?>
                    </div>

                <?php endif; ?>

            </div>


            <!-- =================================================
                 EMAIL
            ================================================== -->

            <div class="form-group">

                <label>Email</label>

                <input
                    type="email"
                    name="email"
                    placeholder="Nhập email..."
                    value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                    class="<?= $errors["email"] !== "" ? 'input-error' : '' ?>"
                >

                <?php if ($errors["email"] !== ""): ?>

                    <div class="field-error">
                        <?= htmlspecialchars($errors["email"], ENT_QUOTES, 'UTF-8') ?>
                    </div>

                <?php endif; ?>

            </div>


            <!-- =================================================
                 CHỦ ĐỀ
            ================================================== -->

            <div class="form-group">

                <label>Chủ đề</label>

                <select
                    name="subject"
                    class="<?= $errors["subject"] !== "" ? 'input-error' : '' ?>"
                >

                    <option
                        value="Hỗ trợ kỹ thuật"
                        <?= $subject === "Hỗ trợ kỹ thuật" ? "selected" : "" ?>
                    >
                        Hỗ trợ kỹ thuật
                    </option>

                    <option
                        value="Góp ý"
                        <?= $subject === "Góp ý" ? "selected" : "" ?>
                    >
                        Góp ý
                    </option>

                    <option
                        value="Tư vấn"
                        <?= $subject === "Tư vấn" ? "selected" : "" ?>
                    >
                        Tư vấn
                    </option>

                </select>

                <?php if ($errors["subject"] !== ""): ?>

                    <div class="field-error">
                        <?= htmlspecialchars($errors["subject"], ENT_QUOTES, 'UTF-8') ?>
                    </div>

                <?php endif; ?>

            </div>


            <!-- =================================================
                 NỘI DUNG
            ================================================== -->

            <div class="form-group">

                <label>Nội dung</label>

                <textarea
                    name="content"
                    placeholder="Nhập nội dung liên hệ..."
                    class="<?= $errors["content"] !== "" ? 'input-error' : '' ?>"
                ><?= htmlspecialchars($content, ENT_QUOTES, 'UTF-8') ?></textarea>

                <div class="note">
                    Nội dung phải từ 10 đến 500 ký tự.
                </div>

                <?php if ($errors["content"] !== ""): ?>

                    <div class="field-error">
                        <?= htmlspecialchars($errors["content"], ENT_QUOTES, 'UTF-8') ?>
                    </div>

                <?php endif; ?>

            </div>


            <!-- =================================================
                 ẢNH ĐẠI DIỆN
            ================================================== -->

            <div class="form-group">

                <label>Ảnh đại diện</label>

                <input
                    type="file"
                    name="avatar"
                    accept=".jpg,.jpeg,.png,.gif"
                    class="<?= $errors["avatar"] !== "" ? 'input-error' : '' ?>"
                >

                <?php if ($errors["avatar"] !== ""): ?>

                    <div class="field-error">
                        <?= htmlspecialchars($errors["avatar"], ENT_QUOTES, 'UTF-8') ?>
                    </div>

                <?php endif; ?>

                <div class="note">
                    Cho phép JPG, JPEG, PNG, GIF và dung lượng tối đa 2MB.
                </div>

            </div>


            <!-- =================================================
                 NÚT GỬI
            ================================================== -->

            <button type="submit">
                Gửi liên hệ
            </button>

        </form>

    </div>


    <div class="footer">
        PHP • MySQL • PDO • MVC • Security • Fetch API
    </div>

</div>

</body>

</html>