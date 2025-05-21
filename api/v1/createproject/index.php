<?php
error_reporting(0);
ini_set('display_errors', 0);

include('../../../cors.php');
include('../../../inc/dbcon.php');
include('../../../methods.php');
include('../../../verify_token.php');
try {
    getMethod('POST');
    $userData;
    $userId = $userData->id;
    $userInput = json_decode(file_get_contents('php://input'), true);
    global $conn;
    $projectName = mysqli_real_escape_string($conn, $userInput['project_name']);
    $projectDescription = mysqli_real_escape_string($conn, $userInput['project_description']);
    $projectTech= mysqli_real_escape_string($conn, $userInput['project_tech']);
    $projectStatus = mysqli_real_escape_string($conn, $userInput['project_status']);
    $projectStartDate = mysqli_real_escape_string($conn, $userInput['project_start_date']);
    $projectDeadlineDate = mysqli_real_escape_string($conn, $userInput['project_deadline_date']);
    $projectLead = mysqli_real_escape_string($conn, $userInput['project_lead']);
    $projectManager = mysqli_real_escape_string($conn, $userInput['project_manager']);
    $projectClient = mysqli_real_escape_string($conn, $userInput['project_client']);
    $manageTool = mysqli_real_escape_string($conn, $userInput['management_tool']);
    $manageUrl = mysqli_real_escape_string($conn, $userInput['management_url']);
    $repoTool = mysqli_real_escape_string($conn, $userInput['repo_tool']);
    $repoUrl = mysqli_real_escape_string($conn, $userInput['repo_url']);

        if (
            empty($projectName) || empty($projectDescription) || empty($projectTech) || empty($projectStatus) || empty($projectStartDate) || empty($projectDeadlineDate) ||
            empty($projectLead) || empty($projectManager) || empty($projectClient) || empty($manageTool) || empty($manageUrl) || empty($repoTool) || empty($repoUrl)
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
        $query = "INSERT INTO em_projects (project_user_id,project_name,project_description,project_tech,project_status,project_startDate,project_deadlineDate,project_lead,project_manager,project_client,management_tool,management_url,repo_tool,repo_url) VALUES
                 ('$userId','$projectName','$projectDescription','$projectTech','$projectStatus','$projectStartDate','$projectDeadlineDate','$projectLead','$projectManager','$projectClient','$manageTool','$manageUrl','$repoTool','$repoUrl')";
        $res = mysqli_query($conn, $query);
        if ($res) {
            $data = [
                "status" => true,
                "message" => "Project created successfully",
                "data" => $res
            ];
            http_response_code(200);
            echo json_encode($data);
        }
    
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