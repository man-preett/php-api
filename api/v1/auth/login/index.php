<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
use Firebase\JWT\JWT;
require_once '../../../../vendor/autoload.php';

include('../../../../cors.php');
include('../../../../methods.php');
include('../../../../inc/dbcon.php');

try {
    getMethod(method: 'POST');

    $userInput = json_decode(file_get_contents('php://input'), true);
    global $db;

    $email = $userInput['user_email'];
    $pass = $userInput['user_password'];
    $md5Pass = md5($pass);

    $collection = $db->em_users;
    if (empty($email) || empty($md5Pass)) {
        $data = [
            "status" => false,
            "message" => "Please fill all fields",
            "data" => []
        ];
        http_response_code(400);
        echo json_encode($data);
        die();
    }
    $user = $collection->findOne([
        'user_email' => $email,
        'user_password' => $md5Pass
    ]);
    if (!$user) {
        $data = [
            "status" => false,
            "message" => "No users found",
            "data" => []
        ];
        http_response_code(404);
        echo json_encode($data);
        die();
    } else {
        // JWT creation
        $secretKey = "gygyg4584cdfwwQQkkkdf";
        $payload = [
            'iat' => time(),
            'exp' => strtotime("+8 hour"),
            'email' => $user['user_email'],
            'id' => (string) $user['_id']
        ];

        $jwt = JWT::encode($payload, $secretKey, "HS256");

        $data = [
            "status" => true,
            "message" => "User logged in",
            "data" => $jwt
        ];

        http_response_code(200);
        echo json_encode($data);
        exit;
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