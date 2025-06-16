<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../../../cors.php');
include('../../../methods.php');
include('../../../inc/dbcon.php');
include('../../../verify_token.php');

try {

    getMethod(method: 'GET');
    global $db;
    $collection = $db->em_projects;
    if ($_GET['id'] == null) {
        $data = [
            "status" => false,
            "message" => "Enter your id",
            "data" => []
        ];
        http_response_code(400);
        echo json_encode($data);
        die();
    }

    $projectId = $_GET['id'];
    $projectObjectId = new MongoDB\BSON\ObjectId($projectId);
    $query = $collection->findOne([
        '_id' => $projectObjectId
    ]);

    if ($query) {

        if ($query) {
            if (!empty($query['project_type'])) {
                $query['project_type'] = explode(',', $query['project_type']);
            }
            $data = [
                "status" => true,
                "message" => "Project fetched successfully",
                "data" => $query
            ];
            http_response_code(200);
            echo json_encode($data);

        } else {
            $data = [
                "status" => false,
                "message" => "No project found",
                "data" => []
            ];
            http_response_code(404);
            echo json_encode($data);

        }

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