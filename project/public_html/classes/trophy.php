
<?php
include_once "classes/DB.php";



class Trophy{
	private $name;
    private $description;
    private $image;
    private $query;
    private $ownerID;
    private $extraDescription;


    public function __construct($name,$image,$query,$extra = null){    
        $this->name = $name;
        $this->description = $name;
        $this->image = $image;
        $this->query = $query;
        $this->extraDescription = $extra;
    }
    
    public function __destruct(){   
    }

    public function getTrophy(){

        if(isset($this->extraDescription)){
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
        if(!isset($this->ownerID)){
            global $DB;
            
            $result = mysql_query($this->query);
            if (!$result) {
                $this->ownerID = NULL;
            }
            $row = mysql_fetch_row($result);
            $this->ownedID = $row[0];          
        }       
        return $this->ownedID;
    }
}
 

  

?>