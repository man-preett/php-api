<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once 'vendor/autoload.php';

function checkAuthorization(){
    $headers=apache_request_headers();
    if(!isset($headers['Authorization']) || empty($headers['Authorization'])){
        $response=[
            'status' =>false,
            'message' =>"No Authorization",
            'data' =>[]
        ];

        http_response_code(401);
        echo json_encode($response);
        return false;
    }
    $authToken = explode(" ", $headers['Authorization']);
    $token = $authToken[1];
    return validateToken($token);
}

function validateToken($token){
    $secretkey="gygyg4584cdfwwQQkkkdf";
    try{
        $decoded=JWT::decode($token,new Key($secretkey,'HS256'));
        // print_r($decoded);
        $currentTime=time();
        if($decoded->exp < $currentTime){
            $response=[
                'status' =>false,
                'message' =>"session expired",
                'data' =>[]
            ];
            http_response_code(401);
            echo json_encode($response);
            return false;
        }
        return $decoded;
    }
    catch(Exception $e){
        $response=[
            'status' =>false,
             'message' =>"Unauthorization",
             'data' =>[]
        ];
        http_response_code(500);
        echo json_encode($response);
        return false;
    }
}
 $userData= checkAuthorization();
 if(!$userData){
die();
 }
?>