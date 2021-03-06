<?php
include_once "classes/DB.php";
include_once "classes/match.php";
include_once "models/trophies.php";

class Admin{

    public function __construct()
    {
    }

    public function __destruct()
    {
    }

    public function recalculate(){
        $this->resetAllPlayers();
        $this->createMatches();

    }

    public function loadTrophies(){
        $trophies = new Trophies;
        return $this->formatQueryResultHTML($trophies->addTrophies());
    }

    public function formatQueryResultHTML($array)
    {
        $result = "";
        foreach ($array as $queryNumber => $evaluation) {
            $result .= "Query " . $queryNumber . ": " . $evaluation . "<br>";        
        }
        return $result;
    }

    public function initTrophies(){
        global $DB;

        $query1 = $DB->query("
            DROP TABLE IF EXISTS trophies;

            CREATE TABLE trophies (
              trophyID INT PRIMARY KEY AUTO_INCREMENT,
              name VARCHAR(256) NOT NULL,
              imagePath varchar(256) NOT NULL,
              holderQuery VARCHAR(256) NOT NULL,
              extraQuery VARCHAR(256) DEFAULT NULL,
              type INT NOT NULL
            );

            DROP TABLE IF EXISTS trophytype;
            CREATE TABLE trophytype (
              trophytypeID INT PRIMARY KEY AUTO_INCREMENT,
              trophytype VARCHAR(64)
            );

            INSERT INTO trophytype VALUES (1,'solo'),(2,'team'),(3,'mixed'); 

            CREATE TABLE IF NOT EXISTS trophyholders(
              trophyID INT,
              holderID INT,
              team BOOLEAN DEFAULT 0,
              `fromDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `toDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
              PRIMARY KEY(trophyID, holderID,fromDate)
              );
            ");
        $trophies = new Trophies;
        $trophies->addTrophies();
        return $this->formatQueryResultHTML($trophies->addTrophies());
        
    }



    public function recalculateStreak(){
        global $DB;

        /*
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
        $this->addToSoloTrophies($largetLoosingSpree); */

        $query1 = $DB->query("
            CREATE TABLE IF NOT EXISTS soloStreak (
            playerID INT,
            win BOOLEAN,
            streak INT,
            startDate TIMESTAMP);"
        );

        $query2 = $DB->query("
            TRUNCATE TABLE soloStreak;"
        );


        $query3 = $DB->query("
            DROP FUNCTION IF EXISTS getLastLoseDate;
            DELIMITER $$
            CREATE FUNCTION getLastLoseDate(playerID INT)
              RETURNS TIMESTAMP
            BEGIN
              SET @lastLose = (SELECT timeCreated FROM matches WHERE loserID = playerID AND team = 0 ORDER BY timeCreated DESC LIMIT 1);
              RETURN @lastLose;
            END;
            $$
            DELIMITER ;"
        );
                   
        $query4 = $DB->query("
            DROP FUNCTION IF EXISTS getLastWinDate;
            DELIMITER $$
            CREATE FUNCTION getLastWinDate(playerID INT)
              RETURNS TIMESTAMP
            BEGIN
              SET @lastWin = (SELECT timeCreated FROM matches WHERE winnerID = playerID AND team = 0 ORDER BY timeCreated DESC LIMIT 1);
              RETURN @lastWin;
            END;
            $$
            DELIMITER ;"
        );
       

        $query5 = $DB->query("
            DROP FUNCTION IF EXISTS getStreakType;
            DELIMITER $$
            CREATE FUNCTION getStreakType(playerID INT)
              RETURNS BOOLEAN
            BEGIN
              SET @lastWin = getLastWinDate(playerID);
              SET @lastLose = getLastLoseDate(playerID);
              SET @win = (SELECT CASE WHEN @lastWin>@lastLose THEN 1 ELSE 0 END);
              RETURN @win;
            END;
            $$
            DELIMITER ;"
        );


        $query6 = $DB->query("
            DROP FUNCTION IF EXISTS getStreak;
            DELIMITER $$
            CREATE FUNCTION getStreak(playerID INT)
              RETURNS INT
            BEGIN
              SET @lastWin = getLastWinDate(playerID);
              SET @lastLose = getLastLoseDate(playerID);
              SET @streak = (SELECT count(*) FROM matches WHERE (winnerID = playerID OR loserID = playerID) AND timeCreated > LEAST(@lastWin ,@lastLose) AND team = 0);
              RETURN @streak;
            END;
            $$
            DELIMITER ;"
        );

        $query7 = $DB->query("
            INSERT INTO soloStreak(playerID, win, streak, startDate)
            SELECT playerID, getStreakType(playerID), getStreak(playerID), LEAST(getLastLoseDate(playerID),getLastWinDate(playerID))  
             FROM players; "
        );

        $query8 = $DB->query("
            DROP TRIGGER IF EXISTS soloStreakTrigger;
            DELIMITER $$
            CREATE TRIGGER soloStreakTrigger AFTER INSERT ON matches
            FOR EACH ROW
              BEGIN
                SET @winnerID = NEW.winnerID;
                SET @loserID = NEW.loserID;
                SET @winnerWin = (SELECT win FROM soloStreak WHERE playerID = @winnerID);
                SET @loserWin = (SELECT win FROM soloStreak WHERE playerID = @loserID);
                  IF @winnerWin = 1 THEN
                    UPDATE soloStreak SET streak = streak + 1 WHERE playerID = @winnerID;
                  ELSE
                    UPDATE soloStreak SET streak = 1, win = 1 WHERE playerID = @winnerID;
                  END IF;

                  IF @loserWin = 1 THEN
                    UPDATE soloStreak SET streak = 1, win = 0 WHERE playerID = @loserID;
                  ELSE
                    UPDATE soloStreak SET streak = streak + 1 WHERE playerID = @loserID;
                  END IF;
              END;
            $$
            DELIMITER ;"
        );
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