<?php
error_reporting(0);
ini_set('display_errors', 0);

include('../../../cors.php');
include('../../../methods.php');
include('../../../inc/dbcon.php');
include('../../../verify_token.php');

try {
    getMethod('GET');
    global $db;

    $userId = $userData->id;
    $userObjectId = new MongoDB\BSON\ObjectId($userId);

    // Pagination
    $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;
    $limit = isset($_GET['limit']) ? min((int) $_GET['limit'], 1000) : 100;
    if ($limit > 1000) {
        http_response_code(400);
        echo json_encode(["status" => false, "message" => "Too many records.", "data" => [], "total" => 0]);
        exit;
    }

    $match = ['project_user_id' => $userObjectId];

    if (!empty($_GET['search'])) {
        $search = $_GET['search'];
        $match['$or'] = array_map(fn($f) => [$f => new MongoDB\BSON\Regex($search, 'i')], [
            'project_name',
            'project_description',
            'project_tech',
            'project_client',
            'project_lead',
            'project_status',
            'project_manager',
            'project_startDate',
            'project_deadlineDate',
            'project_budget',
            'project_priority',
            'management_tool',
            'repo_tool',
            'management_url',
            'repo_url',
            'project_type',
            'project_approval_status'
        ]);
    }

    // Filters
    if (!empty($_GET['filterModel'])) {
        $filterModel = json_decode($_GET['filterModel'], true);
        $andFilters = [];
        foreach ($filterModel as $col => $f) {
            $conds = $f['conditions'] ?? [$f];
            $group = [];
            foreach ($conds as $cond) {
                $val = $cond['filter'];
                $type = $cond['type'] ?? 'contains';
                switch ($type) {
                    case 'equals':
                        $group[] = [$col => $val];
                        break;
                    case 'startsWith':
                        $group[] = [$col => new MongoDB\BSON\Regex('^' . preg_quote($val), 'i')];
                        break;
                    case 'endsWith':
                        $group[] = [$col => new MongoDB\BSON\Regex(preg_quote($val) . '$', 'i')];
                        break;
                    case 'doesNotContain':
                        $group[] = [$col => ['$not' => new MongoDB\BSON\Regex(preg_quote($val), 'i')]];
                        break;
                    case 'equals':
                        $group[] = [$col => $val];
                        break;
                    case 'notBlank':
                        $group[] = [$col => ['$ne' => ''], $col => ['$exists' => true]];
                        break;
                    case 'blank':
                        $group[] = ['$or' => [[$col => ['$exists' => false]], [$col => '']]];
                        break;
                    default:
                        $group[] = [$col => new MongoDB\BSON\Regex(preg_quote($val), 'i')];
                }
            }
            $andFilters[] = ($f['operator'] ?? '') === 'OR'
                ? ['$or' => $group]
                : ['$and' => $group];
        }
        if ($andFilters) {
            $match['$and'] = $andFilters;
        }
    }

    $sortStage = ['created_at' => -1];
  if (!empty($_GET['sort'])) {
    $sortParam = $_GET['sort'];
    $lastUnderscore = strrpos($sortParam, '_');
    if ($lastUnderscore !== false) {
        $col = substr($sortParam, 0, $lastUnderscore); // e.g. project_name
        $dir = substr($sortParam, $lastUnderscore + 1); // e.g. desc

        $dir = strtolower($dir) === 'asc' ? 1 : -1; // ✅ handles asc/desc safely

        $allowed = [
            'project_name', 'project_description', 'project_tech', 'project_status',
            'project_lead', 'project_manager', 'project_client',
            'project_startDate', 'project_deadlineDate', 'project_budget',
            'project_priority', 'project_location', 'management_tool',
            'repo_tool', 'management_url', 'repo_url', 'project_type',
            'project_approval_status', 'created_at'
        ];

        if (in_array($col, $allowed)) {
            $sortStage = [$col => $dir];
        }
    }
}

    $pipeline = [
        ['$match' => $match],
        [
            '$facet' => [
                'data' => [
                    ['$sort' => $sortStage],
                    ['$skip' => $offset],
                    ['$limit' => $limit],
                ],
                'count' => [['$count' => 'total']]
            ]
        ],
        ['$unwind' => ['path' => '$count', 'preserveNullAndEmptyArrays' => true]],
        [
            '$project' => [
                'data' => 1,
                'total' => ['$ifNull' => ['$count.total', 0]]
            ]
        ]
    ];

    $cursor = $db->em_projects->aggregate($pipeline);
    $res = $cursor->toArray()[0] ?? ['data' => [], 'total' => 0];

    http_response_code(200);
    echo json_encode([
        'status' => true,
        'message' => 'Projects fetched successfully',
        'data' => $res['data'],
        'total' => (int) $res['total']
    ]);

} catch (Exception $ex) {
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'message' => $ex->getMessage(),
        'data' => [],
        'total' => 0
    ]);
}
