<?
require ("common.php");

## prints out the web template
print_template($state, $stage);

function print_template ($state, $stage) {
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
        printbody($state, $stage);
        print substr($contents,$pos+strlen("<!-- content here -->")+1);
}
## main body of the script--you must pass the same vars to both print_template and printbody
function printbody($state, $stage){

$statenames=array(
"00"=>"All States",
"01"=>"Connecticut",
"02"=>"Maine",
"03"=>"Massachusetts",
"04"=>"New Hampshire",
"05"=>"Rhode Island",
"06"=>"Vermont",
"11"=>"Delaware",
"12"=>"New Jersey",
"13"=>"New York",
"14"=>"Pennsylvania",
"21"=>"Illinois",
"22"=>"Indiana",
"23"=>"Michigan",
"24"=>"Ohio",
"25"=>"Wisconsin",
"31"=>"Iowa",
"32"=>"Kansas",
"33"=>"Minnesota",
"34"=>"Missouri",
"35"=>"Nebraska",
"36"=>"North Dakota",
"37"=>"South Dakota",
"40"=>"Virginia",
"41"=>"Alabama",
"42"=>"Arkansas",
"43"=>"Florida",
"44"=>"Georgia",
"45"=>"Louisiana",
"46"=>"Mississippi",
"47"=>"North Carolina",
"48"=>"South Carolina",
"49"=>"Texas",
"51"=>"Kentucky",
"52"=>"Maryland",
"53"=>"Oklahoma",
"54"=>"Tennessee",
"56"=>"West Virginia",
"61"=>"Arizona",
"62"=>"Colorado",
"63"=>"Idaho",
"64"=>"Montana",
"65"=>"Nevada",
"66"=>"New Mexico",
"67"=>"Utah",
"68"=>"Wyoming",
"71"=>"California",
"72"=>"Oregon",
"73"=>"Washington",
"81"=>"Alaska",
"82"=>"Hawaii");

print "<h2>Census Data Over Time</h2>\n";

print "<form action=\"long.php\" method=\"POST\">";
print "<input type=\"hidden\" name=\"stage\" value=\"county\">";
print "<p><strong>Select States:</strong></p><p><select name=\"state[]\" size=\"5\" multiple>\n";
$query="Select stateid, name from Geography where st_cnty='S' order by name";
$result=mysql_query($query);
$number=mysql_numrows($result);

for ($i=0; $i<$number; $i++) {
	$statename=mysql_result($result, $i, "name");
	$stateid=mysql_result($result, $i, "stateid");
	if ($stage=="county") {
		if (in_array($stateid, $state)) {
			print "<option value=\"$stateid\" selected>$statename</option>\n";
		}
		else {
			print "<option value=\"$stateid\">$statename</option>\n";
		}
	}
	else {
		print "<option value=\"$stateid\">$statename</option>\n";
	}
}
print "</select></p>\n<p><input type=\"submit\"> &nbsp <input type=\"reset\"></form></p>";

if ($stage=="county") {
print "<form action=\"select3.php\" method=\"POST\">";
/*
foreach ($state as $stateno) {
print "<input type=\"hidden\" name=\"stateid[]\" value=\"$stateno\">";
}
*/
print "<p><strong>Select Counties:</strong></p>";
foreach ($state as $stateno) {
print "<p><strong>$statenames[$stateno]</strong></p>";
print "<p><select name=\"areaname[]\" size=\"5\" multiple>\n";
$query="Select st_cnty, name from Geography where stateid=$stateno";
$result=mysql_query($query);
$number=mysql_numrows($result);

for ($i=0; $i<$number; $i++) {
        $name=mysql_result($result, $i, "name");
        $st_cnty=mysql_result($result, $i, "st_cnty");
        if ($st_cnty=="S") { print "<option value=\"$st_cnty:$stateno:$name\">$name (state totals)</option>\n"; }
        else { print "<option value=\"$st_cnty:$stateno:$name\">$name</option>\n"; } 
}
print "</select></p>\n";
} ##end of foreach
print "</select></p>\n<p><input type=\"submit\"> &nbsp <input type=\"reset\"></form></p>";
}
}
mysql_close();

