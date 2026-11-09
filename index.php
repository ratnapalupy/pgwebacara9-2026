<?php
require 'koneksi.php';

$result = $conn->query("SELECT * FROM warisan_budaya ORDER BY ogc_fid");

function tampilDokumentasi($d) {
    if ($d === null || $d === '') return '-';
    if (preg_match('#^https?://#i', $d)) {
        return "<a href='" . htmlspecialchars($d) . "' target='_blank' rel='noopener'>Buka</a>";
    }
    return htmlspecialchars($d);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Data Warisan Budaya Takbenda</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<h2 class="judul">Persebaran Warisan Budaya Takbenda Kabupaten Sleman Tahun 2025</h2>
<a class="tombol" href="input/index.html">Input Data</a>

<div class="gulir">
<?php if ($result->num_rows > 0) { ?>
<table class="data">
  <tr>
    <th>Kategori</th>
    <th>Keterangan</th>
    <th>Nama Pemilik</th>
    <th>Kecamatan</th>
    <th>Desa</th>
    <th>Dusun</th>
    <th>Dokumentasi</th>
    <th>Point X</th>
    <th>Point Y</th>
    <th>Longitude</th>
    <th>Latitude</th>
    <th colspan="2">Aksi</th>
  </tr>
  <?php while ($row = $result->fetch_assoc()) { ?>
  <tr>
    <td><?= htmlspecialchars($row['kategori']) ?></td>
    <td><?= htmlspecialchars($row['keterangan']) ?></td>
    <td><?= htmlspecialchars($row['nama_pemilik']) ?></td>
    <td><?= htmlspecialchars($row['kecamatan']) ?></td>
    <td><?= htmlspecialchars($row['desa']) ?></td>
    <td><?= htmlspecialchars($row['dusun']) ?></td>
    <td><?= tampilDokumentasi($row['dokumentasi']) ?></td>
    <td align="right"><?= $row['point_x'] ?></td>
    <td align="right"><?= $row['point_y'] ?></td>
    <td align="right"><?= $row['longitude'] ?></td>
    <td align="right"><?= $row['latitude'] ?></td>
    <td><a class="aksi-hapus" href="delete.php?id=<?= (int) $row['ogc_fid'] ?>" onclick="return confirm('Yakin ingin menghapus data ini?')">hapus</a></td>
    <td><a class="aksi-edit" href="edit/index.php?id=<?= (int) $row['ogc_fid'] ?>">edit</a></td>
  </tr>
  <?php } ?>
</table>
<?php } else { ?>
  <p>0 results</p>
<?php } ?>
</div>
</body>
</html>
<?php $conn->close(); ?>