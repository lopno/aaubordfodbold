<?php
//<a href=\"mymain.php\">Main Page</a> | <a href=\"create_player.php\">New Player</a> | <a href=\"create_match.php\">New Match</a> | <a href=\"leaderboard.php\">Leaderboards</a> | <a href=\"profile.php\">Player Profile</a> <img src=\"http://i.imgur.com/aBiKs.gif\" alt=\"NEW!\" title=\"NEW!\"/> | <a href=\"FAQ.php\">FAQ</a>
include_once("classes/DB.php");
date_default_timezone_set("Europe/Copenhagen");
session_start();
//global $DB;
function printHeader($title, $heading){
    echo "<!DOCTYPE html>
        <html>
        <head>
        <link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\" />
        <title>
        $title
        </title>
        </head>
        <body>
        <div class=\"wrapper\">
        <center>
        <a href=\"mymain.php\"><img src=\"../img/aaubordfodbold.png\" alt=\"Banner\"/></a>
        <ul id=\"list-nav\">
        <li><a href=\"mymain.php\">Home</a></li>
        <li><a href=\"create_player.php\">New Player</a></li>
        <li><a href=\"create_match.php\">New Match</a></li>
        <li><a href=\"leaderboard.php\">Leaderboards</a></li>
        <li><a href=\"matchhistory.php?page=1\">History</a></li>
        <li><a href=\"profile.php\">Player Profile</a></li>
        <li><a href=\"FAQ.php\">FAQ</a></li>
        </ul>
        </center>
        <br />
        <center><h1>$heading</h1></center>
        ";
}

function printFooter(){
    echo "
        </div>
        </body>
        </html>";
}

function printPopUp($text, $back){
    echo "<script type=\"text/javascript\">alert(\"$text\");";
    if($back){
        echo "history.back();";
    }
    else{
        echo "window.location.href=window.location.href;";
    }
    echo "</script>";
}

function printLeaderboardLinks(){
    echo "<center><h3><a href=\"leaderboard.php?type=solo\">Solo</a> | <a href=\"leaderboard.php?type=team\">Team</a></h3></center>";
}

function printSiteStats(){
    global $DB;
    
    $newTeamResult = $DB->query("SELECT teams.teamID, teams.timeCreated, players.playerID, players.name
                                 FROM teams
                                 JOIN memberof ON teams.teamID = memberof.teamID
                                 JOIN players ON players.playerID = memberof.playerID
                                 ORDER BY teams.timeCreated DESC
                                 LIMIT 0,2");
                                 
    
    $newPlayerResult = $DB->query("SELECT playerID, name FROM players ORDER BY playerID DESC LIMIT 0,1");
    $newPlayerObj = mysql_fetch_object($newPlayerResult);
    
    $matchCountResult = $DB->query("SELECT COUNT(matchID) FROM matches");
    $matchCountRow = mysql_fetch_row($matchCountResult);
    
    $bestPlayerResult = $DB->query("SELECT playerID, name FROM players ORDER BY ranking DESC LIMIT 0,1");
    $bestPlayerObj = mysql_fetch_object($bestPlayerResult);
    
    //$bestTeamResult = $DB->query("SELECT teamID FROM teams ORDER BY ranking DESC LIMIT 0,1");
    //$bestTeamObj = mysql_fetch_object($bestTeamResult);
    
    //$bestTeamPlayersResult = $DB->query("SELECT name FROM ")
    
    $query = "SELECT teams.teamID, teams.ranking, players.playerID , players.name 
              FROM teams 
              JOIN memberof ON teams.teamID = memberof.teamID 
              JOIN players ON memberof.playerID = players.playerID 
              ORDER BY ranking DESC
              LIMIT 0,2";
    
    $result = $DB->query($query);
    
    /*<li>Newest Team: ";
        $and = TRUE;
        while($row = mysql_fetch_assoc($newTeamResult)){
            echo"<a href=\"profile.php?id={$row["playerID"]}\">{$row["name"]}</a>";
            if($and){
                echo" & ";
            }
            $and = FALSE;
        }
        echo"</li> */
    echo "<h3>Site Statistics</h3>
        <ul id=\"list-stats\">
        <li>Newest Player: <a href=\"profile.php?id=$newPlayerObj->playerID\">$newPlayerObj->name</a></li>
        
        <br></br>
        <li>Total Matches Played: $matchCountRow[0]</li>
        <li>Highest Rated Player: <a href=\"profile.php?id=$bestPlayerObj->playerID\">$bestPlayerObj->name</a></li>
        <li>Highest Rated Team: ";
        $and = TRUE;
        while($row = mysql_fetch_assoc($result)){
            
            echo"<a href=\"profile.php?id={$row["playerID"]}\">{$row["name"]}</a>";
            if($and){
                echo " & ";
            }
            $and = FALSE;
        }
        echo"</li></ul>";
}

function printSelectPlayerForm(){
    global $DB;
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
}

function printFilterPlayerForm(){
    
}
?>