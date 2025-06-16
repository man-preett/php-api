<?php
error_reporting(0);
ini_set('display_errors', 0);

require '../../../vendor/autoload.php'; // MongoDB client
include('../../../cors.php');
include('../../../methods.php');
include('../../../verify_token.php');
include('../../../inc/dbcon.php');

try {
    getMethod('POST');
    global $db;
    $userInput = json_decode(file_get_contents('php://input'), true);
    $country_name = $userInput['country_name'];

    $collection = $db->em_states;

    $result = $collection->aggregate([
        [
            '$lookup' => [
                'from' => 'em_countries',
                'localField' => 'country_id',
                'foreignField' => '_id',
                'as' => 'country'
            ]
        ],
        [ '$unwind' => '$country' ],
        [ '$match' => [ 'country.country_name' => $country_name ] ]
    ])->toArray();
    
    if (empty($result)) {
        http_response_code(404);
        echo json_encode([
            'status' => false,
            'message' => 'No State Found',
            'data' => []
        ]);
        exit;
    }

    http_response_code(200);
    echo json_encode([
        'status' => true,
        'message' => 'All States fetched successfully',
        'data' => $result
    ]);
} catch (Exception $ex) {
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'message' => $ex->getMessage(),
        'data' => []
    ]);
}
