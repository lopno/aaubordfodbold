<?php
include_once "classes/trophy.php";

class Trophies{
    public $soloTrophies = array();
    public $teamTrophies = array();

    public function __construct(){   

        global $DB;
        #where clause is set only to get solu queries because the underlying system do not support team trophies.
        $trophies =  $DB->query("   SELECT t.trophyID as trophyID, holderID, name, imagePath, team, extraQuery 
                                    FROM (SELECT trophyID, holderID FROM trophyholders WHERE toDate = '0000-00-00 00:00:00') as th
                                    INNER JOIN trophies as t
                                    ON t.trophyID = th.trophyID
                                    WHERE team = 0;");
        while ($trophy = mysql_fetch_assoc($trophies))
        {
            $trophyInstance = new Trophy($trophy['name'],$trophy['imagePath'],$trophy['holderID'],$trophy['team'],$trophy['extraQuery']);
            $this->sortAddTrophies($trophyInstance);
        }   
    }     

    public function __destruct(){}

    private function sortAddTrophies($trophy)
    {
        
        # If solo match
        if (!$trophy->getTeam()) {
            $this->soloTrophies[$trophy->getOwner()][] = $trophy;
        }
        else {
            $this->teamTrophies[$trophy->getOwner()][] = $trophy;
        }
    }
        /*

        Most matches played.

        INSERT INTO trophies (name,imagePath,holderQuery,extraQuery, team)
        VALUES ("LIKE A BOSS!", "http://i.imgur.com/SpR4U.gif", "SELECT playerID FROM players ORDER BY ranking DESC LIMIT 1" , NULL, 0);
        
        INSERT INTO trophies (name,imagePath,holderQuery,extraQuery, team)
        VALUES ("Latest win", "http://i.imgur.com/SpR4U.gif", "SELECT winnerID FROM matches WHERE team = 0 ORDER BY timeCreated DESC LIMIT 1" , NULL, 0);

        $firstPlace = new Trophy(
            "LIKE A BOSS!",
            "http://i.imgur.com/SpR4U.gif", 
            "SELECT playerID FROM players ORDER BY ranking DESC LIMIT 1");
        $this->addToSoloTrophies($firstPlace);

        $largetWinningSpree = new Trophy(
            "Largest Winning Spree!",
            "img/achievement_48_hot.png", 
            "SELECT playerID FROM soloStreak WHERE win = 1 ORDER BY streak DESC, startDate DESC LIMIT 1;",
            "SELECT streak FROM soloStreak WHERE win = 1 ORDER BY streak DESC, startDate DESC LIMIT 1;");
        $this->addToSoloTrophies($largetWinningSpree);

        $largetLoosingSpree = new Trophy(
            "Largest Losing Spree!",
            "img/achievement_48_hot.png", 
            "SELECT playerID FROM soloStreak WHERE win = 0 ORDER BY streak DESC, startDate DESC LIMIT 1;",
            "SELECT streak FROM soloStreak WHERE win = 0 ORDER BY streak DESC, startDate DESC LIMIT 1;");
        $this->addToSoloTrophies($largetLoosingSpree);
        
        //Scored goal / goal kill loose 
        //Win lose ratio*/
    
    
    

    
    
}
?>