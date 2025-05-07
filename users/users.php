<?php
include('cors.php');
include('function.php');
$requestMethod = $_SERVER['REQUEST_METHOD'];
if ($requestMethod == 'GET') {
    if (isset($_GET['id'])) {
        $user = getUser($_GET);
        echo $user;
    } else {
        $userList = getUsers();
        echo $userList;
    }


} else {
    $data = [
        'status' => 405,
        'message' => $requestMethod . 'Method not allowed',
    ];
    header('HTTP/1.0 405 Method not allowed');
    echo json_encode($data);
}

?>