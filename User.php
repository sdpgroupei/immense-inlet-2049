<?php
class User
{
	private $id;
	private $title;
	private $lastName;
	private $firstName;
	//private $birthDate;
	//private $gender;
	//private $street;
	//private $number;
	//private $box;
	//private $postCode;
	//private $city;
	private $type;
	//private $gsm;
	private $phone;
	private $email;
	private $ranking;
	private $password;
	private $date;
	private $address; //new
	private $method_of_payement; //new

	public function User($newId, $newTitle, $newLastName, $newFirstName, $newAddress, $newType, $newPhone, $newEmail, $newRanking, $newPassword, $newDate, $newMethod_of_payement) {
		if (!empty($newId)){
			$this->id = $newId;
			$this->title = $newTitle;
			$this->lastName = $newLastName;
			$this->firstName = $newFirstName;
			/*$this->birthDate = $newBirthDate;	//TODO check format
			$this->gender = $newGender;
			$this->street = $newStreet;
			$this->number = $newNumber;
			$this->box = $newBox;
			$this->postCode = $newPostCode;
			$this->city = $newCity;*/
			$this->type = $newType;		//Directement tirer avant le constructeur
			//$this->gsm = $newGsm;
			$this->phone = $newPhone;
			$this->email = $newEmail;
			$this->ranking = $newRanking;
			$this->password = $newPassword;
			$this->date = $newDate; 	//TODO check format
			$this->address = $newAddress;
			$this->method_of_payement = $newMethod_of_payement;
		}
	}

	public function getId()
	{
		return $this->id;
	}

	public function getTitle() 
	{
	  return $this->title;
	}
	    
	public function getLastName() 
	{
	  return $this->lastName;
	}
	    
	public function getFirstName() 
	{
	  return $this->firstName;
	}
	/*    
	public function getBirthDate() 
	{
	  return $this->birthDate;
	}
	    
	public function getGender() 
	{
	  return $this->gender;
	}
	    
	public function getStreet() 
	{
	  return $this->street;
	}
	    
	public function getNumber() 
	{
	  return $this->number;
	}
	    
	public function getBox() 
	{
	  return $this->box;
	}
	    
	public function getPostCode() 
	{
	  return $this->postCode;
	}
	    
	public function getCity() 
	{
	  return $this->city;
	}
	    */
	public function getType() 
	{
	  return $this->type;
	}
	 /*   
	public function getGsm() 
	{
	  return $this->gsm;
	}*/
	
	public function getPhone() 
	{
	  return $this->phone;
	}
	    
	public function getEmail() 
	{
	  return $this->email;
	}
	    
	public function getRanking() 
	{
	  return $this->ranking;
	}
	    
	public function getPassword() 
	{
	  return $this->password;
	}
	    
	public function getDate() 
	{
	  return $this->date;
	}	
	    
	public function getAddress() 
	{
	  return $this->address;
	}	

	public function edit($newTitle, $newLastName, $newFirstName, $newAddress, $newType, $newPhone, $newEmail, $newRanking, $newPassword, $newDate, $newMethod_of_payement){
		$this->id = $newId;
		$this->title = $newTitle;
		$this->lastName = $newLastName;
		$this->firstName = $newFirstName;
		/*$this->birthDate = $newBirthDate;	//TODO check format
		$this->gender = $newGender;
		$this->street = $newStreet;
		$this->number = $newNumber;
		$this->box = $newBox;
		$this->postCode = $newPostCode;
		$this->city = $newCity;*/
		$this->type = $newType;		//Directement tirer avant le constructeur
		//$this->gsm = $newGsm;
		$this->phone = $newPhone;
		$this->email = $newEmail;
		$this->ranking = $newRanking;
		$this->password = $newPassword;
		$this->date = $newDate;
		$this->address = $newAddress;
		$this->method_of_payement = $newMethod_of_payement;
	}
}
?>