<?php
include('cors.php');
include('function.php');
include('methods.php');

getMethod('PUT');

    $inputData = json_decode(file_get_contents('php://input'), true);
    if (empty($inputData)) {
        $updateUsers = updateUser($_POST,$_GET);
    } else {
        $updateUsers = updateUser($inputData,$_GET);
    }
    echo $updateUsers;

?>