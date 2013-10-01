<?php

class Player
{
	private $rating;
	private $name;
	private $wins;
	private $losses;
	
	
    public function __construct($name) 
	{
		$this->setRating(1500);
		$this->setName($name);
		$this->setWins(0);
		$this->setLosses(0);
	}
	
	public function setName($value)
	{
		$this->name = $value;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function setRating($value)
	{
		$this->rating = $value;
	}
	
	public function getRating()
	{
		return $this->rating;
	}
	
	public function setWins($value)
	{
		$this->wins = $value;
	}
		
	public function getWins()
	{
		return $this->wins;
	}
		
	public function setLosses($value)
	{
		$this->losses = $value;
	}
		
	public function getLosses()
	{
		return $this->losses;
	}
}
?>