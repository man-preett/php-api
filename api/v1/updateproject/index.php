<?php
error_reporting(0);
ini_set('display_errors', 0);

include('../../../cors.php');
include('../../../methods.php');
include('../../../inc/dbcon.php');
include('../../../verify_token.php');

try {
    getMethod('PUT');

    // Validate URL param
    if (empty($_GET['id'])) {
        http_response_code(400);
        echo json_encode(["status" => false, "message" => "Project ID is required", "data" => []]);
        exit;
    }
    $projectId = $_GET['id'];

    // Get user identity
    $userId = $userData->id;
    $projectObjectId = new MongoDB\BSON\ObjectId($projectId);
    $userObjectId = new MongoDB\BSON\ObjectId($userId);

    $input = json_decode(file_get_contents('php://input'), true);

    // Required fields
    $required = [
        'project_name',
        'project_description',
        'project_tech',
        'project_status',
        'project_start_date',
        'project_deadline_date',
        'project_lead',
        'project_manager',
        'project_client',
        'management_tool',
        'management_url',
        'repo_tool',
        'repo_url',
        'project_budget',
        'project_milestone_release_date',
        'project_priority',
        'project_location',
        'project_type',
        'project_approval_status'
    ];
    foreach ($required as $f) {
        if (empty($input[$f])) {
            http_response_code(400);
            echo json_encode(["status" => false, "message" => "Field '$f' is required", "data" => []]);
            exit;
        }
    }

    $types = [];
    foreach ((array) $input['project_type'] as $t) {
        $t = trim($t);
        if ($t !== '') {
            $types[] = $t;
        }
    }
    $projectType = implode(',', $types);

    $project = $db->em_projects->findOne([
        '_id' => $projectObjectId,
        'project_user_id' => $userObjectId
    ]);
    if (!$project) {
        http_response_code(404);
        echo json_encode(["status" => false, "message" => "Project not found or access denied", "data" => []]);
        exit;
    }

    $update = [
        'project_name' => $input['project_name'],
        'project_description' => $input['project_description'],
        'project_tech' => $input['project_tech'],
        'project_status' => $input['project_status'],
        'project_startDate' => $input['project_start_date'],
        'project_deadlineDate' => $input['project_deadline_date'],
        'project_lead' => $input['project_lead'],
        'project_manager' => $input['project_manager'],
        'project_client' => $input['project_client'],
        'management_tool' => $input['management_tool'],
        'management_url' => $input['management_url'],
        'repo_tool' => $input['repo_tool'],
        'repo_url' => $input['repo_url'],
        'project_budget' => $input['project_budget'],
        'project_milestone_release_date' => $input['project_milestone_release_date'],
        'project_priority' => $input['project_priority'],
        'project_location' => $input['project_location'],
        'project_type' => $projectType,
        'project_approval_status' => $input['project_approval_status'],
        'updated_at' => new MongoDB\BSON\UTCDateTime()
    ];

    $result = $db->em_projects->updateOne(
        ['_id' => $projectObjectId, 'project_user_id' => $userObjectId],
        ['$set' => $update]
    );

    if ($result->getModifiedCount() > 0) {
        echo json_encode(["status" => true, "message" => "Project updated successfully", "data" => []]);
    } else {
        echo json_encode(["status" => true, "message" => "No changes were made", "data" => []]);
    }
    http_response_code(200);

} catch (Exception $ex) {
    http_response_code(500);
    echo json_encode(["status" => false, "message" => $ex->getMessage(), "data" => []]);
}
