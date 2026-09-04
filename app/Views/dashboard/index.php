<?php
$total = (int)($summary['total'] ?? 0);
$cancelled = (int)($summary['cancelled'] ?? 0);
$active = max(0, $total - $cancelled);
$thisMonth = (int)($summary['this_month'] ?? 0);
$ordinary = (int)($summary['ordinary'] ?? 0);
$activePct = $total > 0 ? round(($active / $total) * 100) : 0;
$cancelPct = $total > 0 ? 100 - $activePct : 0;
$maxTeam = $byTeam ? max(array_map(fn($r)=>(int)$r['total'], $byTeam)) : 1;
?>
<section class="page-heading">
    <div>
        <span class="eyebrow">OVERVIEW</span>
        <h1>Selamat <?= (int)date('H') < 11 ? 'pagi' : ((int)date('H') < 15 ? 'siang' : ((int)date('H') < 18 ? 'sore' : 'malam')) ?>, <?= e(explode(' ', auth_user()['name'])[0] ?? auth_user()['name']) ?> <span class="wave">👋</span></h1>
        <p><?= e(format_date_id(date('Y-m-d'))) ?> • Pantau aktivitas agenda surat dan generate nomor dari satu dashboard.</p>
    </div>
    <div class="heading-actions">
        <a class="btn btn-secondary" href="/letters"><?= ui_icon('mail') ?><span>Lihat Agenda</span></a>
        <a class="btn btn-primary" href="/letters/create"><?= ui_icon('plus') ?><span>Generate Nomor</span></a>
    </div>
</section>

<section class="metric-grid">
    <article class="metric-card metric-blue">
        <div class="metric-top"><span>Total Agenda</span><span class="metric-icon"><?= ui_icon('file') ?></span></div>
        <strong><?= number_format($total) ?></strong>
        <small>Seluruh nomor yang tercatat</small>
        <div class="metric-accent"></div>
    </article>
    <article class="metric-card metric-orange">
        <div class="metric-top"><span>Bulan Ini</span><span class="metric-icon"><?= ui_icon('calendar') ?></span></div>
        <strong><?= number_format($thisMonth) ?></strong>
        <small>Record pada <?= e(month_year_id()) ?></small>
        <div class="metric-accent"></div>
    </article>
    <article class="metric-card metric-green">
        <div class="metric-top"><span>Nomor Aktif</span><span class="metric-icon"><?= ui_icon('check') ?></span></div>
        <strong><?= number_format($active) ?></strong>
        <small><?= $activePct ?>% dari total agenda</small>
        <div class="metric-accent"></div>
    </article>
    <article class="metric-card metric-neutral">
        <div class="metric-top"><span>Dibatalkan</span><span class="metric-icon"><?= ui_icon('ban') ?></span></div>
        <strong><?= number_format($cancelled) ?></strong>
        <small>Tetap tersimpan dalam audit trail</small>
        <div class="metric-accent"></div>
    </article>
</section>

