<?
/* Database connection information.  Called by each script that needs to connect to the database. */
$hostName="***REMOVED***";
$userName="***REMOVED***";
$password="***REMOVED***";
$dbName="cmm2t_CensusBrowser";

$link = mysql_connect($hostName, $userName, $password) or die("Unable to connect to host $hostName");

mysql_select_db($dbName, $link) or die("Unable to select database $dbName");

/**
 * Imports GET/POST/Cookie variables into the global scope
 */
import_request_variables ("gpc");
if (gettype($vars) == 'string') {
  $vars = array($vars);
}

?>
