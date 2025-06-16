<?php
error_reporting(0);
ini_set('display_errors', 0);
include('../../../cors.php');
include('../../../methods.php');
include('../../../inc/dbcon.php');
include('../../../verify_token.php');

use MongoDB\BSON\ObjectId;

try {
    getMethod('PUT');

    $userInput = json_decode(file_get_contents('php://input'), true);
    global $db;

    $userId = $userData->id;
    $objectId = new ObjectId($userId);

    $collection = $db->em_users;

    $user = $collection->findOne([
        '_id' => $objectId,
        'user_isdeleted' => ['$ne' => '1']
    ]);

    if (!$user) {
        echo json_encode([
            "status" => false,
            "message" => "No user found",
            "data" => []
        ]);
        http_response_code(404);
        exit;
    }

    $firstName = $userInput['user_first_name'] ?? '';
    $lastName = $userInput['user_last_name'] ?? '';
    $age = $userInput['user_age'] ?? '';
    $gender = $userInput['user_gender'] ?? '';
    $country = $userInput['user_country'] ?? '';
    $state = $userInput['user_state'] ?? '';
    $city = $userInput['user_city'] ?? '';

    if (empty($firstName) || empty($lastName) || empty($age) || empty($gender) || empty($country) ||empty($state) ||empty($city) ) {
        echo json_encode([
            "status" => false,
            "message" => "Please fill all fields",
            "data" => []
        ]);
        http_response_code(400);
        exit;
    }

    $updateResult = $collection->updateOne(
        ['_id' => $objectId],
        [
            '$set' => [
                'user_first_name' => $firstName,
                'user_last_name' => $lastName,
                'user_age' => $age,
                'user_gender' => $gender,
                'user_country' => $country,
                'user_state' => $state,
                'user_city' => $city
            ]
        ]
    );

    if ($updateResult->getModifiedCount() > 0) {
        $updatedUser = $collection->findOne(['_id' => $objectId]);
        $userData = json_decode(json_encode($updatedUser), true);

        echo json_encode([
            "status" => true,
            "message" => "User updated successfully",
            "data" => $userData
        ]);
        http_response_code(200);
    } else {
        echo json_encode([
            "status" => false,
            "message" => "No changes made",
            "data" => []
        ]);
        http_response_code(200);
    }
} catch (Exception $ex) {
    echo json_encode([
        "status" => false,
        "message" => $ex->getMessage(),
        "data" => []
    ]);
    http_response_code(500);
}
?>