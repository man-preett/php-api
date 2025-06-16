<?php
error_reporting(0);
ini_set('display_errors', 0);
include('../../../cors.php');
include('../../../methods.php');
include('../../../inc/dbcon.php');
include('../../../verify_token.php');

try {
    getMethod('POST');
    global $db;

    $userData;
    $userId = $userData->id; 

    $userObjectId = new MongoDB\BSON\ObjectId($userId);

    $userInput = json_decode(file_get_contents('php://input'), true);
    $current_password = $userInput['current_password'];
    $new_password = $userInput['new_password'];

    $md5currentPass = md5($current_password);
    $md5newPass = md5($new_password);

    $collection = $db->em_users;

    $userDoc = $collection->findOne([
        '_id' => $userObjectId
    ]);

    if (!$userDoc) {
        http_response_code(404);
        echo json_encode([
            "status" => false,
            "message" => "User not found",
            "data" => []
        ]);
        die();
    }

    $db_current = $userDoc['user_password'];
    if ($db_current !== $md5currentPass) {
        http_response_code(400);
        echo json_encode([
            "status" => false,
            "message" => "Old password is incorrect",
            "data" => []
        ]);
        die();
    }

    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[@$!%*?&])[A-Za-z\\d@$!%*?&]{6,}$/', $new_password)) {
        http_response_code(400);
        echo json_encode([
            "status" => false,
            "message" => "New password must contain at least one lowercase, one uppercase, one digit, one special character and be at least 6 characters long",
            "data" => []
        ]);
        die();
    }

    $updateResult = $collection->updateOne(
        ['_id' => $userObjectId],
        ['$set' => ['user_password' => $md5newPass]]
    );

    echo json_encode([
        'status' => true,
        'message' => 'Password changed successfully',
        'data' => []
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
