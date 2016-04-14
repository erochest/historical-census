<?php
require ("common.php");

## prints out the web template
print_template($link, $year);

function print_template($link, $year) {
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
        $contents = str_replace(
            "<!-- title here -->", "Historical Census Browser", $contents
        );
        $pos = strpos($contents, "<!--  content here -->");
        //We don't need any stinking regular expressions!!!!!
        print substr($contents,0,$pos);
        printbody($link, $year);
        print substr($contents,$pos+strlen("<!-- content here -->")+1);
}
## main body of the script--you must pass the same vars to both print_template and printbody
function printbody($link, $year){
    $yr=explode('V', $year);
    print "<h2>Census Data for Year $yr[1]</h2>\n";
    print "<p>The selection lists below represent all of the questions from the $yr[1] census recorded by ";
    print "<a href=\"http://www.icpsr.umich.edu/\">ICPSR</a>.  They have been grouped into categories for
        convenience. The categories are not part of the original data.</p>\n";
    print "<h3>Variables for $yr[1] Census</h3>\n";
    $grp = get_groups($link);
    print "<p>";
    $query2 = "Select Distinct groupid from $year "
        . "where groupid!=0 and groupid!=32 and groupid!=33 "
        . "order by groupid";
    $data2  = query_fetch_all($link, $query2);
    for ($i=0; $i<count($data2); $i++) {
        $groupid = $data2[$i]["groupid"];
        print "<a href=\"#$groupid\">${grp[$groupid]}</a> &nbsp &nbsp";
    }
    print "</p>\n";
    print "<form action=\"state.php\" method=\"POST\">\n";
    print "<input type=\"hidden\" name=\"table\" value=\"$yr[1]\">\n";
    $first = 0;
    $query = "Select * from $year where groupid!=0 and groupid!=32 "
        . "order by groupid";
    $data  = query_fetch_all($link, $query);
    $test  = -1;
    for ($i=0; $i<count($data); $i++) {
        $varname = $data[$i]["varname"];
        $label   = $data[$i]["label"  ];
        $groupid = $data[$i]["groupid"];
        if ($test !== $groupid) {
            if ($first !== 0) {
                $first++;
            } else {
                print "</select></p>\n";
            }
            if ($groupid !== 33) {
                print "<a name=\"$groupid\"><h4>${grp[$groupid]}</h4></a>\n";
            }
            print "<p><select name=\"vars[]\" size=\"7\" multiple>\n";
            $test = $groupid;
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

    mysqli_close($link);
}

