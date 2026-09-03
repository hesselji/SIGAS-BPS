<section class="page-heading">
    <div><span class="eyebrow">ADMINISTRASI</span><h1>Pengguna sistem</h1><p>Kelola akun yang dapat mengakses SIGAS-BPS.</p></div>
    <a class="btn btn-primary" href="/admin/users/create"><?= ui_icon('plus') ?><span>Tambah Pengguna</span></a>
</section>

<section class="panel users-panel">
    <div class="panel-heading"><div><span class="eyebrow">DAFTAR AKUN</span><h2><?= count($users) ?> pengguna terdaftar</h2><p>Akun aktif yang tersimpan pada database prototype.</p></div><span class="panel-icon-soft"><?= ui_icon('users') ?></span></div>
    <div class="data-table-wrap">
        <table class="data-table">
            <thead><tr><th>Pengguna</th><th>Role</th><th>Tim Kerja</th><th>Status</th><th>Dibuat</th></tr></thead>
            <tbody><?php foreach($users as $u): ?>
                <tr>
                    <td><div class="user-cell"><span class="avatar avatar-sm"><?= e(user_initials($u['name'])) ?></span><div><strong><?= e($u['name']) ?></strong><small><?= e($u['email']) ?></small></div></div></td>
                    <td><span class="role-badge role-<?= strtolower($u['role']) ?>"><?= e($u['role']) ?></span></td>
                    <td><?= e($u['work_team_name'] ?? '-') ?></td>
                    <td><span class="badge <?= $u['is_active'] ? 'badge-active' : 'badge-cancelled' ?>"><i></i><?= $u['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                    <td><?= e(format_date_id($u['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?></tbody>
        </table>
    </div>
</section>
