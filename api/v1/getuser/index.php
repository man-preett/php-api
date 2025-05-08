<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../../../cors.php');
include('../../../methods.php');
include('../../../inc/dbcon.php');
// include('../../../verify_token.php');

try {

    getMethod(method: 'GET');
    global $conn;
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

    $userId = mysqli_real_escape_string($conn, $_GET['id']);
    $query = "SELECT * from em_users WHERE user_id= '$userId' AND user_isdeleted != '1' ";
    $result = mysqli_query($conn, $query);
    if ($result) {

        if (mysqli_num_rows($result) == 1) {
            $res = mysqli_fetch_assoc($result);
            $data = [
                "status" => true,
                "message" => "User fetched successfully",
                "data" => $res
            ];
            http_response_code(200);
            echo json_encode($data);

        } else {
            $data = [
                "status" => false,
                "message" => "No user found",
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