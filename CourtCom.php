<?php
class CourtCom
{
	private $id;
	private $txt;
	private $courtId;
	//private $userId;
	//private $date;

	public function CourtCom($newId, $newTxt, $newCourtId) {
		if (!empty($newId)){
			$this->id = $newId;
			$this->txt = $newTxt;
			$this->courtId = $newCourtId;
			//$this->userId = $newUserId;
			//$this->date = $newDate;
		}
	}

	public function getId()
	{
		return $this->id;
	}

	public function getTxt()
	{
		return $this->txt;
	}
	    
	public function getCourtId() 
	{
		//$court = new Court($this->courtId);//, ..); //TODO constructeur avec requetes SQL? 
	  	return $this->courtId;
	}
	    
	/*public function getUserId() 
	{
		//$user = new User($this->userId);//, ..); //TODO constructeur avec requete SQL? 
	  	return $this->userId;
	}
	 
	public function getDate()
	{
		return $this->date;
	}
*/
	public function edit($newTxt, $newCourtId){
		$this->txt = $newTxt;
		$this->courtId = $newCourtId;
		/*$this->userId = $newUserId;
		$this->date = $newDate;*/

		//TODO request SQL
	}
}
?>