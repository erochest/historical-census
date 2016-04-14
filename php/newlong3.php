<?
require ("common.php");

## prints out the web template
print_template($var, $begin, $end, $stateids, $allstates, $valid);

function print_template ($var, $begin, $end, $stateids, $allstates, $valid) {
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
  printbody($var, $begin, $end, $stateids, $allstates, $valid);
  print substr($contents,$pos+strlen("<!-- content here -->")+1);
}
## main body of the script--you must pass the same vars to both print_template and printbody
function printbody($var, $begin, $end, $stateids, $allstates, $valid){
  $width="height=650,width=785,toolbar=no,nemubar=no,resizable=no";

  $abbrev=array(
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


  $query="Select * from Vars where varname='$var'";
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
/*
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
 */
  $fields=array("1790"=>$V1790, "1800"=>$V1800, "1810"=>$V1810, "1820"=>$V1820, "1830"=>$V1830, "1840"=>$V1840,
    "1850"=>$V1850, "1860"=>$V1860, "1870"=>$V1870, "1880"=>$V1880, "1890"=>$V1890, "1900"=>$V1900,
    "1910"=>$V1910, "1920"=>$V1920, "1930"=>$V1930, "1940"=>$V1940, "1950"=>$V1950, "1960"=>$V1960);

/*
$formulas=array("1790"=>$F1790, "1800"=>$F1800, "1810"=>$F1810, "1820"=>$F1820, "1830"=>$F1830, "1840"=>$F1840,
        "1850"=>$F1850, "1860"=>$F1860, "1870"=>$F1870, "1880"=>$F1880, "1890"=>$F1890, "1900"=>$F1900,
        "1910"=>$F1910, "1920"=>$F1920, "1930"=>$F1930, "1940"=>$F1940, "1950"=>$F1950, "1960"=>$F1960);
 */
  switch($allstates){
  case 'ALL':
    $savebegin=$begin;
    print "<div class=\"indent5em\"><table border=\"1\"><tr><th colspan=\"20\">$var</th></tr><tr><th>County</th><th>State</th>";
    $window=rand();
    while ($begin<=$end) {
      $window++;
      $header="<th>$begin<br /><a href=\"javascript:var newwin = window.open('/censusmap/map2.php?year=$begin&long=$var&label=$var&geolevel=us_county','$window','$width');\">
        <img src=\"/gifs/mapbutton.gif\" border=\"0\"/></a></th>";
      print "$header"; 
      $tinyheader="<th>$begin</th>";
      $tinylabel.=$tinyheader;
      $label.=$header; 
      $begin=$begin+10;
    }
    $x=1;
    $column="Y".$end;
    $query3="Select name, stateid, statecode from Geography where st_cnty='C' and $column=1 order by statecode, name";
    $result3 = mysql_query($query3, $link);
    
    $number3 = mysql_num_rows($result3);

    for ($i=0; $i<$number3; $i++){
      $area=mysql_result($result3, $i, "name");
      $stateid=mysql_result($result3, $i, "stateid");
      $statecode=mysql_result($result3, $i, "statecode");
      if ($i==0) { $test=$statecode; }
      if ($statecode>$test) {
        print "<th>County</th><th>State</th>$label";
        $test=$statecode;
        $x=1;
      }
      if ($x%30==0) {
        print "<th>County</th><th>State</th>$tinylabel";
      }
      $x++;
      $begin=$savebegin;
      print "<tr><td>$area</td><td>$abbrev[$stateid]</td>";
      while ($begin<=$end) {
        $query2="Select $fields[$begin] where stateid=$stateid and st_cnty='C' and name='$area'";
        $result2=mysql_query($query2);
        $row2=mysql_fetch_array($result2);
/*
                $numvars=explode(':', $formulas[$begin]);
                if ($numvars[0]>1) { 
                        $splitvars=explode (' from ', $fields[$begin]);
                        $listvars=explode (',', $splitvars[0]);
                        $v=array();
                        foreach ($listvars as $key=>$VAR) {
                                $v[$key]=$row2[$VAR];
                        }
                        eval("\$final=$numvars[1];");
                }
                else { 
 */
        $splitvars=explode (' from ', $fields[$begin]);
        $VAR=$splitvars[0];
        $final=$row2["$VAR"];
        ##              }
        if ($final<0) { 
          print "<td align=\"center\">N/A</td>";
        }
        else{
          print "<td align=\"center\">".number_format($final)."</td>";
        }
        $begin=$begin+10;
      }
      print "</tr>";
    } ##end of for loop
    print "</table></div><br />";
    break;

  case '':
    foreach ($stateids as $stateid) {
      $savebegin=$begin;
      print "<div class=\"indent5em\"><table border=\"1\"><tr><th colspan=\"19\">$abbrev[$stateid]: $var</th></tr><tr><th>County</th>";
      $window=rand();
      while ($begin<=$end) {
        $window++;
        $header="<th>$begin<br /><a href=\"javascript:var newwin = window.open('/censusmap/map2.php?statename=$abbrev[$stateid]&year=$begin&long=$var&label=$var&stateid=$stateid&geolevel=state_by_county','$window','$width');\">
          <img src=\"/gifs/mapbutton.gif\" border=\"0\"/></a></th>";
        print "$header";
        $label.=$header;
        $begin=$begin+10;
      }
      $x=1;
      $query3="Select name from Geography where stateid=$stateid and st_cnty='C' order by name";
      $result3=mysql_query($query3);
      $number3=mysql_num_rows($result3);
      for ($i=0; $i<$number3; $i++){
        $area=mysql_result($result3, $i, "name");
        $x++;
        if ($x%30==0) {
          print "<th>County</th>$label";
        }
        $begin=$savebegin;
        print "<tr><td>$area</td>";
        while ($begin<=$end) {
          $query2="Select $fields[$begin] where stateid=$stateid and st_cnty='C' and name='$area'";
          $result2=mysql_query($query2);
          $row2=mysql_fetch_array($result2);
/*
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
 */
          $splitvars=explode (' from ', $fields[$begin]);
          $VAR=$splitvars[0];
          $final=$row2["$VAR"];
          ##        	}
          if ($final<0) { 
            print "<td align=\"center\">N/A</td>";
          }
          else{
            print "<td align=\"center\">".number_format($final)."</td>";
          }
          $begin=$begin+10;
        }
        print "</tr>";
      }
      print "</table></div><br />";
      $begin=$savebegin;
      $header=$label="";
      $x=0;
    } ## end of foreach
    break;
  } ## end of switch
}
mysql_close();

