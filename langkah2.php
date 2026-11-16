<?php
require 'koneksi.php';

$result = $conn->query("SELECT * FROM warisan_budaya
                        WHERE longitude IS NOT NULL AND latitude IS NOT NULL");
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'id'          => (int) $row['ogc_fid'],
        'kategori'    => $row['kategori'],
        'keterangan'  => $row['keterangan'],
        'pemilik'     => $row['nama_pemilik'],
        'kecamatan'   => $row['kecamatan'],
        'desa'        => $row['desa'],
        'dusun'       => $row['dusun'],
        'dokumentasi' => (string) $row['dokumentasi'],
        'lon'         => (float) $row['longitude'],
        'lat'         => (float) $row['latitude']
    ];
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Latihan Web GIS - Titik dari Database</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
html, body, #map {
  height: 100%;
  width: 100%;
  margin: 0px;
}
</style>
</head>
<body>
<div id="map"></div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>

var data = <?= json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

var map = L.map('map').setView([-7.7, 110.4], 11);

L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
  attribution: 'Tiles &copy; Esri'
}).addTo(map);

function esc(t) {
  return String(t).replace(/[&<>"']/g, function (c) {
    return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[c];
  });
}

var titik = L.featureGroup();
data.forEach(function (d) {
  L.marker([d.lat, d.lon])
    .bindPopup('<b>' + esc(d.keterangan) + '</b>' +
               '<br>Kategori: ' + esc(d.kategori) +
               '<br>Kecamatan: ' + esc(d.kecamatan))
    .addTo(titik);
});
titik.addTo(map);

if (data.length > 0) {
  map.fitBounds(titik.getBounds().pad(0.2), {maxZoom: 13});
}
</script>
</body>
</html>