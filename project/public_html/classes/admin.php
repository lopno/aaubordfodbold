<?php
include_once "classes/DB.php";
include_once "classes/match.php";

class Admin{

    public function __construct()
    {
    }

    public function _destruct()
    {
    }

    public function recalculate(){
        $this->resetAllPlayers();
        $this->createMatches();
    }

    function resetAllPlayers()
    {
        global $DB;

        $DB->query("UPDATE players 
                    SET wins = '0', losses = '0', ranking = '1500'");

        $DB->query("UPDATE teams 
                    SET wins = '0', losses = '0', ranking = '1500'");
    }

    function createMatches()
    {
        global $DB;
        global $match;

        $result = $DB->query("SELECT matchID, winnerID, loserID, winScore, lossScore, team
                              FROM matches
                              ORDER BY matchID ASC");

        while($obj = mysql_fetch_object($result))
        {
            $match->createMatch($obj->winnerID, $obj->loserID, $obj->winScore, $obj->lossScore, $obj->team, $emulate = TRUE, $id = $obj->matchID);
        }
    }
}

$admin = new Admin();
?>