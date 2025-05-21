<?php
error_reporting(0);
ini_set('display_errors', 0);

include('../../../cors.php');
include('../../../methods.php');
include('../../../inc/dbcon.php');
include('../../../verify_token.php');

try {
    getMethod('DELETE');
    global $conn;
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

    $projectId = mysqli_real_escape_string($conn, $_GET['id']);

    $check_id = "SELECT * FROM em_projects WHERE project_id = '$projectId'";
    $result = mysqli_query($conn, $check_id);
    $delectqry = mysqli_fetch_assoc($result);
    if (mysqli_num_rows($result) == 0) {
        $data = [
            "status" => false,
            "message" => "No project found",
            "data" => []
        ];
        http_response_code(404);
        echo json_encode($data);
        die();
    }
    $query = "DELETE FROM em_projects WHERE project_id = '$projectId' LIMIT 1";
    $res = mysqli_query($conn, $query);
    $data = [
        "status" => true,
        "message" => "project deleted successfully",
        "data" => $delectqry
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