<?php
include('../../../cors.php');
include('../../../inc/dbcon.php');
include('../../../methods.php');

try {
    getMethod('POST');

    $userInput = json_decode(file_get_contents('php://input'), true);
    global $conn;
    $firstName = mysqli_real_escape_string($conn, $userInput['user_first_name']);
    $lastName = mysqli_real_escape_string($conn, $userInput['user_last_name']);
    $email = mysqli_real_escape_string($conn, $userInput['user_email']);
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
    $pass = mysqli_real_escape_string($conn, $userInput['user_password']);
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
    $query_email = "SELECT 'user_email' FROM em_users WHERE 'user_email'= '$email'";
    $result = mysqli_query($conn, $query_email);
    if (empty($firstName) || empty($lastName) || empty($email) || empty($pass)) {
        $data = [
            "status" => false,
            "message" => "Please fill all fields",
            "data" => []
        ];
        http_response_code(400);
        echo json_encode($data);

    } else {
        $query_email = "SELECT * FROM em_users WHERE user_email= '$email'";
        $result = mysqli_query($conn, $query_email);
        if (mysqli_num_rows($result) > 0) {
            $data = [
                "status" => false,
                "message" => "Email is already used",
                "data" => []
            ];
            http_response_code(400);
            echo json_encode($data);
        } else {
            $query = "INSERT INTO em_users (user_first_name,user_last_name,user_email,user_password) VALUES ('$firstName','$lastName','$email','$md5_pass')";
            echo $query;
            $res = mysqli_query($conn, $query);
            if ($res) {
                $data = [
                    "status" => true,
                    "message" => "User created successfully",
                    "data" => $res
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