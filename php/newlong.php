<?php
require ("common.php");

## prints out the web template
print_template($link, $subject);

function print_template ($link, $subject) {
    $handle   = fopen ("script_template.html","rb");
    $contents = "";
    do {
        $data = fread($handle, 1024);
        if (strlen($data) === 0) {
            break;
        }
        $contents .= $data;
    } while (true);
    fclose($handle);
    $contents = str_replace(
        "<!-- title here -->", "Historical Census Browser", $contents
    );
    $pos      = strpos($contents, "<!--  content here -->");
    //We don't need any stinking regular expressions!!!!!
    print substr($contents,0,$pos);
    printbody($link, $subject);
    print substr($contents,$pos+strlen("<!-- content here -->")+1);
}

## main body of the script--you must pass the same vars to both print_template and printbody
function printbody($link, $subject) {
    if (empty($subject)) {
        $subject = 1;
    }

    print "<h2>Census Data Over Time</h2>\n";
    print "<form action=\"newlong2.php\" method=\"POST\">\n";

    $grp = get_groups($link, $subject);
?>
<p><strong>Display data from: </strong><br /><select name="begin">
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
</select> &nbsp
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
<?php

    print "<p><strong>Select from the topics below in the category "
        . "of ${grp[$subject]}:</strong></p>\n"; 
    print "<p><select name=\"varname\" size=\"5\">\n";

    ##$query="Select varname, groupid, first, last from Vars order by groupid, varname";
    $query  = "Select varname, first, last from Vars "
        . "where groupid="
        . mysqli_real_escape_string($link, $subject)
        . " order by varname";
    $rows = query_fetch_all($link, $query);
    foreach ($rows as $row) {
        $groupid = $row["groupid"];
        $varname = $row["varname"];
        $start   = $row["first"];
        $finish  = $row["last"];
    /*
        if ($test==$groupid) { }
        else {
                if ($first==0) { $first++; }
                else {                }
                print "<option value=\"\">============================$grp[$groupid]============================</option>";    
                $test=$groupid;
        }
     */
        print "<option value=\"$varname:$start:$finish\">$varname ($start--$finish)</option>\n";
    }
    print "</select></p>\n";

    print "<p><input type=\"submit\"> &nbsp <input type=\"reset\"></form></p>\n";

    print "<form action=\"newlong.php\" method=\"POST\">\n";
    print "<p><strong>Change category listing</strong>:<br /><select name=\"subject\">\n";

    $query4  = "SELECT DISTINCT Vars.groupid, Groups.groupname "
        . "FROM Vars, Groups "
        . "where Vars.groupid = Groups.groupid";
    $rows = query_fetch_all($link, $query4);
    foreach ($rows as $row) {
        $id   = $row["groupid"];
        $name = $row["groupname"];
        if ($id == $subject) {
            $selected = " selected";
        } else {
            $selected = "";
        }
        print "<option value=\"$id\" $selected>$name</option>\n";
    }
    print "</select></p>\n";
    print "<p><input type=\"submit\"> &nbsp <input type=\"reset\"></form></p>\n";
}
mysqli_close($link);

