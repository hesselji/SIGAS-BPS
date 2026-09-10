<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\View;
use App\Models\AuditLog;
use App\Models\MasterData;
use App\Models\OutgoingLetter;
use App\Services\LetterNumberGenerator;

final class OutgoingLetterController
{
    public function index(): void
    {
        Auth::requireLogin();

        $filters = [
            'q' => trim($_GET['q'] ?? ''),
            'team' => $_GET['team'] ?? '',
            'sensitivity' => $_GET['sensitivity'] ?? '',
            'status' => $_GET['status'] ?? '',
            'year' => $_GET['year'] ?? date('Y'),
        ];

        View::render('letters/index', [
            'letters' => OutgoingLetter::search($filters),
            'filters' => $filters,
            'teams' => MasterData::workTeams(),
            'sensitivities' => MasterData::sensitivities(),
        ]);
    }

    public function create(): void
    {
        Auth::requireLogin();

        View::render('letters/create', [
            'teams' => MasterData::workTeams(),
            'types' => MasterData::letterTypes(),
            'sensitivities' => MasterData::sensitivities(),
            'archiveTypes' => MasterData::archiveTypes(),
        ]);
    }

    public function store(): void
    {
        Auth::requireLogin();
        Csrf::validate($_POST['_token'] ?? null);

        $input = [
            'letter_type_id' => (int)($_POST['letter_type_id'] ?? 0),
            'work_team_id' => (int)($_POST['work_team_id'] ?? 0),
            'system_type' => $_POST['system_type'] ?? '',
            'letter_date' => $_POST['letter_date'] ?? '',
            'sensitivity' => $_POST['sensitivity'] ?? '',
            'uses_budget' => $_POST['uses_budget'] ?? '',
            'archive_type' => $_POST['archive_type'] ?? '',
            'classification_parent' => strtoupper(
                trim($_POST['classification_parent'] ?? '')
            ),
            'classification_child' => trim(
                $_POST['classification_child'] ?? ''
            ),
            'recipient' => trim($_POST['recipient'] ?? ''),
            'subject' => trim($_POST['subject'] ?? ''),
            'notes' => trim($_POST['notes'] ?? ''),
            'requested_by' => Auth::id(),
        ];

        $errors = [];

        foreach ([
            'letter_type_id',
            'work_team_id',
            'system_type',
            'letter_date',
            'sensitivity',
            'uses_budget',
            'archive_type',
            'classification_parent',
            'classification_child',
            'recipient',
            'subject'
        ] as $field) {
            if (empty($input[$field])) {
                $errors[$field] = 'Wajib diisi.';
            }
        }

        if (
            !in_array(
                $input['system_type'],
                ['SRIKANDI', 'NON_SRIKANDI'],
                true
            )
        ) {
            $errors['system_type'] = 'Pilihan tidak valid.';
        }

        if (
            !in_array(
                $input['uses_budget'],
                ['Y', 'T'],
                true
            )
        ) {
            $errors['uses_budget'] = 'Pilihan tidak valid.';
        }

        if (
            !in_array(
                $input['archive_type'],
                ['FASILITATIF', 'SUBSTANTIF'],
                true
            )
        ) {
            $errors['archive_type'] = 'Pilihan tidak valid.';
        }

        /*
         * =========================================================
         * TAHAP 2
         * ATURAN TANGGAL 62710 / 62711
         * =========================================================
         *
         * MAIN_62710:
         * tanggal surat tidak boleh sebelum hari ini.
         *
         * SUBBAG_62711:
         * tanggal surat tetap fleksibel dan boleh tanggal lampau.
         *
         * Validasi ini dilakukan di backend agar tidak bisa
         * dilewati hanya dengan memanipulasi HTML / JavaScript.
         */

        $selectedType = null;

        foreach (MasterData::letterTypes() as $candidateType) {
            if (
                (int)$candidateType['id']
                ===
                (int)$input['letter_type_id']
            ) {
                $selectedType = $candidateType;
                break;
            }
        }

        if (
            !$selectedType
            &&
            $input['letter_type_id']
        ) {
            $errors['letter_type_id'] =
                'Jenis surat atau aturan nomor tidak aktif.';
        }

        $dateObj = $input['letter_date']
            ? \DateTime::createFromFormat(
                '!Y-m-d',
                $input['letter_date']
            )
            : false;

        if (
            $input['letter_date']
            &&
            (
                !$dateObj
                ||
                $dateObj->format('Y-m-d')
                    !== $input['letter_date']
            )
        ) {
            $errors['letter_date'] =
                'Tanggal surat tidak valid.';
        } elseif (
            $selectedType
            &&
            ($selectedType['rule_code'] ?? '')
                === 'MAIN_62710'
            &&
            $input['letter_date'] < date('Y-m-d')
        ) {
            $errors['letter_date'] =
                'Untuk jalur 62710, tanggal surat tidak boleh sebelum hari ini.';
        }

        /*
         * Legacy scope tetap dipertahankan untuk compatibility
         * dengan struktur record versi sebelumnya.
         */
        $input['scope_key'] =
            $input['uses_budget']
            .
            (
                $input['archive_type'] === 'FASILITATIF'
                    ? 'F'
                    : 'S'
            );

        if (
            $input['archive_type']
            &&
            $input['classification_parent']
            &&
            $input['classification_child']
            &&
            !MasterData::validClassification(
                $input['archive_type'],
                $input['classification_parent'],
                $input['classification_child']
            )
        ) {
            $errors['classification_child'] =
                'Kode klasifikasi tidak sesuai jenis arsip atau kelompok KKA yang dipilih.';
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;

            header('Location:/letters/create');
            exit;
        }

        try {
            $result = (
                new LetterNumberGenerator()
            )->generate($input);

            AuditLog::write(
                Auth::id(),
                'GENERATE_NUMBER',
                'outgoing_letter',
                $result['id'],
                [
                    'letter_number' => $result['letter_number'],
                    'rule_code' => $result['rule_code']
                ]
            );

            unset($_SESSION['old']);

            $_SESSION['flash_success'] =
                'Nomor surat berhasil di-generate: '
                .
                $result['letter_number'];

            header(
                'Location:/letters/'
                .
                $result['id']
            );

            exit;
        } catch (\Throwable $e) {
            $_SESSION['flash_error'] =
                'Gagal generate nomor: '
                .
                $e->getMessage();

            $_SESSION['old'] = $_POST;

            header('Location:/letters/create');
            exit;
        }
    }

