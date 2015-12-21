<?php
class UserCom
{
	private $id;
	private $txt;
	private $userId;
	//private $date;

	public function UserCom($newId, $newTxt, $newUserId) {
		if (!empty($newId)){
			$this->id = $newId;
			$this->txt = $newTxt;
			$this->userId = $newUserId;
			//$this->date = $newDate; //TODO tocheck format
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
	 /*
	public function getDate()
	{
		return $this->date;
	}
*/
	
	public function edit($newTxt, $newUserId){
		$this->txt = $newTxt;
		$this->userId = $newUserId;
		//$this->date = $newDate;

		//TODO request SQL
	}
}
?>