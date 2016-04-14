<?php
require ("common.php");

//$dels = "";
$direction = 'ASC';
$sort = "";
## prints out the web template
print_template($vars, $table, $numerator, $denominator, $sort, $direction, $dels);

function print_template ($vars, $table, $numerator, $denominator, $sort, $direction = 'ASC', $dels) {
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
        printbody($vars, $table, $numerator, $denominator, $sort, $direction, $dels);
        print substr($contents,$pos+strlen("<!-- content here -->")+1);
}
## main body of the script--you must pass the same vars to both print_template and printbody
function printbody($vars, $table, $numerator, $denominator, $sort, $direction, $dels){
$width="height=650,width=1008,toolbar=no,nemubar=no,resizable=yes";
if (count($dels)==0) {}
else {
	$diff=array_diff($vars, $dels);
	$vars=$diff;
}
print "<h3>State Level Results for $table</h3>\n";
print "<form action=\"county.php\" method=\"POST\" name=\"mainform\">\n";
foreach ($vars as $varname) {
	print "<input type=\"hidden\" name=\"vars[]\" value=\"$varname\">\n";
}
print "<input type=\"hidden\" name=\"table\" value=\"$table\">\n";

if (empty($numerator)) { }
else {
        foreach ($numerator as $num) {
                print "<input type=\"hidden\" name=\"numerator[]\" value=\"$num\">\n";                  
        }
print "<input type=\"hidden\" name=\"denominator\" value=\"$denominator\">\n";
}
print "<ul><li>You may add more topics to the table by selecting them from the list below the table.</li>
<li>You may delete topics from the table by selecting them from the list below the table.</li>
<li>To display proportions of the selected topics, choose one or more numerators and a single denominator.</li>
<li>To sort the data, choose one of the options below for sorting and choose ascending or descending.</li>
<li>To create a national map by state of one category, click on the \"Map it!\" button at the top of the column.</li></ul>";

print "<p><a href=\"#addvars\">Add new topics, create proportions, change sort order</a></p>";
print "<p><strong>To select county-level data for all states, check the box for \"All States\" at the
top of the list.</strong> </p>";
print "<div class=\"indent5em\"><table border=\"1\">";
print "<tr><th>State<br /><font size=\"-1\">
<i>To retrieve county-level data, select a state and click the submit button below</i></font></th>";
$header="<tr><th>State</th>";

$countvars = 0;

$varlist="V".$table;
$window=rand();
foreach ($vars as $varname) {
	$countvars++;
	$query2="Select label from $varlist where varname='$varname'"; 
	$result=mysql_query($query2);
	$row=mysql_fetch_array($result);
	$label=$row["label"];
	$all_labels[$varname]=$label;
	print "<th><font size=\"-2\">$label</font><br />
	<a href=\"javascript:var newwin = window.open('/censusmap/map2.php?year=$table&var=$varname&label=$label&geolevel=us_state','$window', '$width');\">
	<img src=\"/gifs/mapbutton.gif\" border=\"0\"/></a></th>";
	$window++;
	$new="<th><font size=\"-2\">$label</font><br />
	<a href=\"javascript:var newwin = window.open('/censusmap/map2.php?year=$table&var=$varname&label=$label&geolevel=us_state','$window', '$width');\">
	<img src=\"/gifs/mapbutton.gif\" border=\"0\"/></a></th>"; 
/*
	print "<th><font size=\"-2\">$label</font></th>";
	$new="<th><font size=\"-2\">$label</font></th>";
*/
	$header .=$new;
}
if (empty($numerator)) { }   
else {
	$testno=rand();
        foreach ($numerator as $num) {
		$ratlabel="$all_labels[$num]/$all_labels[$denominator] (Percent)";
                print "<th><font size=\"-2\">$ratlabel</font><br />
		<a href=\"javascript:var newwin = window.open('/censusmap/map2.php?year=$table&numerator=$num&denominator=$denominator&label=$ratlabel&geolevel=us_state','$testno', '$width');\"> 
		<img src=\"/gifs/mapbutton.gif\" border=\"0\"/></a></th>";
		$testno++;
		$newrat="<th><font size=\"-2\">$ratlabel</font><br />
		<a href=\"javascript:var newwin = window.open('/censusmap/map2.php?year=$table&numerator=$num&denominator=$denominator&label=$ratlabel&geolevel=us_state','$testno','$width');\"> 
		<img src=\"/gifs/mapbutton.gif\" border=\"0\"/></a></th>";
		$header .=$newrat;
		$countvars++;
        }
}
print "</tr>\n";
$endline="</tr>\n";
$header .=$endline;
$line="Select ";
$count=sizeof($vars);
$test=0;
$span=$countvars+1;
print "<tr><td colspan=\"$span\"><input type=\"checkbox\" name=\"allstates\" value=\"00\" onclick=\"javascript:selectall(1);\">ALL STATES</td></tr>";

foreach ($vars as $varname) {
	$line.=$varname.", "; 
}

if (empty($sort)) { $sortby="name"; }
else { $sortby=$sort." ".$direction; }
$data="C".$table;
$line2="name, stateid from $data where st_cnty='S' order by $sortby";
$query=$line.$line2;
$result=mysql_query($query);
$number=mysql_numrows($result);
?>
<script language="JavaScript">
function selectall(x) {
	if (x) {
	for (i=1; i< document.mainform.elements.length; i++) {
		if (document.mainform.elements[i].name == "allstates") {
		}
		else {
		document.mainform.elements[i].checked = false;
		}		
	} 
	}
	else {
		document.mainform.allstates.checked=false;
	}

}
</script>
<?

$ncols=mysql_num_fields($result);
for ($i=0; $i<$ncols; $i++) {
$col_info=mysql_fetch_field($result, $i);
$dec[$col_info->name]=$col_info->type;
}
for ($i=0; $i<$number; $i++) {
	$stateid=mysql_result($result, $i, "stateid");
	$name=mysql_result($result, $i, "name");
	print "<tr><td><input type=\"checkbox\" name=\"stateid[]\" value=\"$stateid\" onclick=\"javascript:selectall();\">$name</td>";
	foreach ($vars as $varname) {
		$y[$varname]=mysql_result($result, $i, "$varname");
	}
	foreach ($y as $x => $value) {
                switch ($value){
			case "-8":
                                print "<td align=\"right\">N/A</td>";
                                break;
			case "-6":
                                print "<td align=\"right\">N/A</td>";
                                break;
                        case "-9":
                                print "<td align=\"right\">N/A</td>";   
                                break;
                        case "-1":
                                print "<td align=\"right\">N/A</td>";
                                break;
                        default:
				if ($dec[$x]=="real") {
					print "<td align=\"right\">".number_format($value,2)."</td>";
				}
				else {
				print "<td align=\"right\">".number_format($value)."</td>";
				} 
                                break;
                }
	}

if (empty($numerator)) { }
else {
	foreach ($numerator as $num) {
		$n=$y[$num];
		$d=$y[$denominator];
		if ($n<0 || $d<0) { 
			print "<td align=\"right\">N/A</td>";
		}
		else {
			$percent=(($n/$d)*100);
			print "<td align=\"right\">".number_format($percent, 2)."</td>";
		}
		$countvars++;
	}
}
print "</tr>\n";
if ($i==35) { print "$header"; }
}
print "</table></div>";
print "<p><input type=\"submit\" value=\"Retrieve County-Level Data\"> &nbsp <input type=\"reset\"></form></p>";

$query2="Select * from Groups";
$result2=mysql_query($query2);
$number2=mysql_numrows($result2);
for ($i=0; $i<$number2; $i++) {
        $grpid=mysql_result($result2, $i, "groupid");
        $groupname=mysql_result($result2, $i, "groupname");
        $grp[$grpid]=$groupname;
}
print "<a name=\"addvars\"></a>";
print "<form action=\"state.php\" method=\"POST\" name=\"subform\">\n";
foreach ($vars as $varname) {
	print "<input type=\"hidden\" name=\"vars[]\" value=\"$varname\">\n";
}
if (empty($numerator)) { }  
else {
        foreach ($numerator as $num) {
                print "<input type=\"hidden\" name=\"numerator[]\" value=\"$num\">\n";
        }
print "<input type=\"hidden\" name=\"denominator\" value=\"$denominator\">\n"; 
}
print "<input type=\"hidden\" name=\"sort\" value=\"$sort\">\n";  
print "<input type=\"hidden\" name=\"table\" value=\"$table\">\n";
print "<div class=\"indent5em\"><table border=\"1\" cellpadding=\"2\">\n";
print "<tr><th>Add or Remove Topics</th><th>Create Proportions:</th></tr>\n";
print "<tr><td rowspan=\"4\"><strong>Add New Topics:</strong><br /><select name=\"vars[]\" size=\"10\"
multiple>\n";

$query3="Select varname, label, groupid from $varlist where groupid!=32 and groupid!=0 order by groupid";
$result3=mysql_query($query3);
$number3=mysql_numrows($result3);
for ($i=0; $i<$number3; $i++) {
	$v=mysql_result($result3, $i, "varname");
	$l=mysql_result($result3, $i, "label");
	$groupid=mysql_result($result3, $i, "groupid");
	if (in_array($v, $vars)) { }
	else { 
	        if ($test==$groupid) { }
        	else {
			$groupname=strtoupper($grp[$groupid]);
                	print "<option value=\" \">==========$groupname==========</option>\n";
                	$test=$groupid;
        	}
	$lab=strtolower($l);
	print "<option value=\"$v\">$lab</option>\n"; 
	}

}
print "</select><br /><strong>Delete Topics from this list:</strong><br />\n";
print "<select name=\"dels[]\" size=\"5\" multiple>\n";
foreach ($vars as $v) {
	$query4="Select label from $varlist where varname='$v'";
        $result4=mysql_query($query4);
        $row4=mysql_fetch_array($result4);
        $l=$row4["label"];
        $lab=strtolower($l);
        print "<option value=\"$v\">$lab</option>\n";
}
print "</select></td>";
print "<td><strong>Select Numerator:</strong><br /><select name=\"numerator[]\" size=\"5\" multiple>\n";
foreach ($vars as $v) {
        $query4="Select label from $varlist where varname='$v'";
        $result4=mysql_query($query4);
        $row4=mysql_fetch_array($result4);
        $l=$row4["label"];
        $lab=strtolower($l);
        print "<option value=\"$v\">$lab</option>\n";
} 
print "</select></td></tr>";
print "<td><strong>Select Denominator:</strong><br /><select name=\"denominator\" size=\"5\">";
foreach ($vars as $v) {
        $query4="Select label from $varlist where varname='$v'";
        $result4=mysql_query($query4);
        $row4=mysql_fetch_array($result4);
        $l=$row4["label"];
        $lab=strtolower($l);
        print "<option value=\"$v\">$lab</option>\n";
} 
print "</select></td></tr>\n";
print "<th>Sort Data by:</th></tr><td><select name=\"sort\">";
print "<option value=\"name\">State Name (default)</option>";
foreach ($vars as $v) {
        $query4="Select label from $varlist where varname='$v'";
        $result4=mysql_query($query4);
        $row4=mysql_fetch_array($result4);
        $l=$row4["label"];
        $lab=strtolower($l);
        print "<option value=\"$v\">$lab</option>\n";
} 
if (empty($numerator)) { }
else {
        foreach ($numerator as $num) {
		$per=$all_labels[$num]."/".$all_labels[$denominator];
		$lowper=strtolower($per);
                print "<option value=\"$num/$denominator\">$lowper</option>\n";
        }
}

print "</select><br /><input type=\"radio\" name=\"direction\" value=\"ASC\" checked>Ascending &nbsp";
print "<input type=\"radio\" name=\"direction\" value=\"DESC\">Descending</td></tr>";
print "<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\"> &nbsp <input type=\"reset\"></td></tr>\n";
print "</table></div></form>";

}
mysql_close();
