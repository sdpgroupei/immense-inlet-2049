<?php
class Match
{
	private $id;
	private $set1;
	private $set2;
	private $set3;
	private $set4;
	private $set5;
	//private $tournamentId;
	//private $groupId;
	private $pair1;
	private $pair2;

	public function Match($newId, $newSet1, $newSet2, $newSet3, $newSet4, $newSet5, $newPair1, $newPair2) {
		if (!empty($newId)){
			$this->id = $newId;
			$this->set1 = $newSet1;	// à 0 les premières fois ou null selon l'appel qu'ils font
			$this->set2 = $newSet2;
			$this->set3 = $newSet3;
			$this->set4 = $newSet4;
			$this->set5 = $newSet5;
			//$this->tournamentId = $newTournamentId;
			//$this->groupId = $newGroupId;
			$this->pair1 = $newPair1;
			$this->pair2 = $newPair2;
		}
	}

	public function getId()
	{
		return $this->id;
	}
	    
	public function getSet1() 
	{
	  return $this->set1;
	}

	public function getSet2()
	{
		return $this->set2;
	}
	    
	public function getSet3() 
	{
	  return $this->set3;
	}
	    
	public function getSet4() 
	{
	  return $this->set4;
	}
	    
	public function getSet5() 
	{
	  return $this->set5;
	}
	
	/*public function getTournamentId()
	{
		//$tournament = new Tournament($this->tournamentId);//, ..); //TODO constructeur avec requete SQL?
		return $this->tournamentId;
	}

	public function getGroupid()
	{
		//$group = new Group($this->groupId);//, ..); //TODO constructeur avec requete SQL? ici Staff est l'ID de l'utilisateur
		return $this->groupId;
	}*/
	
	public function getPair1()
	{
		//$pair = new Pair($this->pair1);//, ..); //TODO constructeur avec requete SQL? ici Staff est l'ID de l'utilisateur
		return $this->pair1;
	}

	public function getPair2()
	{
		//$pair = new Pair($this->pair2);//, ..); //TODO constructeur avec requete SQL? ici Staff est l'ID de l'utilisateur
		return $this->pair2;
	}

	
	public function edit($newSet1, $newSet2, $newSet3, $newSet4, $newSet5, $newPair1, $newPair2){
		$this->set1 = $newSet1;
		$this->set2 = $newSet2;
		$this->set3 = $newSet3;
		$this->set4 = $newSet4;
		$this->set5 = $newSet5;
		//$this->tournamentId = $newTournamentId;
		//$this->groupId = $newGroupId;
		$this->pair1 = $newPair1;
		$this->pair2 = $newPair2;
	}
}
?>