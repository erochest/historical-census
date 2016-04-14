<?
include ("ed.php");
if ($year=="") { print "Please enter some information."; }
else {
$query="Insert into Errata
	(Yr, State, County, Item, ICPSR, Correct, Source, Cmments, Updated, DateUpdated)
	Values ('$year', '$state', '$county', '$item', '$icpsr', '$correct', '$source', '$comment',
	'$update', '$when')";

$result=mysql_query($query);   

print "<b>Database updated!</b>";
}
print "<p><a href=\"input.html\">Input errata</a>";
print "<p><a href=\"index.php\">Search the errata</a>";
print "<p><a href=\"../index.html\">Return to the Census Browser</a>";
mysql_close();
?>
