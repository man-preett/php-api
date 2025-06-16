<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../../../cors.php');
include('../../../inc/dbcon.php');
include('../../../methods.php');

try {
    getMethod('POST');

    $userInput = json_decode(file_get_contents('php://input'), true);
    global $db;
    $firstName = $userInput['user_first_name'];
    $lastName = $userInput['user_last_name'];
    $age = $userInput['user_age'];
    $gender = $userInput['user_gender'];
    $email = $userInput['user_email'];
    $country = $userInput['user_country'];
    $state = $userInput['user_state'];
    $city = $userInput['user_city'];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $data = [
            "status" => false,
            "message" => "Invalid email format",
            "data" => []
        ];
        http_response_code(400);
        echo json_encode($data);
        die();
    }
    $pass = $userInput['user_password'];
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $pass)) {
        $data = [
            "status" => false,
            "message" => "Atleast use one lowercase ,one uppercase letter,one digit and minimum lenght of 8 characters",
            "data" => []
        ];
        http_response_code(400);
        echo json_encode($data);
        die();
    }
    $md5_pass = md5($pass);
    $collection = $db->em_users;
    $query_email = $collection->findOne(['user_email' => $email]);

    if (empty($firstName) || empty($lastName) || empty($email) || empty($pass) || empty($age)|| empty($gender) || empty($country) || empty($state) || empty($city)) {
        $data = [
            "status" => false,
            "message" => "Please fill all fields",
            "data" => []
        ];
        http_response_code(400);
        echo json_encode($data);

    } else {

        if ($query_email) {
            $data = [
                "status" => false,
                "message" => "Email is already used",
                "data" => []
            ];
            http_response_code(400);
            echo json_encode($data);
        } else {
            $user = $collection->insertOne([
                'user_first_name' => $firstName,
                'user_last_name' => $lastName,
                'user_age' => $age,
                'user_gender' => $gender,
                'user_email' => $email,
                'user_password' => $md5_pass,
                'user_country' => $country,
                'user_state' => $state,
                'user_city' => $city
            ]);
            if ($user) {
                $data = [
                    "status" => true,
                    "message" => "User created successfully",
                    "data" => (string) $user->getInsertedId()
                ];
                http_response_code(200);
                echo json_encode($data);
            }
        }
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