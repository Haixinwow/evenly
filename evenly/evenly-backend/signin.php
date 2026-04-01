<?php
header('Content-Type: application/json');

// example tasks
$tasks = [
    ['id' => 1, 'name' => 'Do dishes'],
    ['id' => 2, 'name' => 'Take out trash'],
    ['id' => 3, 'name' => 'Clean bathroom']
];

echo json_encode($tasks);

session_start();

// https://stackoverflow.com/questions/74937545/uncaught-syntaxerror-unexpected-token-br-when-using-json-parse-on-the
require 'db.php';
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1); 
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);


// if (!hash_equals($_SESSION["token"], $json_obj['token'])) {
//     echo json_encode(array(
//         'success' => false,
//         'message' => "CSRF submitted token does not match token that came from form"
//     ));
//     exit;
// }

if (isset($_SESSION['username'])) {
    echo json_encode(array( 
        'success' => false,
        'message' => 'Already logged in'
    ));
    exit;
}


$username = htmlentities($json_obj['username']);
$password = htmlentities($json_obj['password']);

if (!preg_match('/^[\w_\-]+$/', $username) || $username == "") {
    echo json_encode(array(
        'success' => false,
        'message' => "Empty or includes characters not supported"
    ));
    exit;
}

$check_stmt = $mysqli->prepare("SELECT 1 FROM users WHERE username = ?");
if (!$check_stmt) {
    echo json_encode(array(
        'success' => false,
        'message' => "database error"
    ));
    exit;
}
$check_stmt->bind_param('s', $username);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    echo json_encode(array(
        'success' => false,
        'message' => "username taken"
    ));
    exit;
}
$check_stmt->close();

$password_hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $mysqli->prepare("INSERT INTO users (username, user_password) VALUES (?, ?)");
if (!$stmt) {
    echo json_encode(array(
        'success' => false,
        'message' => "database error"
    ));
    exit;
}

//attach placeholders with actual username and password_hash
$stmt->bind_param('ss', $username, $password_hash);

if (!$stmt->execute()) {
    echo json_encode(array(
        'success' => false,
        'message' => "database error"
    ));
    exit;
} else {
    $stmt->close();

    $stmt = $mysqli->prepare("SELECT user_id FROM users WHERE username = ?");
    if (!$stmt) {
        echo json_encode(array(
            'success' => false,
            'message' => "database error"
        ));
        exit;
    }
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->bind_result($user_id);
    while ($stmt->fetch()) {
        $_SESSION['user_id'] = $user_id;
    }
    $stmt->close();

    $_SESSION['username'] = $username;
    $_SESSION["token"] = bin2hex(random_bytes(32));
    echo json_encode(array(
        'success' => true,
        'token' => $_SESSION["token"],
        'username' => $username, 
        'user_id' => $user_id
    ));

    exit;
}
?>