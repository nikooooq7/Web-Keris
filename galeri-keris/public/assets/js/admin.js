/* ADMIN.JS — sedikit JavaScript untuk Panel Admin */
(function () {
  'use strict';

  // Menu samping di layar kecil
  var side = document.getElementById('adminSide');
  var backdrop = document.getElementById('sideBackdrop');
  var btn = document.getElementById('menuBtn');
  function toggle(open) {
    if (!side) { return; }
    side.classList.toggle('open', open);
    if (backdrop) { backdrop.classList.toggle('show', open); }
  }
  if (btn) { btn.addEventListener('click', function () { toggle(!side.classList.contains('open')); }); }
  if (backdrop) { backdrop.addEventListener('click', function () { toggle(false); }); }

  // Konfirmasi sebelum menghapus
  document.querySelectorAll('form[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      if (!window.confirm(form.getAttribute('data-confirm'))) { e.preventDefault(); }
    });
  });

  // Format harga otomatis: 1500000 -> 1.500.000
  var price = document.getElementById('price');
  if (price) {
    price.addEventListener('input', function () {
      var digits = price.value.replace(/\D+/g, '');
      price.value = digits ? digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
    });
  }

  // Pratinjau foto sebelum diunggah
  document.querySelectorAll('input[type=file][data-preview]').forEach(function (input) {
    var box = document.getElementById(input.getAttribute('data-preview'));
    if (!box) { return; }
    input.addEventListener('change', function () {
      box.innerHTML = '';
      Array.prototype.forEach.call(input.files, function (file) {
        if (file.type.indexOf('image/') === 0) {
          var img = document.createElement('img');
          img.alt = file.name;
          img.src = URL.createObjectURL(file);
          box.appendChild(img);
        } else {
          var span = document.createElement('span');
          span.className = 'badge text-bg-secondary';
          span.textContent = file.name;
          box.appendChild(span);
        }
      });
    });
  });

  // Peringatan jika file yang dipilih terlalu besar untuk server
  document.querySelectorAll('input[type=file][data-max-bytes]').forEach(function (input) {
    input.addEventListener('change', function () {
      var max = parseInt(input.getAttribute('data-max-bytes'), 10);
      var total = 0;
      Array.prototype.forEach.call(input.files, function (f) { total += f.size; });
      var warn = input.parentNode.querySelector('.size-warning');
      if (warn) { warn.remove(); }
      if (max > 0 && total > max) {
        var p = document.createElement('div');
        p.className = 'size-warning text-danger small mt-1';
        p.textContent = 'Total ukuran file yang dipilih (' + (total / 1048576).toFixed(1) + ' MB) melebihi batas server. Kurangi jumlah/ukuran file.';
        input.parentNode.appendChild(p);
      }
    });
  });

  // Pesan notifikasi sukses hilang otomatis
  document.querySelectorAll('.alert-success').forEach(function (el) {
    setTimeout(function () {
      if (window.bootstrap) { bootstrap.Alert.getOrCreateInstance(el).close(); }
    }, 6000);
  });
})();
