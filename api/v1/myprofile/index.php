<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../../../cors.php');
include('../../../methods.php');
include('../../../inc/dbcon.php');
include('../../../verify_token.php');
try {
    getMethod('GET');
    global $conn;
    $userData;
    $userId = $userData->id;
    $query = "SELECT * FROM em_users WHERE user_id = '$userId'";
    $res = mysqli_query($conn, $query);
    $result = mysqli_fetch_assoc($res);
    if (mysqli_num_rows($res) == 0) {

        $data = [
            "status" => false,
            "message" => "No user found",
            "data" => []
        ];
        http_response_code(200);
        echo json_encode($data);
        die();
    }
    $data = [
        "status" => true,
        "message" => "Data fetched successfully",
        "data" => $result
    ];
    http_response_code(200);
    echo json_encode($data);

}
catch (Exception $ex) {
    http_response_code(500);
    $server_response_error = array(
        "status" => false,
        "message" => $ex->getMessage(),
        "data" => []
    );
    echo json_encode($server_response_error);
}
