<section class="page-heading">
    <div><a class="breadcrumb" href="/admin/users"><?= ui_icon('arrow-left') ?> Pengguna</a><span class="eyebrow">ADMINISTRASI</span><h1>Tambah pengguna baru</h1><p>Buat akun dan tentukan hak akses awal pengguna SIGAS-BPS.</p></div>
</section>

<section class="panel form-panel form-panel-narrow">
    <form method="post" action="/admin/users" class="modern-form" data-loading-form>
        <?= csrf_field() ?>
        <div class="form-section no-border">
            <div class="form-section-head"><span class="section-step">01</span><div><h2>Informasi Akun</h2><p>Gunakan identitas yang mudah dikenali administrator.</p></div></div>
            <div class="form-grid form-grid-2">
                <label class="field"><span>Nama Lengkap <i>*</i></span><input name="name" value="<?= old('name') ?>" placeholder="Nama lengkap pengguna" required><?php if(!empty($errors['name'])):?><small class="field-error"><?= e($errors['name']) ?></small><?php endif;?></label>
                <label class="field"><span>Email <i>*</i></span><input type="email" name="email" value="<?= old('email') ?>" placeholder="nama@bps.go.id" required><?php if(!empty($errors['email'])):?><small class="field-error"><?= e($errors['email']) ?></small><?php endif;?></label>
                <label class="field"><span>Password Awal <i>*</i></span><input type="password" name="password" placeholder="Minimal 8 karakter" required minlength="8"><?php if(!empty($errors['password'])):?><small class="field-error"><?= e($errors['password']) ?></small><?php endif;?></label>
                <label class="field"><span>Role <i>*</i></span><select name="role" required><option value="USER" <?= old('role','USER')==='USER'?'selected':'' ?>>USER — Mengajukan nomor</option><option value="ADMIN" <?= old('role')==='ADMIN'?'selected':'' ?>>ADMIN — Akses administrasi</option></select></label>
                <label class="field span-all"><span>Tim Kerja</span><select name="work_team_id"><option value="">Tidak ditentukan</option><?php foreach($teams as $t):?><option value="<?= (int)$t['id'] ?>" <?= old('work_team_id')==(string)$t['id']?'selected':'' ?>><?= e($t['name']) ?></option><?php endforeach;?></select></label>
            </div>
        </div>
        <div class="form-actions-sticky"><div class="form-security-note"><?= ui_icon('shield') ?><span>Password disimpan dalam bentuk hash BCRYPT.</span></div><div><a class="btn btn-secondary" href="/admin/users">Batal</a><button class="btn btn-primary" type="submit"><?= ui_icon('plus') ?><span>Simpan Pengguna</span></button></div></div>
    </form>
</section>
<?php unset($_SESSION['old']); ?>
