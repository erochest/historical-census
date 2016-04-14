<?php
require ("common.php");

$allstates = "";
$dels      = array();

## prints out the web template
print_template(
    $link, $vars, $stateid, $table, $numerator, $denominator, $sort, $direction,
    $allstates, $dels
);

function print_template(
    $link, $vars, $stateid, $table, $numerator, $denominator, $sort, $direction,
    $allstates, $dels
) {
    $handle   = fopen ("script_template.html","rb");
    $contents = "";
    do {
        $data = fread($handle, 1024);
        if (strlen($data) == 0) {
            break;
        }
        $contents .= $data;
    } while (true);

    fclose($handle);

    $contents=str_replace(
        "<!-- title here -->", "Historical Census Browser", $contents
    );
    $pos = strpos($contents, "<!--  content here -->");
    //We don't need any stinking regular expressions!!!!!
    print substr($contents,0,$pos);
    printbody(
        $link, $vars, $stateid, $table, $numerator, $denominator, $sort,
        $direction, $allstates, $dels
    );
    print substr($contents,$pos+strlen("<!-- content here -->")+1);
}
## main body of the script--you must pass the same vars to both print_template and printbody
function printbody(
    $link, $vars, $stateid, $table, $numerator, $denominator, $sort, $direction,
    $allstates, $dels
) {
    $width  = "height=650,width=1008,toolbar=no,nemubar=no,resizable=yes";
    $abbrev = array(
        "00" => "All States",
        "01" => "Connecticut",
        "02" => "Maine",
        "03" => "Massachusetts",
        "04" => "New Hampshire",
        "05" => "Rhode Island",
        "06" => "Vermont",
        "11" => "Delaware",
        "12" => "New Jersey",
        "13" => "New York",
        "14" => "Pennsylvania",
        "21" => "Illinois",
        "22" => "Indiana",
        "23" => "Michigan",
        "24" => "Ohio",
        "25" => "Wisconsin",
        "31" => "Iowa",
        "32" => "Kansas",
        "33" => "Minnesota",
        "34" => "Missouri",
        "35" => "Nebraska",
        "36" => "North Dakota",
        "37" => "South Dakota",
        "40" => "Virginia",
        "41" => "Alabama",
        "42" => "Arkansas",
        "43" => "Florida",
        "44" => "Georgia",
        "45" => "Louisiana",
        "46" => "Mississippi",
        "47" => "North Carolina",
        "48" => "South Carolina",
        "49" => "Texas",
        "51" => "Kentucky",
        "52" => "Maryland",
        "53" => "Oklahoma",
        "54" => "Tennessee",
        "56" => "West Virginia",
        "61" => "Arizona",
        "62" => "Colorado",
        "63" => "Idaho",
        "64" => "Montana",
        "65" => "Nevada",
        "66" => "New Mexico",
        "67" => "Utah",
        "68" => "Wyoming",
        "71" => "California",
        "72" => "Oregon",
        "73" => "Washington",
        "81" => "Alaska",
        "82" => "Hawaii"
    );

    if (count($dels) !== 0) {
        $diff = array_diff($vars, $dels);
        $vars = $diff;
    }

    print "<h3>County-Level Results for $table</h3>\n";
    print "<p><a href=\"javascript:self.history.back();\">Back</a></p>";

    print "<ul><li>You may add more topics to the table by selecting them from the list below the table.</li>
        <li>You may delete topics from the table by selecting them from the list below the table.</li>
        <li>To display proportions of the selected topics, choose one or more numerators and a single denominator.</li>
        <li>To sort the data, choose one of the options below for sorting and choose ascending or descending.</li>
        <li>To create a map by county of one category, click on the \"Map it!\" button at the top of the column.</li></ul>";
    print "<p><a href=\"#addvars\">Add new topics, create proportions, change sort order</a></p>";

    print "<form action=\"county.php\" method=\"POST\">\n";

    $countvars = 0;
    foreach ($vars as $varname) {
        print "<input type=\"hidden\" name=\"vars[]\" value=\"$varname\">\n";
        $countvars++;
    }

    if (!empty($numerator)) {
        foreach ($numerator as $num) {
            print "<input type=\"hidden\" name=\"numerator[]\" value=\"$num\">\n";
            $countvars++;
        }
        print "<input type=\"hidden\" name=\"denominator\" value=\"$denominator\">\n";
        $countvars++;
    }
    print "<input type=\"hidden\" name=\"sort\" value=\"$sort\">\n";
    print "<input type=\"hidden\" name=\"table\" value=\"$table\">\n";
    foreach ($stateid as $state) {
        print "<input type=\"hidden\" name=\"stateid[]\" value=\"$state\">\n";
    }
    if ($allstates === "00") {
        print "<input type=\"hidden\" name=\"allstates\" value=\"$allstates\">\n";


        print "<div class=\"indent5em\"><table border=\"1\">\n";
        $span = $countvars + 2;
        print "<tr><th colspan=\"$span\">${abbrev[$state]}</th></tr>";
        print "<tr><th>County</th><th>State</th>";
        $header     = "<tr><th>County</th><th>State</th>";
        $varlist    = "V$table";
        $window     = rand();
        $all_labels = array();
        foreach ($vars as $varname) {
            $query = "Select label from $varlist where varname='"
                . mysqli_real_escape_string($link, $varname)
                . "'";
            $row   = query_fetch_one($link, $query);
            $label = $row["label"];
            $all_labels[$varname] = $label;
            print "<th><font size=\"-2\">$label</font><br /><a href=\"javascript:var newwin = window.open('/censusmap/map2.php?year=$table&var=$varname&label=$label&geolevel=us_county','$window', '$width');\">
                <img src=\"/gifs/mapbutton.gif\" border=\"0\"/></a></th>";
            $window++;
            $new="<th><font size=\"-2\">$label</font><br /><a href=\"javascript:var newwin = window.open('/censusmap/map2.php?year=$table&var=$varname&label=$label&geolevel=us_county','$window', '$width');\">
                <img src=\"/gifs/mapbutton.gif\" border=\"0\"/></a></th>";
/*
        print "<th><font size=\"-2\">$label</font></th>";
    $new="<th><font size=\"-2\">$label</font></th>";
 */
            $header .= $new;
        }
        if (!empty($numerator)) {
            $testno = rand();
            foreach ($numerator as $num) {
                $ratlabel="${all_labels[$num]}/${all_labels[$denominator]} (Percent)";
                print "<th><font size=\"-2\">$ratlabel</font><br /><a href=\"javascript:var newwin = window.open('/censusmap/map2.php?year=$table&numerator=$num&denominator=$denominator&label=$ratlabel&geolevel=us_county', '$testno', '$width');\">
                    <img src=\"/gifs/mapbutton.gif\" border=\"0\"/></a></th>\n";
                $testno++;
                $newrat="<th><font size=\"-2\">$ratlabel</font><br /><a href=\"javascript:var newwin = window.open('/censusmap/map2.php?year=$table&numerator=$num&denominator=$denominator&label=$ratlabel&geolevel=us_county', '$testno','$width');\">
                    <img src=\"/gifs/mapbutton.gif\" border=\"0\"/></a></th>\n";
                $header .=$newrat;
            }
        }
        print "</tr>\n";
        $endline  = "</tr>\n";
        $header  .= $endline;
        $line     = "Select st_cnty, ";
        foreach ($vars as $varname) {
            $line .= $varname . ", ";
        }
        if (empty($sort)) {
            $sortby="name";
        } else {
            $sortby = $sort . " " . $direction;
        }
        $data   = "C$table";
        $test   = 1;
        $line2  = "stateid, name from $data ";
        $line3  = "order by $sortby";
        $query  = $line . $line2 . $line3;
        $result = mysqli_query($link, $query);
        $number = $result->num_rows;
        $fields = $result->fetch_fields();
        $rows   = $result->fetch_all(MYSQLI_ASSOC);

        $dec    = array();
        foreach ($fields as $f) {
            $dec[$f->name] = $f->type;
        }

        $i = 0;
        foreach ($rows as $row) {
            $i ++;
            $check = $i + 1;
            if (($check % 32) === 0) {
                print "$header";
            }
            $name    = $row["name"];
            $stateno = $row["stateid"];
            $st_cnty = $row["st_cnty"];
            if ($st_cnty === "S") {
                print "<tr><td colspan=\"2\">${abbrev[$stateno]} "
                    . "STATE TOTALS</td>";
            } else {
                print "<tr><td>$name</td><td>${abbrev[$stateno]}</td>";
            }
            foreach ($vars as $varname) {
                $y[$varname] = $row[$varname];
            }
            foreach ($y as $x => $value) {
                switch ($value){
                case "-8":
                    print "<td align=\"right\">N/A</td>";
                    break;
                case "-9":
                    print "<td align=\"right\">N/A</td>";
                    break;
                case "-6":
                    print "<td align=\"right\">N/A</td>";
                    break;
                case "-1":
                    print "<td align=\"right\">N/A</td>";
                    break;
                default:
                    if ($dec[$x] === "real") {
                        print "<td align=\"right\">"
                            . number_format($value, 2)
                            . "</td>";
                    }
                    else {
                        print "<td align=\"right\">"
                            . number_format($value)
                            . "</td>";
                    }
                    break;
                }
            }
            if (!empty($numerator)) {
                foreach ($numerator as $num) {
                    $n = $y[$num];
                    $d = $y[$denominator];
                    if ($n<0 || $d <0) {
                        $percent = "N/A";
                        print "<td align=\"right\">N/A</td>";
                    }
                    else {
                        $percent = ($n / $d) * 100;
                        print "<td align=\"right\">"
                            . number_format($percent, 2)
                            . "</td>";
                    }
                }
            }
            print "</tr>\n";
        }

        print "</table></div><br />";

    } else {
        foreach ($stateid as $state) {
            $statename = $abbrev[$state];
            $span      = $countvars + 1;
            print "<div class=\"indent5em\"><table border=\"1\">\n";
            print "<tr><th colspan=\"$span\">${abbrev[$state]}</th></tr>";
            print "<tr><th>County</th>";
            $header  = "<tr><th>County</th>";
            $varlist = "V$table";
            $window  = rand();
            foreach ($vars as $varname) {
                $query = "Select label from $varlist where varname='"
                    . mysqli_real_escape_string($link, $varname)
                    . "'";
                $row   = query_fetch_one($link, $query);
                $label = $row["label"];
                $all_labels[$varname] = $label;
                print "<th><font size = \"-2\">$label</font><br />
                    <a href           = \"javascript:var newwin                   = window.open('/censusmap/map2.php?statename = $statename&year = $table&var = $varname&label = $label&stateid = $state&geolevel = state_by_county', '$window', '$width');\">
                    <img src          = \"/gifs/mapbutton.gif\" border            = \"0\"/></a></th>";
                $window++;
                $new = "<th><font size=\"-2\">$label</font><br /><a href=\"javascript:var newwin = window.open('/censusmap/map2.php?statename=$statename&year=$table&var=$varname&label=$label&stateid=$state&geolevel=state_by_county', '$window', '$width');\">
                    <img src=\"/gifs/mapbutton.gif\" border=\"0\" /></a></th>";
                $header .= $new;
            }
            if (!empty($numerator)) {
                $testno = rand();
                foreach ($numerator as $num) {
                    $ratlabel = "${all_labels[$num]}/${all_labels[$denominator]} (Percent)";
                    print "<th><font size=\"-2\">$ratlabel</font><br />
                        <a href=\"javascript:var newwin = window.open('/censusmap/map2.php?statename=$statename&year=$table&numerator=$num&denominator=$denominator&label=$ratlabel&stateid=$state&geolevel=state_by_county', '$testno', '$width');\">
                        <img src=\"/gifs/mapbutton.gif\" border=\"0\"/></a></th>";
                    $testno++;
                    $newrat="<th><font size=\"-2\">$ratlabel</font><br />
                        <a href=\"javascript:var newwin = window.open('/censusmap/map2.php?statename=$statename&year=$table&numerator=$num&denominator=$denominator&label=$ratlabel&stateid=$state&geolevel=state_by_county', '$testno', '$width');\">
                        <img src=\"/gifs/mapbutton.gif\" border=\"0\"/></a></th>";
                    $header .=$newrat;
                }
            }
            print "</tr>\n";
            $endline  = "</tr>\n";
            $header  .= $endline;
            $line     = "Select st_cnty, ";
            foreach ($vars as $varname) {
                $line .= $varname . ", ";
            }
            if (empty($sort)) {
                $sortby="name";
            } else {
                $sortby = $sort . " " . $direction;
            }
            $data      = "C$table";
            $test      = 1;
            $line2     = "stateid, name from $data where ";
            $stateline = "stateid=" . mysqli_real_escape_string($link, $state)
                . " ";
            $line3     = "order by $sortby";
            $query     = $line . $line2 . $stateline . $line3;
            $result    = mysqli_query($link, $query);
            $number    = $result->num_rows;
            $fields    = $result->fetch_fields();
            $rows      = $result->fetch_all(MYSQLI_ASSOC);

            $dec       = array();
            foreach ($fields as $f) {
                $dec[$f->name] = $f->type;
            }

            $i = 0;
            foreach ($rows as $row) {
                $i ++;
                $check = $i + 1;
                if (($check % 32) === 0) {
                    print "$header";
                }
                $name    = $row["name"];
                $stateno = $row["stateid"];
                $st_cnty = $row["st_cnty"];
                if ($st_cnty === "S") {
                    print "<tr><td>${abbrev[$stateno]} STATE TOTALS</td>";
                } else {
                    print "<tr><td>$name</td>";
                }
                foreach ($vars as $varname) {
                    $y[$varname] = $row[$varname];
                }
                foreach ($y as $x => $value) {
                    switch ($value){
                    case "-8":
                        print "<td align=\"right\">N/A</td>";
                        break;
                    case "-9":
                        print "<td align=\"right\">N/A</td>";
                        break;
                    case "-6":
                        print "<td align=\"right\">N/A</td>";
                        break;
                    case "-1":
                        print "<td align=\"right\">N/A</td>";
                        break;
                    default:
                        if ($dec[$x] === "real") {
                            print "<td align=\"right\">"
                                . number_format($value, 2)
                                . "</td>";
                        }
                        else {
                            print "<td align=\"right\">"
                                . number_format($value)
                                . "</td>";
                        }
                        break;
                    }
                }
                if (!empty($numerator)) {
                    foreach ($numerator as $num) {
                        $n = $y[$num];
                        $d = $y[$denominator];
                        if ($n < 0 || $d == 0 ) {
                            print "<td align=\"right\">N/A</td>";
                        } else {
                            $percent = ($n / $d) * 100;
                            print "<td align=\"right\">"
                                . number_format($percent, 2)
                                . "</td>";
                        }
                    }
                }
                print "</tr>";
            }

            print "</table></div><br />";
        }
    }
    $grp = get_groups($link);
    print "<a name=\"addvars\"></a>";
    print "<p></p><div class=\"indent5em\"><table border=\"1\" cellpadding=\"2\">\n";
    print "<tr><th>Add or Delete Topics</th><th>Create Proportions:</th></tr>\n";
    print "<tr><td rowspan=\"4\"><strong>Add New Topics:</strong><br /><select name=\"vars[]\" size=\"10\" multiple>\n";

    $varlist = "V$table";

    $query3  = "Select varname, label, groupid from $varlist "
        . "where groupid!=32 and groupid!=0 order by groupid";
    $rows = query_fetch_all($link, $query3);
    foreach ($rows as $row) {
        $v       = $row["varname"];
        $l       = $row["label"];
        $groupid = $row["groupid"];
        if (!in_array($v, $vars)) {
            if ($test !== $groupid) {
                $groupname = strtoupper($grp[$groupid]);
                $test      = $groupid;
                print "<option value=\" \">==========$groupname==========</option>\n";
            }
            $lab=strtolower($l);
            print "<option value=\"$v\">$lab</option>\n";
        }
    }

    print "</select><br /><strong>Delete Topics from this list:</strong><br />\n";
    print "<select name=\"dels[]\" size=\"5\" multiple>\n";
    foreach ($vars as $v) {
        $query = "Select label from $varlist where varname = '"
            . mysqli_real_escape_string($link, $v)
            . "'";
        $row   = query_fetch_one($link, $query);
        $l     = $row["label"];
        $lab   = strtolower($l);
        print "<option value=\"$v\">$lab</option>\n";
    }
    print "</select></td>";
    print "<td>Select Numerator:<br /><select name=\"numerator[]\" size=\"5\" multiple>\n";
    foreach ($vars as $v) {
        $query = "Select label from $varlist where varname = '"
            . mysqli_real_escape_string($link, $v)
            . "'";
        $row   = query_fetch_one($link, $query);
        $l     = $row["label"];
        $lab   = strtolower($l);
        print "<option value=\"$v\">$lab</option>\n";
    }
    print "</select></td></tr>";
    print "<td>Select Denominator:<br /><select name=\"denominator\" size=\"5\">";
    foreach ($vars as $v) {
        $query = "Select label from $varlist where varname = '"
            . mysqli_real_escape_string($link, $v)
            . "'";
        $row   = query_fetch_one($link, $query);
        $l     = $row["label"];
        $lab   = strtolower($l);
        print "<option value=\"$v\">$lab</option>\n";
    }
    print "</select></td></tr>\n";
    print "<th>Sort Data by:</th></tr><td><select name=\"sort\">";
    print "<option value=\"name\">State or County Name (default)</option>";
    if ($allstates === "00") {
        print "<option value=\"fipscode\">State</option>";
    }
    foreach ($vars as $v) {
        $query = "Select label from $varlist where varname = '"
            . mysqli_real_escape_string($link, $v)
            . "'";
        $row   = query_fetch_one($link, $query);
        $l     = $row["label"];
        $lab   = strtolower($l);
        print "<option value=\"$v\">$lab</option>\n";
    }
    if (!empty($numerator)) {
        foreach ($numerator as $num) {
            $per    = $all_labels[$num] . "/" . $all_labels[$denominator];
            $lowper = strtolower($per);
            print "<option value=\"$num/$denominator\">$lowper</option>\n";
        }
    }

    print "</select><br /><input type=\"radio\" name=\"direction\" value=\"ASC\" checked>Ascending &nbsp";
    print "<input type=\"radio\" name=\"direction\" value=\"DESC\">Descending</td></tr>";
    print "<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\"> &nbsp <input type=\"reset\"></td></tr>\n";
    print "</table></div></form>";

}

mysqli_close($link);
