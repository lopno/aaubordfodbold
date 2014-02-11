<?php

include_once "classes/DB.php";



class Profile{

    

    public function __construct(){

        

    }

    

    public function __destruct(){

        

    }

    

    public function format($float){

        return number_format(($float), 2, '.', '');

    }

    

}



$profile = new Profile(); 

?>