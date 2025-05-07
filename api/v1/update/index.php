<?php
include('../../../cors.php');
include('../../../methods.php');
include('../../../inc/dbcon.php');
try {

    getMethod('PUT');

    $userInput = json_decode(file_get_contents('php://input'), true);

    global $conn;
    if (!isset($_GET['id'])) {
        $data = [
            "status" => false,
            "message" => "id is not found in the url",
            "data" => []
        ];
        http_response_code(400);
        echo json_encode($data);
        die();
    } elseif ($_GET['id'] == null) {
        $data = [
            "status" => false,
            "message" => "Enter your id",
            "data" => []
        ];
        http_response_code(400);
        echo json_encode($data);
        die();
    }

    $userId = mysqli_real_escape_string($conn, $_GET['id']);
    $check_id = "SELECT * FROM em_users WHERE user_id = '$userId'";
    $result = mysqli_query($conn, $check_id);

    if (mysqli_num_rows($result) == 0) {
        $data = [
            "status" => false,
            "message" => "No user found",
            "data" => []
        ];
        http_response_code(404);
        echo json_encode($data);
        die();

    }
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
    if (empty($firstName) || empty($lastName) || empty($email)) {
        $data = [
            "status" => false,
            "message" => "Please fill all fields",
            "data" => []
        ];
        http_response_code(400);
        echo json_encode($data);
        die();

    }
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
        die();
    }
    $sql = "UPDATE em_users SET user_first_name='$firstName',user_last_name='$lastName',user_email = '$email' WHERE user_id = '$userId' LIMIT 1";
    $res = mysqli_query($conn, $sql);
    if ($res) {
        $data = [
            "status" => true,
            "message" => "User updated successfully",
            "data" => $res
        ];
        http_response_code(200);
        echo json_encode($data);

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