<?php
$errs = $errors ?? [];
$oldData = $_SESSION['old'] ?? [];
?>

<section class="page-heading">
    <div>
        <a class="breadcrumb" href="/letters">
            <?= ui_icon('arrow-left') ?> Agenda Surat
        </a>

        <span class="eyebrow">GENERATE NOMOR</span>

        <h1>Buat nomor surat keluar</h1>

        <p>
            Lengkapi data surat. Sistem akan membentuk nomor otomatis
            dari rule penomoran dan Kode Klasifikasi Arsip.
        </p>
    </div>

    <div class="heading-hint">
        <span>
            <?= ui_icon('shield') ?>
        </span>

        <div>
            <strong>Sequence aman</strong>
            <small>
                Nomor dibuat melalui transaksi database.
            </small>
        </div>
    </div>
</section>

<div class="create-layout">

    <section class="panel form-panel">

        <form
            method="post"
            action="/letters"
            id="letterForm"
            class="modern-form"
            data-loading-form
        >

            <?= csrf_field() ?>

            <!-- ===================================================
                 01. INFORMASI SURAT
                 =================================================== -->

            <div class="form-section">

                <div class="form-section-head">
                    <span class="section-step">
                        01
                    </span>

                    <div>
                        <h2>Informasi Surat</h2>

                        <p>
                            Data dasar yang digunakan dalam agenda.
                        </p>
                    </div>
                </div>

                <div class="form-grid form-grid-2">

                    <label class="field">

                        <span>
                            Jenis Surat <i>*</i>
                        </span>

                        <select
                            name="letter_type_id"
                            id="letterType"
                            required
                        >

                            <option value="">
                                Pilih jenis surat
                            </option>

                            <?php foreach ($types as $t): ?>

                                <?php
                                /*
                                 * Tahap 2:
                                 *
                                 * MAIN_62710
                                 * tidak boleh tanggal lampau.
                                 *
                                 * Rule selain itu, termasuk
                                 * SUBBAG_62711, tetap fleksibel.
                                 */
                                $dateMode =
                                    ($t['rule_code'] ?? '')
                                    === 'MAIN_62710'
                                        ? 'NO_PAST'
                                        : 'ANY';
                                ?>

                                <option
                                    value="<?= (int)$t['id'] ?>"
                                    data-unit="<?= e($t['unit_code']) ?>"
                                    data-pattern="<?= e($t['pattern']) ?>"
                                    data-rule="<?= e($t['rule_code']) ?>"
                                    data-date-mode="<?= e($dateMode) ?>"
                                    <?= (
                                        (string)(
                                            $oldData[
                                                'letter_type_id'
                                            ] ?? ''
                                        )
                                        ===
                                        (string)$t['id']
                                    )
                                        ? 'selected'
                                        : ''
                                    ?>
                                >
                                    <?= e($t['name']) ?>
                                    —
                                    <?= e($t['rule_code']) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                        <small
                            class="field-help"
                            id="dateRuleHelp"
                        >
                            Pilih jenis surat untuk melihat
                            aturan tanggal dan jalur sequence.
                        </small>

                        <?php if (
                            !empty(
                                $errs['letter_type_id']
                            )
                        ): ?>

                            <small class="field-error">
                                <?= e(
                                    $errs[
                                        'letter_type_id'
                                    ]
                                ) ?>
                            </small>

                        <?php endif; ?>

                    </label>


                    <label class="field">

                        <span>
                            Tim Kerja <i>*</i>
                        </span>

                        <select
                            name="work_team_id"
                            required
                        >

                            <option value="">
                                Pilih tim kerja
                            </option>

                            <?php foreach ($teams as $t): ?>

                                <option
                                    value="<?= (int)$t['id'] ?>"
                                    <?= (
                                        (string)(
                                            $oldData[
                                                'work_team_id'
                                            ] ?? ''
                                        )
                                        ===
                                        (string)$t['id']
                                    )
                                        ? 'selected'
                                        : ''
                                    ?>
                                >
                                    <?= e($t['name']) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                        <?php if (
                            !empty(
                                $errs['work_team_id']
                            )
                        ): ?>

                            <small class="field-error">
                                <?= e(
                                    $errs[
                                        'work_team_id'
                                    ]
                                ) ?>
                            </small>

                        <?php endif; ?>

                    </label>


                    <label class="field">

                        <span>
                            Sistem <i>*</i>
                        </span>

                        <select
                            name="system_type"
                            required
                        >

                            <option value="">
                                Pilih sistem
                            </option>

                            <option
                                value="SRIKANDI"
                                <?= (
                                    $oldData[
                                        'system_type'
                                    ] ?? ''
                                ) === 'SRIKANDI'
                                    ? 'selected'
                                    : ''
                                ?>
                            >
                                SRIKANDI
                            </option>

                            <option
                                value="NON_SRIKANDI"
                                <?= (
                                    $oldData[
                                        'system_type'
                                    ] ?? ''
                                ) === 'NON_SRIKANDI'
                                    ? 'selected'
                                    : ''
                                ?>
                            >
                                Non Srikandi
                            </option>

                        </select>

                    </label>


                    <label class="field">

                        <span>
                            Tanggal Surat <i>*</i>
                        </span>

                        <div class="field-icon-wrap">

                            <?= ui_icon('calendar') ?>

                            <input
                                type="date"
                                name="letter_date"
                                id="letterDate"
                                value="<?= e(
                                    $oldData[
                                        'letter_date'
                                    ]
                                    ??
                                    date('Y-m-d')
                                ) ?>"
                                required
                            >

                        </div>

                        <?php if (
                            !empty(
                                $errs['letter_date']
                            )
                        ): ?>

                            <small class="field-error">
                                <?= e(
                                    $errs[
                                        'letter_date'
                                    ]
                                ) ?>
                            </small>

                        <?php endif; ?>

                    </label>

                </div>
            </div>


            <!-- ===================================================
                 02. ATURAN & KLASIFIKASI
                 =================================================== -->

            <div class="form-section">

                <div class="form-section-head">

                    <span class="section-step orange">
                        02
                    </span>

                    <div>
                        <h2>
                            Aturan & Klasifikasi
                        </h2>

                        <p>
                            Pilih sifat surat dan kode klasifikasi
                            sesuai dokumen Kode Klasifikasi.
                        </p>
                    </div>

                </div>

                <div class="form-grid form-grid-2">

                    <label class="field">

                        <span>
                            Sifat Surat <i>*</i>
                        </span>

                        <select
                            name="sensitivity"
                            id="sensitivity"
                            required
                        >

                            <option value="">
                                Pilih sifat surat
                            </option>

                            <?php foreach (
                                $sensitivities as $s
                            ): ?>

                                <option
                                    value="<?= e(
                                        $s['code']
                                    ) ?>"
                                    data-prefix="<?= e(
                                        $s['prefix']
                                    ) ?>"
                                    <?= (
                                        $oldData[
                                            'sensitivity'
                                        ] ?? ''
                                    ) === $s['code']
                                        ? 'selected'
                                        : ''
                                    ?>
                                >
                                    <?= e($s['name']) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </label>


                    <label class="field">

                        <span>
                            Ada Anggaran? <i>*</i>
                        </span>

                        <select
                            name="uses_budget"
                            id="usesBudget"
                            required
                        >

                            <option value="">
                                Pilih kondisi
                            </option>

                            <option
                                value="Y"
                                <?= (
                                    $oldData[
                                        'uses_budget'
                                    ] ?? ''
                                ) === 'Y'
                                    ? 'selected'
                                    : ''
                                ?>
                            >
                                Ya
                            </option>

                            <option
                                value="T"
                                <?= (
                                    $oldData[
                                        'uses_budget'
                                    ] ?? ''
                                ) === 'T'
                                    ? 'selected'
                                    : ''
                                ?>
                            >
                                Tidak
                            </option>

                        </select>

                        <small class="field-help">
                            Disimpan sebagai atribut surat.
                            Pembatasan KKA berdasarkan anggaran
                            belum dipaksakan sebelum rule BPS
                            dikonfirmasi.
                        </small>

                    </label>


                    <label class="field">

                        <span>
                            Jenis Arsip <i>*</i>
                        </span>

                        <select
                            name="archive_type"
                            id="archiveType"
                            required
                        >

                            <option value="">
                                Pilih jenis arsip
                            </option>

                            <?php foreach (
                                $archiveTypes as $a
                            ): ?>

                                <option
                                    value="<?= e(
                                        $a['code']
                                    ) ?>"
                                    <?= (
                                        $oldData[
                                            'archive_type'
                                        ] ?? ''
                                    ) === $a['code']
                                        ? 'selected'
                                        : ''
                                    ?>
                                >
                                    <?= e($a['name']) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </label>


                    <div class="scope-card">

                        <span>
                            Master Klasifikasi
                        </span>

                        <strong id="scopePreview">
                            —
                        </strong>

                        <small>
                            Kelompok KKA ditentukan oleh
                            Jenis Arsip.
                        </small>

                    </div>


                    <label class="field">

                        <span>
                            Kelompok Klasifikasi (KKA)
                            <i>*</i>
                        </span>

                        <select
                            name="classification_parent"
                            id="classificationParent"
                            data-old="<?= e(
                                $oldData[
                                    'classification_parent'
                                ] ?? ''
                            ) ?>"
                            required
                            disabled
                        >

                            <option value="">
                                Pilih jenis arsip dahulu
                            </option>

                        </select>

                        <?php if (
                            !empty(
                                $errs[
                                    'classification_parent'
                                ]
                            )
                        ): ?>

                            <small class="field-error">
                                <?= e(
                                    $errs[
                                        'classification_parent'
                                    ]
                                ) ?>
                            </small>

                        <?php endif; ?>

                    </label>


                    <label class="field">

                        <span>
                            Kode Klasifikasi <i>*</i>
                        </span>

                        <select
                            name="classification_child"
                            id="classificationChild"
                            data-old="<?= e(
                                $oldData[
                                    'classification_child'
                                ] ?? ''
                            ) ?>"
                            required
                            disabled
                        >

                            <option value="">
                                Pilih kelompok KKA dahulu
                            </option>

                        </select>

                        <?php if (
                            !empty(
                                $errs[
                                    'classification_child'
                                ]
                            )
                        ): ?>

                            <small class="field-error">
                                <?= e(
                                    $errs[
                                        'classification_child'
                                    ]
                                ) ?>
                            </small>

                        <?php endif; ?>

                    </label>

                </div>
            </div>


            <!-- ===================================================
                 03. TUJUAN & PERIHAL
                 =================================================== -->

            <div class="form-section">

                <div class="form-section-head">

                    <span class="section-step green">
                        03
                    </span>

                    <div>
                        <h2>
                            Tujuan & Perihal
                        </h2>

                        <p>
                            Informasi yang akan tampil
                            pada record agenda.
                        </p>
                    </div>

                </div>

                <div class="form-grid form-grid-2">

                    <label class="field">

                        <span>
                            Tujuan Surat <i>*</i>
                        </span>

                        <input
                            name="recipient"
                            value="<?= old('recipient') ?>"
                            placeholder="Contoh: Kepala Dinas ..."
                            required
                        >

                        <?php if (
                            !empty(
                                $errs['recipient']
                            )
                        ): ?>

                            <small class="field-error">
                                <?= e(
                                    $errs[
                                        'recipient'
                                    ]
                                ) ?>
                            </small>

                        <?php endif; ?>

                    </label>


                    <label class="field">

                        <span>
                            Perihal <i>*</i>
                        </span>

                        <input
                            name="subject"
                            value="<?= old('subject') ?>"
                            placeholder="Perihal / keperluan surat"
                            required
                        >

                        <?php if (
                            !empty(
                                $errs['subject']
                            )
                        ): ?>

                            <small class="field-error">
                                <?= e(
                                    $errs[
                                        'subject'
                                    ]
                                ) ?>
                            </small>

                        <?php endif; ?>

                    </label>


                    <label class="field span-all">

                        <span>
                            Catatan Tambahan
                        </span>

                        <textarea
                            name="notes"
                            rows="4"
                            placeholder="Tambahkan catatan jika diperlukan..."
                        ><?= old('notes') ?></textarea>

                    </label>

                </div>
            </div>


            <div class="form-actions-sticky">

                <div class="form-security-note">

                    <?= ui_icon('lock') ?>

                    <span>
                        Data dan KKA divalidasi kembali
                        di server sebelum nomor dibuat.
                    </span>

                </div>

                <div>

                    <a
                        class="btn btn-secondary"
                        href="/letters"
                    >
                        Batal
                    </a>

                    <button
                        class="btn btn-primary btn-lg"
                        type="submit"
                    >

                        <?= ui_icon('mail-plus') ?>

                        <span>
                            Generate & Simpan Nomor
                        </span>

                    </button>

                </div>
            </div>

        </form>

    </section>


    <!-- ===========================================================
         LIVE PREVIEW
         =========================================================== -->

    <aside class="create-aside">

        <section
            class="panel preview-panel sticky-panel"
        >

            <span class="eyebrow">
                LIVE PREVIEW
            </span>

            <h3>
                Perkiraan format nomor
            </h3>

            <p id="rulePreviewText">
                Pilih jenis surat untuk melihat
                pattern yang digunakan.
            </p>

            <div
                class="number-preview dynamic-number-preview"
                id="numberPreview"
            >
                ?-???/62710/KKA.000/<?= date('Y') ?>
            </div>

            <div class="preview-legend">

                <div>
                    <i class="blue"></i>
                    <span>
                        Prefix & sequence
                    </span>
                </div>

                <div>
                    <i class="green"></i>
                    <span>
                        Kode unit / segmen
                    </span>
                </div>

                <div>
                    <i class="orange"></i>
                    <span>
                        Klasifikasi KKA
                    </span>
                </div>

            </div>

            <div class="preview-info">

                <span>
                    <?= ui_icon('info') ?>
                </span>

                <p>
                    Preview mengikuti pattern jenis surat.
                    Sequence final tetap dibuat backend agar
                    tidak terjadi duplikasi.
                </p>

            </div>

        </section>


        <section class="panel aside-guide">

            <span class="eyebrow">
                ALUR SISTEM
            </span>

            <div class="guide-step">

                <b>1</b>

                <div>
                    <strong>Validasi</strong>

                    <small>
                        Jenis arsip, KKA, dan field
                        surat dicek.
                    </small>
                </div>

            </div>


            <div class="guide-step">

                <b>2</b>

                <div>
                    <strong>
                        Kunci Sequence
                    </strong>

                    <small>
                        Database mencegah nomor ganda.
                    </small>
                </div>

            </div>


            <div class="guide-step">

                <b>3</b>

                <div>
                    <strong>
                        Generate
                    </strong>

                    <small>
                        Pattern rule diisi token
                        secara otomatis.
                    </small>
                </div>

            </div>


            <div class="guide-step">

                <b>4</b>

                <div>
                    <strong>
                        Simpan Record
                    </strong>

                    <small>
                        Nomor dan klasifikasi masuk
                        ke agenda.
                    </small>
                </div>

            </div>

        </section>

    </aside>

</div>

<?php
unset($_SESSION['old']);
?>