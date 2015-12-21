<?php
class Pair
{
	private $id;
	private $groupId;
	private $tournamentId;
	private $userId0;
	private $userId1;
	private $isSoloPlayer;

	public function Pair($newId, $newGroupId, $newTournamentId, $newUserId0, $newUserId1, $newSolo) {
		if (!empty($newId)){
			$this->id = $newId;
			$this->groupId = $newGroupId;
			$this->tournamentId = $newTournamentId;
			$this->userId0 = $newUserId0;
			$this->userId1 = $newUserId1;
			$this->isSoloPlayer = $newSolo;
		}
	}

	public function getId()
	{
		return $this->id;
	}

	public function getGroupId()
	{
		//$group = new Group($this->groupId);//, ..); //TODO constructeur avec requete SQL?
		return $this->groupId;
	}
	 
	public function getTournamentId()
	{
		//$tournament = new Tournament($this->tournamentId);//, ..); //TODO constructeur avec requete SQL?  
		return $this->tournamentId;
	}
	 
	public function getUserId0()
	{
		//$user = new User($this->userId0);//, ..); //TODO constructeur avec requete SQL?  
		return $this->userId0;
	}
	 
	public function getUserId1()
	{
		//$user = new User($this->userId1);//, ..); //TODO constructeur avec requete SQL? 
		return $this->userId1;
	}
	public function getIsSoloPlayer()
	{
		//$user = new User($this->userId1);//, ..); //TODO constructeur avec requete SQL? 
		return $this->isSoloPlayer;
	}

	public function edit($newGroupId, $newTournamentId, $newUserId0, $newUserId1, $newSolo){
		$this->groupId = $newGroupId;
		$this->tournamentId = $newTournamentId;
		$this->userId0 = $newUserId0;
		$this->userId1 = $newUserId1;
		$this->isSoloPlayer = $newSolo;

	}
}
?>