<?php

use App\Core\Auth;

/*
|--------------------------------------------------------------------------
| User yang sedang login
|--------------------------------------------------------------------------
|
| Digunakan untuk membedakan akun admin yang sedang digunakan,
| supaya tombol Bekukan dan Hapus tidak ditampilkan pada akun sendiri.
|
*/

$currentUserId = (int) (Auth::id() ?? 0);

?>


<section class="page-heading">

    <div>

        <span class="eyebrow">
            ADMINISTRASI
        </span>

        <h1>
            Pengguna sistem
        </h1>

        <p>
            Kelola akun yang dapat mengakses SIGAS-BPS.
        </p>

    </div>


    <a
        class="btn btn-primary"
        href="/admin/users/create"
    >

        <?= ui_icon('plus') ?>

        <span>
            Tambah Pengguna
        </span>

    </a>

</section>


<section class="panel users-panel">


    <div class="panel-heading">

        <div>

            <span class="eyebrow">
                DAFTAR AKUN
            </span>

            <h2>
                <?= count($users) ?> pengguna terdaftar
            </h2>

            <p>
                Kelola akun aktif maupun akun yang sedang dibekukan.
            </p>

        </div>


        <span class="panel-icon">

            <?= ui_icon('users') ?>

        </span>

    </div>


    <div class="data-table-wrap">


        <table class="data-table users-table">


            <thead>

                <tr>

                    <th>
                        Pengguna
                    </th>

                    <th>
                        Role
                    </th>

                    <th>
                        Tim Kerja
                    </th>

                    <th>
                        Status
                    </th>

                    <th>
                        Dibuat
                    </th>

                    <th>
                        Aksi
                    </th>

                </tr>

            </thead>


            <tbody>


                <?php if (empty($users)): ?>


                    <tr>

                        <td
                            colspan="6"
                            class="table-empty"
                        >

                            <div class="empty-state">

                                <span class="empty-state-icon">

                                    <?= ui_icon('users') ?>

                                </span>


                                <strong>
                                    Belum ada pengguna
                                </strong>


                                <p>
                                    Tambahkan pengguna baru untuk mulai
                                    memberikan akses ke SIGAS-BPS.
                                </p>


                                <a
                                    href="/admin/users/create"
                                    class="btn btn-primary btn-sm"
                                >

                                    <?= ui_icon('plus') ?>

                                    <span>
                                        Tambah Pengguna
                                    </span>

                                </a>

                            </div>

                        </td>

                    </tr>


                <?php else: ?>


                    <?php foreach ($users as $u): ?>


                        <?php

                        /*
                        |--------------------------------------------------------------------------
                        | State pengguna
                        |--------------------------------------------------------------------------
                        */

                        $userId =
                            (int) $u['id'];

                        $isSelf =
                            $userId === $currentUserId;

                        $isActive =
                            (int) $u['is_active'] === 1;

                        ?>


                        <tr>


                            <!-- =========================================
                                 PENGGUNA
                            ========================================== -->

                            <td>


                                <div class="user-cell">


                                    <span class="avatar avatar-sm">

                                        <?= e(
                                            user_initials(
                                                $u['name']
                                            )
                                        ) ?>

                                    </span>


                                    <div class="user-cell-copy">


                                        <strong>

                                            <?= e(
                                                $u['name']
                                            ) ?>


                                            <?php if ($isSelf): ?>

                                                <span class="you-badge">
                                                    Anda
                                                </span>

                                            <?php endif; ?>


                                        </strong>


                                        <small>

                                            <?= e(
                                                $u['email']
                                            ) ?>

                                        </small>


                                    </div>


                                </div>


                            </td>


                            <!-- =========================================
                                 ROLE
                            ========================================== -->

                            <td>


                                <span
                                    class="role-badge role-<?= e(
                                        strtolower(
                                            $u['role']
                                        )
                                    ) ?>"
                                >

                                    <?= e(
                                        $u['role']
                                    ) ?>

                                </span>


                            </td>


                            <!-- =========================================
                                 TIM KERJA
                            ========================================== -->

                            <td>


                                <span class="team-name">

                                    <?= e(
                                        $u['work_team_name']
                                        ?? '-'
                                    ) ?>

                                </span>


                            </td>


                            <!-- =========================================
                                 STATUS
                            ========================================== -->

                            <td>


                                <span
                                    class="badge <?= $isActive
                                        ? 'badge-active'
                                        : 'badge-cancelled'
                                    ?>"
                                >

                                    <i></i>

                                    <?= $isActive
                                        ? 'Aktif'
                                        : 'Dibekukan'
                                    ?>

                                </span>


                            </td>


                            <!-- =========================================
                                 DIBUAT
                            ========================================== -->

                            <td>


                                <span class="date-cell">

                                    <?= e(
                                        format_date_id(
                                            $u['created_at']
                                        )
                                    ) ?>

                                </span>


                            </td>


                            <!-- =========================================
                                 AKSI
                            ========================================== -->

                            <td>


                                <div class="user-actions">


                                    <!-- =================================
                                         EDIT
                                    ================================== -->

                                    <a
                                        href="/admin/users/<?= $userId ?>/edit"
                                        class="btn btn-ghost btn-sm user-action-btn"
                                        title="Edit pengguna"
                                    >

                                        <?= ui_icon('settings') ?>

                                        <span>
                                            Edit
                                        </span>

                                    </a>


                                    <?php if (!$isSelf): ?>


                                        <!-- =============================
                                             BEKUKAN / AKTIFKAN
                                        ============================== -->

                                        <form
                                            method="post"
                                            action="/admin/users/<?= $userId ?>/toggle-status"
                                            class="user-action-form"
                                            onsubmit="return confirm(
                                                '<?= $isActive
                                                    ? 'Bekukan akun ' . e($u['name']) . '? Pengguna tidak akan dapat login sampai akun diaktifkan kembali.'
                                                    : 'Aktifkan kembali akun ' . e($u['name']) . '?'
                                                ?>'
                                            );"
                                        >


                                            <?= csrf_field() ?>


                                            <button
                                                type="submit"
                                                class="btn btn-sm user-action-btn <?= $isActive
                                                    ? 'btn-freeze'
                                                    : 'btn-activate'
                                                ?>"
                                                title="<?= $isActive
                                                    ? 'Bekukan akun'
                                                    : 'Aktifkan akun'
                                                ?>"
                                            >


                                                <?php if ($isActive): ?>

                                                    <?= ui_icon('ban') ?>

                                                    <span>
                                                        Bekukan
                                                    </span>

                                                <?php else: ?>

                                                    <?= ui_icon('refresh') ?>

                                                    <span>
                                                        Aktifkan
                                                    </span>

                                                <?php endif; ?>


                                            </button>


                                        </form>


                                        <!-- =============================
                                             HAPUS
                                        ============================== -->

                                        <form
                                            method="post"
                                            action="/admin/users/<?= $userId ?>/delete"
                                            class="user-action-form"
                                            onsubmit="return confirm(
                                                'Hapus akun <?= e($u['name']) ?>? Akun akan hilang dari daftar pengguna, tetapi histori surat dan audit tetap dipertahankan.'
                                            );"
                                        >


                                            <?= csrf_field() ?>


                                            <button
                                                type="submit"
                                                class="btn btn-danger btn-sm user-action-btn"
                                                title="Hapus pengguna"
                                            >

                                                <?= ui_icon('x') ?>

                                                <span>
                                                    Hapus
                                                </span>

                                            </button>


                                        </form>


                                    <?php else: ?>


                                        <!-- =============================
                                             AKUN SENDIRI
                                        ============================== -->

                                        <span
                                            class="self-account-tag"
                                            title="Akun yang sedang digunakan"
                                        >

                                            Akun Anda

                                        </span>


                                    <?php endif; ?>


                                </div>


                            </td>


                        </tr>


                    <?php endforeach; ?>


                <?php endif; ?>


            </tbody>


        </table>


    </div>


</section>