    public function show(string $id): void
    {
        Auth::requireLogin();

        $letter = OutgoingLetter::find(
            (int)$id
        );

        if (!$letter) {
            http_response_code(404);

            View::render(
                'errors/404'
            );

            return;
        }

        View::render(
            'letters/show',
            [
                'letter' => $letter
            ]
        );
    }

    public function cancel(string $id): void
    {
        Auth::requireAdmin();

        Csrf::validate(
            $_POST['_token'] ?? null
        );

        $reason = trim(
            $_POST['reason'] ?? ''
        );

        if (strlen($reason) < 5) {
            $_SESSION['flash_error'] =
                'Alasan pembatalan minimal 5 karakter.';

            header(
                'Location:/letters/'
                .
                $id
            );

            exit;
        }

        OutgoingLetter::cancel(
            (int)$id,
            (int)Auth::id(),
            $reason
        );

        AuditLog::write(
            Auth::id(),
            'CANCEL_NUMBER',
            'outgoing_letter',
            (int)$id,
            [
                'reason' => $reason
            ]
        );

        $_SESSION['flash_success'] =
            'Nomor ditandai DIBATALKAN dan tetap tersimpan sebagai audit trail.';

        header(
            'Location:/letters/'
            .
            $id
        );

        exit;
    }

    public function classificationGroups(): void
    {
        Auth::requireLogin();

        header(
            'Content-Type: application/json; charset=utf-8'
        );

        $archiveType =
            $_GET['archive_type'] ?? '';

        if (
            !in_array(
                $archiveType,
                [
                    'FASILITATIF',
                    'SUBSTANTIF'
                ],
                true
            )
        ) {
            http_response_code(422);

            echo json_encode([
                'success' => false,
                'message' =>
                    'Jenis arsip tidak valid'
            ]);

            return;
        }

        echo json_encode(
            [
                'success' => true,
                'data' =>
                    MasterData::classificationGroups(
                        $archiveType
                    ),
            ],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function classificationItems(): void
    {
        Auth::requireLogin();

        header(
            'Content-Type: application/json; charset=utf-8'
        );

        $group = strtoupper(
            trim(
                $_GET['group'] ?? ''
            )
        );

        if (
            !preg_match(
                '/^[A-Z]{2,3}$/',
                $group
            )
        ) {
            http_response_code(422);

            echo json_encode([
                'success' => false,
                'message' =>
                    'Kelompok klasifikasi tidak valid'
            ]);

            return;
        }

        echo json_encode(
            [
                'success' => true,
                'data' =>
                    MasterData::classificationItems(
                        $group
                    ),
            ],
            JSON_UNESCAPED_UNICODE
        );
    }

    /**
     * Endpoint legacy tetap dipertahankan supaya
     * bookmark / JS versi sebelumnya tidak rusak.
     */
    public function classifications(): void
    {
        Auth::requireLogin();

        header(
            'Content-Type: application/json; charset=utf-8'
        );

        $scope =
            $_GET['scope'] ?? '';

        $parent =
            $_GET['parent'] ?? null;

        if (
            !preg_match(
                '/^(YF|YS|TF|TS)$/',
                $scope
            )
        ) {
            http_response_code(422);

            echo json_encode([
                'success' => false,
                'message' =>
                    'Scope tidak valid'
            ]);

            return;
        }

        echo json_encode(
            [
                'success' => true,
                'data' =>
                    MasterData::classifications(
                        $scope,
                        $parent ?: null
                    ),
            ],
            JSON_UNESCAPED_UNICODE
        );
    }
}