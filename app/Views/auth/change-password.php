<section class="page-heading">

    <div>

        <span class="eyebrow">
            KEAMANAN AKUN
        </span>

        <h1>
            Ganti Password
        </h1>

        <p>
            Perbarui password akun SIGAS-BPS Anda
            untuk menjaga keamanan akun.
        </p>

    </div>

</section>


<section class="panel form-panel form-panel-narrow">


    <form
        method="post"
        action="/account/password"
        class="modern-form"
        data-loading-form
        autocomplete="off"
    >


        <?= csrf_field() ?>


        <div class="form-section no-border">


            <!-- =================================
                 HEADER FORM
            ================================== -->

            <div class="form-section-head">


                <span class="section-step">

                    <?= ui_icon('lock') ?>

                </span>


                <div>

                    <h2>
                        Keamanan Password
                    </h2>

                    <p>
                        Masukkan password saat ini
                        sebelum membuat password baru.
                    </p>

                </div>


            </div>


            <!-- =================================
                 FORM FIELDS
            ================================== -->

            <div class="form-grid">


                <!-- PASSWORD SAAT INI -->

                <label class="field">

                    <span>
                        Password Saat Ini
                        <i>*</i>
                    </span>


                    <input
                        type="password"
                        name="current_password"
                        placeholder="Masukkan password saat ini"
                        required
                        autocomplete="current-password"
                    >


                    <?php if (
                        !empty(
                            $errors['current_password']
                        )
                    ): ?>

                        <small class="field-error">

                            <?= e(
                                $errors[
                                    'current_password'
                                ]
                            ) ?>

                        </small>

                    <?php endif; ?>


                </label>


                <!-- PASSWORD BARU -->

                <label class="field">

                    <span>
                        Password Baru
                        <i>*</i>
                    </span>


                    <input
                        type="password"
                        name="new_password"
                        placeholder="Minimal 8 karakter"
                        minlength="8"
                        maxlength="72"
                        required
                        autocomplete="new-password"
                    >


                    <?php if (
                        !empty(
                            $errors['new_password']
                        )
                    ): ?>

                        <small class="field-error">

                            <?= e(
                                $errors[
                                    'new_password'
                                ]
                            ) ?>

                        </small>

                    <?php endif; ?>


                    <small class="field-help">
                        Gunakan minimal 8 karakter.
                        Hindari menggunakan password
                        yang sama dengan akun lain.
                    </small>


                </label>


                <!-- KONFIRMASI -->

                <label class="field">

                    <span>
                        Konfirmasi Password Baru
                        <i>*</i>
                    </span>


                    <input
                        type="password"
                        name="new_password_confirmation"
                        placeholder="Ketik ulang password baru"
                        minlength="8"
                        maxlength="72"
                        required
                        autocomplete="new-password"
                    >


                    <?php if (
                        !empty(
                            $errors[
                                'new_password_confirmation'
                            ]
                        )
                    ): ?>

                        <small class="field-error">

                            <?= e(
                                $errors[
                                    'new_password_confirmation'
                                ]
                            ) ?>

                        </small>

                    <?php endif; ?>


                </label>


            </div>


        </div>


        <!-- =====================================
             ACTION
        ====================================== -->

        <div class="form-actions-sticky">


            <div class="form-security-note">

                <?= ui_icon('shield') ?>

                <span>
                    Password disimpan dalam bentuk hash
                    BCRYPT dan tidak dapat dilihat kembali,
                    termasuk oleh administrator.
                </span>

            </div>


            <div>


                <a
                    class="btn btn-secondary"
                    href="/dashboard"
                >
                    Batal
                </a>


                <button
                    class="btn btn-primary"
                    type="submit"
                >

                    <?= ui_icon('lock') ?>

                    <span>
                        Simpan Password Baru
                    </span>

                </button>


            </div>


        </div>


    </form>


</section>