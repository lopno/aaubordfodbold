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

        
        if(isset($trophies))
        {
            $numberOfResults = mysql_num_rows($trophies);
            for ($i=0; $i < $numberOfResults; $i++) 
            { 
                $trophy = mysql_fetch_assoc($trophies);
                $trophyInstance = new Trophy($trophy['name'],$trophy['imagePath'],$trophy['holderID'],$trophy['team'],$trophy['extraQuery']);
                $this->sortAddTrophies($trophyInstance);
            }
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

    public function addTrophies(){
        /*
         A trophy have the following attributes  
        -- @name is the html image title and description
        -- @imagePath is the path from root (public_html) to the image, URLs also allowed
        -- @holderQuery is a string containing a query that when evaluated return a playerID or teamID
        -- @extraQuery is also a query which result is concatinated to the html images title (visiable when mouse over)
        -- @type determines who can optain the trophie, 1 = solo, 2 = team, 3 = both. 
        -- The holderQuery should return the same match type as the type attribute.
        */

        global $DB;
        $result = array();

        $result['truncate'] = $DB->query("
        TRUNCATE trophies;
        ");
        
        $result['highest ranked player'] =$DB->query(" 
        INSERT INTO trophies (name,imagePath,holderQuery,extraQuery, type)
        VALUES ('Highest Ranked Player!', 'img/first.png', 'SELECT playerID FROM players ORDER BY ranking DESC LIMIT 1' , NULL, 1);
        ");

        $result['latest win'] =$DB->query("
        INSERT INTO trophies (name,imagePath,holderQuery,extraQuery, type)
        VALUES ('Latest win', 'http://i.imgur.com/SpR4U.gif', 'SELECT winnerID FROM matches WHERE team = 0 ORDER BY timeCreated DESC LIMIT 1' , NULL, 1);
        ");


        return $result;
    }
        /*

        Most matches played.
         //Scored goal / goal kill loose 
        //Win lose ratio
        

        
        
       
        */
    
    

    
    
}