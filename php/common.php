<?php
/* Database connection information.  Called by each script that needs to connect to the database. */
$hostName = getenv('MYSQL_HOST');
$userName = getenv('MYSQL_USER');
$password = getenv('MYSQL_PASSWORD');
$dbName   = getenv('MYSQL_DBNAME');

$link = mysqli_connect($hostName, $userName, $password, $dbName)
    or die("Unable to connect to host $dbName@$hostName");

function query_fetch_all($link, $query) {
    $result = mysqli_query($link, $query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function query_fetch_one($link, $query) {
    $result = mysqli_query($link, $query);
    return $result->fetch_assoc();
}

function get_groups($link) {
    $query = "Select * from Groups";
    $data  = query_fetch_all($link, $query);
    $grp   = array();

    foreach ($data as $row) {
        $grpid       = $row["groupid"];
        $groupname   = $row["groupname"];
        $grp[$grpid] = $groupname;
    }

    return $grp;
}

/**
 * Imports GET/POST/Cookie variables into the global scope
 */
function array_get($key, $array) {
    if (array_key_exists($key, $array)) {
        return $array[$key];
    } else {
        return null;
    }
}

function validate_regex($value, $re, $name) {
    if (!empty($value) && preg_match($re, $value) === 0) {
        trigger_error("Invalid $name value: '$value'");
        exit;
    }
}

function validate_array($value, $re, $name) {
    if (empty($value)) {
        return array();
    }
    if (!is_array($value)) {
        $value = array($value);
    }

    foreach ($value as $v) {
        validate_regex($v, $re, $name);
    }

    return $value;
}

$input_vars = $_GET + $_POST;
$denominator = array_get('denominator', $input_vars);
$direction   = array_get('direction',   $input_vars);
$numerator   = array_get('numerator',   $input_vars);
$sort        = array_get('sort',        $input_vars);
$stateid     = array_get('stateid',     $input_vars);
$subject     = array_get('subject',     $_GET);
$table       = array_get('table',       $input_vars);
$vars        = array_get('vars',        $input_vars);
$year        = array_get('year',        $input_vars);

$var_valid   = '/^var\d+$/';
$numerator   = validate_array($numerator   , $var_valid , 'numerator');
$stateid     = validate_array($stateid     , '/^\d+$/'  , 'stateid');
$vars        = validate_array($vars        , $var_valid , 'var');
validate_regex($denominator , $var_valid      , 'denominator');
validate_regex($direction   , '/ASC|DESC/'    , 'direction');
validate_regex($sort        , '/^\w+$/'       , 'sort');
validate_regex($table       , '/^\d\d\d\d$/'  , 'table');
validate_regex($year        , '/^V\d\d\d\d$/' , 'year' );

