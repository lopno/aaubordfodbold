<?php
include_once "functions/html.php";
include_once "classes/DB.php";
include_once "classes/match.php";
include_once "models/player_model.php";

printHeader("AAU Bordfodbold - New Match", "Set New Match Result");

if (!isset($_POST['submit'])){
	$match->printCreateMatchForm();
}

else{
	$winner1 = (int)$_POST['winner1'];
	$winner2 = (int)$_POST['winner2'];
	$loser1 = (int)$_POST['loser1'];
	$loser2 = (int)$_POST['loser2'];
	$winScore = (int)$_POST['winScore'];
	$lossScore = (int)$_POST['lossScore'];
	
	//1v1
	if(
	   (($winner1 == -1 && $winner2 != -1)
	   || ($winner1 != -1 && $winner2 == -1)) //Team 1 is one person
	   &&
	   (($loser1 == -1 && $loser2 != -1)
	   || ($loser1 != -1 && $loser2 == -1)) //Team 2 is one person
	   )
	{
		if($winner1 == -1){
			$winner1v1 = $winner2;
		} else{
			$winner1v1 = $winner1;
		}
		
		if($loser1 == -1){
			$loser1v1 = $loser2;
		} else{
			$loser1v1 = $loser1;
		}

        $id1 = $winner1v1;
		$id2 = $loser1v1;
		//echo "Win: " . $id1 . "loss: " . $id2;
		if($id1 == $id2)
		{
            printPopUp("Players can't play against themselves.", TRUE);
			echo "Players can't play against themselves.";
		}
		elseif($winScore <= $lossScore)
		{
			printPopUp("The loser can't have a greater score than the winner.", TRUE);
			echo "The loser can't have a greater score than the winner.";
		} 
		elseif(is_null((new PlayerModel($id1))->getName()))
		{
			printPopUp("ID of winning player is invalid", TRUE);
			echo "ID of winning player is invalid";
		}
		elseif(is_null((new PlayerModel($id2))->getName()))
		{
			printPopUp("ID of losing player is invalid", TRUE);
			echo "ID of losing player is invalid";
		}
		else
		{
			$match->createMatch($id1, $id2, $winScore, $lossScore, false);
			printPopUp("Succesfully created match!", FALSE);
			echo "Succesfully created match: " . $winner1v1 . " " . $winScore . " - " . $lossScore . " " . $loser1v1 . "<br />";
		}
	}

	elseif($winner1 == -1 || $winner2 == -1 || $loser1 == -1 || $loser2 == -1)
	{
	    printPopUp("Ranked matches must be played in either the format 1v1 or 2v2.", TRUE);
		echo "Ranked matches must be played in either the format 1v1 or 2v2.";
	}
	
	//2v2
	else
	{
		//echo "Success: " . teamExists($winner1, $winner2);
		$winTeam = $match->teamExists($winner1, $winner2);
		if(!$winTeam){
			$winTeam = $match->createTeam($winner1, $winner2);
		}
		
		$lossTeam = $match->teamExists($loser1, $loser2);
		if(!$lossTeam){
			$lossTeam = $match->createTeam($loser1, $loser2);
		}
		
		$winIDs[0] = $winner1;
        $winIDs[1] = $winner2;
		
        $lossIDs[0] = $loser1;
        $lossIDs[1] = $loser2;
        
		$error1 = false;
		foreach($winIDs as $win){
			foreach($lossIDs as $loss){
				if($win == $loss){
					$error1 = true;
				}
			}
		}
		if($winner1 == $winner2 || $loser1 == $loser2){
			$error1 = true;
		}
		
		$error2 = false;
		if($lossScore >= $winScore){
			$error2 = true;
		}
		
		
		if($error1 == true){
		    printPopUp("Players can't play against themselves.", TRUE);
			echo "Players can't play against themselves.";
		} elseif($error2 == true){
			printPopUp("The losing team can't have a greater score than the winning team.", TRUE);
			echo "The losing team can't have a greater score than the winning team.";
		}
		elseif(is_null((new PlayerModel($winIDs[0]))->getName()) || is_null((new PlayerModel($winIDs[1]))->getName()))
		{
			printPopUp("ID of a winning player is invalid", TRUE);
			echo "ID of a winning player is invalid";
		}
		elseif(is_null((new PlayerModel($lossIDs[0]))->getName()) || is_null((new PlayerModel($lossIDs[1]))->getName()))
		{
			printPopUp("ID of a losing player is invalid", TRUE);
			echo "ID of a losing player is invalid";
		}
		else{
			$match->createMatch($winTeam, $lossTeam, $winScore, $lossScore, true);
            printPopUp("Succesfully created match!", FALSE);
			echo "Succesfully created match!";
		}
	}
}

echo "<h3>Recently Played Matches</h3>";

$match->printRecentlyPlayedMatches(0,5);

printFooter();
