function esc(t) {
  return String(t).replace(/[&<>"']/g, function (c) {
    return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[c];
  });
}
function buatKotak() {
  var div = L.DomUtil.create('div');
  div.style.cssText = 'background:#fff;padding:6px 10px;border-radius:6px;' +
                      'box-shadow:0 1px 5px rgba(0,0,0,.4);font:13px Arial,sans-serif;';
  return div;
}
function isiPopup(d) {
  var dok = '-';
  if (/^https?:\/\//i.test(d.dokumentasi)) {
    dok = '<a href="' + esc(d.dokumentasi) + '" target="_blank" rel="noopener">Buka dokumentasi</a>';
  } else if (d.dokumentasi) {
    dok = esc(d.dokumentasi);
  }
  return '<b>' + esc(d.keterangan) + '</b>' +
         '<br>Kategori: ' + esc(d.kategori) +
         '<br>Pemilik: ' + esc(d.pemilik) +
         '<br>Lokasi: ' + esc(d.dusun) + ', ' + esc(d.desa) + ', ' + esc(d.kecamatan) +
         '<br>Dokumentasi: ' + dok;
}

var map = L.map('map');

var basemap1 = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
  attribution: 'Tiles &copy; Esri'
});
var basemap2 = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
  maxZoom: 20,
  subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
  attribution: 'Map data &copy; Google'
});
var basemap3 = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 19,
  attribution: '&copy; OpenStreetMap contributors'
});
basemap1.addTo(map);

var palet = ['#db2777', '#2563eb', '#16a34a', '#f59e0b', '#7c3aed', '#0891b2', '#dc2626', '#65a30d'];
var daftarKategori = [];
data.forEach(function (d) {
  if (daftarKategori.indexOf(d.kategori) === -1) daftarKategori.push(d.kategori);
});
function warnaKategori(k) {
  return palet[daftarKategori.indexOf(k) % palet.length];
}

var layerKategori = {};
daftarKategori.forEach(function (k) {
  layerKategori[k] = L.layerGroup().addTo(map);
});

var markerById = {};
data.forEach(function (d) {
  var m = L.circleMarker([d.lat, d.lon], {
    radius: 9,
    color: '#ffffff',
    weight: 2,
    fillColor: warnaKategori(d.kategori),
    fillOpacity: 0.95
  });
  m.kategori = d.kategori;
  m.bindPopup(isiPopup(d));
  m.bindTooltip(esc(d.keterangan));
  m.addTo(layerKategori[d.kategori]);
  markerById[d.id] = m;
});

if (data.length > 0) {
  var batas = L.latLngBounds(data.map(function (d) { return [d.lat, d.lon]; }));
  map.fitBounds(batas.pad(0.2), {maxZoom: 13});
} else {
  map.setView([-7.7, 110.4], 11);
}

function terbangKe(id) {
  var m = markerById[id];
  if (!m) return;
  if (!map.hasLayer(m)) layerKategori[m.kategori].addTo(map);
  map.flyTo(m.getLatLng(), 15);
  m.openPopup();
}

var baseMaps = {
  "Esri Imagery": basemap1,
  "Google Street": basemap2,
  "OpenStreetMap": basemap3
};
var overlayMaps = {};
daftarKategori.forEach(function (k) {

  overlayMaps['<span style="color:' + warnaKategori(k) + ';font-size:16px">&#9679;</span> ' + esc(k)] = layerKategori[k];
});
L.control.layers(baseMaps, overlayMaps, {collapsed: false}).addTo(map);

L.control.scale({maxWidth: 150, position: 'bottomright'}).addTo(map);

var kontrolKoordinat = L.control({position: 'bottomleft'});
kontrolKoordinat.onAdd = function () {
  this._div = buatKotak();
  this._div.innerHTML = 'Arahkan kursor ke peta';
  return this._div;
};
kontrolKoordinat.addTo(map);
map.on('mousemove', function (e) {
  kontrolKoordinat._div.innerHTML = 'Lat: ' + e.latlng.lat.toFixed(5) +
                                    ' | Lon: ' + e.latlng.lng.toFixed(5);
});

var modeUkur = false;
var titikUkur = [];
var jarakTotal = 0;
var garisUkur = L.polyline([], {color: '#db2777', weight: 3}).addTo(map);
var penandaUkur = L.layerGroup().addTo(map);

function formatJarak(m) {
  return m < 1000 ? m.toFixed(1) + ' m' : (m / 1000).toFixed(2) + ' km';
}

var kontrolUkur = L.control({position: 'topleft'});
kontrolUkur.onAdd = function () {
  var div = L.DomUtil.create('div', 'leaflet-bar');
  div.innerHTML =
    '<a href="#" id="btnUkur" title="Ukur jarak" style="font-size:18px;text-align:center;">&#128207;</a>' +
    '<a href="#" id="btnReset" title="Hapus hasil ukur" style="font-size:16px;text-align:center;">&#10006;</a>';
  L.DomEvent.disableClickPropagation(div);
  return div;
};
kontrolUkur.addTo(map);

var infoUkur = L.control({position: 'topleft'});
infoUkur.onAdd = function () {
  this._div = buatKotak();
  this._div.innerHTML = 'Klik &#128207; untuk mengukur jarak';
  return this._div;
};
infoUkur.addTo(map);

document.getElementById('btnUkur').addEventListener('click', function (e) {
  e.preventDefault();
  modeUkur = !modeUkur;
  map.getContainer().style.cursor = modeUkur ? 'crosshair' : '';
  infoUkur._div.innerHTML = modeUkur
    ? 'Mode ukur aktif: klik peta untuk menambah titik'
    : 'Mode ukur dimatikan';
});

document.getElementById('btnReset').addEventListener('click', function (e) {
  e.preventDefault();
  titikUkur = [];
  jarakTotal = 0;
  garisUkur.setLatLngs([]);
  penandaUkur.clearLayers();
  infoUkur._div.innerHTML = 'Hasil ukur dihapus';
});

map.on('click', function (e) {
  if (!modeUkur) return;
  if (titikUkur.length > 0) {
    jarakTotal += titikUkur[titikUkur.length - 1].distanceTo(e.latlng);
  }
  titikUkur.push(e.latlng);
  garisUkur.setLatLngs(titikUkur);
  L.circleMarker(e.latlng, {radius: 4, color: '#db2777'}).addTo(penandaUkur);
  infoUkur._div.innerHTML = 'Jarak: <b>' + formatJarak(jarakTotal) + '</b>';
});