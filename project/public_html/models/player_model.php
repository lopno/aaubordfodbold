<?php
include_once "classes/DB.php";

class PlayerModel{
    
    private $id;
    private $name;
    private $rating;
    private $wins;
    private $losses;
    
    
    public function __construct($playerId){
        $result = $DB->query();
        $playerObj = mysql_fetch_object($result);
        
        $id = $playerObj->playerID;
        $name = $playerObj->name;
        $rating = $playerObj->ranking;
        $wins = $playerObj->wins;
        $losses = $playerObj->losses;
        
    }
    
    public function __destruct(){
        
    }


}
?>