<?php

class DB{
    private $mysql_host;
    private $mysql_database;
    private $mysql_user;
    private $mysql_password;
    private $connected;

    

    public function __construct(){

        $this->mysql_host = "localhost";
        $this->mysql_database = "aaubordfodbold";
        $this->mysql_user = "aaubordfodbold";
        $this->mysql_password = "aaubordfodbold";
        $this->connection = false;
    }


    public function __destruct(){

        $this->disconnect();
    }

    

    function connect(){

        //echo "connect function<br/>";

        if(!$this->connection){

            // open connection

            //echo "connecting<br/>";

            $this->connection = mysql_connect($this->mysql_host, $this->mysql_user, $this->mysql_password);

            if(!$this->connection){

                return false;

            }

            // select database

            if(!mysql_select_db($this->mysql_database, $this->connection)){

                return false;

            }

            

        }

        //echo "connected<br/>";

        return true;

    }

    

    function disconnect(){

        if($this->connection){

            mysql_close($this->connection);

            

            $this->connection = false;

            

            return true;

        }

        

        return false;

    }



    public function query($query){

        //echo $query . "<br/>";

        if($this->connect()){

            return mysql_query($query, $this->connection);

        }

        else{

            echo "could not connect";

            return false;

        }



    }

    

    public function escape($arg){

        $this->connect();

        return mysql_real_escape_string($arg, $this->connection);

    }

}



$DB = new DB();

?>