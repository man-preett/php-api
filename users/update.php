<?php
include('cors.php');
include('function.php');

$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod == 'PUT') {

    $inputData = json_decode(file_get_contents('php://input'), true);
    if (empty($inputData)) {
        $updateUsers = updateUser($_POST,$_GET);
    } else {
        $updateUsers = updateUser($inputData,$_GET);
    }
    echo $updateUsers;

} else {
    $data = [
        'status' => 405,
        'message' => $requestMethod . 'Method not allowed',
    ];
    header('HTTP/1.0 405 Method not allowed');
    echo json_encode($data);
}

?>