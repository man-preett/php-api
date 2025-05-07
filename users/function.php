<?php
include('../inc/dbcon.php');
function getUsers(): bool|string
{
    global $conn;
    $query = 'SELECT * FROM em_users';
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        if (mysqli_num_rows($query_run) > 0) {
            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);
            $data = [
                'status' => 200,
                'message' => 'OK',
                'data' => $res
            ];
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'No users found',
            ];
            return json_encode($data);
        }

    } else {
        $data = [
            'status' => 405,
            'message' => 'Internal server error',
        ];
        header('HTTP/1.0 405 Method not allowed');
        return json_encode($data);
    }

}

function storeUser($userInput)
{
    global $conn;

    $firstName = mysqli_real_escape_string($conn, $userInput['user_first_name']);
    $lastName = mysqli_real_escape_string($conn, $userInput['user_last_name']);
    $email = mysqli_real_escape_string($conn, $userInput['user_email']);
    $pass = mysqli_real_escape_string($conn, $userInput['user_password']);
    $md5_pass = md5($pass);
    $query_email = "SELECT 'user_email' FROM em_users WHERE 'user_email'= '$email'";
    $result = mysqli_query($conn, $query_email);
    if (empty(trim($firstName))) {
        return error422("Enter your First name");

    } elseif (empty(trim($lastName))) {

        return error422("Enter your Last name");
    } elseif (empty(trim($email))) {

        return error422("Enter your email");
    } elseif (mysqli_num_rows($result) > 0) {
        return error422("This email is already used");
    } elseif (empty(trim($md5_pass))) {
        return error422("Enter your password");
    } else {
        $query = "INSERT INTO em_users (user_first_name,user_last_name,user_email,user_password) VALUES ('$firstName','$lastName','$email','$md5_pass')";
        $res = mysqli_query($conn, $query);
        if ($res) {
            $data = [
                'status' => 200,
                'message' => 'User created successfully',
                'data' => $res
            ];
            header('HTTP/1.0 200  created');
            return json_encode($data);

        } else {
            $data = [
                'status' => 500,
                'message' => 'Internal server error',
            ];
            header('HTTP/1.0 422 Method not allowed');
            return json_encode($data);
        }
    }
}

function getUser($userParams)
{

    global $conn;
    if ($userParams['id'] == null) {
        return error422("Enter your user id");

    }

    $userId = mysqli_real_escape_string($conn, $userParams['id']);
    $query = "SELECT * from em_users WHERE user_id= '$userId' LIMIT 1";
    $result = mysqli_query($conn, $query);
    if ($result) {

        if (mysqli_num_rows($result) == 1) {
            $res = mysqli_fetch_assoc($result);
            $data = [
                'status' => 200,
                'message' => 'User Fetched successfully',
                'data' => $res
            ];
            header('HTTP/1.0 200 Method not allowed');
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'No user found',
            ];
            header('HTTP/1.0 404 Method not allowed');
            return json_encode($data);
        }

    } else {
        $data = [
            'status' => 500,
            'message' => 'Internal server error',
        ];
        header('HTTP/1.0 422 Method not allowed');
        return json_encode($data);
    }

}

function updateUser($userInput, $userParams): bool|string
{
    global $conn;
    if (!isset($userParams['id'])) {
        return error422(message: 'User id not found in the URL');
    } elseif ($userParams['id'] == null) {
        return error422("Enter your user id");
    }
    $userId = mysqli_real_escape_string($conn, $userParams['id']);
    $check_id = "SELECT * FROM em_users WHERE user_id = '$userId'";
    $result = mysqli_query($conn, $check_id);

    if (mysqli_num_rows($result) == 0) {
        $data = [
            'status' => 404,
            'message' => 'No user found',
        ];
        header('HTTP/1.0 404 Method not allowed');
        return json_encode($data);
    }
    $firstName = mysqli_real_escape_string($conn, $userInput['user_first_name']);
    $lastName = mysqli_real_escape_string($conn, $userInput['user_last_name']);
    $email = mysqli_real_escape_string($conn, $userInput['user_email']);
    $pass = mysqli_real_escape_string($conn, $userInput['user_password']);
    $md5_pass = md5($pass);
    if (empty(trim($firstName))) {
        return error422("Enter your name");

    } elseif (empty(trim($lastName))) {

        return error422("Enter your Last name");
    } elseif (empty(trim($email))) {

        return error422("Enter your email");
    } elseif (empty(trim($md5_pass))) {
        return error422("Enter your password");
    } else {

        $sql = "UPDATE em_users SET user_first_name='$firstName',user_last_name='$lastName',user_email = '$email',user_password = '$md5_pass' WHERE user_id = '$userId' LIMIT 1";
        $res = mysqli_query($conn, $sql);
        if ($res) {
            $data = [
                'status' => 200,
                'message' => 'User updated successfully',
                'data' => $res
            ];
            header('HTTP/1.0 200  updated');
            return json_encode($data);

        } else {
            $data = [
                'status' => 404,
                'message' => 'No user found',
            ];
            header('HTTP/1.0 404 Method not allowed');
            return json_encode($data);
        }
    }
}

function deleteUser($userParams)
{
    global $conn;
    if (!isset($userParams['id'])) {
        return error422(message: 'User id not found in the URL');
    } elseif ($userParams['id'] == null) {
        return error422("Enter your user id");
    }

    $userId = mysqli_real_escape_string($conn, $userParams['user_id']);
    $check_id = "SELECT * FROM em_users WHERE user_id = '$userId'";
    $result = mysqli_query($conn, $check_id);

    if (mysqli_num_rows($result) == 0) {
        $data = [
            'status' => 404,
            'message' => 'No user found',
        ];
        header('HTTP/1.0 404 Method not allowed');
        return json_encode($data);
    }
    $query = "DELETE FROM em_users WHERE user_id = '$userId' LIMIT 1";
    $res = mysqli_query($conn, $query);
    if ($res) {
        $data = [
            'status' => 200,
            'message' => 'User Deleted successfully',
            'data' => $res
        ];
        header('HTTP/1.0 200  deleted');
        return json_encode($data);
    } else {
        $data = [
            'status' => 500,
            'message' => 'Internal server error',
        ];
        header('HTTP/1.0 500 Method not allowed');
        return json_encode($data);
    }

}

function error422($message)
{
    $data = [
        'status' => 422,
        'message' => $message,
    ];
    header('HTTP/1.0 422 Method not allowed');
    return json_encode($data);
}


?>