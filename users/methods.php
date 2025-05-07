<?php

function getMethod($method){
    $requestMethod = $_SERVER['REQUEST_METHOD'] ;
    if($requestMethod !== $method){
        $data = [
            'status' => 405,
            'message' => $requestMethod . 'Method not allowed',
        ];
        header('HTTP/1.0 405 Method not allowed');
        echo json_encode($data);
    }

}
?>