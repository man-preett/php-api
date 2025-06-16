<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../../../cors.php');
include('../../../methods.php');
include('../../../inc/dbcon.php');
include('../../../verify_token.php');

try {
    getMethod('GET');

    global $db;

    $userId = $userData->id;

    $userObjectId = new MongoDB\BSON\ObjectId($userId);

    $collection = $db->em_users;
    $user = $collection->findOne(['_id' => $userObjectId]);

    if (!$user) {
        $data = [
            "status" => false,
            "message" => "No user found",
            "data" => []
        ];
        http_response_code(200);
        echo json_encode($data);
        exit;
    }

    $user = json_decode(json_encode($user), true);

    $data = [
        "status" => true,
        "message" => "Data fetched successfully",
        "data" => $user
    ];
    http_response_code(200);
    echo json_encode($data);

} catch (Exception $ex) {
    http_response_code(500);
    $server_response_error = [
        "status" => false,
        "message" => $ex->getMessage(),
        "data" => []
    ];
    echo json_encode($server_response_error);
}
?>