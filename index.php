<?php
require 'koneksi.php';

$bacaSaja = isset($bacaSaja) ? $bacaSaja : false;

$result = $conn->query("SELECT * FROM warisan_budaya ORDER BY ogc_fid");
$rows = [];
while ($r = $result->fetch_assoc()) {
    $rows[] = $r;
}
$conn->close();

$data = [];
foreach ($rows as $r) {
    if ($r['longitude'] === null || $r['latitude'] === null) continue;
    $data[] = [
        'id'          => (int) $r['ogc_fid'],
        'kategori'    => $r['kategori'],
        'keterangan'  => $r['keterangan'],
        'pemilik'     => $r['nama_pemilik'],
        'kecamatan'   => $r['kecamatan'],
        'desa'        => $r['desa'],
        'dusun'       => $r['dusun'],
        'dokumentasi' => (string) $r['dokumentasi'],
        'lon'         => (float) $r['longitude'],
        'lat'         => (float) $r['latitude']
    ];
}

$jmlTitik     = count($rows);
$jmlKategori  = count(array_unique(array_column($rows, 'kategori')));
$jmlKecamatan = count(array_unique(array_column($rows, 'kecamatan')));
$jmlDesa      = count(array_unique(array_column($rows, 'desa')));

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
<title>Web GIS Warisan Budaya Takbenda Sleman</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<link rel="stylesheet" href="web.css">
</head>
<body>
<div class="bungkus">

  <header class="banner">
    <div class="banner-ikon">&#127917; &#128506;</div>
    <h1>Web GIS Warisan Budaya Takbenda Sleman</h1>
    <p>Persebaran Warisan Budaya Takbenda Kabupaten Sleman Tahun 2025</p>
    <div class="chip-baris">
      <span class="chip">Kabupaten Sleman</span>
      <span class="chip">Tahun 2025</span>
      <span class="chip">PHP &bull; MySQL &bull; LeafletJS</span>
    </div>
  </header>

  <section class="statistik">
    <div class="stat s1"><span class="ikon">&#128205;</span><div><div class="angka"><?= $jmlTitik ?></div><div class="label">Titik Warisan</div></div></div>
    <div class="stat s2"><span class="ikon">&#127991;</span><div><div class="angka"><?= $jmlKategori ?></div><div class="label">Kategori</div></div></div>
    <div class="stat s3"><span class="ikon">&#127960;</span><div><div class="angka"><?= $jmlKecamatan ?></div><div class="label">Kecamatan</div></div></div>
    <div class="stat s4"><span class="ikon">&#127969;</span><div><div class="angka"><?= $jmlDesa ?></div><div class="label">Desa</div></div></div>
  </section>

  <h2 class="judul-bagian">Peta Persebaran</h2>
  <div id="map"></div>

  <h2 class="judul-bagian">Data Tabel</h2>
  <?php if (!$bacaSaja) { ?>
  <a class="tombol" href="input/index.html">+ Input Data</a>
  <?php } ?>
  <p class="catatan">Klik salah satu baris tabel untuk menuju titiknya di peta.</p>

  <div class="gulir">
  <?php if (count($rows) > 0) { ?>
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
      <?php if (!$bacaSaja) { ?><th colspan="2">Aksi</th><?php } ?>
    </tr>
    <?php foreach ($rows as $r) { ?>
    <tr data-id="<?= (int) $r['ogc_fid'] ?>">
      <td><span class="kat" data-k="<?= htmlspecialchars($r['kategori']) ?>"><?= htmlspecialchars($r['kategori']) ?></span></td>
      <td><?= htmlspecialchars($r['keterangan']) ?></td>
      <td><?= htmlspecialchars($r['nama_pemilik']) ?></td>
      <td><?= htmlspecialchars($r['kecamatan']) ?></td>
      <td><?= htmlspecialchars($r['desa']) ?></td>
      <td><?= htmlspecialchars($r['dusun']) ?></td>
      <td><?= tampilDokumentasi($r['dokumentasi']) ?></td>
      <td align="right"><?= $r['point_x'] ?></td>
      <td align="right"><?= $r['point_y'] ?></td>
      <td align="right"><?= $r['longitude'] ?></td>
      <td align="right"><?= $r['latitude'] ?></td>
      <?php if (!$bacaSaja) { ?>
      <td><a class="aksi-hapus" href="delete.php?id=<?= (int) $r['ogc_fid'] ?>" onclick="return confirm('Yakin ingin menghapus data ini?')">hapus</a></td>
      <td><a class="aksi-edit" href="edit/index.php?id=<?= (int) $r['ogc_fid'] ?>">edit</a></td>
      <?php } ?>
    </tr>
    <?php } ?>
  </table>
  <?php } else { ?>
    <p>0 results</p>
  <?php } ?>
  </div>

  <div class="kaki">Web GIS Warisan Budaya Takbenda Sleman &bull; PGWeb Acara 10</div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
var data = <?= json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
</script>
<script src="peta.js"></script>
<script>

document.querySelectorAll('.kat').forEach(function (el) {
  el.style.setProperty('--w', warnaKategori(el.dataset.k));
});

document.querySelectorAll('table.data tr[data-id]').forEach(function (tr) {
  tr.addEventListener('click', function (e) {
    if (e.target.closest('a')) return;           // abaikan klik pada link
    document.getElementById('map').scrollIntoView({behavior: 'smooth'});
    terbangKe(tr.dataset.id);
  });
});
</script>
</body>
</html>