<?php
include_once "functions/html.php";
include_once "classes/DB.php";

printHeader("AAU Bordfodbold - New Player", "Create New Player");

function __autoload($class_name) {
    include $class_name . '.php';
}

if(!isset($_POST['Pname'])){
?>

<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
    <div align="center">
	<br />
	<b>Name</b>
	<br /><br />
	<input type="text" size="20" maxlength="20" name="Pname"> <br /><br />
	<!--Password:<input type="password" size="20" maxlength="20" name="password"><br />
	Verify Password:<input type="password" size="20" maxlength="20" name="verify"><br /> -->
	<input type="submit" value="Create Player">
    </div>
</form>

<?php
} else{
	$tempName = $_POST['Pname'];
	$player = new player($tempName);
	
	$pName = $player->getName();
	$pRank = $player->getRating();
	$pWins = $player->getWins();
	$pLosses = $player->getLosses();
	
	//Check for duplicates
	
	//create query
	$checkQuery = "SELECT name FROM players";
	$checkResult = $DB->query($checkQuery);
	$i = 0;
	while ($row = mysql_fetch_array($checkResult, MYSQL_NUM)) {
	    $existingNames[$i] = $row[0];
	    $i++;
	}
	
	$error = false;
	$nameError = false;
	foreach($existingNames as $check){
	    if($pName == $check || $pName == "Choose Player..."){
		$error = true;
		$nameError = true;
	    }
	}
	$charError = preg_match('![^a-zA-Z ]!i', $pName);
	if($charError){
	    $error = true;
	}
	
	if($error == false){
	// create query
	$query = "INSERT INTO players (name, ranking, wins, losses) 
	          VALUES ('".$DB->escape($pName, $connection)."', '".$DB->escape($pRank)."', '".(int)$pWins."', '".(int)$pLosses."')";
	
	// execute query
	$result = $DB->query($query);

	echo "Succesfully Created Player \"" . $player->getName() . "\".";
	}
	
	elseif($charError){
	    printPopUp("Special characters are not allowed.", TRUE);
	    echo "Special characters are not allowed.";
	}
	
	else{
	    printPopUp("That player name already exists. Please Choose another player name.", TRUE);
	    echo "That player name already exists. Please Choose another player name.";
	}
}

printFooter();

?>