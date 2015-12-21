<?php
class Group
{
	private $id;
	private $first;
	private $second;
	private $third;
	private $courtId;
	private $tournamentId;
	private $staff;

	public function Group($newId, $newFirst, $newSecond, $newThird, $newCourtId, $newTournamentId, $newStaff) {
		if (!empty($newId)){
			$this->id = $newId;
			$this->first = $newFirst;	//vides les premières fois qu'ils sont appelés
			$this->second = $newSecond; 
			$this->third = $newThird;
			$this->courtId = $newCourtId;
			$this->tournamentId = $newTournamentId;
			$this->staff = $newStaff;
		}
	}

	public function getId()
	{
		return $this->id;
	}
	
	public function getFirst()
	{
		//$pair = new Pair($this->first);//, ..); //TODO constructeur avec requete SQL?
		return $this->first;
	}
	
	public function getSecond()
	{
		//$pair = new Pair($this->second);//, ..); //TODO constructeur avec requete SQL?
		return $this->second;
	}
	
	public function getThird()
	{
		//$pair = new Pair($this->third);//, ..); //TODO constructeur avec requete SQL?
		return $this->third;
	}
	
	public function getCourtId()
	{
		//$court = new Court($this->courtId);//, ..); //TODO constructeur avec requete SQL?
		return $this->courtId;
	}

	public function getTournamentId()
	{
		//$tournament = new Tournament($this->tournamentId);//, ..); //TODO constructeur avec requete SQL?
		return $this->tournamentId;
	}
	
	public function getStaff()
	{
		//$staffUser = new User($this->staff);//, ..); //TODO constructeur avec requete SQL? ici Staff est l'ID de l'utilisateur
		return $this->staff;
	}

	public function edit($newFirst, $newSecond, $newThird, $newCourtId, $newTournamentId, $newStaff){
		$this->first = $newFirst;
		$this->second = $newSecond; 
		$this->third = $newThird;
		$this->courtId = $newCourtId;
		$this->tournamentId = $newTournamentId;
		$this->staff = $newStaff;
	}
}
?>