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

function get_groups($link, $groupid=null) {
    $query = "Select * from Groups";
    if (!is_null($groupid)) {
        $query .= " where groupid='"
            . mysqli_real_escape_string($link, $groupid)
            . "'";
    }

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

$input_vars  = $_GET + $_POST;
$allstates   = array_get('allstates'   , $input_vars);
$begin       = array_get('begin'       , $input_vars);
$denominator = array_get('denominator' , $input_vars);
$direction   = array_get('direction'   , $input_vars);
$end         = array_get('end'         , $input_vars);
$geolevel    = array_get('geolevel'    , $input_vars);
$label       = array_get('label'       , $input_vars);
$long        = array_get('long'        , $input_vars);
$numerator   = array_get('numerator'   , $input_vars);
$sort        = array_get('sort'        , $input_vars);
$stateid     = array_get('stateid'     , $input_vars);
$stateids    = array_get('stateids'    , $input_vars);
$statename   = array_get('statename'   , $input_vars);
$subject     = array_get('subject'     , $input_vars);
$table       = array_get('table'       , $input_vars);
$valid       = array_get('valid'       , $input_vars);
$varname     = array_get('varname'     , $input_vars);
$var         = array_get('var'         , $input_vars);
$vars        = array_get('vars'        , $input_vars);
$year        = array_get('year'        , $input_vars);

$var_valid = '/^var\d+$/';
$num_valid = '/^\d+$/';
$numerator = validate_array($numerator   , $var_valid , 'numerator' );
$stateid   = validate_array($stateid     , '/^\d+$/'  , 'stateid'   );
$stateids  = validate_array($stateids    , '/^\d+$/'  , 'stateids'  );
$valid     = validate_array($valid       , $num_valid , 'valid'     );
$vars      = validate_array($vars        , $var_valid , 'var'       );
validate_regex($allstates   , '/^ALL$/'           , 'allstates'     );
validate_regex($begin       , $num_valid          , 'begin'         );
validate_regex($denominator , $var_valid          , 'denominator'   );
validate_regex($direction   , '/ASC|DESC/'        , 'direction'     );
validate_regex($end         , $num_valid          , 'end'           );
validate_regex($geolevel    , '/^\w+$/'           , 'geolevel'      );
validate_regex($label       , '/^[\w \\/()%]*$/'  , 'label'         );
validate_regex($long        , '/^[\w ]*$/'        , 'long'          );
validate_regex($sort        , '/^\w*$/'           , 'sort'          );
validate_regex($statename   , '/^[\w ]*$/'        , 'statename'     );
validate_regex($subject     , $num_valid          , 'subject'       );
validate_regex($table       , '/^\d\d\d\d$/'      , 'table'         );
validate_regex($var         , '/^[\w ]*$/'        , 'var'           );
validate_regex($varname     , '/^[^:]+:\d+:\d+$/' , 'varname'       );
validate_regex($year        , '/^V?\d\d\d\d$/'     , 'year'          );

