<?php

require_once('../../config.php');

class DB{

    private $mysql_host;
    private $mysql_database;
    private $mysql_user;
    private $mysql_password;
    private $connected;
    

    public function __construct()
    {
        $this->mysql_host = "";
        $this->mysql_database = "";
        $this->mysql_user = "";
        $this->mysql_password = "";
        $this->connection = false;

        if(defined('DB_HOST')) $this->mysql_host = DB_HOST;
        if(defined('DB_NAME')) $this->mysql_database = DB_NAME;
        if(defined('DB_USER')) $this->mysql_user = DB_USER;
        if(defined('DB_PASS')) $this->mysql_password = DB_PASS;

    }

    

    public function __destruct()
    {
        $this->disconnect();
    }

    

    function connect()
    {
        if(!$this->connection)
        {
            // open connection
            $this->connection = mysql_connect($this->mysql_host, $this->mysql_user, $this->mysql_password);
            if(!$this->connection)
            {
                return false;
            }
            // select database
            if(!mysql_select_db($this->mysql_database, $this->connection))
            {
                return false;
            }
        }

        return true;
    }

    

    function disconnect()
    {
        if($this->connection)
        {
            mysql_close($this->connection);
            $this->connection = false;
            return true;
        }

        return false;
    }



    public function query($query)
    {
        if($this->connect())
        {
            return mysql_query($query, $this->connection);
        }
        else
        {
            echo "could not connect";
            return false;
        }
    }

    public function escape($arg)
    {
        $this->connect();
        return mysql_real_escape_string($arg, $this->connection);
    }
}



$DB = new DB();

