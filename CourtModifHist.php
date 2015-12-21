<?php
class CourtModifHist
{
	private $id;
	private $courtId;
	private $action;

	public function CourtModifHist($newId, $newCourtId, $newAction) {
		if (!empty($newId)){
			$this->id = $newId;
			$this->courtId = $newCourtId;
			$this->action = $newAction;
		}
	}

	public function getId()
	{
		return $this->id;
	}

	public function getCourtId()
	{
		//$court = new Court($this->courtId);//, ..); //TODO constructeur avec requetes SQL? 
	  	return $this->courtId;
	}

	public function getAction()
	{
		return $this->action;
	}

	public function edit($newCourtId, $newAction){
		$this->courtId = $newCourtId;
		$this->action = $newAction;

		//TODO request SQL
	}
}
?>