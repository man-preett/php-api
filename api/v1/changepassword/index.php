<?php
error_reporting(0);
ini_set('display_errors', 0);
include('../../../cors.php');
include('../../../methods.php');
include('../../../inc/dbcon.php');
include('../../../verify_token.php');
try {

    getMethod('POST');
    global $conn;
    $userData;
    $userId = $userData->id;
    $userInput = json_decode(file_get_contents('php://input'), true);
    $current_password = $userInput['current_password'];
    $new_password = $userInput['new_password'];
    $md5currentPass = md5($current_password);
    $md5newPass = md5($new_password);

    $current_pass_query = "SELECT user_password from em_users WHERE user_id = '$userId'";
    $qres = mysqli_query($conn, $current_pass_query);
    $row = mysqli_fetch_array(result: $qres);
    $db_current = $row['user_password'];


    if ($db_current !== $md5currentPass) {
        $data = [
            "status" => false,
            "message" => "Old and new password does not match",
            "data" => []
        ];
        http_response_code(400);
        echo json_encode($data);
        die();
    }

    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[@$!%*?&])[A-Za-z\\d@$!%*?&]{6,}$/', $new_password)) {
        $data = [
            "status" => false,
            "message" => "Atleast use one lowercase ,one uppercase letter,one digit and minimum lenght of 6 characters",
            "data" => []
        ];
        http_response_code(400);
        echo json_encode($data);
        die();
    }
    $query = "UPDATE em_users SET user_password='$md5newPass' WHERE user_id ='$userId'";
    $res = mysqli_query($conn, $query);
    $data = [
        'status' => true,
        'message' => 'Password changed successfully',
        'data' => []
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