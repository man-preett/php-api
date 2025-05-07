<?php
include('../inc/dbcon.php');
function getUsers(): bool|string
{
    global $conn;
    $query = 'SELECT * FROM users';
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

    $name = mysqli_real_escape_string($conn, $userInput['name']);
    $email = mysqli_real_escape_string($conn, $userInput['email']);
    $address = mysqli_real_escape_string($conn, $userInput['address']);
    $query_email = "SELECT 'email' FROM users WHERE 'email'= '$email'";
    $result = mysqli_query($conn, $query_email);
    if (empty(trim($name))) {
        return error422("Enter your name");

    } elseif (empty(trim($email))) {

        return error422("Enter your email");
    } 
    elseif (mysqli_num_rows($result)>0) {
        return error422("This email is already used");
    } 
    elseif (empty(trim($address))) {
        return error422("Enter your address");
    } else {
        $query = "INSERT INTO users (name,email,address) VALUES ('$name','$email','$address')";
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
    $query = "SELECT * from users WHERE id= '$userId' LIMIT 1";
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

function updateUser($userInput, $userParams)
{
    global $conn;
    if (!isset($userParams['id'])) {
        return error422(message: 'User id not found in the URL');
    } elseif ($userParams['id'] == null) {
        return error422("Enter your user id");
    }



    $userId = mysqli_real_escape_string($conn, $userParams['id']);

    $check_id = "SELECT * FROM users WHERE id = '$userId'";
    $result = mysqli_query($conn, $check_id);

    if (mysqli_num_rows($result) == 0) {
        $data = [
            'status' => 404,
            'message' => 'No user found',
        ];
        header('HTTP/1.0 404 Method not allowed');
        return json_encode($data);
    }
    $name = mysqli_real_escape_string($conn, $userInput['name']);
    $email = mysqli_real_escape_string($conn, $userInput['email']);
    $address = mysqli_real_escape_string($conn, $userInput['address']);
    if (empty(trim($name))) {
        return error422("Enter your name");

    } elseif (empty(trim($email))) {

        return error422("Enter your email");
    } elseif (empty(trim($address))) {
        return error422("Enter your address");
    } else {

        $sql = "UPDATE users SET name='$name',email = '$email',address = '$address' WHERE id = '$userId' LIMIT 1";
        echo $sql;


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

    $userId = mysqli_real_escape_string($conn, $userParams['id']);
    $check_id = "SELECT * FROM users WHERE id = '$userId'";
    $result = mysqli_query($conn, $check_id);

    if (mysqli_num_rows($result)==0) {
        $data = [
            'status' => 404,
            'message' => 'No user found',
        ];
        header('HTTP/1.0 404 Method not allowed');
        return json_encode($data);
    }
    $query = "DELETE FROM users WHERE id = '$userId' LIMIT 1";
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