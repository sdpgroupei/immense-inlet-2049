<?php
class Tournament
{
    private $id; 
    private $name;
    private $day;

    public function Tournament($newId, $newName, $newDay) {
        if (!empty($newId)){
			$this->id = $newId;
			$this->name = $newName;
			$this->day = $newDay; //TODO toCheck Date format
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
        
    public function getDay()
    {
        return $this->day;
    }
	
	public function edit($newName, $newDay){
		$this->name = $newName;
		$this->day = $newDay;
	}
}
?>