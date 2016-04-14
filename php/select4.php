<?php
require ("common.php");

## prints out the web template
print_template($areaname, $begin, $end, $var);

function print_template ($areaname, $begin, $end, $var) {
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
        printbody($areaname, $begin, $end, $var);
        print substr($contents,$pos+strlen("<!-- content here -->")+1);
}
## main body of the script--you must pass the same vars to both print_template and printbody
function printbody($areaname, $begin, $end, $var){

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
foreach ($areaname as $area) {
$ids=explode(':', $area);
$place=$ids[2];
$stateid=$ids[1];
$level=$ids[0];
$levelnames=array("S"=>"State", "C"=>"County");

if ($level=="S") { print "<h3>Results for $place</h3>"; }
else {
	if (ereg("CITY", $place)) { print "<h3>Results for $place, $abbrev[$stateid]</h3>"; }
	else { print "<h3>Results for $place $levelnames[$level], $abbrev[$stateid]</h3>"; }
}
$savebegin=$begin;
$saveend=$end;
foreach ($var as $key => $value) {
$query="Select * from Vars where varname='$value'";
$result=mysql_query($query);
$row=mysql_fetch_array($result);
$V1790=$row["C1790"];
$V1800=$row["C1800"];
$V1810=$row["C1810"];
$V1820=$row["C1820"];
$V1830=$row["C1830"];
$V1840=$row["C1840"];
$V1850=$row["C1850"];
$V1860=$row["C1860"];
$V1870=$row["C1870"];
$V1880=$row["C1880"];
$V1890=$row["C1890"];
$V1900=$row["C1900"];
$V1910=$row["C1910"];
$V1920=$row["C1920"];
$V1930=$row["C1930"];
$V1940=$row["C1940"];
$V1950=$row["C1950"];
$V1960=$row["C1960"];

$F1790=$row["F1790"];
$F1800=$row["F1800"];
$F1810=$row["F1810"];
$F1820=$row["F1820"];
$F1830=$row["F1830"];
$F1840=$row["F1840"];
$F1850=$row["F1850"];
$F1860=$row["F1860"];
$F1870=$row["F1870"];
$F1880=$row["F1880"];
$F1890=$row["F1890"];
$F1900=$row["F1900"];  
$F1910=$row["F1910"];
$F1920=$row["F1920"];
$F1930=$row["F1930"];
$F1940=$row["F1940"];
$F1950=$row["F1950"];
$F1960=$row["F1960"];

$fields=array("1790"=>$V1790, "1800"=>$V1800, "1810"=>$V1810, "1820"=>$V1820, "1830"=>$V1830, "1840"=>$V1840,
        "1850"=>$V1850, "1860"=>$V1860, "1870"=>$V1870, "1880"=>$V1880, "1890"=>$V1890, "1900"=>$V1900,
        "1910"=>$V1910, "1920"=>$V1920, "1930"=>$V1930, "1940"=>$V1940, "1950"=>$V1950, "1960"=>$V1960);

$formulas=array("1790"=>$F1790, "1800"=>$F1800, "1810"=>$F1810, "1820"=>$F1820, "1830"=>$F1830, "1840"=>$F1840,
        "1850"=>$F1850, "1860"=>$F1860, "1870"=>$F1870, "1880"=>$F1880, "1890"=>$F1890, "1900"=>$F1900,
        "1910"=>$F1910, "1920"=>$F1920, "1930"=>$F1930, "1940"=>$F1940, "1950"=>$F1950, "1960"=>$F1960);

print "<div class=\"indent5em\"><table border=\"1\"><tr>";
if ($level=="S") { print "<th colspan=\"2\">$value for $place</th></tr>";}
else {
	if (ereg("CITY", $place)) { print "<th colspan=\"2\">$value for $place</th></tr>";}
	else { print "<th colspan=\"2\">$value for $place $levelnames[$level]</th></tr>"; }
}
while ($begin <=$end) {
	if ($level=="S") { $query2="Select $fields[$begin] where stateid='$stateid' and st_cnty='$level'"; }
	else { $query2="Select $fields[$begin] where name='$place' and stateid='$stateid' and st_cnty='$level'"; }
	$result2=mysql_query($query2);
	$row2=mysql_fetch_array($result2);
	$numvars=explode(':', $formulas[$begin]);
	if ($numvars[0]>1) { 
		$splitvars=explode (' from ', $fields[$begin]);
		$listvars=explode (',', $splitvars[0]);
		$v=array();
		foreach ($listvars as $key=>$VAR) {
			$v[$key]=$row2[$key];
		}
		eval("\$final=$numvars[1];");
	}
	else { 
		$splitvars=explode (' from ', $fields[$begin]);
		$VAR=$splitvars[0];
		$final=$row2["$VAR"];
	}
	if ($final<=0) { 
		print "<tr><td align=\"center\">$begin</td><td align=\"center\">N/A</td></tr>";
	}
	else{
	print "<tr><td align=\"center\">$begin</td><td align=\"center\">".number_format($final)."</td></tr>";
	}
	$begin=$begin+10;
}
print "</table></div><br />";
$begin=$savebegin;
$end=$saveend;
}
}##end of areaname foreach
}
mysql_close();

