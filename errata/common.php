<?

$hostName="datastore.lib.virginia.edu";
$userName="***REMOVED***";
$password="***REMOVED***";
$dbName="cmm2t_Census";

mysql_connect($hostName, $userName, $password) or die("Unable to connect to host $hostName");

mysql_select_db($dbName) or die("Unable to select database $dbName");

import_request_variables (gpc);

?>

