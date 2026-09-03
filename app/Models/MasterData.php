<?php
namespace App\Models;

use App\Core\Database;

final class MasterData
{
    public static function workTeams(): array { return Database::connection()->query('SELECT id,name FROM work_teams WHERE is_active=1 ORDER BY name')->fetchAll(); }
    public static function letterTypes(): array { return Database::connection()->query('SELECT lt.id,lt.code,lt.name,nr.code AS rule_code,nr.unit_code FROM letter_types lt JOIN numbering_rules nr ON nr.id=lt.numbering_rule_id WHERE lt.is_active=1 ORDER BY lt.name')->fetchAll(); }
    public static function sensitivities(): array { return Database::connection()->query('SELECT code,name,prefix FROM letter_sensitivities WHERE is_active=1 ORDER BY sort_order')->fetchAll(); }
    public static function archiveTypes(): array { return Database::connection()->query('SELECT code,name,short_code FROM archive_types WHERE is_active=1 ORDER BY name')->fetchAll(); }

    public static function classifications(string $scope, ?string $parent = null): array
    {
        if ($parent === null) {
            $stmt = Database::connection()->prepare('SELECT code,name FROM classifications WHERE scope_key=? AND level=2 AND is_active=1 ORDER BY code');
            $stmt->execute([$scope]);
        } else {
            $stmt = Database::connection()->prepare('SELECT code,name FROM classifications WHERE scope_key=? AND level=3 AND parent_code=? AND is_active=1 ORDER BY code');
            $stmt->execute([$scope, $parent]);
        }
        return $stmt->fetchAll();
    }

    public static function validClassification(string $scope, string $parent, string $child): bool
    {
        $stmt = Database::connection()->prepare('SELECT COUNT(*) FROM classifications WHERE scope_key=? AND level=3 AND parent_code=? AND code=? AND is_active=1');
        $stmt->execute([$scope,$parent,$child]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
