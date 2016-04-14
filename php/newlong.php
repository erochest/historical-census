<?
require ("common.php");

$subject = mysqli_real_escape_string($subject);
## prints out the web template
print_template($subject);

function print_template ($subject) {
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
        printbody($subject);
        print substr($contents,$pos+strlen("<!-- content here -->")+1);
}

## main body of the script--you must pass the same vars to both print_template and printbody
function printbody($subject){
if ($subject=="") { $subject=1; }

print "<h2>Census Data Over Time</h2>\n";
print "<form action=\"newlong2.php\" method=\"POST\">\n";

$query2="Select * from Groups where groupid=$subject";  
$result2=mysql_query($query2);
$number2=mysql_numrows($result2);
for ($i=0; $i<$number2; $i++) {
        $grpid=mysql_result($result2, $i, "groupid");
        $groupname=mysql_result($result2, $i, "groupname");
        $grp[$grpid]=$groupname;
}
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
<?

print "<p><strong>Select from the topics below in the category of $grp[$subject]:</strong></p>\n"; 
print "<p><select name=\"varname\" size=\"5\">\n";

##$query="Select varname, groupid, first, last from Vars order by groupid, varname";
$query="Select varname, first, last from Vars where groupid=$subject order by varname";
$result=mysql_query($query);   
$number=mysql_numrows($result);
for ($i=0; $i<$number; $i++) {
        $groupid=mysql_result($result, $i, "groupid");
        $varname=mysql_result($result, $i, "varname");
        $start=mysql_result($result, $i, "first");
        $finish=mysql_result($result, $i, "last");
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

$query4="SELECT DISTINCT Vars.groupid, Groups.groupname FROM Vars, Groups where Vars.groupid=Groups.groupid";
$result4=mysql_query($query4);
$number4=mysql_numrows($result4); 
for ($i=0; $i<$number4; $i++) {
        $id=mysql_result($result4, $i, "Vars.groupid");
        $name=mysql_result($result4, $i, "Groups.groupname");
	print "<option value=\"$id\">$name</option>\n";
}
print "</select></p>\n";
print "<p><input type=\"submit\"> &nbsp <input type=\"reset\"></form></p>\n";
}
mysql_close();

