<?php
error_reporting(0);
ini_set('display_errors', 0);
include('../../../cors.php');
include('../../../methods.php');
include('../../../inc/dbcon.php');
include('../../../verify_token.php');

try {

    getMethod('PUT');

    $userInput = json_decode(file_get_contents('php://input'), true);

    global $conn;

    $userData;

    $userId = $userData->id;
    $check_id = "SELECT * FROM em_users WHERE user_id = '$userId' AND user_isdeleted != '1' ";
    $result = mysqli_query($conn, $check_id);

    if (mysqli_num_rows($result) == 0) {
        $data = [
            "status" => false,
            "message" => "No user found",
            "data" => []
        ];
        http_response_code(404);
        echo json_encode($data);
        die();

    }
    $firstName = mysqli_real_escape_string($conn, $userInput['user_first_name']);
    $lastName = mysqli_real_escape_string($conn, $userInput['user_last_name']);
    $age = mysqli_real_escape_string($conn, $userInput['user_age']);
    $gender = mysqli_real_escape_string($conn, $userInput['user_gender']);
    $country = mysqli_real_escape_string($conn, $userInput['user_country']);
    $state = mysqli_real_escape_string($conn, $userInput['user_state']);
    $city = mysqli_real_escape_string($conn, $userInput['user_city']);

    if (empty($firstName) || empty($lastName)) {
        $data = [
            "status" => false,
            "message" => "Please fill all fields",
            "data" => []
        ];
        http_response_code(400);
        echo json_encode($data);
        die();

    }

    $sql = "UPDATE em_users SET user_first_name='$firstName',user_last_name='$lastName',user_age='$age',user_gender='$gender',user_country='$country',user_state='$state',user_city='$city' WHERE user_id = '$userId' LIMIT 1";
    $res = mysqli_query( $conn,$sql);
        if ($res) {
        $selectSql = "SELECT * FROM em_users WHERE user_id = '$userId'";
        $selectRes = mysqli_query($conn, $selectSql);
        $result = mysqli_fetch_assoc($selectRes);
        $data = [
            "status" => true,
            "message" => "User updated successfully",
            "data" => $result
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