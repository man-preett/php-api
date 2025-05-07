<?php
include('cors.php');
include('function.php');
include('methods.php');
getMethod(method: 'GET');

if (isset($_GET['id'])) {
        $user = getUser($_GET);
        echo $user;
    } else {
        $userList = getUsers();
        echo $userList;
    }


?>