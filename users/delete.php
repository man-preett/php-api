<?php
include('cors.php');
include('function.php');
include('methods.php');

getMethod('DELETE');

    $deleteuser = deleteUser($_GET);
    echo $deleteuser;


?>