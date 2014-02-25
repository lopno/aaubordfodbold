
<?php
include_once "classes/DB.php";



class Trophy{
	private $name;
    private $description;
    private $image;
    private $ownerID;
    private $extraDescription;
    private $team;


    public function __construct($name,$image,$ownerID,$team,$extra = null){    
        $this->name = $name;
        $this->description = $name;
        $this->image = $image;
        $this->ownerID = $ownerID;
        $this->team = $team;
        $this->extraDescription = $extra;
    }
    
    public function __destruct(){   
    }

    public function getTeam(){     
        return $this->team;
    }

    public function getTrophy(){

        if(!is_null($this->extraDescription)){
             global $DB;
            
            $result = mysql_query($this->extraDescription);
            $extra = mysql_fetch_row($result);
            if(is_array($extra))
            {
                $extra = $extra[0];
            }
            $this->name = $this->name . " (" . $extra . ")";
            $this->description = $this->name;
        }
        return "<img src=\"$this->image\" alt=\"$this->description\" title=\"$this->name\"/>";
        
    }
    
    public function getOwner(){    
        return $this->ownerID;
    }
}
 

  

?>