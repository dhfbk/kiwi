<?php

$mysqli = @new mysqli($DB_HOST, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
if ($mysqli->connect_errno) {
    dieWithError("Error in DB connection: " . $mysqli->connect_error);
}

mysqli_options($mysqli, MYSQLI_OPT_INT_AND_FLOAT_NATIVE, true);

function loadOptions() {
    global $mysqli;
    
    $Options = array();
    $Options['api'] = array();
    $Options['backend'] = array();
    $query = "SELECT * FROM options";
    
    $result = $mysqli->query($query);
    if ($result) {
        while ($obj = $result->fetch_object()) {
            $label = "backend";
            if ($obj->api) {
                $label = "api";
            }
            $Options[$label][$obj->id] = $obj->value;
        }
    }
    return $Options;
}

function checkField($var, $err_msg) {
    if (!$var) {
        dieWithError($err_msg);
    }
    return $var;
}

function dieWithError($text, $code = 400, $addenda = []) {
    http_response_code($code);
    $ret = array();
    $ret['result'] = "ERR";
    $ret['error'] = $text;
    $ret = array_merge($ret, $addenda);
    echo json_encode($ret);
    exit();
}

function isLogged() {
    return !empty($_SESSION['Login']);
}

function checkLogin() {
    if (!isLogged()) {
        dieWithError("User not logged in", 401);
    }
}

function find($table, $id, $text) {
    global $mysqli;
    
    $stmt_up = $mysqli->prepare("SELECT * FROM {$table} WHERE id = ?");
    $stmt_up->bind_param("s", $id);
    $stmt_up->execute();
    $r = $stmt_up->get_result();
    if ($r->num_rows) {
        $row = $r->fetch_assoc();
        if ($row['data']) {
            $row['data'] = json_decode($row['data'], true);
        }
        return $row;
    }

    dieWithError($text);
}

function queryinsert($table, $a) {
    $array1 = $array2 = array();
    foreach ($a as $i => $v) {
        $array1[] = "`".$i."`";
        $array2[] = "'".addslashes($v)."'";
    }
    $query = "INSERT INTO ".$table." (".
        implode(", ", $array1).") VALUES (".
        implode(", ", $array2).")";
    return $query;
}
