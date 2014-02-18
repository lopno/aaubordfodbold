<?php
include_once "classes/DB.php";

class Match{
    
    public function __construct(){
        
    }
    
    public function __destruct(){
        
    }
    
    public function teamExists($id1, $id2){
        global $DB;

        $result3 = $DB->query("SELECT teamID
                               FROM memberof
                               WHERE playerID = $id1 AND (teamID) IN
                               (SELECT teamID
                               FROM memberof
                               WHERE playerID = $id2)");
        
        $row3 = mysql_fetch_array($result3, MYSQL_NUM); 
        $id3 = $row3[0];
        
        if(!$row3){
            //echo "no row";
            return false;
        }else{
            //echo "row found";
            return $id3;
        }
    }
    
    public function createTeam($id1, $id2){
        
        global $DB;
        $odd = FALSE;
        
        $result = $DB->query("INSERT INTO teams (ranking, wins, losses) VALUES (1500, 0, 0)");
        
        $teamID = mysql_insert_id();
        
        $DB->query("INSERT INTO memberof (playerID, teamID) VALUES ($id1, $teamID)");
        $DB->query("INSERT INTO memberof (playerID, teamID) VALUES ($id2, $teamID)");
        
        //echo "Succesfully Created Team \"" . $teamID . "\".";
        
        return $teamID;
    }

    public function createMatch($team1, $team2, $winScore, $lossScore, $isTeam, $emulate = FALSE, $id = NULL){ //takes ID from teams or names from players
    
        global $DB;
        
        $this->updateTrophies();

        if($isTeam == true){
            $ratingQuery1 = "SELECT ranking FROM teams WHERE teamID = '".(int)$team1."'";
            $ratingQuery2 = "SELECT ranking FROM teams WHERE teamID = '".(int)$team2."'";
        }
        else{
            $ratingQuery1 = "SELECT ranking FROM players WHERE playerID = '".(int)$team1."'";
            $ratingQuery2 = "SELECT ranking FROM players WHERE playerID = '".(int)$team2."'";
        }
    
        
        
        $data1 = $DB->query($ratingQuery1);
        $data2 = $DB->query($ratingQuery2);
        
        $row1 = mysql_fetch_array($data1, MYSQL_NUM); 
        $rating1 = $row1[0];
        
        $row2 = mysql_fetch_array($data2, MYSQL_NUM); 
        $rating2 = $row2[0];
        
        //probability1
        $difference1 = $rating1 - $rating2;
        
        if($difference1 < 0){
            $difference1 = - $difference1;
        }
        
        $probability1 = 0;
        
        if ($difference1 >= 400)
        {
            if ($rating1 > $rating2)
            {
                $probability1 = 0.92;
            }
            else
            {
                $probability1 = 0.08;
            }
        } else {
            $probability1 = 1/(1 + pow(10,($rating2 - $rating1)/400));
        }
        
        //probability2
        $difference2 = $rating2 - $rating1;
        
        if($difference2 < 0){
            $difference2 = - $difference2;
        }
        
        $probability2 = 0;
        
        if ($difference2 >= 400)
        {
            if ($rating2 > $rating1)
            {
                $probability2 = 0.92;
            }
            else
            {
                $probability2 = 0.08;
            }
        } else {
            $probability2 = 1/(1 + pow(10,($rating1 - $rating2)/400));
        }
        
        $pointsWon = 20 * (1 - $probability1);
        $pointsLost = 20 * (0 - $probability2);
        
        $newRating1 = $rating1 + $pointsWon;
        $newRating2 = $rating2 + $pointsLost;
        
        if($emulate == FALSE){
            
            if($isTeam == true){
                $query = "INSERT INTO matches (matchID, timeCreated, winnerID, loserID, winScore, lossScore, team, points) 
                          VALUES ('', '" . date("Y-m-d H:i:s") . "',
                          '".(int)$team1."',
                          '".(int)$team2."',
                          '".(int)$winScore."',
                          '".(int)$lossScore."',
                          1, 
                          '". (double)$pointsWon ."')";
            } else{
                $query = "INSERT INTO matches (matchID, timeCreated, winnerID, loserID, winScore, lossScore, team, points) 
                          VALUES ('', '" . date("Y-m-d H:i:s") . "',
                          '".(int)$team1."',
                          '".(int)$team2."',
                          '".(int)$winScore."',
                          '".(int)$lossScore."',
                          0,
                          '". (double)$pointsWon ."')";
            }
        } else{
            $query = "UPDATE matches 
                      SET points = '". $pointsWon ."'
                      WHERE matchID = '". (int)$id ."'
                      ";
        }
        
        
        
        //change ratings
        if($isTeam){
            $updateQueryWinner = "UPDATE `teams` SET `ranking` = $newRating1 WHERE `teams`.`teamID` ='".(int)$team1."'";
            $updateQueryLoser = "UPDATE `teams` SET `ranking` = $newRating2 WHERE `teams`.`teamID` ='".(int)$team2."'";
        } else{
            $updateQueryWinner = "UPDATE `players` SET `ranking` = $newRating1 WHERE `players`.`playerID` ='".(int)$team1."'";
            $updateQueryLoser = "UPDATE `players` SET `ranking` = $newRating2 WHERE `players`.`playerID` ='".(int)$team2."'";
        }
        
        $DB->query($updateQueryWinner);
        $DB->query($updateQueryLoser);
        
        //Add wins and losses
        
        if($isTeam == true){
            $incrementQuery1 = "UPDATE teams SET wins = wins + 1 WHERE teams.teamID = '".(int)$team1."'";
            $incrementQuery2 = "UPDATE teams SET losses = losses + 1 WHERE teams.teamID = '".(int)$team2."'";
        } else{
            $incrementQuery1 = "UPDATE players SET wins = wins + 1 WHERE players.playerID = '".(int)$team1."'";
            $incrementQuery2 = "UPDATE players SET losses = losses + 1 WHERE players.playerID = '".(int)$team2."'";
        }
        
        //echo "4: " . $score1 . ", " . $score2 . "<br />";
        $DB->query($incrementQuery1);
        $DB->query($incrementQuery2);
        
        //echo "5: " . $score1 . ", " . $score2 . "<br />";
        
        //echo "Succesfully Created Match \"" . $matchID . "\"."; //MatchID is always 0, dunno why :(
        
        $result = $DB->query($query);
        $matchID = mysql_insert_id();

        
        
        return $matchID;
    }
    
    public function printCreateMatchForm(){
        global $DB;
        $odd = FALSE;
        
        $result = $DB->query("SELECT playerID, name FROM players ORDER BY name");   

        $playerArray[0]['name'] = "Choose Player...";
        $playerArray[0]['playerID'] = -1;
        
        while ($row = mysql_fetch_array($result, MYSQL_BOTH)) {
            $playerArray[] = $row;
        }
        
        echo "<form action=\"{$_SERVER['PHP_SELF']}\" method=\"POST\">
    
        <div align=\"center\">";
            
        echo"<table>
            <tr>
            <th>Winner(s)</th>
            <th>Score</th>
            <th>Loser(s)</th>
            <th>Score</th>
            </tr>
            <tr>";
            
       if($odd){
            echo "<td id=\"odd\">";
        } else{
            echo "<td>";
        }

       echo "<select name=\"winner1\">";
            foreach($playerArray as $player){
                echo"<option value=\"{$player['playerID']}\">{$player['name']}</option>";
            }
            echo"</select>
            <br />
            <br />
            <select name=\"winner2\">";
            foreach($playerArray as &$player){
            echo"<option value=\"{$player['playerID']}\">{$player['name']}</option>";
            }
            echo"</select></td>";
            
            if($odd){
                echo "<td id=\"odd\">";
            } else{
                echo "<td>";
            }

            echo "<select name=\"winScore\">";
            for($i = 1; $i <= 20; $i++){
                echo"<option value=$i>$i</option>";
            }
            echo"</select></td>";
            
            if($odd){
                echo "<td id=\"odd\">";
            } else{
                echo "<td>";
            }

            echo "<select name=\"loser1\">";
            
            foreach($playerArray as &$player){
            echo "<option value=\"{$player['playerID']}\">{$player['name']}</option>";
            }
            echo "</select>
            <br/><br/>
            <select name=\"loser2\">";
            foreach($playerArray as &$player){
                echo"<option value=\"{$player['playerID']}\">{$player['name']}</option>";
            }
            echo "</select></td>";
                  
            if($odd){
                echo "<td id=\"odd\">";
            } else{
                echo "<td>";
            }
        
                  
            echo "<select name=\"lossScore\">";
                  
            for($i = 0; $i < 20; $i++){
                echo"<option value=$i>$i</option>";
            }
            echo "</select></td></tr>
                  </table><br />
    
            <input type=\"submit\" name=\"submit\" value=\"Submit!\">
            <div align=\"center\">
            </form>";
    
            
        }

    public function printRecentlyPlayedMatches($start, $count, $id = -1){
        
        global $DB;
        
        $cleanId = (int) $DB->escape($id);
        
        $odd = FALSE;
        if($id < 0)
        {            
            $matchResult = $DB->query("SELECT timeCreated, winnerID, loserID, winScore, lossScore, team, points 
                                        FROM matches 
                                        ORDER BY timeCreated DESC 
                                        LIMIT $start,$count");
        }else{
            $matchResult = $DB->query("SELECT timeCreated, winnerID, loserID, winScore, lossScore, team, points 
                                        FROM matches 
                                        WHERE NOT team and (winnerID = $id or loserID = $id) or team and (winnerID IN (
                                            SELECT teams.teamID
                                            FROM teams
                                                JOIN memberof ON teams.teamID = memberof.teamID
                                            WHERE memberof.playerID = $id
                                            ) or loserID IN (
                                            SELECT teams.teamID
                                            FROM teams
                                                JOIN memberof ON teams.teamID = memberof.teamID
                                            WHERE memberof.playerID = $id
                                            ))
                                        ORDER BY timeCreated DESC 
                                        LIMIT $start,$count");
        }
        
        echo"
            <div align=\"center\">
                <table>
                    <tr>
                        <th>Time Played</th>
                        <th>Winner(s)</th>
                        <th>Winner Score</th>
                        <th>Loser Score</th>
                        <th>Loser(s)</th>
                        <th>Points Won</th>
                    </tr>";
                    
        while($matchRow = mysql_fetch_array($matchResult, MYSQL_BOTH)){
            
            if($odd){
                $odd = FALSE;
            }else{
                $odd = TRUE;
            }
            
            if($matchRow["team"]){
                
                $idWinResult = $DB->query("SELECT playerID FROM memberof WHERE memberof.teamID = '".(int)$matchRow["winnerID"]."'");
                
                $idLossResult = $DB->query("SELECT playerID FROM memberof WHERE memberof.teamID = '".(int)$matchRow["loserID"]."'");
                
                $i = 0;
                while ($winRow1 = mysql_fetch_array($idWinResult, MYSQL_NUM)) {
                
                $winArray[$i] = $winRow1[0];
                $i++;
                }
                
                $i = 0;
                while ($lossRow1 = mysql_fetch_array($idLossResult, MYSQL_NUM)) {
                
                $lossArray[$i] = $lossRow1[0];
                $i++;
                }               
                
                $j = 0;
                foreach($winArray as $player){
                    $nameQuery1 = "SELECT name FROM players WHERE playerID = '".(int)$player."'";
                    $nameResult1 = $DB->query($nameQuery1);
                    
                    $winRow2 = mysql_fetch_array($nameResult1, MYSQL_NUM);
                    
                    $winNames[$j] = "<a href=\"profile.php?id={$player}\">{$winRow2[0]}</a>";
                    $j++;
            }
                
                $j = 0;
                foreach($lossArray as $player){
                    $nameQuery2 = "SELECT name FROM players WHERE playerID = '".(int)$player."'";
                    $nameResult2 = $DB->query($nameQuery2);
                    
                    $lossRow2 = mysql_fetch_array($nameResult2, MYSQL_NUM);
                    
                    $lossNames[$j] = "<a href=\"profile.php?id={$player}\">{$lossRow2[0]}</a>";
                    $j++;
                }
                //1 v 1
            } else{
                $nameQuery1 = "SELECT name FROM players WHERE playerID = '".(int)$matchRow["winnerID"]."'";
                $nameResult1 = $DB->query($nameQuery1);
                    
                $winRow1 = mysql_fetch_array($nameResult1, MYSQL_NUM);
                    
                $winName = "<a href=\"profile.php?id={$matchRow["winnerID"]}\">{$winRow1[0]}</a>";
                
                $nameQuery2 = "SELECT name FROM players WHERE playerID = '".(int)$matchRow["loserID"]."'";
                $nameResult2 = $DB->query($nameQuery2);
                    
                $lossRow2 = mysql_fetch_array($nameResult2, MYSQL_NUM);
                    
                $lossName = "<a href=\"profile.php?id={$matchRow["loserID"]}\">{$lossRow2[0]}</a>";
            }

            echo"<tr>";
            
            if($odd){
                echo "<td id=\"odd\">";
            } else{
                echo "<td>";
            }
        
            echo "{$matchRow['timeCreated']}</td>";
            
            if($odd){
                echo "<td id=\"odd\">";
            } else{
                echo "<td>";
            }
        
            if($matchRow['team']){
                foreach($winNames as $winner){
                    echo $winner . "<br/>";
                }
            }else{
                echo $winName;
            }
            echo"</td>";
            if($odd){
                echo "<td id=\"odd\">";
            } else{
                echo "<td>";
            }
            echo"{$matchRow['winScore']}</td>";
            if($odd){
                echo "<td id=\"odd\">";
            } else{
                echo "<td>";
            }
            echo"{$matchRow['lossScore']}</td>";
            if($odd){
                echo "<td id=\"odd\">";
            } else{
                echo "<td>";
            }
        
            if($matchRow['team']){
                foreach($lossNames as $loser){
                    echo $loser . "<br />";
                }
            }else{
                echo $lossName;
            }
            echo "</td>";
            
            if($odd){
                echo "<td id=\"odd\">";
            } else{
                echo "<td>";
            }
            
            echo " + {$matchRow['points']}</td>";
            echo"</tr>";

        }

        echo"</table></div>";
    }

    private function updateTrophies(){
        global $DB;

        #only solo trophies
        $newTrophies = $DB->query("SELECT trophyID, holderQuery FROM trophies WHERE team = 0");

        $insertIntoQuery = "INSERT INTO trophyholders (trophyID, playerID) VALUES ";
        $insertInto = "";

        while ($trophy = mysql_fetch_assoc($newTrophies)) #foreach trophy
            {
                $playerID = $this->getOwner($trophy['holderQuery']);     
                $existingTrophyRecordQuery = $DB->query("   SELECT trophyID, playerID, fromDate 
                                                            FROM trophyholders 
                                                            WHERE trophyID = $trophy[trophyID] 
                                                            ORDER BY fromDate DESC 
                                                            LIMIT 1");

                $existingTrophyRecord = mysql_fetch_assoc($existingTrophyRecordQuery);

                if($playerID == $existingTrophyRecord['playerID']){ #no trophy ownerchange

                }
                elseif(!isset($existingTrophyRecord)){ #if trophy is new
                    if (isset($playerID)) {
                        $insertInto .= "($trophy[trophyID],$playerID),";
                    }
                }
                elseif($playerID != $existingTrophyRecord['playerID']){ #trophy ownership change

                    $insertInto .= "($trophy[trophyID],$playerID),";
                    $updateQuery = "    UPDATE trophyholders 
                                        SET toDate = NOW() 
                                        WHERE trophyID = $trophy[trophyID] 
                                        && playerID = $playerID 
                                        && fromDate = $trophy[fromDate]";
                    $DB->query($updateQuery);
                }
            }  
        $insertIntoQuery .= rtrim($insertInto, ",") . ";";
        $DB->query($insertIntoQuery); 


        exit;

    }

    private function getOwner($query){
       
        global $DB;
        $result = mysql_query($query);
        if (!$result) 
        {
            return NULL;
        }
        else
        {
            $row = mysql_fetch_row($result);
            return $ownedID = $row[0]; 
        }          
        
    }

}


    
$match = new Match();
?>