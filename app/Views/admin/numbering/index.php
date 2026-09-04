<section class="page-heading">
    <div>
        <span class="eyebrow">ADMINISTRASI</span>
        <h1>Aturan penomoran</h1>
        <p>Kelola pattern nomor dan mapping jenis surat tanpa mengubah source code.</p>
    </div>
    <a class="btn btn-secondary" href="/admin/classifications"><?= ui_icon('archive') ?><span>Katalog KKA</span></a>
</section>

<section class="panel master-note-panel">
    <div class="panel-heading">
        <div>
            <span class="eyebrow">CATATAN PENTING</span>
            <h2>Rule dapat disesuaikan setelah konfirmasi BPS</h2>
            <p>Token yang didukung: <code>{PREFIX}</code>, <code>{SEQ}</code>, <code>{SEQ3}</code>, <code>{SEQ4}</code>, <code>{UNIT}</code>, <code>{KKA}</code>, <code>{YEAR}</code>.</p>
        </div>
        <span class="panel-icon-soft"><?= ui_icon('settings') ?></span>
    </div>
</section>

<div class="admin-grid-2">
    <?php foreach($rules as $rule): ?>
        <section class="panel rule-card">
            <div class="panel-heading compact-heading">
                <div>
                    <span class="eyebrow"><?= e($rule['code']) ?></span>
                    <h2><?= e($rule['name']) ?></h2>
                    <p><?= (int)$rule['type_count'] ?> jenis surat menggunakan rule ini.</p>
                </div>
                <span class="badge <?= $rule['is_active'] ? 'badge-active' : 'badge-cancelled' ?>"><i></i><?= $rule['is_active'] ? 'Aktif' : 'Nonaktif' ?></span>
            </div>
            <form class="modern-form" method="post" action="/admin/numbering-rules/<?= (int)$rule['id'] ?>" data-loading-form>
                <?= csrf_field() ?>
                <label class="field"><span>Nama Rule</span><input name="name" value="<?= e($rule['name']) ?>" required></label>
                <label class="field"><span>Kode Unit / Segmen Tetap</span><input name="unit_code" value="<?= e($rule['unit_code']) ?>" required></label>
                <label class="field"><span>Pattern</span><input name="pattern" value="<?= e($rule['pattern']) ?>" required></label>
                <label class="toggle-line"><input type="checkbox" name="is_active" value="1" <?= $rule['is_active'] ? 'checked' : '' ?>><span>Rule aktif</span></label>
                <button class="btn btn-primary" type="submit"><?= ui_icon('check') ?><span>Simpan Rule</span></button>
            </form>
        </section>
    <?php endforeach; ?>
</div>

<section class="panel users-panel section-gap">
    <div class="panel-heading">
        <div><span class="eyebrow">MAPPING JENIS SURAT</span><h2>Jenis surat → aturan penomoran</h2><p>Ubah mapping di sini jika hasil konfirmasi BPS berbeda dari working rule saat ini.</p></div>
        <span class="panel-icon-soft"><?= ui_icon('mail') ?></span>
    </div>
    <div class="data-table-wrap">
        <table class="data-table">
            <thead><tr><th>Jenis Surat</th><th>Rule Saat Ini</th><th>Unit/Segmen</th><th>Pattern</th><th>Ubah Mapping</th></tr></thead>
            <tbody>
            <?php foreach($types as $type): ?>
                <tr>
                    <td><strong><?= e($type['name']) ?></strong><small class="table-subtext"><?= e($type['code']) ?></small></td>
                    <td><?= e($type['rule_name']) ?></td>
                    <td><code><?= e($type['unit_code']) ?></code></td>
                    <td><code class="code-wrap"><?= e($type['pattern']) ?></code></td>
                    <td>
                        <form class="inline-admin-form" method="post" action="/admin/letter-types/<?= (int)$type['id'] ?>">
                            <?= csrf_field() ?>
                            <select name="numbering_rule_id">
                                <?php foreach($rules as $rule): ?>
                                    <option value="<?= (int)$rule['id'] ?>" <?= (int)$type['numbering_rule_id']===(int)$rule['id'] ? 'selected' : '' ?>><?= e($rule['code']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label class="inline-check"><input type="checkbox" name="is_active" value="1" <?= $type['is_active'] ? 'checked' : '' ?>> Aktif</label>
                            <button class="btn btn-secondary btn-sm" type="submit">Simpan</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
