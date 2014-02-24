<?php
include_once "classes/DB.php";

class PlayerModel
{
    private $id;
    private $name;
    private $rating;
    private $wins;
    private $losses;

    public function __construct($playerId)
    {
        global $DB;

        $result = $DB->query("SELECT * FROM players WHERE playerID = ". $DB->escape($playerId));

        $playerObj = mysql_fetch_object($result);

        if(is_object($playerObj))
        {
            $this->id = $playerObj->playerID;
            $this->name = $playerObj->name;
            $this->rating = $playerObj->ranking;
            $this->wins = $playerObj->wins;
            $this->losses = $playerObj->losses;
        }
    }

    public function __destruct()
    {
    }
    
    public function getName()
    {
        return $this->name;
    }
}
