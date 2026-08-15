<?php

require_once "../config/database.php";

header("Content-Type: application/json; charset=UTF-8");

$category_id = $_GET["category_id"] ?? 0;

if (!$category_id) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT id, name
        FROM genres
        WHERE category_id = :category_id
        AND status = 1
        ORDER BY name ASC";

$stmt = $conn->prepare($sql);

$stmt->execute([
    ":category_id" => $category_id
]);

$genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($genres, JSON_UNESCAPED_UNICODE);