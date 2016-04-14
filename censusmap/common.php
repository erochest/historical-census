<?
/* Database connection information.  Called by each script that needs to connect to the database. */
$hostName="mysql.lib.virginia.edu";
$userName="***REMOVED***";
$password="***REMOVED***";
$dbName="cmm2t_CensusBrowser";

mysql_connect($hostName, $userName, $password) or die("Unable to connect to host $hostName");

mysql_select_db($dbName) or die("Unable to select database $dbName");

import_request_variables ("gpc");

?>
