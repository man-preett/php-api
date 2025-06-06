<?php
error_reporting(0);
ini_set('display_errors', 0);
include('../../../cors.php');
include('../../../methods.php');
include('../../../inc/dbcon.php');
include('../../../verify_token.php');

try {
    getMethod(method: 'GET');

    global $conn;

    $userId = $userData->id;

    // Pagination
    $startRow = isset($_GET['startRow']) ? (int) $_GET['startRow'] : 0;
    $endRow = isset($_GET['endRow']) ? (int) $_GET['endRow'] : 50;
    $limit = $endRow - $startRow;

    $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
    $sortQuery = '';
    $filterQuery = '';

    // Sort logic
    if (isset($_GET['sort']) && !empty($_GET['sort'])) {
        $parts = explode('_', $_GET['sort']);
        $direction = strtoupper(array_pop($parts));
        $column = implode('_', $parts);

        $allowedColumns = [
            'project_name',
            'project_description',
            'project_tech',
            'project_status',
            'project_lead',
            'project_manager',
            'project_client',
            'project_startDate',
            'project_deadlineDate',
            'project_budget',
            'project_priority',
            'project_location',
            'management_tool',
            'repo_tool',
            'management_url',
            'repo_url',
            'project_type',
            'project_approval_status'
        ];

        if (in_array($column, $allowedColumns) && in_array($direction, ['ASC', 'DESC'])) {
            $sortQuery = " ORDER BY $column $direction";
        }
    }

    // Search logic
    $searchQuery = "";
    if (!empty($search)) {
        $searchQuery = " AND (
            project_name LIKE '%$search%' OR
            project_description LIKE '%$search%' OR
            project_tech LIKE '%$search%' OR
            project_client LIKE '%$search%' OR
            project_lead LIKE  '%$search%' OR
            project_status LIKE '%$search%' OR
            project_manager LIKE '%$search%' OR
            project_startDate LIKE '%$search%' OR
            project_deadlineDate LIKE '%$search%' OR
            project_budget LIKE '%$search%' OR
            project_priority LIKE '%$search%' OR
            management_tool LIKE '%$search%' OR
            repo_tool LIKE '%$search%' OR
            management_url LIKE '%$search%' OR
            repo_url LIKE '%$search%' OR
            project_type LIKE '%$search%' OR
            project_approval_status LIKE '%$search%'
        )";
    }

    // 🔧 Filter logic (with AND/OR compound support)
    function buildFilterCondition($column, $filter, $conn)
    {
        $value = mysqli_real_escape_string($conn, $filter['filter']);
        $type = $filter['type'] ?? 'contains';

        switch ($type) {
            case 'contains':
                return "`$column` LIKE '%$value%'";
            case 'equals':
                return "`$column` = '$value'";
            case 'startsWith':
                return "`$column` LIKE '$value%'";
            case 'endsWith':
                return "`$column` LIKE '%$value'";
            case 'doesNotContain':
                return "`$column` NOT LIKE '%$value%'";
            case 'doesNotEqual':
                return "`$column` != '$value'";
            case 'blank':
                return "(`$column` IS NULL OR `$column` = '')";
            case 'notBlank':
                return "`$column` IS NOT NULL AND `$column` != ''";
            default:
                return "`$column` LIKE '%$value%'";
        }
    }

    $filterQuery = '';

    if (isset($_GET['filterModel']) && !empty($_GET['filterModel'])) {
        $filterModel = json_decode($_GET['filterModel'], true);
        if (is_array($filterModel)) {
            foreach ($filterModel as $column => $filter) {
                if (isset($filter['operator']) && isset($filter['conditions']) && is_array($filter['conditions'])) {
                    // Compound filter
                    $op = strtoupper($filter['operator']) === 'OR' ? 'OR' : 'AND';
                    $conditions = [];
                    foreach ($filter['conditions'] as $cond) {
                        $conditions[] = buildFilterCondition($column, $cond, $conn);
                    }
                    if (count($conditions) > 0) {
                        $filterQuery .= " AND (" . implode(" $op ", $conditions) . ")";
                    }
                } else {
                    // Simple filter
                    $filterQuery .= " AND " . buildFilterCondition($column, $filter, $conn);
                }
            }
        }
    }

    $query = "SELECT SQL_CALC_FOUND_ROWS * FROM em_projects 
          WHERE project_user_id = '$userId' 
          $searchQuery 
          $filterQuery 
          $sortQuery 
          LIMIT $startRow, $limit";

    $res = mysqli_query($conn, $query);
    $rows = mysqli_fetch_all($res, MYSQLI_ASSOC);

    $totalRes = mysqli_query($conn, "SELECT FOUND_ROWS() as total");
    $total = mysqli_fetch_assoc($totalRes)['total'];

    $data = [
        "status" => true,
        "message" => "Projects fetched successfully",
        "rows" => $rows,
        "lastRow" => (int) $total
    ];

    http_response_code(200);
    echo json_encode($data);
} catch (Exception $ex) {
    http_response_code(500);
    echo json_encode([
        "status" => false,
        "message" => $ex->getMessage(),
        "rows" => [],
        "lastRow" => 0
    ]);
}
?>