<?
require ("common.php");

$abbrev=array(
"01"=>"ct",
"02"=>"me",
"03"=>"ma",
"04"=>"nh",
"05"=>"ri",
"06"=>"vt",
"11"=>"de",
"12"=>"nj",
"13"=>"ny",
"14"=>"pa",
"21"=>"il",
"22"=>"in",
"23"=>"mi",
"24"=>"oh",
"25"=>"wi",
"31"=>"ia",
"32"=>"ks",
"33"=>"mn",
"34"=>"mo",
"35"=>"ne",
"36"=>"nd",
"37"=>"sd",
"40"=>"va",
"41"=>"al",
"42"=>"ar",
"43"=>"fl",
"44"=>"ga",
"45"=>"la",
"46"=>"ms",
"47"=>"nc",
"48"=>"sc",
"49"=>"tx",
"51"=>"ky",
"52"=>"md",
"53"=>"ok",
"54"=>"tn",
"56"=>"wv",
"61"=>"az",
"62"=>"co",
"63"=>"id",
"64"=>"mt",
"65"=>"nv",
"66"=>"nm",
"67"=>"ut",
"68"=>"wy",
"71"=>"ca",
"72"=>"or",
"73"=>"wa",
"81"=>"ak",
"82"=>"hi");

$dir="/p0/geostats/tmp_census/";
$temp=getmypid();
$plus=rand(1,9999);
$pid=$temp+$plus;
mkdir("$dir/$pid", 0775);
$path="$dir/$pid";
$table="C".$year;

