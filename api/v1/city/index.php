<?php
error_reporting(0);
ini_set('display_errors', 0);
include('../../../cors.php');
include('../../../inc/dbcon.php');
include('../../../methods.php');
include('../../../verify_token.php');

try {
    getMethod('POST');
    global $db;
    $collection = $db->em_cities;
    $userInput = json_decode(file_get_contents('php://input'), true);
    $state_name = $userInput['state_name'];

    $query = $collection->aggregate(pipeline: [
        [
            '$lookup' => [
                'from' => "em_states",
                'localField' => "state_id",
                'foreignField' => "_id",
                "as" => "state"
            ]
        ],
        ['$unwind' => '$state'],
        ['$match' => ['state.state_name' => $state_name]]
    ])->toArray();


    if (empty($query)) {
        $data = [
            'status' => false,
            'message' => 'No City Found',
            'data' => []
        ];
        http_response_code(404);
        echo json_encode($data);
        die();

    }

    $data = [
        'status' => true,
        'message' => 'All cities fetched successfully',
        'data' => $query
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