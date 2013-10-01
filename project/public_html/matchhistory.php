<?php
include_once "functions/html.php";
include_once "classes/DB.php";
include_once "classes/match.php";

printHeader("AAU Bordfodbold - Match History", "Match History");

$matchesPerPage = 20;

$result = $DB->query("SELECT COUNT(matchID) from matches");
$matchCount = mysql_fetch_row($result);

$tempCalc = $matchCount[0] / $matchesPerPage;
$pageCount = ceil($tempCalc);

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
$match->printRecentlyPlayedMatches($start,$matchesPerPage);

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