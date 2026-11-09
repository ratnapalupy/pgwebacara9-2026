<?php
require '../koneksi.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = $conn->prepare("SELECT * FROM warisan_budaya WHERE ogc_fid = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Form Edit</title>
<link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="kotak">
  <h2>Form Edit</h2>
<?php if ($row) { ?>
  <form action="edit.php" onsubmit="return validateForm(event)" method="post">
    <input type="hidden" name="id" value="<?= (int) $row['ogc_fid'] ?>">
    <label for="kategori">Kategori:</label>
    <input type="text" id="kategori" name="kategori" value="<?= htmlspecialchars($row['kategori']) ?>">
    <label for="keterangan">Keterangan:</label>
    <input type="text" id="keterangan" name="keterangan" value="<?= htmlspecialchars($row['keterangan']) ?>">
    <label for="nama_pemilik">Nama Pemilik (boleh kosong):</label>
    <input type="text" id="nama_pemilik" name="nama_pemilik" value="<?= htmlspecialchars($row['nama_pemilik']) ?>">
    <label for="kecamatan">Kecamatan:</label>
    <input type="text" id="kecamatan" name="kecamatan" value="<?= htmlspecialchars($row['kecamatan']) ?>">
    <label for="desa">Desa:</label>
    <input type="text" id="desa" name="desa" value="<?= htmlspecialchars($row['desa']) ?>">
    <label for="dusun">Dusun:</label>
    <input type="text" id="dusun" name="dusun" value="<?= htmlspecialchars($row['dusun']) ?>">
    <label for="dokumentasi">Dokumentasi (link atau nama file, boleh kosong):</label>
    <input type="text" id="dokumentasi" name="dokumentasi" value="<?= htmlspecialchars((string) $row['dokumentasi']) ?>">
    <label for="point_x">Point X:</label>
    <input type="text" id="point_x" name="point_x" value="<?= htmlspecialchars($row['point_x']) ?>">
    <label for="point_y">Point Y:</label>
    <input type="text" id="point_y" name="point_y" value="<?= htmlspecialchars($row['point_y']) ?>">
    <label for="longitude">Longitude:</label>
    <input type="text" id="longitude" name="longitude" value="<?= htmlspecialchars($row['longitude']) ?>">
    <label for="latitude">Latitude:</label>
    <input type="text" id="latitude" name="latitude" value="<?= htmlspecialchars($row['latitude']) ?>">
    <input type="submit" value="Submit">
  </form>
<?php } else { ?>
  <p class="pesan">Data tidak ditemukan.</p>
<?php } ?>
  <p id="informasi"></p>
  <a class="kembali" href="../index.php">Kembali ke tabel</a>
</div>
<script src="../validasi.js"></script>
</body>
</html>