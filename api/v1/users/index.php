<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../../../cors.php');
include('../../../inc/dbcon.php');
include('../../../methods.php');
include('../../../verify_token.php');

try {
    getMethod(method: 'GET');
    global $db;
    $collection = $db->em_users;
    $cursor = $collection->find([
        'user_isdeleted' => ['$ne' => '1']
    ]);
    $query = iterator_to_array($cursor);
    if ($query) {
        $data = [
            "status" => true,
            "message" => "Users fetched successfully",
            "data" => $query
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
    $server_response_error = array(
        "status" => false,
        "message" => $ex->getMessage(),
        "data" => []
    );
    echo json_encode($server_response_error);
}

?>