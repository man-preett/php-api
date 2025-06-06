<?php
error_reporting(0);
ini_set('display_errors', 0);

include('../../../cors.php');
include('../../../inc/dbcon.php');
include('../../../methods.php');
include('../../../verify_token.php');

function randomString($length = 10)
{
    return substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

function randomDate($start, $end)
{
    return date('Y-m-d', mt_rand(strtotime($start), strtotime($end)));
}

try {
    getMethod('POST');

    global $conn;
    $userData;
    $userId = $userData->id; // Fallback if $userData is not set

    for ($i = 0; $i < 50000; $i++) {
        $projectName = "Project_" . randomString(5);
        $projectDescription = "Description of " . $projectName;
        $projectTech = "C#, PostgreSQL";
        $projectStatus = "active";
        $projectStartDate = randomDate('2024-01-01', '2024-12-31');
        $projectDeadlineDate = randomDate('2025-01-01', '2025-12-31');
        $projectLead = "Lead_" . randomString(4);
        $projectManager = "Manager_" . randomString(4);
        $projectClient = "Client_" . randomString(4);

        // Randomly pick from Jira or Trello
        $manageTools = ['Jira', 'Trello'];
        $manageTool = $manageTools[array_rand($manageTools)];
        $manageUrl = "https://{$manageTool}.example.com/" . randomString(5);

        // Randomly pick from Git Lab or Bit Bucket
        $repoTools = ['Git Lab', 'Bit Bucket'];
        $repoTool = $repoTools[array_rand($repoTools)];
        $repoUrl = "https://{$repoTool}.com/" . randomString(5);

        // Random priority
        $priorities = ['Low', 'Medium', 'High'];
        $projectPriority = $priorities[array_rand($priorities)];

        $projectLocation = "Location_" . randomString(3);

        // Project type from detailed list
        $projectTypes = [
            'Mobile Development',
            'E-commerce Development',
            'Web Development',
            'Data Analytics',
            'Supply Chain',
            'Healthcare CRM',
            'Machine Learning',
            'Travel'
        ];
        $projectType = $projectTypes[array_rand($projectTypes)];

        // Approval status
        $approvalStatuses = ['Approved', 'Pending', 'Rejected'];
        $projectApproveStatus = $approvalStatuses[array_rand($approvalStatuses)];

        $projectBudget = rand(10000, 100000);


        $query = "INSERT INTO em_projects (
            project_user_id, project_name, project_description, project_tech, project_status,
            project_startDate, project_deadlineDate, project_lead, project_manager, project_client,
            management_tool, management_url, repo_tool, repo_url, project_budget,
            project_priority, project_location, project_type, project_approval_status
        ) VALUES (
            '$userId', '$projectName', '$projectDescription', '$projectTech', '$projectStatus',
            '$projectStartDate', '$projectDeadlineDate', '$projectLead', '$projectManager', '$projectClient',
            '$manageTool', '$manageUrl', '$repoTool', '$repoUrl', '$projectBudget',
            '$projectPriority', '$projectLocation', '$projectType', '$projectApproveStatus'
        )";

        mysqli_query($conn, $query);
    }

    $data = [
        "status" => true,
        "message" => "100 random projects created successfully",
        "data" => []
    ];
    http_response_code(200);
    echo json_encode($data);

} catch (Exception $ex) {
    http_response_code(500);
    echo json_encode([
        "status" => false,
        "message" => $ex->getMessage(),
        "data" => []
    ]);
}
?>
