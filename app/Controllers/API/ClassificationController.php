<?php
namespace App\Controllers\Api;

use App\Models\ClassificationItem;

final class ClassificationController
{
    /**
     * Search classifications by keyword
     * GET /api/classifications/search?q=keyword
     */
    public function search(): void
    {
        header('Content-Type: application/json');
        
        $keyword = trim($_GET['q'] ?? '');
        
        // Minimum 2 characters for search
        if (strlen($keyword) < 2) {
            echo json_encode([]);
            return;
        }
        
        $results = ClassificationItem::searchByKeyword($keyword, 20);
        echo json_encode($results);
    }
    
    /**
     * Get classifications by group
     * GET /api/classifications/by-group?group=KU
     */
    public function getByGroup(): void
    {
        header('Content-Type: application/json');
        
        $groupCode = trim($_GET['group'] ?? '');
        
        if (empty($groupCode)) {
            echo json_encode([]);
            return;
        }
        
        $results = ClassificationItem::getByGroup($groupCode);
        echo json_encode($results);
    }
}