switch($long) {
	case "":
if ($geolevel=="us_county") {
$shapename=$year."counties.shp";
$dbfname=$year."counties.dbf";
$shxname=$year."counties.shx";
copy ("/p0/geostats/GIS_data/husco/Merged_States/$shapename", "$path/histcen.shp");
copy ("/p0/geostats/GIS_data/husco/Merged_States/$dbfname", "$path/orig$pid.dbf");
copy ("/p0/geostats/GIS_data/husco/Merged_States/$shxname", "$path/histcen.shx");
$shapename=$year."states.shp";
$dbfname=$year."states.dbf";
$shxname=$year."states.shx";
copy ("/p0/geostats/GIS_data/husco/Merged_States/$shapename", "$path/histstate.shp");
copy ("/p0/geostats/GIS_data/husco/Merged_States/$dbfname", "$path/histstate.dbf");
copy ("/p0/geostats/GIS_data/husco/Merged_States/$shxname", "$path/histstate.shx");
copy ("/var/www/html/mapservr/HUSCO/counties.map", "$path/counties.map");
$stateid="00";
if ($var=="") {
	$query="Select fipscode, $numerator, $denominator from $table where st_cnty='C'";
	$ratio=1;
}
else {
	$query="Select fipscode, $var from $table where st_cnty='C'";
	$ratio=0;
}
}
if ($geolevel=="us_state") {
$stateid="00";
$shapename=$year."states.shp";
$dbfname=$year."states.dbf";
$shxname=$year."states.shx";
copy ("/p0/geostats/GIS_data/husco/Merged_States/$shapename", "$path/histcen.shp");
copy ("/p0/geostats/GIS_data/husco/Merged_States/$dbfname", "$path/orig$pid.dbf");
copy ("/p0/geostats/GIS_data/husco/Merged_States/$shxname", "$path/histcen.shx");
copy ("/var/www/html/mapservr/HUSCO/states.map", "$path/states.map");
if ($var=="") {
        $query="Select fipscode, $numerator, $denominator from $table where st_cnty='S'";
        $ratio=1;  
}
else {
        $query="Select fipscode, $var from $table where st_cnty='S'";
        $ratio=0;
}
}
if ($geolevel=="state_by_county") {
$shapename=$abbrev[$stateid].$year.".shp";
$dbfname=$abbrev[$stateid].$year.".dbf";
$shxname=$abbrev[$stateid].$year.".shx";
copy ("/p0/geostats/GIS_data/husco/$year/$shapename", "$path/histcen.shp");
copy ("/p0/geostats/GIS_data/husco/$year/$dbfname", "$path/orig$pid.dbf");
copy ("/p0/geostats/GIS_data/husco/$year/$shxname", "$path/histcen.shx");
$shapename=$year."states.shp";
$dbfname=$year."states.dbf";
$shxname=$year."states.shx";
copy ("/p0/geostats/GIS_data/husco/Merged_States/$shapename", "$path/histstate.shp");
copy ("/p0/geostats/GIS_data/husco/Merged_States/$dbfname", "$path/histstate.dbf");
copy ("/p0/geostats/GIS_data/husco/Merged_States/$shxname", "$path/histstate.shx");
copy ("/var/www/html/mapservr/HUSCO/counties.map", "$path/counties.map");
$query="Select fipscode, $var from $table where st_cnty='C' and stateid=$stateid";
if ($var=="") {
        $query="Select fipscode, $numerator, $denominator from $table where st_cnty='C' and stateid=$stateid";
        $ratio=1;
}
else {
        $query="Select fipscode, $var from $table where st_cnty='C' and stateid=$stateid";
        $ratio=0;
}
}
$filename=fopen("$path/$pid.ph", "w") or die ("can't open $pid.ph: $php_errormsg");
fwrite ($filename, "#! /usr/local/bin/perl\n");
fwrite ($filename, "sub values {\n");
fwrite ($filename, "%fipsvals=(\n");
$result=mysql_query($query);
$number=mysql_num_rows($result);
$counter=0;
for ($i=0; $i<$number; $i++) {
	$fips=mysql_result($result, $i, "fipscode");
	if ($ratio==0) { 
	$value=mysql_result($result, $i, "$var"); 
	if ($fips=="") { $fips=0; }
	if ($i+1==$number) { 
		$line="'$fips'=>$value);\n}\n\$true=1;\n";
	}
	else {
		$line="'$fips'=>$value,\n";
	}
	} ##end of ratio if
	elseif ($ratio==1)  {
		$num=mysql_result($result, $i, "$numerator");
		$denom=mysql_result($result, $i, "$denominator");
		$value=(($num/$denom)*100);  
		if ($value<0) { $value=-1; }
		if ($fips=="") { $fips=0; }
		if ($i+1==$number) {
                $line="'$fips'=>".number_format($value,2).");\n}\n\$true=1;\n";
        }
        else {
                $line="'$fips'=>".number_format($value,2).",\n";
        }
	}##end of elsif

	fwrite ($filename, $line);
}
	break;

	default:
##$formula="F".$year;
$fields="C".$year;
$queryX="Select $fields from Vars where varname='$long'";
$resultX=mysql_query($queryX);
$rowX=mysql_fetch_array($resultX);
##$form=$rowX[$formula];
$phrase=$rowX[$fields];
$col="Y".$year;


if ($geolevel=="us_county") {
$shapename=$year."counties.shp";
$dbfname=$year."counties.dbf";
$shxname=$year."counties.shx";
copy ("/p0/geostats/GIS_data/husco/Merged_States/$shapename", "$path/histcen.shp");
copy ("/p0/geostats/GIS_data/husco/Merged_States/$dbfname", "$path/orig$pid.dbf");
copy ("/p0/geostats/GIS_data/husco/Merged_States/$shxname", "$path/histcen.shx");
$shapename=$year."states.shp";
$dbfname=$year."states.dbf";
$shxname=$year."states.shx";
copy ("/p0/geostats/GIS_data/husco/Merged_States/$shapename", "$path/histstate.shp");
copy ("/p0/geostats/GIS_data/husco/Merged_States/$dbfname", "$path/histstate.dbf");
copy ("/p0/geostats/GIS_data/husco/Merged_States/$shxname", "$path/histstate.shx");
copy ("/var/www/html/mapservr/HUSCO/counties.map", "$path/counties.map");
$stateid="00";
$query="Select fipscode, $phrase where st_cnty='C'";
}

if ($geolevel=="us_state") {
$stateid="00";
$shapename=$year."states.shp";
$dbfname=$year."states.dbf";
$shxname=$year."states.shx";
copy ("/p0/geostats/GIS_data/husco/Merged_States/$shapename", "$path/histcen.shp");
copy ("/p0/geostats/GIS_data/husco/Merged_States/$dbfname", "$path/orig$pid.dbf");
copy ("/p0/geostats/GIS_data/husco/Merged_States/$shxname", "$path/histcen.shx");
copy ("/var/www/html/mapservr/HUSCO/states.map", "$path/states.map");
$query="Select fipscode, $phrase where st_cnty='S'";
}

if ($geolevel=="state_by_county") {
$shapename=$abbrev[$stateid].$year.".shp";
$dbfname=$abbrev[$stateid].$year.".dbf";
$shxname=$abbrev[$stateid].$year.".shx";
copy ("/p0/geostats/GIS_data/husco/$year/$shapename", "$path/histcen.shp");
copy ("/p0/geostats/GIS_data/husco/$year/$dbfname", "$path/orig$pid.dbf");
copy ("/p0/geostats/GIS_data/husco/$year/$shxname", "$path/histcen.shx");
$shapename=$year."states.shp";
$dbfname=$year."states.dbf";
$shxname=$year."states.shx";
copy ("/p0/geostats/GIS_data/husco/Merged_States/$shapename", "$path/histstate.shp");
copy ("/p0/geostats/GIS_data/husco/Merged_States/$dbfname", "$path/histstate.dbf");
copy ("/p0/geostats/GIS_data/husco/Merged_States/$shxname", "$path/histstate.shx");
copy ("/var/www/html/mapservr/HUSCO/counties.map", "$path/counties.map");
$query="Select fipscode, $phrase where st_cnty='C' and stateid=$stateid";
}

$filename=fopen("$path/$pid.ph", w) or die ("can't open $pid.ph: $php_errormsg");
fwrite ($filename, "#! /usr/local/bin/perl\n");
fwrite ($filename, "sub values {\n");
fwrite ($filename, "%fipsvals=(\n");

$result=mysql_query($query);
$number=mysql_num_rows($result);
$splitvars=explode(' from ', $phrase);
$var=$splitvars[0];
$counter=0;
for ($i=0; $i<$number; $i++) {
        $fips=mysql_result($result, $i, "fipscode");
        $value=mysql_result($result, $i, "$var");
        if ($fips=="") { $fips=0; }
        if ($i+1==$number) {
                $line="'$fips'=>$value);\n}\n\$true=1;\n";
        }
        else {
                $line="'$fips'=>$value,\n";
        }
        fwrite ($filename, $line);
}
	break;

} ##end of switch
print "<html><head><meta http-equiv=\"Refresh\" CONTENT=\".01;
URL=/cgi-local/censusmapbin/dbfconvert.pl?pid=$pid&variable=$var&label=$label&stateid=$stateid&geolevel=$geolevel&year=$year&statename=$statename\">";
print "<title>Historical Census Browser</title></head>";
print "<body>Creating Map for $geolevel $statename $year $label<br />";
print "</body></html>";
fclose($filename);
mysql_close();
?>


