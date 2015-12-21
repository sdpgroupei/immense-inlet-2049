<?php
class Court
{
	private $id;
	private $name;
	private $address;
	private $isClub;
	private $geoZone;
	private $mapNum;
	private $courtInstruct;
	private $mapPic;
	private $satePic;
	private $isLend;
	private $state;
	private $answerMean;
	private $surface;
	private $owner;
	private $staff;

	public function Court($newId, $newName, $newAddress, $newIsClub, $newGeoZone, $newMapNum, $newCourtInstruct, $newMapPic, $newSatePic, $newIsLend, $newState, $newAnswerMean, $newSurface, $newOwner, $newStaff) {
		if (!empty($newId)){
			$this->id = $newId;
			$this->name = $newName;
			$this->address = $newAddress;
			$this->isClub = $newIsClub;
			$this->geoZone = $newGeoZone;
			$this->mapNum = $newMapNum;
			$this->courtInstruct = $newCourtInstruct;
			$this->mapPic = $newMapPic;
			$this->satePic = $newSatePic;
			$this->isLend = $newIsLend;
			$this->state = $newState;
			$this->answerMean = $newAnswerMean;
			$this->surface = $newSurface;	//requete direct avant le constructeur
			$this->owner = $newOwner;	//id
			$this->staff = $newStaff;	//id
		}
	}

	public function getId()
	{
		return $this->id;
	}

	public function getName()
	{
		return $this->name;
	}
	
	public function getAddress() 
	{
	  	return $this->address;
	}
	    
	public function getIsClub() 
	{
	  	return $this->isClub;
	}
	
	public function getGeoZone() 
	{
	  	return $this->geoZone;
	}
	    
	public function getMapNum() 
	{
	  	return $this->mapNum;
	}
	    
	public function getCourtInstruct() 
	{
	  	return $this->courtInstruct;
	}
	    
	public function getMapPic() 
	{
	  	return $this->mapPic;
	}
	    
	public function getSatePic() 
	{
	  	return $this->satePic;
	}
	    
	public function getIsLend() 
	{
	  	return $this->isLend;
	}
	    
	public function getState() 
	{
	  	return $this->state;
	}
	    
	public function getAnswerMean() 
	{
	  	return $this->answerMean;
	}
	    
	public function getSurface() 	//request to do before the construction, it s given as parameter for the constructor
	{
		return $this->surface;
	}
	    
	public function getOwner() 
	{
	  	//$ownerUser = new User($this->owner);//, ..); //TODO constructeur avec requete SQL?  Ici Owner est l'ID de l'utilisateur
	  	return $this->owner;
	}
	    
	public function getStaff() 
	{
		//$staffUser = new User($this->staff);//, ..); //TODO constructeur avec requete SQL? ici Staff est l'ID de l'utilisateur
	  	return $this->staff;
	}

	public function edit($newName, $newAddress, $newIsClub, $newGeoZone, $newMapNum, $newCourtInstruct, $newMapPic, $newSatePic, $newIsLend, $newState, $newAnswerMean, $newSurface, $newOwner, $newStaff) {
		$this->name = $newName;
		$this->address = $newAddress;
		$this->isClub = $newIsClub;
		$this->geoZone = $newGeoZone;
		$this->mapNum = $newMapNum;
		$this->courtInstruct = $newCourtInstruct;
		$this->mapPic = $newMapPic;
		$this->satePic = $newSatePic;
		$this->isLend = $newIsLend;
		$this->state = $newState;
		$this->answerMean = $newAnswerMean;
		$this->surface = $newSurface;
		$this->owner = $newOwner;
		$this->staff = $newStaff;

	}
}
?>