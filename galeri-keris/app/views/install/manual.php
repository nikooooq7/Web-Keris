<div class="panel">
  <h2 class="h5 text-warning">Satu langkah terakhir (manual)</h2>
  <p>Database sudah siap, tetapi folder <code>config</code> tidak bisa ditulis otomatis oleh server. Lakukan ini:</p>
  <ol>
    <li>Buka folder <code>config</code> di File Manager hosting Anda.</li>
    <li>Buat file baru bernama <code>config.php</code>.</li>
    <li>Tempelkan seluruh isi kotak di bawah ini ke dalamnya, lalu simpan.</li>
    <li>Buka kembali alamat website Anda, lalu masuk di <code>/admin</code>.</li>
  </ol>
  <textarea class="form-control font-monospace" rows="16" readonly onclick="this.select()"><?= e($content) ?></textarea>
  <p class="text-muted small mt-2 mb-0">Isi ini memuat password database — jangan dibagikan ke siapa pun.</p>
</div>
