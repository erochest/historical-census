<?php
require ("common.php");

## prints out the web template
print_template($year);

function print_template ($year) {
        $handle=fopen ("script_template.html","rb");
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
        printbody($year);
        print substr($contents,$pos+strlen("<!-- content here -->")+1);
}
## main body of the script--you must pass the same vars to both print_template and printbody
function printbody($year){
$yr=explode('V', $year);
print "<h2>Census Data for Year $yr[1]</h2>\n"; 
print "<p>The selection lists below represent all of the questions from the $yr[1] census recorded by ";
print "<a href=\"http://www.icpsr.umich.edu/\">ICPSR</a>.  They have been grouped into categories for
convenience. The categories are not part of the original data.</p>\n";
print "<h3>Variables for $yr[1] Census</h3>\n";
$query2="Select * from Groups";
$result2=mysql_query($query2);
$number2=mysql_numrows($result2);
for ($i=0; $i<$number2; $i++) {
	$grpid=mysql_result($result2, $i, "groupid");
	$groupname=mysql_result($result2, $i, "groupname");
	$grp[$grpid]=$groupname;
}
print "<p>";
$query2="Select Distinct groupid from $year where groupid!=0 and groupid!=32 order by groupid";
$result2=mysql_query($query2); 
$number2=mysql_numrows($result2);
for ($i=0; $i<$number2; $i++) {
	$groupid=mysql_result($result2, $i, "groupid");
	if ($groupid==33) {}
	else { 
		print "<a href=\"#$groupid\">$grp[$groupid]</a> &nbsp &nbsp"; 
	}
}
print "</p>\n";
print "<form action=\"state.php\" method=\"POST\">\n";
print "<input type=\"hidden\" name=\"table\" value=\"$yr[1]\">\n";
$first=0;
$query="Select * from $year where groupid!=0 and groupid!=32 order by groupid";
$result=mysql_query($query);
$number=mysql_numrows($result);
for ($i=0; $i<$number; $i++) {
	$varname=mysql_result($result, $i, "varname");
	$label=mysql_result($result, $i, "label");
	$groupid=mysql_result($result, $i, "groupid");
	if ($test==$groupid) { }
	else { 
		if ($first==0) { $first++; }
		else {
			print "</select></p>\n";
		}
		if ($groupid==33) {}
		else { print "<a name=\"$groupid\"><h4>$grp[$groupid]</h4></a>\n"; }
        	print "<p><select name=\"vars[]\" size=\"7\" multiple>\n"; 
		$test=$groupid;
	}
	switch ($groupid){
		case 32:
			break;
		case 0:
			break;
		default:
			$l=strtolower($label);
			print "<option value=\"$varname\">$l</option>\n"; 
			break;
	}
}
print "</select></p>\n<p>
<input type=\"submit\"> &nbsp <input type=\"reset\">
</form></p>\n";

mysql_close();
}

