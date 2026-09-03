<section class="hero compact">
    <div><span class="eyebrow">ADMIN</span>
        <h1>Tambah Pengguna</h1>
    </div>
</section>
<section class="card form-card">
    <form method="post" action="/admin/users" class="form-grid"><?= csrf_field() ?><label>Nama<input name="name" required></label><label>Email<input type="email" name="email" required></label><label>Password<input type="password" name="password" minlength="8" required></label><label>Role<select name="role">
                <option value="USER">USER</option>
                <option value="ADMIN">ADMIN</option>
            </select></label><label class="span-2">Tim kerja<select name="work_team_id">
                <option value="">Tanpa tim</option><?php foreach ($teams as $t): ?><option value="<?= $t['id'] ?>"><?= e($t['name']) ?></option><?php endforeach; ?>
            </select></label>
        <div class="span-2 actions"><button class="btn primary" type="submit">Simpan User</button><a class="btn ghost" href="/admin/users">Batal</a></div>
    </form>
</section>