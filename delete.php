<?php
require 'koneksi.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = $conn->prepare("DELETE FROM warisan_budaya WHERE ogc_fid = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: index.php");   // jangan ada echo sebelum header()
    exit;
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>