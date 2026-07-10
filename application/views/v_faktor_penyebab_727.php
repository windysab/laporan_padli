<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6"><h5>Tabel 727 — Faktor Penyebab Perceraian (<?= htmlspecialchars($selected_wilayah_label) ?> - <?= $selected_tahun ?>)</h5></div>
        </div>
      </div>
    </section>
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <form action="<?= base_url('index.php/Faktor_perceraian_detail/tabel_727') ?>" method="POST">
                  Wilayah:
                  <select name="wilayah">
                    <option value="HSU" <?= $selected_wilayah=='HSU'?'selected':'' ?>>HSU</option>
                    <option value="Balangan" <?= $selected_wilayah=='Balangan'?'selected':'' ?>>Balangan</option>
                    <option value="SEMUA" <?= $selected_wilayah=='SEMUA'?'selected':'' ?>>HSU + Balangan</option>
                  </select>
                  Tahun:
                  <select name="lap_tahun">
                    <?php for ($y=2016; $y<=2025; $y++): ?>
                      <option value="<?= $y ?>" <?= $selected_tahun==$y?'selected':'' ?>><?= $y ?></option>
                    <?php endfor; ?>
                  </select>
                  <input class="btn btn-primary btn-sm" type="submit" name="btn" value="Tampilkan" />
                  <hr>
                </form>
                <table class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th rowspan="2">No</th>
                      <th rowspan="2">Faktor Penyebab Perceraian</th>
                      <th colspan="5">Usia Saat Kawin</th>
                    </tr>
                    <tr>
                      <th>16-19</th><th>20-25</th><th>26-30</th><th>31-35</th><th>36+</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $usia_label = ['usia_16_19','usia_20_25','usia_26_30','usia_31_35','usia_36']; ?>
                    <?php foreach ($rows as $r): ?>
                      <tr>
                        <td><?= $r['no'] ?></td>
                        <td><?= htmlspecialchars($r['faktor']) ?></td>
                        <?php foreach ($usia_label as $k): ?>
                          <td><?= $r[$k] ?></td>
                        <?php endforeach; ?>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                  <tfoot>
                    <tr style="font-weight:bold;background:#ffeeba">
                      <td colspan="2">TOTAL</td>
                      <?php foreach ($usia_label as $k): ?>
                        <td><?= $totals[$k] ?></td>
                      <?php endforeach; ?>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>
</body>
</html>
