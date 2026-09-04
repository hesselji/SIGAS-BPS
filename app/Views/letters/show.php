<section class="page-heading detail-heading">
    <div>
        <a class="breadcrumb" href="/letters"><?= ui_icon('arrow-left') ?> Kembali ke Agenda</a>
        <span class="eyebrow">DETAIL RECORD</span>
        <h1>Informasi agenda surat</h1>
        <p>Record lengkap dari nomor agenda yang telah di-generate.</p>
    </div>
    <span class="badge badge-<?= strtolower($letter['status']) ?> badge-lg"><i></i><?= e(status_label($letter['status'])) ?></span>
</section>

<section class="number-hero-card <?= $letter['status'] === 'CANCELLED' ? 'is-cancelled' : '' ?>">
    <div class="number-hero-icon"><?= ui_icon('file') ?></div>
    <div class="number-hero-main"><span>Nomor Surat</span><strong id="letterNumberValue"><?= e($letter['letter_number']) ?></strong><small>Agenda #<?= e($letter['agenda_number']) ?> • <?= e($letter['letter_type_name']) ?></small></div>
    <button class="btn btn-secondary" type="button" data-copy-target="#letterNumberValue"><?= ui_icon('copy') ?><span>Salin Nomor</span></button>
</section>

<div class="detail-layout">
    <section class="panel detail-panel">
        <div class="panel-heading"><div><span class="eyebrow">DATA SURAT</span><h2>Informasi utama</h2><p>Detail yang tersimpan pada record agenda.</p></div><span class="panel-icon-soft"><?= ui_icon('mail') ?></span></div>
        <div class="detail-data-grid">
            <div><span>Tim Kerja</span><strong><?= e($letter['work_team_name']) ?></strong></div>
            <div><span>Pengaju</span><strong><?= e($letter['requester_name']) ?></strong></div>
            <div><span>Sistem</span><strong><?= e(str_replace('_', ' ', $letter['system_type'])) ?></strong></div>
            <div><span>Tanggal Surat</span><strong><?= e(format_date_id($letter['letter_date'])) ?></strong></div>
            <div><span>Sifat Surat</span><strong><span class="sensitivity-badge sens-<?= strtolower($letter['sensitivity']) ?>"><?= e(sensitivity_label($letter['sensitivity'])) ?></span></strong></div>
            <div><span>Ada Anggaran</span><strong><?= $letter['uses_budget']==='Y'?'Ya':'Tidak' ?></strong></div>
            <div><span>Jenis Arsip</span><strong><?= e(ucwords(strtolower($letter['archive_type']))) ?></strong></div>
            <div><span>Klasifikasi</span><strong class="classification-code"><?= e($letter['classification_code']) ?></strong><small class="table-subtext"><?= e(($letter['classification_group_name'] ?? '').(($letter['classification_item_name'] ?? '') ? ' • '.($letter['classification_item_name'] ?? '') : '')) ?></small></div>
            <div class="detail-span-2"><span>Tujuan Surat</span><strong><?= e($letter['recipient']) ?></strong></div>
            <div class="detail-span-2"><span>Perihal</span><strong><?= e($letter['subject']) ?></strong></div>
            <?php if($letter['notes']):?><div class="detail-span-2"><span>Catatan</span><p><?= nl2br(e($letter['notes'])) ?></p></div><?php endif;?>
        </div>
    </section>

    <aside class="detail-aside">
        <section class="panel audit-panel">
            <div class="panel-heading"><div><span class="eyebrow">AUDIT TRAIL</span><h2>Status Nomor</h2></div><span class="panel-icon-soft"><?= ui_icon('shield') ?></span></div>
            <?php if($letter['status']==='ACTIVE'):?>
                <div class="audit-status active"><span><?= ui_icon('check') ?></span><div><b>Nomor aktif</b><small>Nomor sudah tercatat dan dianggap terpakai.</small></div></div>
                <?php if(\App\Core\Auth::isAdmin()):?>
                    <div class="divider"></div>
                    <form method="post" action="/letters/<?= (int)$letter['id'] ?>/cancel" class="cancel-form" data-loading-form>
                        <?= csrf_field() ?>
                        <label class="field"><span>Alasan Pembatalan</span><textarea name="reason" rows="4" placeholder="Jelaskan alasan pembatalan..." required minlength="5"></textarea></label>
                        <div class="danger-note"><?= ui_icon('info') ?><span>Nomor batal tetap tersimpan dan tidak digunakan ulang.</span></div>
                        <button class="btn btn-danger btn-block" type="submit" data-confirm="Nomor akan ditandai dibatalkan dan tidak dapat digunakan ulang. Lanjut?"><?= ui_icon('ban') ?><span>Batalkan Nomor</span></button>
                    </form>
                <?php endif;?>
            <?php else:?>
                <div class="audit-status cancelled"><span><?= ui_icon('ban') ?></span><div><b>Nomor dibatalkan</b><small><?= e(format_date_id($letter['cancelled_at'], true)) ?></small></div></div>
                <div class="cancellation-detail"><span>Alasan</span><p><?= e($letter['cancellation_reason']) ?></p><small>Dibatalkan oleh <?= e($letter['cancelled_by_name'] ?? '-') ?></small></div>
            <?php endif;?>
        </section>
        <section class="panel metadata-panel">
            <span class="eyebrow">METADATA</span>
            <div><span>Dibuat</span><strong><?= e(format_date_id($letter['created_at'], true)) ?></strong></div>
            <div><span>Terakhir Diperbarui</span><strong><?= e(format_date_id($letter['updated_at'], true)) ?></strong></div>
            <div><span>Tahun Agenda</span><strong><?= e((string)$letter['year']) ?></strong></div>
        </section>
    </aside>
</div>
