<?

$hostName="datastore.lib.virginia.edu";
$userName="djt5k";
$password="iXfYd64i";
$dbName="cmm2t_Census";

mysql_pconnect($hostName, $userName, $password) or die("Unable to connect to host $hostName");

mysql_select_db($dbName) or die("Unable to select database $dbName");

import_request_variables (gpc);

?>
