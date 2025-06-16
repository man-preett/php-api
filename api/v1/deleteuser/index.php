<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../../../cors.php');
include('../../../methods.php');
include('../../../inc/dbcon.php');
include('../../../verify_token.php');

use MongoDB\BSON\ObjectId;

try {
    getMethod('PUT');

    global $db;

    if (!isset($_GET['id'])) {
        echo json_encode([
            "status" => false,
            "message" => "ID is not found in the URL",
            "data" => []
        ]);
        http_response_code(400);
        exit;
    }

    $userId = $_GET['id'];

    if (!preg_match('/^[a-f\d]{24}$/i', $userId)) {
        echo json_encode([
            "status" => false,
            "message" => "Invalid ObjectId",
            "data" => []
        ]);
        http_response_code(400);
        exit;
    }

    $objectId = new ObjectId($userId);
    $collection = $db->em_users;

    $user = $collection->findOne(['_id' => $objectId]);

    if (!$user) {
        echo json_encode([
            "status" => false,
            "message" => "No user found",
            "data" => []
        ]);
        http_response_code(404);
        exit;
    }

    $update = $collection->updateOne(
        ['_id' => $objectId],
        ['$set' => ['user_isdeleted' => '1']]
    );

    echo json_encode([
        "status" => true,
        "message" => "User deleted successfully",
        "data" => [
            "modifiedCount" => $update->getModifiedCount()
        ]
    ]);
    http_response_code(200);

} catch (Exception $ex) {
    http_response_code(500);
    echo json_encode([
        "status" => false,
        "message" => $ex->getMessage(),
        "data" => []
    ]);
}
?>