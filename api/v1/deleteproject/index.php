<?php
error_reporting(0);
ini_set('display_errors', 0);

include('../../../cors.php');
include('../../../methods.php');
include('../../../inc/dbcon.php');
include('../../../verify_token.php');

try {
    getMethod('DELETE');
    global $db;
    $collection = $db->em_projects;
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

    $projectId = $_GET['id'];
    $projectObjectId = new MongoDB\BSON\ObjectId($projectId);


    $check_id = $collection->findOne([
        "_id" => $projectObjectId
    ]);


    if (count($check_id) == 0) {
        $data = [
            "status" => false,
            "message" => "No project found",
            "data" => []
        ];
        http_response_code(404);
        echo json_encode($data);
        die();
    }
    $query = $collection->deleteOne([
        '_id' => $projectObjectId
    ]);

    $data = [
        "status" => true,
        "message" => "project deleted successfully",
        "data" => $query
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