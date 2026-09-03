<?php
namespace App\Services;

use App\Core\Database;
use PDO;

final class LetterNumberGenerator
{
    public function generate(array $input): array
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('SELECT lt.id,lt.name,nr.id rule_id,nr.code rule_code,nr.unit_code,nr.pattern FROM letter_types lt JOIN numbering_rules nr ON nr.id=lt.numbering_rule_id WHERE lt.id=? AND lt.is_active=1 FOR UPDATE');
            $stmt->execute([(int)$input['letter_type_id']]);
            $type = $stmt->fetch();
            if (!$type) throw new \RuntimeException('Jenis surat tidak valid.');

            $stmt = $pdo->prepare('SELECT prefix FROM letter_sensitivities WHERE code=? AND is_active=1');
            $stmt->execute([$input['sensitivity']]);
            $prefix = $stmt->fetchColumn();
            if ($prefix === false) throw new \RuntimeException('Sifat surat tidak valid.');

            $year = (int) date('Y', strtotime($input['letter_date']));
            // Pastikan row sequence tersedia. ON DUPLICATE KEY membuat inisialisasi
            // awal tahun/rule tetap aman bila dua request datang hampir bersamaan.
            $ensure = $pdo->prepare('INSERT INTO number_sequences (numbering_rule_id,year,current_number,created_at,updated_at) VALUES (?,?,0,NOW(),NOW()) ON DUPLICATE KEY UPDATE updated_at=updated_at');
            $ensure->execute([(int)$type['rule_id'],$year]);

            // Lock row sequence sampai transaksi selesai agar dua user tidak
            // memperoleh nomor yang sama.
            $seqStmt = $pdo->prepare('SELECT id,current_number FROM number_sequences WHERE numbering_rule_id=? AND year=? FOR UPDATE');
            $seqStmt->execute([(int)$type['rule_id'],$year]);
            $sequence = $seqStmt->fetch();
            if (!$sequence) throw new \RuntimeException('Sequence nomor tidak dapat dibuat.');
            $seqId=(int)$sequence['id'];
            $current=(int)$sequence['current_number'];
            $next=$current+1;
            $pdo->prepare('UPDATE number_sequences SET current_number=?,updated_at=NOW() WHERE id=?')->execute([$next,$seqId]);
            $agenda = str_pad((string)$next, 3, '0', STR_PAD_LEFT);
            $kka = $input['classification_parent'] . '.' . $input['classification_child'];
            $number = strtr($type['pattern'], [
                '{PREFIX}' => $prefix,
                '{SEQ3}' => $agenda,
                '{UNIT}' => $type['unit_code'],
                '{KKA}' => $kka,
                '{YEAR}' => (string)$year,
            ]);

            $insert=$pdo->prepare('INSERT INTO outgoing_letters (agenda_number,letter_number,year,letter_type_id,work_team_id,system_type,letter_date,sensitivity,uses_budget,archive_type,scope_key,classification_parent,classification_child,classification_code,recipient,subject,notes,status,requested_by,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,\'ACTIVE\',?,NOW(),NOW())');
            $insert->execute([$agenda,$number,$year,(int)$input['letter_type_id'],(int)$input['work_team_id'],$input['system_type'],$input['letter_date'],$input['sensitivity'],$input['uses_budget'],$input['archive_type'],$input['scope_key'],$input['classification_parent'],$input['classification_child'],$kka,$input['recipient'],$input['subject'],$input['notes'] ?: null,(int)$input['requested_by']]);
            $id=(int)$pdo->lastInsertId();
            $pdo->commit();
            return ['id'=>$id,'agenda_number'=>$agenda,'letter_number'=>$number,'letter_type_name'=>$type['name']];
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }
}
