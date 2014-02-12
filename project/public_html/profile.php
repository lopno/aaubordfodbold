<?php

include_once "functions/html.php";
include_once "classes/DB.php";
include_once "classes/match.php";
include_once "classes/profile.php";
include_once "models/trophies.php";

if(!isset($_GET['id'])){
   
    printHeader("AAU Bordfodbold - Player Profile", "Select Player Profile");
    //global $DB;
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
else
{
    $id = (int)$_GET['id'];

    $playerResult = $DB->query("SELECT * FROM players WHERE playerID =" . $id);

    $player = mysql_fetch_object($playerResult);
    if(is_null($player->name))
    {
      echo 'Illegal id ' . $id;
      exit;
    }
    

    $teamsResult = $DB->query("SELECT sum(wins), sum(losses) FROM teams JOIN memberof ON teams.teamID = memberof.teamID WHERE memberof.playerID = " . $id . " GROUP BY memberof.playerID");

    $row = mysql_fetch_array($teamsResult, MYSQL_NUM);

    $teamWins = $row[0];

    $teamLosses = $row[1];

    

    $teamRatio = 0;

    if($teamWins > 0){

        $teamRatio = $teamWins / ($teamWins + $teamLosses);

    }

    

    $totalWins = ($teamWins + $player->wins);

    $totalLosses = ($teamLosses + $player->losses);

    

    $totalRatio = 0;

    if($totalWins > 0){

        $totalRatio = $profile->format($totalWins / ($totalWins + $totalLosses));

    }

    $teamRatingsResult = $DB->query("SELECT ranking, wins, losses
                                    FROM teams 
                                    JOIN memberof ON teams.teamID = memberof.teamID 
                                    WHERE memberof.playerID = $id
                                    GROUP BY teams.teamID");

    $teamRatingTemp = 0;

    while($teamRatings = mysql_fetch_object($teamRatingsResult)){

        $teamRatingTemp += ($teamRatings->ranking * ($teamRatings->wins + $teamRatings->losses));

    }

    $totalRating = 1500;

    if(($totalWins + $totalLosses) > 0){

        $totalRating = (($player->ranking * ($player->wins + $player->losses)) + $teamRatingTemp) / ($totalWins + $totalLosses);

    }

    

    $teamRating = 0;

    if(($teamWins + $teamLosses) > 0){

        $teamRating = $profile->format($teamRatingTemp / ($teamWins + $teamLosses));

    }else{

        $teamRating = 1500;

    }

    

    $soloRatio = 0;

    if($player->wins > 0){

        $soloRatio = ($player->wins / ($player->wins + $player->losses));

    }

    $soloStreakResult = $DB->query("SELECT winnerID FROM matches WHERE (winnerID = $id OR loserID = $id) AND team = 0 ORDER BY timecreated DESC");

    

    $soloStreak = 0;

    while($soloStreakTemp = mysql_fetch_object($soloStreakResult)){

        if($soloStreakTemp->winnerID == $id){

            $soloStreak++;

        }else{

            break;

        }

    }

    

    $soloRank = 0;

    $soloRankResult = $DB->query("SELECT playerID, ranking, wins, losses FROM players ORDER BY ranking DESC");

    while($soloRankTemp = mysql_fetch_object($soloRankResult)){

        if($soloRankTemp->wins + $soloRankTemp->losses > 0){

            $soloRank++;

        }

        if($soloRankTemp->playerID == $id){

            if(($soloRankTemp->wins + $soloRankTemp->losses) == 0){

                $soloRank = '-';

            }

            break;

        }

    }

    

    if($teamWins + $teamLosses > 0){

    

        $bestTeamResult = $DB->query("SELECT teams.teamID, timeCreated, ranking, wins, losses
                                        FROM teams
                                        JOIN memberof ON teams.teamID = memberof.teamID
                                        WHERE memberof.playerID = $id
                                        GROUP BY ranking
                                        ORDER BY ranking DESC
                                        LIMIT 0,1");

                                        

        $bestTeamObject = mysql_fetch_object($bestTeamResult);

        

        $bestTeamId = $bestTeamObject->teamID;

        

        $bestTeamRatio = 0;

        if($bestTeamObject->wins > 0){

            $bestTeamRatio = $profile->format($bestTeamObject->wins / ($bestTeamObject->wins + $bestTeamObject->losses));

        }

        

        $bestTeamMateResult = $DB->query("SELECT players.name
                                          FROM players
                                          JOIN memberof ON players.playerID = memberof.playerID
                                          WHERE memberof.teamID = $bestTeamId
                                          AND players.playerID != $id");

                                        

        $bestTeamMate = mysql_fetch_object($bestTeamMateResult);

        

        $bestTeamStreakResult = $DB->query("SELECT winnerID FROM matches WHERE (winnerID = $bestTeamId OR loserID = $bestTeamId) AND team = 1 ORDER BY timecreated DESC");

        

        $bestTeamStreak = 0;

        while($bestTeamStreakTemp = mysql_fetch_object($bestTeamStreakResult)){

            if($bestTeamStreakTemp->winnerID == $bestTeamId){

                $bestTeamStreak++;

            }else{

                break;

            }

        }

        

        $teamRank = 0;

        $teamRankResult = $DB->query("SELECT teamID, ranking FROM teams ORDER BY ranking DESC");

        while($teamRankTemp = mysql_fetch_object($teamRankResult)){

            $teamRank++;

            if($teamRankTemp->teamID == $bestTeamId){

                break;

            }

        }



        $bestTeamMateName = $bestTeamMate->name;

        $bestTeamWins = $bestTeamObject->wins;

        $bestTeamLosses = $bestTeamObject->losses;

        $bestTeamRating = $bestTeamObject->ranking;



    } else{ //no team games played

        

        $teamWins = 0;

        $teamLosses = 0;

        $bestTeamRatio = 0;

        $bestTeamStreak = 0;

        $teamRank = '-';

        $bestTeamMateName = '-';

        $bestTeamWins = 0;

        $bestTeamLosses = 0;

        $bestTeamRatio = 0;

        $bestTeamRating = 0;

        $bestTeamStreak = 0;

        $teamRank = '-';

    }


    printHeader("AAU Bordfodbold - " . $player->name . "'s Profile", $player->name . "'s Profile Page");

      $trophies = new Trophies;
      //show trophies if he owns any


    echo "
    <div align=\"center\">
          <h3>Overall Stats</h3>
          <table>
          <tr>
          <th>Wins</th><th>Losses</th><th>Ratio</th><th>Rating</th>
          </tr>
          <tr>
          <td id=\"odd\"> $totalWins </td>
          <td id=\"odd\"> $totalLosses </td>
          <td id=\"odd\">" . $profile->format($totalRatio) . "</td>
          <td id=\"odd\">" .  $profile->format($totalRating) . "</td>
          </tr>
          </table>
          
          <h3>Solo Stats</h3>
          <table>
          <tr>
          <th>Wins</th><th>Losses</th><th>Ratio</th><th>Rating</th><th>Win Streak</th><th>Rank</th>
          </tr>
          <tr>
          <td id=\"odd\"> $player->wins </td>
          <td id=\"odd\"> $player->losses </td>
          <td id=\"odd\"> " . $profile->format($soloRatio) . "</td>
          <td id=\"odd\"> $player->ranking </td>
          <td id=\"odd\"> $soloStreak </td>
          <td id=\"odd\"> $soloRank </td>
          </tr>
          </table>
          
          <h3>Team Stats</h3>
          <table border=\"1\">
          <tr>
          <th>Wins</th><th>Losses</th><th>Ratio</th><th>Rating</th>
          </tr>
          <tr>
          <td id=\"odd\"> $teamWins </td>
          <td id=\"odd\"> $teamLosses </td>
          <td id=\"odd\"> " . $profile->format($teamRatio) . "</td>
          <td id=\"odd\"> $teamRating </td>
          </tr>
          </table>
          
          <h3>Best Team Stats</h3>
          <table border=\"1\">
          <tr>
          <th>Team Mate</th><th>Wins</th><th>Losses</th><th>Ratio</th><th>Rating</th><th>Win Streak</th><th>Rank</th>
          </tr>
          <tr>
          <td id=\"odd\">$bestTeamMateName</td>
          <td id=\"odd\">$bestTeamWins</td>
          <td id=\"odd\">$bestTeamLosses</td>
          <td id=\"odd\">$bestTeamRatio</td>
          <td id=\"odd\">$bestTeamRating</td>
          <td id=\"odd\">$bestTeamStreak</td>
          <td id=\"odd\">$teamRank</td>
          </tr>
          </table>
      <h3>Recent Matches</h3>";
      
      $match->printRecentlyPlayedMatches(0,5,$id);
      
      echo '</div>';
      



      printFooter();
}
