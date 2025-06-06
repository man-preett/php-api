<?php
error_reporting(0);
ini_set('display_errors', 0);
include('../../../cors.php');
include('../../../methods.php');
include('../../../inc/dbcon.php');
include('../../../verify_token.php');

try {

    getMethod('PUT');
        if (!isset($_GET['id'])) {
            $data = [
                "status" => false,
                "message" => "id is not found in the url",
                "data" => []
            ];
            http_response_code(400);
            echo json_encode($data);
            die();
        } elseif ($_GET['id'] == null) {
            $data = [
                "status" => false,
                "message" => "Enter your id",
                "data" => []
            ];
            http_response_code(400);
            echo json_encode($data);
            die();
        }
    $userInput = json_decode(file_get_contents('php://input'), true);

    global $conn;

    $userData;
    $projectId = mysqli_real_escape_string($conn, $_GET['id']);
    $userId = $userData->id;
    $projectName = mysqli_real_escape_string($conn, $userInput['project_name']);
    $projectDescription = mysqli_real_escape_string($conn, $userInput['project_description']);
    $projectTech = mysqli_real_escape_string($conn, $userInput['project_tech']);
    $projectStatus = mysqli_real_escape_string($conn, $userInput['project_status']);
    $projectStartDate = mysqli_real_escape_string($conn, $userInput['project_start_date']);
    $projectDeadlineDate = mysqli_real_escape_string($conn, $userInput['project_deadline_date']);
    $projectLead = mysqli_real_escape_string($conn, $userInput['project_lead']);
    $projectManager = mysqli_real_escape_string($conn, string: $userInput['project_manager']);
    $projectClient = mysqli_real_escape_string($conn, $userInput['project_client']);
    $manageTool = mysqli_real_escape_string($conn, $userInput['management_tool']);
    $manageUrl = mysqli_real_escape_string($conn, $userInput['management_url']);
    $repoTool = mysqli_real_escape_string($conn, $userInput['repo_tool']);
    $repoUrl = mysqli_real_escape_string($conn, $userInput['repo_url']);
    $projectBudget = mysqli_real_escape_string($conn, $userInput['project_budget']);
    $projectMileStoneDate = mysqli_real_escape_string($conn, $userInput['project_milestone_release_date']);
    $projectPriority = mysqli_real_escape_string($conn, $userInput['project_priority']);
    $projectLocation = mysqli_real_escape_string($conn, $userInput['project_location']);
    $projectApproveStatus = mysqli_real_escape_string($conn, $userInput['project_approval_status']);


    $projectTypesRaw = $userInput['project_type'];
    $projectTypesSanitized = [];

    if (is_array($projectTypesRaw)) {
        foreach ($projectTypesRaw as $type) {
            $trimmedType = trim($type);
            if (!empty($trimmedType)) {
                $projectTypesSanitized[] = mysqli_real_escape_string($conn, $trimmedType);
            }
        }
    }

    $projectType = implode(',', $projectTypesSanitized);

    if (
        empty($projectName) || empty($projectDescription) || empty($projectTech) || empty($projectStatus) || empty($projectStartDate) || empty($projectDeadlineDate) ||
        empty($projectLead) || empty($projectManager) || empty($projectClient) || empty($manageTool) || empty($manageUrl) || empty($repoTool) || empty($repoUrl) ||
        empty($projectBudget) || empty($projectMileStoneDate) || empty($projectPriority) || empty($projectLocation) || empty($projectType) || empty($projectApproveStatus)
    ) {
        $data = [
            "status" => false,
            "message" => "Please fill all fields",
            "data" => []
        ];
        http_response_code(400);
        echo json_encode($data);
        die();

    }

    $check_id = "SELECT * from em_projects WHERE project_user_id = '$userId' AND project_id = '$projectId' LIMIT 1";
    $res = mysqli_query($conn, $check_id);

    $result = mysqli_fetch_assoc($res);

    if (!$result) {
        $data = [
            "status" => false,
            "message" => "No project found for this user",
            "data" => [$result]
        ];
        http_response_code(404);
        echo json_encode($data);
        die();
    }

    $sql = "UPDATE em_projects SET project_name='$projectName',project_description='$projectDescription',project_tech='$projectTech',project_status='$projectStatus',
    project_startDate='$projectStartDate',project_deadlineDate='$projectDeadlineDate',project_lead='$projectLead',project_manager='$projectManager',project_client='$projectClient',
    management_tool='$manageTool',management_url='$manageUrl',repo_tool='$repoTool',repo_url='$repoUrl',project_budget='$projectBudget',project_milestone_release_date='$projectMileStoneDate',project_priority='$projectPriority',project_location='$projectLocation',project_type='$projectType',project_approval_status='$projectApproveStatus' WHERE project_id = '$projectId'";
    $res1 = mysqli_query($conn, $sql);
       $data = [
            "status" => true,
            "message" => "Project Updated Successfully",
            "data" => [$res1]
        ];
        http_response_code(200);
        echo json_encode($data);

} catch (Exception $ex) {
    http_response_code(500);
    $server_response_error = array(
        "status" => false,
        "message" => $ex->getMessage(),
        "data" => []
    );
    echo json_encode($server_response_error);
}



?>