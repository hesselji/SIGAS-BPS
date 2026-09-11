<section class="login-page">
    <div class="login-visual" aria-hidden="true">
        <div class="login-glow glow-blue"></div>
        <div class="login-glow glow-orange"></div>
        <div class="login-glow glow-green"></div>

        <div class="login-brand-lockup">
            <div class="login-brand-mark">
                <div class="login-logo"><img src="/assets/images/penamas-icon.png" alt=""></div>
                <div class="login-brand-name">
                    <strong>PENA MAS</strong>
                    <small>Penomoran Agenda dan Manajemen Arsip Surat</small>
                </div>
            </div>

            <span class="eyebrow">BPS KOTA PALANGKA RAYA • PROTOTYPE INTERNAL</span>
            <h1>Administrasi surat yang<br><span>lebih cepat & tertib.</span></h1>
            <p>PENA MAS membantu proses penomoran, pencatatan agenda, pengelolaan arsip, dan pelacakan surat dalam satu sistem terintegrasi.</p>

            <div class="login-feature-grid">
                <div><span class="feature-icon blue"><?= ui_icon('shield') ?></span><b>Aman & Tercatat</b><small>Setiap nomor tersimpan sebagai record.</small></div>
                <div><span class="feature-icon orange"><?= ui_icon('mail-plus') ?></span><b>Penomoran Otomatis</b><small>Nomor dibentuk dari rule dan klasifikasi.</small></div>
                <div><span class="feature-icon green"><?= ui_icon('archive') ?></span><b>Arsip Terkelola</b><small>Agenda dan KKA mudah dipantau.</small></div>
            </div>
        </div>
        <div class="visual-watermark">PENA</div>
    </div>

    <div class="login-form-side">
        <div class="login-mobile-brand">
            <img src="/assets/images/penamas-icon.png" alt="Logo PENA MAS"><strong>PENA MAS</strong>
        </div>

        <div class="login-card">

          <div class="login-card-brand">
    <img src="/assets/images/user-icon.svg" alt="User Icon">
    <span class="login-label">LOGIN</span>
</div>

            <div class="login-heading">
                <span class="status-dot"><i></i> Sistem siap digunakan</span>
                <h2>Selamat datang</h2>
                <p>Masuk ke akun Anda untuk melanjutkan ke PENA MAS.</p>
            </div>

            <form method="post" action="/login" class="login-form" data-loading-form autocomplete="off">
                <?= csrf_field() ?>
                <label>
                    <span>Email</span>
                    <div class="input-shell"><?= ui_icon('user') ?><input type="email" name="email" placeholder="nama@bps.go.id" required autofocus autocomplete="off"></div>
                </label>
                <label>
                    <span>Password</span>
                    <div class="input-shell password-shell"><?= ui_icon('lock') ?><input id="passwordInput" type="password" name="password" placeholder="Masukkan password" required autocomplete="new-password"><button type="button" id="passwordToggle" class="input-action" aria-label="Tampilkan password"><?= ui_icon('eye') ?></button></div>
                </label>
                <button class="btn btn-primary btn-lg btn-block" type="submit"><span>Masuk ke PENA MAS</span><?= ui_icon('arrow-right') ?></button>
            </form>

            <div class="demo-credentials">
                <div class="demo-title"><span><?= ui_icon('info') ?></span><b>Akun demo prototype</b></div>
                <div class="credential-row"><span>Admin</span><code>admin@demo.local</code><code>Admin123!</code></div>
                <div class="credential-row"><span>User</span><code>user@demo.local</code><code>User123!</code></div>
                <div class="credential-row"><span>Masuk</span><code>incoming@demo.local</code><code>Incoming123!</code></div>
            </div>
            <p class="login-disclaimer">Prototype untuk validasi alur kerja. Gunakan data dummy untuk pengujian; jangan masukkan data rahasia/produksi pada prototype.</p>
        </div>
    </div>
</section>
