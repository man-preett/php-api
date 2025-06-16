<?php
error_reporting(0);
ini_set('display_errors', 0);

include('../../../cors.php');
include('../../../inc/dbcon.php');
include('../../../methods.php');
include('../../../verify_token.php');

try {
    getMethod('POST');
    global $db;
    $collection = $db->em_projects;
    $userId = $userData->id;
    $userObjectId = new MongoDB\BSON\ObjectId($userId);

    $userInput = json_decode(file_get_contents('php://input'), true);

    $requiredFields = [
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
        '   '
    ];

    foreach ($requiredFields as $field) {
        if (empty($userInput[$field])) {
            http_response_code(400);
            echo json_encode([
                "status" => false,
                "message" => "Please fill all fields",
                "data" => []
            ]);
            exit;
        }
    }

    $projectTypesRaw = $userInput['project_type'];
    $projectTypesSanitized = [];

    if (is_array($projectTypesRaw)) {
        foreach ($projectTypesRaw as $type) {
            $trimmedType = trim($type);
            if (!empty($trimmedType)) {
                $projectTypesSanitized[] = $trimmedType;
            }
        }
    }

    $projectType = implode(',', $projectTypesSanitized);

    $projectDocument = [
        'project_user_id' => $userObjectId,
        'project_name' => $userInput['project_name'],
        'project_description' => $userInput['project_description'],
        'project_tech' => $userInput['project_tech'],
        'project_status' => $userInput['project_status'],
        'project_startDate' => $userInput['project_start_date'],
        'project_deadlineDate' => $userInput['project_deadline_date'],
        'project_lead' => $userInput['project_lead'],
        'project_manager' => $userInput['project_manager'],
        'project_client' => $userInput['project_client'],
        'management_tool' => $userInput['management_tool'],
        'management_url' => $userInput['management_url'],
        'repo_tool' => $userInput['repo_tool'],
        'repo_url' => $userInput['repo_url'],
        'project_budget' => $userInput['project_budget'],
        'project_milestone_release_date' => $userInput['project_milestone_release_date'],
        'project_priority' => $userInput['project_priority'],
        'project_location' => $userInput['project_location'],
        'project_type' => $projectType,
        'project_approval_status' => $userInput['project_approval_status'],
        'created_at' => new MongoDB\BSON\UTCDateTime()
    ];

    $insertResult = $collection->insertOne($projectDocument);

    if ($insertResult->getInsertedCount() > 0) {
        echo json_encode([
            "status" => true,
            "message" => "Project created successfully",
            "data" => ['inserted_id' => (string) $insertResult->getInsertedId()]
        ]);
        http_response_code(200);
    } else {
        throw new Exception("Project insertion failed");
    }

} catch (Exception $ex) {
    http_response_code(500);
    echo json_encode([
        "status" => false,
        "message" => $ex->getMessage(),
        "data" => []
    ]);
}
?>