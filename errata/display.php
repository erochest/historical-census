<HTML>
<HEAD>
<TITLE>Errata for Historical Census Browser</TITLE>
<link href="/styles/geostat_main.css" rel="stylesheet" type="text/css" />
</HEAD>
<BODY>

<TABLE BORDER=0 CELLPADDING=2 CELLSPACING=2>
<TR>
        <TD VALIGN="top">
                <CENTER>
                <B><FONT SIZE=6>Errata<BR>
                </FONT>
		<Font Size=5>United States Historical Census Browser</font>
                </CENTER></b>
                <P>
                <HR>
                <P><font face="arial" size="1">

<?
include ("common.php");
if ($year=="ALL" && $state=="ALL") {
	$query="Select * from Errata";
}
elseif ($year=="ALL") {
	$query="Select * from Errata where State='$state'";
}
elseif ($state=="ALL") {
	$query="Select * from Errata where Yr='$year'";
}
else {
	$query="Select * from Errata 
		where Yr=$year and State='$state'";
}
$result=mysql_query($query);
$number=mysql_numrows($result);


if ($number==0){
print "<p>No errata for that year and state combination.";
print "<p><a href=\"index.php\">Try another combination</a>\n";
print "<p><a href=\"../index.html\">Return to the Census Browser</a>\n";
}
else {

print "<table border=1>\n";
print "<tr><th>Year</th><th>State</th><th>County</th><th>Item</th><th>Incorrect value</th>\n"; 
print "<th>Correct Value</th><th>Source</th><th>Comments</th><th>Updated</th><th>Date Updated</th></tr>\n";
for ($i=0; $i<$number; $i++) {
        $year=mysql_result($result, $i, "Yr");
        $state=mysql_result($result, $i, "State");
        $county=mysql_result($result, $i, "County"); 
        $item=mysql_result($result, $i, "Item");
        $icpsr=mysql_result($result, $i, "ICPSR");
        $correct=mysql_result($result, $i, "Correct");
        $source=mysql_result($result, $i, "Source");
        $comments=mysql_result($result, $i, "Cmments");
 	$update=mysql_result($result, $i, "Updated");
	$date=mysql_result($result, $i, "DateUpdated");
        print "<tr><td>$year</td><td>$state</td><td>$county</td><td>$item</td>\n";
        print "<td>".number_format($icpsr)."</td><td>".number_format($correct);
	print "</td><td>$source&nbsp</td><td>$comments&nbsp</td><td>$update</td><td>$date&nbsp</td></tr>\n";
}
print"</table>\n";
print "<p><a href=\"index.php\">Make another selection</a>\n";
print "<p><a href=\"../index.html\">Return to the Census Browser</a>\n";

}
mysql_close();
?>
</font>
</td></tr></table>
<script src='/libtools.js' type='text/javascript'></script></body></html>

