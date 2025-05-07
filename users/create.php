<?php
include('cors.php');
include('function.php');
include('methods.php');
   
    getMethod('POST');

    $inputData = json_decode(file_get_contents('php://input'), true);
    if(empty($inputData)){
        $storeUsers = storeUser(userInput: $_POST);
    }
    else{
        $storeUsers = storeUser($inputData);
    }
    echo $storeUsers;



?>