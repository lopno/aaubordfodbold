<?php
/*  
include_once "functions/html.php";
include_once "classes/DB.php";

printHeader("AAU Bordfodbold - Team Leaderboard", "Team Leaderboard");

$DB = new DB();
$query = "SELECT teams.teamID, teams.ranking, teams.wins, teams.losses, players.name 
          FROM teams 
          JOIN memberof ON teams.teamID = memberof.teamID 
          JOIN players ON memberof.playerID = players.playerID 
          ORDER BY ranking DESC";

$result = $DB->query($query);

echo"<div align=\"center\">
        <table border=\"1\">
        <tr>
        <th>Rank</th>
        <th>Names</th>
        <th>Wins</th>
        <th>Losses</th>
        <th>Rating</th>
        </tr>";
        
$rank = 0;
$second = 0;
while ($row = mysql_fetch_assoc($result)) {
    $second++;
    if(($second % 2) == 1){
        $rank++;
        echo"<tr><td>$rank</td>";
        echo"<td>{$row['name']}<br/>";
    }else{
        echo"{$row['name']}</td>
            <td>{$row['wins']}</td>
            <td>{$row['losses']}</td>
            <td>{$row["ranking"]}</td>
            </tr>";
    }
}
echo"</table></div>";

printFooter();
 */
?>