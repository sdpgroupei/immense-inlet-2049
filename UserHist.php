<?php
class UserHist
{
	private $id;
	private $txt;
	private $userId;
	//private $tournamentId;

	public function UserHist($newId, $newTxt, $newUserId) {
		if (!empty($newId)){
			$this->id = $newId;
			$this->txt = $newTxt;
			$this->userId = $newUserId;
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
	 
	public function getUserId()
	{
	//$user = new User($this->userId);//, ..); //TODO constructeur avec requete SQL?
	return $this->userId;
	}
	 

	public function edit($newTxt, $newUserId){
		$this->txt = $newTxt;
		$this->userId = $newUserId;
	}
}
?>