<?php
use Firebase\JWT\JWT;
require_once '../../../../vendor/autoload.php';

include('../../../../cors.php');
include('../../../../methods.php');
include('../../../../inc/dbcon.php');

try{
    getMethod(method: 'GET');

    $userInput = json_decode(file_get_contents('php://input'), true);
        global $conn;
        $email = mysqli_real_escape_string($conn, $userInput['user_email']);
        $pass = mysqli_real_escape_string($conn, $userInput['user_password']);
        $md5Pass = md5($pass);
        $query = "SELECT * FROM em_users WHERE user_email= '$email' AND user_password= '$md5Pass'";
        $query_run = mysqli_query($conn, $query);
        $row = mysqli_fetch_array($query_run);
        $userId = $row['user_id'];

        if (mysqli_num_rows($query_run) == 0) {
            $data = [
                "status" => false,
                "message" => "No users found",
                "data" => []
            ];
            http_response_code(response_code: 404);
            echo json_encode($data);

        } else {
            $secretKey = "gygyg4584cdfwwQQkkkdf";
            $data = [
                'iat' => time(),
                'exp' => strtotime("+1 hour"),
                'email' => $email,
                'id' => $userId
            ];
            $jwt = JWT::encode($data, $secretKey, "HS256");
            $data = [
                "status" => true,
                "message" => "User logged in",
                "data" => $jwt
            ];
            http_response_code(200);
            echo json_encode($data);
        }

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

