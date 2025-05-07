<?php
include('cors.php');
include('function.php');
$requestMethod = $_SERVER['REQUEST_METHOD'];
if ($requestMethod == 'DELETE') {

    $deleteuser = deleteUser($_GET);
    echo $deleteuser;


} else {
    $data = [
        'status' => 405,
        'message' => $requestMethod . 'Method not allowed',
    ];
    header('HTTP/1.0 405 Method not allowed');
    echo json_encode($data);
}

?>