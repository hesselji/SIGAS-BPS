<section class="page-heading detail-heading">
    <div>
        <a class="breadcrumb" href="/letters"><?= ui_icon('arrow-left') ?> Kembali ke Agenda</a>
        <span class="eyebrow">AKSES TERLINDUNGI</span>
        <h1>Surat <?= e(sensitivity_label($letter['sensitivity'])) ?></h1>
        <p>Detail surat hanya dapat dibuka oleh Admin setelah verifikasi ulang password akun.</p>
    </div>
    <span class="sensitivity-badge sens-<?= e(strtolower($letter['sensitivity'])) ?>"><?= e(sensitivity_label($letter['sensitivity'])) ?></span>
</section>

<section class="panel confidential-lock-card">
    <div class="confidential-lock-icon"><?= ui_icon('lock') ?></div>
    <span class="eyebrow">RE-AUTHENTICATION</span>
    <h2>Verifikasi identitas Admin</h2>
    <p>Masukkan password akun Admin yang sedang digunakan. Password tidak disimpan pada surat maupun audit log.</p>

    <div class="confidential-meta">
        <div><span>Nomor Surat</span><strong><?= e($letter['letter_number']) ?></strong></div>
        <div><span>Tanggal Surat</span><strong><?= e(format_date_id($letter['letter_date'])) ?></strong></div>
        <div><span>Sifat</span><strong><?= e(sensitivity_label($letter['sensitivity'])) ?></strong></div>
    </div>

    <form method="post" action="/letters/<?= (int)$letter['id'] ?>/verify-confidential" class="reauth-form" data-loading-form autocomplete="off">
        <?= csrf_field() ?>
        <label class="field">
            <span>Password Admin <i>*</i></span>
            <div class="password-field-shell">
                <input id="confidentialPassword" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan password akun Anda">
                <button type="button" class="password-eye" data-password-toggle="#confidentialPassword" aria-label="Tampilkan atau sembunyikan password"><?= ui_icon('eye') ?></button>
            </div>
        </label>
        <button class="btn btn-primary btn-lg" type="submit"><?= ui_icon('lock') ?><span>Verifikasi & Buka Surat</span></button>
    </form>

    <div class="danger-note"><?= ui_icon('shield') ?><span>Akses yang berhasil akan dicatat di audit trail dan berlaku singkat untuk surat ini.</span></div>
</section>
