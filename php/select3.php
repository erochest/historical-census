<?php
require ("common.php");

## prints out the web template
print_template($areaname);

function print_template ($areaname) {
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
        printbody($areaname);
        print substr($contents,$pos+strlen("<!-- content here -->")+1);
}
## main body of the script--you must pass the same vars to both print_template and printbody
function printbody($areaname){
$abbrev=array(
"01"=>"CT",
"02"=>"ME",
"03"=>"MA",
"04"=>"NH",
"05"=>"RI",
"06"=>"VT",
"11"=>"DE",
"12"=>"NJ",
"13"=>"NY",
"14"=>"PA",
"21"=>"IL",
"22"=>"IN",
"23"=>"MI",
"24"=>"OH",
"25"=>"WI",
"31"=>"IA",
"32"=>"KS",
"33"=>"MN",
"34"=>"MO",
"35"=>"NE",
"36"=>"ND",
"37"=>"SD",
"40"=>"VA",
"41"=>"AL",
"42"=>"AR",
"43"=>"FL",
"44"=>"GA",
"45"=>"LA",
"46"=>"MS",
"47"=>"NC",
"48"=>"SC",
"49"=>"TX",
"51"=>"KY",
"52"=>"MD",
"53"=>"OK",
"54"=>"TN",
"56"=>"WV",
"61"=>"AZ",
"62"=>"CO",
"63"=>"ID",
"64"=>"MT",
"65"=>"NV",
"66"=>"NM",
"67"=>"UT",
"68"=>"WY",
"71"=>"CA",
"72"=>"OR",
"73"=>"WA",
"81"=>"AK",
"82"=>"HI");
print "<h2>Census Data Over Time</h2>\n";
print "<form action=\"select4.php\" method=\"POST\">\n";
print "<p><strong>Data available for:</strong></p>\n";
foreach ($areaname as $area) {
$ids=explode(':', $area);
$place=$ids[2];
$stateid=$ids[1];
$level=$ids[0];

$query2="Select * from Groups";  
$result2=mysql_query($query2);
$number2=mysql_numrows($result2);
for ($i=0; $i<$number2; $i++) {
        $grpid=mysql_result($result2, $i, "groupid");
        $groupname=mysql_result($result2, $i, "groupname");
        $grp[$grpid]=$groupname;
}
print "<input type=\"hidden\" name=\"areaname[]\" value=\"$area\">\n";
if ($level=="S") { print "<p>$place "; }
else { 
	if (ereg("CITY", $place)) { print "<p>$place, $abbrev[$stateid] "; }
	else { print "<p>$place COUNTY, $abbrev[$stateid] "; } 
}
$query="Select * from Geography where stateid=$stateid and name='$place' and st_cnty='$level'";
$result=mysql_query($query);
$row=mysql_fetch_array($result);

$y1790=$row["Y1790"];
$y1800=$row["Y1800"];
$y1810=$row["Y1810"];
$y1820=$row["Y1820"];
$y1830=$row["Y1830"];
$y1840=$row["Y1840"];
$y1850=$row["Y1850"];
$y1860=$row["Y1860"];
$y1870=$row["Y1870"];
$y1880=$row["Y1880"];
$y1890=$row["Y1890"];
$y1900=$row["Y1900"];
$y1910=$row["Y1910"];
$y1920=$row["Y1920"];
$y1930=$row["Y1930"];
$y1940=$row["Y1940"];
$y1950=$row["Y1950"];
$y1960=$row["Y1960"];
  
$years=array("1790"=>$y1790, "1800"=>$y1800, "1810"=>$y1810, "1820"=>$y1820, "1830"=>$y1830, "1840"=>$y1840,
	"1850"=>$y1850, "1860"=>$y1860, "1870"=>$y1870, "1880"=>$y1880, "1890"=>$y1890, "1900"=>$y1900,
	"1910"=>$y1910, "1920"=>$y1920, "1930"=>$y1930, "1940"=>$y1940, "1950"=>$y1950, "1960"=>$y1960); 
foreach ($years as $yr=>$value) {
	if ($value==1) { 
		$z++;
		$yrs[$z]=$yr; 		
	} 
}
print "from $yrs[1]";
$check=$yrs[1];
foreach ($years as $yr=>$value) {
        if ($value==1) { 
		$x++;
		if ($x==$z) { print " to $yrs[$x]</p>\n"; } 
	}
}
$yrs=array();
$z=$x=0;
} ##end of foreach
?>
<p><strong>Display data from: </strong><select name="begin">
<option value="1790" selected>1790</option>
<option value="1800">1800</option>
<option value="1810">1810</option>
<option value="1820">1820</option>  
<option value="1830">1830</option>  
<option value="1840">1840</option>  
<option value="1850">1850</option>  
<option value="1860">1860</option>  
<option value="1870">1870</option>  
<option value="1880">1880</option>  
<option value="1890">1890</option>  
<option value="1900">1900</option>  
<option value="1910">1910</option>  
<option value="1920">1920</option>  
<option value="1930">1930</option>  
<option value="1940">1940</option>  
<option value="1950">1950</option>  
<option value="1960">1960</option>  
</select> &nbsp &nbsp
<strong>to: </strong>
<select name="end">
<option value="1790">1790</option>
<option value="1800">1800</option>
<option value="1810">1810</option>
<option value="1820">1820</option>
<option value="1830">1830</option>
<option value="1840">1840</option>
<option value="1850">1850</option>
<option value="1860">1860</option>
<option value="1870">1870</option>
<option value="1880">1880</option>
<option value="1890">1890</option>
<option value="1900">1900</option>
<option value="1910">1910</option>
<option value="1920">1920</option>
<option value="1930">1930</option>
<option value="1940">1940</option>
<option value="1950">1950</option>
<option value="1960" selected>1960</option>
</select>
<?
print "<p><strong>Select from the subject groupings below:</strong></p>\n"; 
$query="Select varname, groupid, first, last from Vars order by groupid";
$result=mysql_query($query);
$number=mysql_numrows($result);
for ($i=0; $i<$number; $i++) {
	$groupid=mysql_result($result, $i, "groupid");
	$varname=mysql_result($result, $i, "varname");
	$start=mysql_result($result, $i, "first");
	$finish=mysql_result($result, $i, "last");
        if ($test==$groupid) { }
        else {
                if ($first==0) { $first++; }
                else {
                        print "</select>\n";
                }
		if ($finish<$check) {}
		else{
                print "<p><strong>$grp[$groupid]</strong></p>\n";
                print "<p><select name=\"var[]\" size=\"3\" multiple>\n";
                $test=$groupid;
		}
        }
	if ($finish<$check) {}
	else {
	print "<option value=\"$varname\">$varname ($start--$finish)</option>\n"; 
	}
}
print "</select></p>\n";


print "<p><input type=\"submit\"> &nbsp <input type=\"reset\"></form></p>\n";
}
mysql_close();

