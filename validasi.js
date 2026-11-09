// Dipakai oleh form input dan form edit
function validateForm(event) {
  const wajib = [
    ["kategori", "Kategori"], ["keterangan", "Keterangan"],
    ["kecamatan", "Kecamatan"], ["desa", "Desa"], ["dusun", "Dusun"]
  ];
  const angka = [
    ["point_x", "Point X"], ["point_y", "Point Y"],
    ["longitude", "Longitude"], ["latitude", "Latitude"]
  ];
  let text = "";

  for (const [id, nama] of wajib) {
    if (document.getElementById(id).value.trim() === "") {
      text = nama + " harus diisi";
      break;
    }
  }
  if (text === "") {
    for (const [id, nama] of angka) {
      let v = document.getElementById(id).value.trim();
      if (v === "" || isNaN(v)) {
        text = nama + " harus berupa angka";
        break;
      }
    }
  }
  if (text === "") {
    let lon = Number(document.getElementById("longitude").value);
    let lat = Number(document.getElementById("latitude").value);
    if (lon < -180 || lon > 180) text = "Longitude harus antara -180 sampai 180";
    else if (lat < -90 || lat > 90) text = "Latitude harus antara -90 sampai 90";
  }

  document.getElementById("informasi").innerHTML = text;
  if (text !== "") {
    event.preventDefault();   // batalkan pengiriman form
    return false;
  }
  return true;
}