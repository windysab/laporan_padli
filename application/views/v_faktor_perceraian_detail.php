<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6"><h5>Faktor Perceraian Detail (<?= htmlspecialchars($selected_wilayah_label) ?> - <?= $selected_tahun ?>)</h5></div>
        </div>
      </div>
    </section>
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <form action="<?= base_url('index.php/Faktor_perceraian_detail') ?>" method="POST">
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
                <table class="table table-bordered table-striped" id="example1">
                  <thead>
                    <tr><th>No</th><th>Faktor Perceraian</th><th>Laki-Laki</th><th>Perempuan</th><th>Total</th></tr>
                  </thead>
                  <tbody>
                    <?php $no=1; foreach ($datafilter as $item): ?>
                      <?php $is_total = ($item->FaktorPerceraian == 'TOTAL'); ?>
                      <tr <?= $is_total ? 'style="font-weight:bold;background:#ffeeba"' : '' ?>>
                        <td><?= $is_total ? '' : $no++ ?></td>
                        <td><?= htmlspecialchars($item->FaktorPerceraian) ?></td>
                        <td><?= $item->{'Laki-Laki'} ?></td>
                        <td><?= $item->Perempuan ?></td>
                        <td><?= $item->Total ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
