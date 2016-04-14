<?

print_template();

function print_template () {
        $handle=fopen ("http://fisher.lib.virginia.edu/cgi-local/script_template.html","rb");
        $contents="";
        do {
                $data=fread($handle, 1024);
                if (strlen($data) == 0) {
                        break;
                }
                $contents .=$data;
        } while (true);
        fclose($handle);
        $contents=str_replace("<!-- title here -->", "Historical Census Browser", $contents);
        $pos = strpos($contents, "<!--  content here -->");
        //We don't need any stinking regular expressions!!!!!
        print substr($contents,0,$pos);
        printbody();
        print substr($contents,$pos+strlen("<!-- content here -->")+1);
}
## main body of the script--you must pass the same vars to both print_template and printbody
function printbody(){
?>
<h2>United States Historical Census Browser</h2>
<h3>Errata Search</h3>

<p>The data contained in the Historical Census Browser database were entered manually in the 1970s.
Due to the size of the database, some errors were not noticed before the database was made available
to the public through ICPSR. In many cases, variables at the county level were summed to the state
and nation, and thus errors at the county level can affect the same variable at the state and
national levels.</p>

<p>This errata log contains a listing of all errors that have come to our attention. As errors are
reported to us, we check the data against the original source, and correct the database accordingly.
The log includes information on the year, geography, and variable, as well as the original source of
the data and any pertinent comments. You can view all errata for a given year, a given state, or all
changes we have made to the database to date.</p>

<p>If you question any of the data found in the Historical Census Browser, please email us at
<a href="mailto:geostat@virginia.edu">geostat@virginia.edu</a>, describing the year, geography, and
variable involved. We will research the problem, make corrections to the database if appropriate, and
respond to your email notifying you of the outcome. All changes we make to the database are shared
with ICPSR.</p>
<div class="indent5em">
<form action="display.php" method="post"> 
Year:<select name="year"> 
<option value="ALL">ALL YEARS</option>
<?
include ("common.php");
$query="Select Distinct Yr from Errata order by Yr";

$result=mysql_query($query);
$number=mysql_numrows($result);

for ($i=0; $i<$number; $i++) {
        $year=mysql_result($result, $i, "Yr");
	print "<option value=\"$year\">$year</option>";
}
print "</select>\n &nbsp&nbsp State:<select name=\"state\">\n";
print "<option value=\"ALL\">ALL STATES & US</option>\n";
$query2="Select Distinct State from Errata order by State";

$result2=mysql_query($query2);
$number2=mysql_numrows($result2);

for ($i=0; $i<$number2; $i++) {
        $state=mysql_result($result2, $i, "State");
        print "<option value=\"$state\">$state</option>";
}

?>
</select>
<p><input type="submit">&nbsp&nbsp<input type="reset"></p>
</form>
</div>
<p><A HREF="http://fisher.lib.virginia.edu/collections/stats/histcensus">Back</A> to the Census Search Page</p>
<?
mysql_close();
}
?>
