<?php
include_once "functions/html.php";
include_once "classes/DB.php";
include_once "models/trophies.php";


if(!isset($_GET['type']) || $_GET['type'] == 'solo'){
    
    printHeader("AAU Bordfodbold - Solo Leaderboard", "Solo Leaderboard");
    printLeaderboardLinks();
    
    $result = $DB->query("SELECT playerID, name, wins, losses, ranking FROM players ORDER BY ranking DESC");
    $odd = FALSE;
    
    $soloTrophies = new Trophies;

  
    echo"<div align=\"center\">
        <table>
        <tr>
        <th>Rank</th>
        <th>Name</th>
        <th>Wins</th>
        <th>Losses</th>
        <th>Rating</th>
        </tr>";
    
    $rank = 0;
    while ($row = mysql_fetch_assoc($result))
    {    
        if(($row['wins']+$row['losses']) > 0)
        {    
            if($odd)
            {
                $odd = FALSE;
            }
            else
            {
                $odd = TRUE;
            }
            
            $rank++;
            echo"<tr>";
                if($odd)
                {
                    echo "<td id=\"odd\">";
                }
                else
                {
                    echo "<td>";
                }
            echo"$rank</td>";
                if($odd){
                    echo "<td id=\"odd\">";
                } else{
                    echo "<td>";
                }
            echo"<a href=\"profile.php?id={$row['playerID']}\">{$row['name']}</a></td>";
                if($odd){
                    echo "<td id=\"odd\">";
                } else{
                    echo "<td>";
                }
            echo"{$row['wins']}</td>";
                if($odd){
                    echo "<td id=\"odd\">";
                } else{
                    echo "<td>";
                }
            echo"{$row['losses']}</td>";
                if($odd){
                    echo "<td id=\"odd\">";
                } else{
                    echo "<td>";
                }
            echo"{$row['ranking']}</td>";
            if(isset($soloTrophies->soloTrophies[$row['playerID']])){
                foreach ($soloTrophies->soloTrophies[$row['playerID']] as $key => $trophy) {
                    echo "<td>".$trophy->getTrophy()."</td>";
                }
                
            }
            echo"</tr>";
        }
    }
    echo"</table>
    </div>";
}
elseif($_GET['type'] == 'team'){
    printHeader("AAU Bordfodbold - Team Leaderboard", "Team Leaderboard");
    
    printLeaderboardLinks();
    
    $query = "SELECT teams.teamID, teams.ranking, teams.wins, teams.losses, players.playerID , players.name 
              FROM teams 
              JOIN memberof ON teams.teamID = memberof.teamID 
              JOIN players ON memberof.playerID = players.playerID 
              ORDER BY teams.ranking DESC, teams.teamID DESC";
    
    $result = $DB->query($query);
    
    echo"<div align=\"center\">
            <table>
            <tr>
            <th>Rank</th>
            <th>Names</th>
            <th>Wins</th>
            <th>Losses</th>
            <th>Rating</th>
            </tr>";
            
    $rank = 0;
    $second = 0;
    $odd = FALSE;
    while ($row = mysql_fetch_assoc($result)) {
        $second++;
        
        if(($second % 2) == 1){
            if($odd){
                $odd = FALSE;
            }else{
                $odd = TRUE;
            }  
            $rank++;
            echo"<tr>";
            if($odd){
                echo "<td id=\"odd\">";
            } else{
                echo "<td>";
            }
            echo"$rank</td>";
            if($odd){
                echo "<td id=\"odd\">";
            } else{
                echo "<td>";
            }
            echo"<a href=\"profile.php?id={$row['playerID']}\">{$row['name']}</a><br/>";
        }else{
            echo"<a href=\"profile.php?id={$row['playerID']}\">{$row['name']}</a></td>";
            if($odd){
                echo "<td id=\"odd\">";
            } else{
                echo "<td>";
            }
            echo"{$row['wins']}</td>";
            if($odd){
                echo "<td id=\"odd\">";
            } else{
                echo "<td>";
            }
            echo"{$row['losses']}</td>";
            if($odd){
                echo "<td id=\"odd\">";
            } else{
                echo "<td>";
            }
            echo"{$row['ranking']}</td>
                </tr>";
        }
    }
    $second = 0;
    $rank = 0;
    echo"</table></div>";
}

printFooter();
?>