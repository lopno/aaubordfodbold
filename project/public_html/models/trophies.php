<?php
include_once "classes/trophy.php";

class Trophies{
    public $soloTrophies = array();

    public function __construct(){   

        global $DB;

            $trophies = $DB->query("SELECT name, imagePath, holderQuery, extraQuery FROM trophies WHERE team = 0");
    
            while ($trophy = mysql_fetch_assoc($trophies))
            {
                $this->soloTrophies[] = new Trophy($trophy['name'],$trophy['imagePath'],$trophy['holderQuery'],$trophy['extraQuery']);

            }        

        /*

        INSERT INTO trophies (name,imagePath,holderQuery,extraQuery, team)
        VALUES ("LIKE A BOSS!", "http://i.imgur.com/SpR4U.gif", "SELECT playerID FROM players ORDER BY ranking DESC LIMIT 1" , NULL, 0)

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
    
    public function __destruct(){   
    }

    
    
}
?>