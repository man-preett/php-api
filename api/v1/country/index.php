<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../../../cors.php');
include('../../../inc/dbcon.php');
include('../../../methods.php');
// include('../../../verify_token.php');

try {
    getMethod('GET');
    global $conn;

    $query = "SELECT * FROM em_countries";
    $res = mysqli_query($conn, $query);
    $result = mysqli_fetch_all($res, MYSQLI_ASSOC);
    if (mysqli_num_rows($res) < 0) {
        $data = [
            'status' => false,
            'message' => 'No country Found',
            'data' => []
        ];
        http_response_code(404);
        echo json_encode($data);
        die();

    }

    $data = [
        'status' => true,
        'message' => 'All countries fetched successfully',
        'data' => $result
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