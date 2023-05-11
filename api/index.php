<?php

ini_set("session.use_cookies", 0);

header("Content-Type: application/json; charset=UTF-8");

header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// use Ds\Map;
// use Ds\Set;

// CORS stuff
// https://stackoverflow.com/questions/53298478/has-been-blocked-by-cors-policy-response-to-preflight-request-doesn-t-pass-acce
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    http_response_code(200);
    exit();
}

http_response_code(500);
date_default_timezone_set("Europe/Rome");

ob_start();

$script_uri = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['SCRIPT_NAME']}";

$request_body = file_get_contents('php://input');
if ($request_body) {
    // print_r(substr($request_body, 0, 1000));
    $payload = json_decode($request_body, true);
    if ($payload) {
        $_REQUEST = array_merge($payload, $_REQUEST);
    }
}

if (isset($_REQUEST['session_id']) && $_REQUEST['session_id']) {
    session_id($_REQUEST['session_id']);
}
session_start();

require_once("config.php");
require_once("include.php");

$Action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$Options = loadOptions();
$ret = [];
$ret['result'] = "OK";

switch ($Action) {
    case "login":
        $username = @checkField($_REQUEST['username'], "Missing username parameter");
        $password = @checkField($_REQUEST['password'], "Missing password parameter");
        $username = addslashes($username);

        $query = "SELECT * FROM users u
            WHERE u.username = '{$username}' AND u.deleted = 0";
        $result = $mysqli->query($query);
        if (!$result->num_rows) {
            dieWithError("User " . $username . " does not exist", 401);
        }
        $RowUser = $result->fetch_array(MYSQLI_ASSOC);
        if ($RowUser['password'] != md5($password)) {
            dieWithError("Invalid password", 401);
        }

        $_SESSION['Login'] = $RowUser['id'];

        $ret['session_id'] = session_id();
        break;

    case "userinfo":
        checkLogin();
        $Row = find("users", $_SESSION['Login'], "Unable to find user");
        unset($Row['password']);
        $ret['data'] = $Row;
        $ret['options'] = $Options['api'];
        break;

    case "cleanOptions":
        unset($_SESSION['Options']);
        break;

    case "logout":
        unset($_SESSION['Login']);
        break;
}

http_response_code(200);
echo json_encode($ret);
