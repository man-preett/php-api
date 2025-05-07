<?php
include('../../../cors.php');
include('../../../inc/dbcon.php');
include('../../../methods.php');

try {
    global $conn;
    $query = 'SELECT * FROM em_users';
    $query_run = mysqli_query($conn, $query);

    if (mysqli_num_rows($query_run) > 0) {
        $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);
        $data = [
            "status" => true,
            "message" => "Users fetched successfully",
            "data" => $res
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