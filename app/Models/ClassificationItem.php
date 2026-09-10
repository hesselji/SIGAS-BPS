<?php
namespace App\Models;

use App\Core\Database;

final class ClassificationItem
{
    /**
     * Search classification items by keyword
     * Searches in code, name, and group name
     */
    public static function searchByKeyword(string $keyword, int $limit = 20): array
    {
        $db = Database::connection();
        $stmt = $db->prepare("
            SELECT 
                ci.id,
                ci.code,
                ci.name,
                ci.group_code,
                cg.name as group_name,
                cg.type as group_type
            FROM classification_items ci
            INNER JOIN classification_groups cg ON ci.group_code = cg.code
            WHERE ci.code LIKE :keyword 
               OR ci.name LIKE :keyword
               OR cg.name LIKE :keyword
            ORDER BY ci.code ASC
            LIMIT :limit
        ");
        
        $searchTerm = "%{$keyword}%";
        $stmt->bindValue(':keyword', $searchTerm, \PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Get classification items by group code
     */
    public static function getByGroup(string $groupCode): array
    {
        $stmt = Database::connection()->prepare("
            SELECT id, code, name, group_code
            FROM classification_items
            WHERE group_code = ?
            ORDER BY code ASC
        ");
        $stmt->execute([$groupCode]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Get single classification item by code
     */
    public static function findByCode(string $code): ?array
    {
        $stmt = Database::connection()->prepare("
            SELECT 
                ci.id,
                ci.code,
                ci.name,
                ci.group_code,
                cg.name as group_name
            FROM classification_items ci
            INNER JOIN classification_groups cg ON ci.group_code = cg.code
            WHERE ci.code = ?
            LIMIT 1
        ");
        $stmt->execute([$code]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }
}