<section class="dashboard-primary-grid">
    <article class="panel status-panel">
        <div class="panel-heading">
            <div><span class="eyebrow">RINGKASAN</span><h2>Status Agenda</h2><p>Distribusi status nomor surat saat ini.</p></div>
            <span class="period-chip"><?= ui_icon('calendar') ?> <?= e(month_year_id()) ?></span>
        </div>
        <div class="status-content">
            <div class="donut-wrap">
                <div class="donut" style="--active: <?= $activePct ?>%; --cancelled: <?= $cancelPct ?>%;">
                    <div class="donut-center"><strong><?= number_format($total) ?></strong><span>Total</span></div>
                </div>
            </div>
            <div class="legend-list">
                <div><span class="legend-dot green"></span><span>Aktif</span><strong><?= number_format($active) ?></strong><small><?= $activePct ?>%</small></div>
                <div><span class="legend-dot red"></span><span>Dibatalkan</span><strong><?= number_format($cancelled) ?></strong><small><?= $cancelPct ?>%</small></div>
                <div><span class="legend-dot blue"></span><span>Surat Biasa</span><strong><?= number_format($ordinary) ?></strong><small><?= $total ? round(($ordinary/$total)*100) : 0 ?>%</small></div>
            </div>
        </div>
    </article>

    <article class="panel latest-panel">
        <div class="panel-heading">
            <div><span class="eyebrow">TERBARU</span><h2>Agenda Terakhir</h2><p>Record nomor yang baru dibuat.</p></div>
            <a class="text-link" href="/letters">Lihat Semua <?= ui_icon('arrow-right') ?></a>
        </div>
        <div class="latest-list">
            <?php if (!$latest): ?>
                <div class="empty-mini"><span><?= ui_icon('mail') ?></span><b>Belum ada agenda</b><small>Generate nomor pertama untuk mulai mengisi daftar.</small></div>
            <?php else: foreach ($latest as $r): ?>
                <a class="latest-item" href="/letters/<?= (int)$r['id'] ?>">
                    <span class="latest-file-icon"><?= ui_icon('file') ?></span>
                    <span class="latest-main"><strong><?= e($r['letter_number']) ?></strong><small><?= e($r['subject']) ?></small></span>
                    <span class="latest-meta"><span class="badge badge-<?= strtolower($r['status']) ?>"><?= e(status_label($r['status'])) ?></span><small><?= e(format_date_id($r['created_at'])) ?></small></span>
                </a>
            <?php endforeach; endif; ?>
        </div>
    </article>
</section>

<section class="dashboard-secondary-grid">
    <article class="panel team-panel">
        <div class="panel-heading">
            <div><span class="eyebrow">STATISTIK</span><h2>Surat per Tim Kerja</h2><p>Perbandingan record berdasarkan tim.</p></div>
            <span class="panel-icon-soft"><?= ui_icon('chart') ?></span>
        </div>
        <div class="bar-chart-list">
            <?php if (!$byTeam): ?>
                <div class="empty-mini"><b>Belum ada data tim</b><small>Statistik akan tampil setelah record dibuat.</small></div>
            <?php else: foreach ($byTeam as $idx => $row): $pct = max(5, round(((int)$row['total']/$maxTeam)*100)); ?>
                <div class="bar-chart-row">
                    <div class="bar-chart-label"><span><?= e($row['name']) ?></span><strong><?= (int)$row['total'] ?></strong></div>
                    <div class="bar-track"><i style="--w: <?= $pct ?>%; --delay: <?= $idx * 80 ?>ms"></i></div>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </article>

    <article class="panel quick-panel">
        <div class="panel-heading"><div><span class="eyebrow">AKSI CEPAT</span><h2>Mulai dari sini</h2><p>Akses fitur utama SIGAS-BPS.</p></div></div>
        <div class="quick-grid">
            <a class="quick-card quick-blue" href="/letters/create"><span><?= ui_icon('mail-plus') ?></span><div><b>Generate Nomor</b><small>Buat nomor agenda baru</small></div><?= ui_icon('arrow-right') ?></a>
            <a class="quick-card quick-green" href="/letters"><span><?= ui_icon('archive') ?></span><div><b>Agenda Surat</b><small>Lihat seluruh record</small></div><?= ui_icon('arrow-right') ?></a>
            <?php if (\App\Core\Auth::isAdmin()): ?><a class="quick-card quick-orange" href="/admin/users"><span><?= ui_icon('users') ?></span><div><b>Kelola Pengguna</b><small>Atur akun akses</small></div><?= ui_icon('arrow-right') ?></a><?php endif; ?>
            <?php if (\App\Core\Auth::isAdmin()): ?><a class="quick-card quick-muted" href="/admin/classifications"><span><?= ui_icon('database') ?></span><div><b>Katalog KKA</b><small>613 kode klasifikasi</small></div><?= ui_icon('arrow-right') ?></a><?php endif; ?>
        </div>
    </article>
</section>
