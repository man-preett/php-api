<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../../../cors.php');
include('../../../methods.php');
include('../../../inc/dbcon.php');
include('../../../verify_token.php');

try {
    getMethod(method: 'GET');
    global $db;

    if (empty($_GET['id'])) {
        $data = [
            "status" => false,
            "message" => "Enter your id",
            "data" => []
        ];
        http_response_code(400);
        echo json_encode($data);
        exit;
    }

    $userId = $_GET['id'];
    $collection = $db->em_users;

    try {
        $userObjectId = new MongoDB\BSON\ObjectId($userId);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            "status" => false,
            "message" => "Invalid user ID format",
            "data" => []
        ]);
        exit;
    }

    $user = $collection->findOne([
        '_id' => $userObjectId,
        'user_isdeleted' => ['$ne' => '1']
    ]);

    if ($user) {
        $data = [
            "status" => true,
            "message" => "User fetched successfully",
            "data" => $user
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

} catch (Exception $ex) {
    http_response_code(500);
    echo json_encode([
        "status" => false,
        "message" => $ex->getMessage(),
        "data" => []
    ]);
}
?>