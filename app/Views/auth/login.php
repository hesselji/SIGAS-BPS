<section class="login-page">

    <!-- =========================
         BAGIAN VISUAL KIRI
    ========================== -->
    <div class="login-visual" aria-hidden="true">

        <div class="login-glow glow-blue"></div>
        <div class="login-glow glow-orange"></div>
        <div class="login-glow glow-green"></div>

        <div class="login-brand-lockup">

            <div class="login-logo">
                <img
                    src="/assets/images/bps-logo.png"
                    alt=""
                >
            </div>

            <span class="eyebrow">
                BPS • PROTOTYPE INTERNAL
            </span>

            <h1>
                Administrasi surat yang
                <br>
                <span>lebih cepat & tertib.</span>
            </h1>

            <p>
                SIGAS-BPS membantu proses generate nomor,
                pencatatan agenda, dan pelacakan surat
                dalam satu sistem terintegrasi.
            </p>

            <div class="login-feature-grid">

                <div>
                    <span class="feature-icon blue">
                        <?= ui_icon('shield') ?>
                    </span>

                    <b>Aman & Tercatat</b>

                    <small>
                        Setiap nomor tersimpan sebagai record.
                    </small>
                </div>

                <div>
                    <span class="feature-icon orange">
                        <?= ui_icon('mail-plus') ?>
                    </span>

                    <b>Generate Otomatis</b>

                    <small>
                        Nomor dibentuk dari rule dan klasifikasi.
                    </small>
                </div>

                <div>
                    <span class="feature-icon green">
                        <?= ui_icon('chart') ?>
                    </span>

                    <b>Mudah Dipantau</b>

                    <small>
                        Dashboard statistik agenda surat.
                    </small>
                </div>

            </div>
        </div>

        <div class="visual-watermark">
            SIGAS
        </div>

    </div>


    <!-- =========================
         BAGIAN LOGIN KANAN
    ========================== -->
    <div class="login-form-side">

        <!-- Brand untuk tampilan mobile -->
        <div class="login-mobile-brand">
            <img
                src="/assets/images/bps-logo.png"
                alt="Logo BPS"
            >

            <strong>
                SIGAS-BPS
            </strong>
        </div>


        <div class="login-card">

            <!-- =========================
                 HEADER LOGIN
            ========================== -->
            <div class="login-heading">

                <span class="status-dot">
                    <i></i>
                    Sistem siap digunakan
                </span>

                <h2>
                    Selamat datang
                </h2>

                <p>
                    Masuk ke akun Anda untuk melanjutkan
                    ke SIGAS-BPS.
                </p>

            </div>


            <!-- =========================
                 FORM LOGIN
            ========================== -->
            <form
                method="post"
                action="/login"
                class="login-form"
                data-loading-form
                autocomplete="off"
            >

                <?= csrf_field() ?>


                <!-- EMAIL -->
                <label>

                    <span>
                        Email
                    </span>

                    <div class="input-shell">

                        <?= ui_icon('user') ?>

                        <input
                            type="email"
                            name="email"
                            placeholder="nama@bps.go.id"
                            required
                            autofocus
                            autocomplete="off"
                        >

                    </div>

                </label>


                <!-- PASSWORD -->
                <label>

                    <span>
                        Password
                    </span>

                    <div class="input-shell password-shell">

                        <?= ui_icon('lock') ?>

                        <input
                            id="passwordInput"
                            type="password"
                            name="password"
                            placeholder="Masukkan password"
                            required
                            autocomplete="new-password"
                        >

                        <button
                            type="button"
                            id="passwordToggle"
                            class="input-action"
                            aria-label="Tampilkan password"
                        >
                            <?= ui_icon('eye') ?>
                        </button>

                    </div>

                </label>


                <!-- BUTTON LOGIN -->
                <button
                    class="btn btn-primary btn-lg btn-block"
                    type="submit"
                >

                    <span>
                        Masuk ke SIGAS-BPS
                    </span>

                    <?= ui_icon('arrow-right') ?>

                </button>

            </form>


            <!-- =========================
                 AKUN DEMO
                 Nanti hapus saat production
            ========================== -->
            <div class="demo-credentials">

                <div class="demo-title">

                    <span>
                        <?= ui_icon('info') ?>
                    </span>

                    <b>
                        Akun demo prototype
                    </b>

                </div>


                <div class="credential-row">

                    <span>
                        Admin
                    </span>

                    <code>
                        admin@demo.local
                    </code>

                    <code>
                        Admin123!
                    </code>

                </div>


                <div class="credential-row">

                    <span>
                        User
                    </span>

                    <code>
                        user@demo.local
                    </code>

                    <code>
                        User123!
                    </code>

                </div>

            </div>


            <!-- =========================
                 DISCLAIMER
            ========================== -->
            <p class="login-disclaimer">

                Prototype untuk validasi alur kerja.
                Aturan penomoran dan hak akses akan
                disesuaikan setelah konfirmasi BPS.

            </p>

        </div>
    </div>

</section>