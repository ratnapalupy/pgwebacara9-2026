<?php
require '../koneksi.php';

$id           = $_POST['id'];
$kategori     = trim($_POST['kategori']);
$keterangan   = trim($_POST['keterangan']);
$nama_pemilik = trim($_POST['nama_pemilik']);
$kecamatan    = trim($_POST['kecamatan']);
$desa         = trim($_POST['desa']);
$dusun        = trim($_POST['dusun']);
$dokumentasi  = trim($_POST['dokumentasi']);
$point_x      = $_POST['point_x'];
$point_y      = $_POST['point_y'];
$longitude    = $_POST['longitude'];
$latitude     = $_POST['latitude'];

if ($nama_pemilik === '') $nama_pemilik = '-';

if (!ctype_digit($id) || $kategori === '' || $keterangan === '' || $kecamatan === '' || $desa === '' || $dusun === ''
    || !is_numeric($point_x) || !is_numeric($point_y)
    || !is_numeric($longitude) || $longitude < -180 || $longitude > 180
    || !is_numeric($latitude)  || $latitude < -90   || $latitude > 90) {
    die("Data tidak valid. <a href='javascript:history.back()'>Kembali</a>");
}

$stmt = $conn->prepare(
    "UPDATE warisan_budaya SET kategori = ?, keterangan = ?, nama_pemilik = ?, kecamatan = ?,
    desa = ?, dusun = ?, dokumentasi = ?, point_x = ?, point_y = ?, longitude = ?, latitude = ?
    WHERE ogc_fid = ?"
);
$stmt->bind_param("sssssssddddi", $kategori, $keterangan, $nama_pemilik, $kecamatan,
                $desa, $dusun, $dokumentasi, $point_x, $point_y, $longitude, $latitude, $id);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: ../index.php");   // jangan ada echo sebelum header()
    exit;
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>