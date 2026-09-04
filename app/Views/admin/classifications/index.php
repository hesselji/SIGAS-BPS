<?php
$activeGroups = array_values(array_filter($groups, fn($g) => (int)$g['is_active'] === 1));
$totalItems = array_sum(array_map(fn($g)=>(int)$g['item_count'],$activeGroups));
?>
<section class="page-heading">
    <div>
        <span class="eyebrow">MASTER DATA</span>
        <h1>Katalog Kode Klasifikasi</h1>
        <p>Katalog KKA hasil import dari dokumen Kode.pdf yang diberikan untuk project.</p>
    </div>
    <a class="btn btn-secondary" href="/admin/numbering-rules"><?= ui_icon('settings') ?><span>Aturan Nomor</span></a>
</section>

<div class="stat-grid compact-stat-grid">
    <article class="stat-card"><span class="stat-icon blue"><?= ui_icon('archive') ?></span><div><small>Kelompok Aktif</small><strong><?= count($activeGroups) ?></strong><em>substantif + fasilitatif</em></div></article>
    <article class="stat-card"><span class="stat-icon green"><?= ui_icon('file') ?></span><div><small>Kode Terdata</small><strong><?= $totalItems ?></strong><em>item klasifikasi</em></div></article>
    <article class="stat-card"><span class="stat-icon orange"><?= ui_icon('database') ?></span><div><small>Sumber</small><strong>Kode.pdf</strong><em>master v0.3</em></div></article>
</div>

<section class="panel section-gap">
    <form class="filter-form master-filter" method="get" action="/admin/classifications">
        <div class="filter-search"><?= ui_icon('search') ?><input type="search" name="q" value="<?= e($filters['q']) ?>" placeholder="Cari kode atau nama klasifikasi..."></div>
        <select name="archive_type">
            <option value="">Semua jenis arsip</option>
            <option value="SUBSTANTIF" <?= $filters['archive_type']==='SUBSTANTIF'?'selected':'' ?>>Substantif</option>
            <option value="FASILITATIF" <?= $filters['archive_type']==='FASILITATIF'?'selected':'' ?>>Fasilitatif</option>
        </select>
        <select name="group">
            <option value="">Semua kelompok KKA</option>
            <?php foreach($activeGroups as $g): ?>
                <option value="<?= e($g['code']) ?>" <?= $filters['group']===$g['code']?'selected':'' ?>><?= e($g['code']) ?> — <?= e($g['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-primary" type="submit"><?= ui_icon('filter') ?><span>Filter</span></button>
        <a class="btn btn-secondary" href="/admin/classifications">Reset</a>
    </form>
</section>

<section class="panel users-panel section-gap">
    <div class="panel-heading"><div><span class="eyebrow">HASIL</span><h2><?= count($rows) ?> baris ditampilkan</h2><p>Kode 1–3 digit dinormalisasi menjadi tiga digit untuk pembentukan KKA; kode empat digit dipertahankan.</p></div><span class="panel-icon-soft"><?= ui_icon('archive') ?></span></div>
    <div class="data-table-wrap">
        <table class="data-table classification-table">
            <thead><tr><th>Jenis Arsip</th><th>Kelompok</th><th>Kode</th><th>Nama</th><th>Keterangan</th></tr></thead>
            <tbody>
            <?php foreach($rows as $row): ?>
                <tr>
                    <td><span class="role-badge role-<?= strtolower($row['archive_type']) ?>"><?= e(ucfirst(strtolower($row['archive_type']))) ?></span></td>
                    <td><strong><?= e($row['group_code']) ?></strong><small class="table-subtext"><?= e($row['group_name']) ?></small></td>
                    <td><code><?= e($row['code'] ?? '-') ?></code><?php if(!empty($row['raw_code']) && $row['raw_code']!==$row['code']): ?><small class="table-subtext">sumber: <?= e($row['raw_code']) ?></small><?php endif; ?></td>
                    <td><?= e($row['name'] ?? '-') ?></td>
                    <td><small><?= nl2br(e($row['description'] ?? '')) ?></small></td>
                </tr>
            <?php endforeach; ?>
            <?php if(!$rows): ?><tr><td colspan="5"><div class="empty-state compact-empty"><strong>Data tidak ditemukan</strong><span>Ubah filter atau kata kunci.</span></div></td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
