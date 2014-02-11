<?php
include_once "functions/html.php";
include_once "classes/DB.php";
include_once "classes/match.php";

printHeader("AAU Bordfodbold - Match History", "Match History");
$matchesPerPage = 20;
/*
if(!isset($_GET['page'])){
    $_GET['page'] = 1;
}

//Filter form
$result = $DB->query("SELECT playerID, name FROM players ORDER BY name");   

$playerArray[0]['name'] = "Choose Player...";
$playerArray[0]['playerID'] = -1;

while ($row = mysql_fetch_array($result, MYSQL_BOTH)) {
    $playerArray[] = $row;
}

echo "<form action=\"{$_SERVER['PHP_SELF']}\" method=\"GET\">

<div align=\"center\">";

echo "<select name=\"id\">";
foreach($playerArray as $player){
    echo"<option value=\"{$player['playerID']}\">{$player['name']}</option>";
}
echo"</select>
    <br/><br/>
    <input type=\"submit\" value=\"Submit!\"/>

</div>
</form>";

echo "Filter: " . $_GET['id'];
*/
//Filter stuff ends here

//if(!isset($_SESSION['id'])){
    $result = $DB->query("SELECT COUNT(matchID) from matches");
//}
$matchCount = mysql_fetch_row($result);

$pageCount = ceil($matchCount[0] / $matchesPerPage);

$page = intval($_GET['page']);

echo "<center>";
for($i = 1; $i <= $pageCount; $i++){
    if($i == $page){
        echo $i;
        echo " ";
    }else{
        echo "<a href=\"matchhistory.php?page=$i\">$i</a> ";
    }
}
echo "</center>";


$start = ($page * $matchesPerPage) - $matchesPerPage;

//if(!isset($_SESSION['id'])){
    $match->printRecentlyPlayedMatches($start,$matchesPerPage);
//}

echo "<br/><center>";
for($i = 1; $i <= $pageCount; $i++){
    if($i == $page){
        echo $i;
        echo " ";
    }else{
        echo "<a href=\"matchhistory.php?page=$i\">$i</a> ";
    }
}
echo "</center>";

printFooter();

?>