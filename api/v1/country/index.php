<?php
error_reporting(0);
ini_set('display_errors', 0);
include('../../../cors.php');
include('../../../inc/dbcon.php'); 
include('../../../methods.php');
include('../../../verify_token.php');

try {
    getMethod('GET');
    global $db;
    $collection = $db->em_countries;

    $query = $collection->find();
    $countries = iterator_to_array(iterator: $query);
    $countries = json_decode(json_encode($countries), true);

    if (count(value: $countries) === 0) {
        $data = [
            'status' => false,
            'message' => 'No country found',
            'data' => []
        ];
        http_response_code(404);
        echo json_encode($data);
        exit;
    }

    $data = [
        'status' => true,
        'message' => 'All countries fetched successfully',
        'data' => $countries
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
