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
    $userData;
    $userInput = json_decode(file_get_contents('php://input'), true);
    
    $userId = $userData->id;


    $query = "SELECT * from em_projects WHERE project_user_id= '$userId' ";
    $res = mysqli_query($conn, $query);

    if ($res) {

        if (mysqli_num_rows($res) > 0) {
            $result = mysqli_fetch_all($res, MYSQLI_ASSOC);
            $data = [
                "status" => true,
                "message" => "Projects fetched successfully",
                "data" => $result
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