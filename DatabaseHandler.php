<?php

class DatabaseHandler {
	//STATIC TYPES OF OBJECT AVAILABLE
	static $USER = 'users';
	static $USER_HIST = 'users_history';
	static $USER_COMS = 'users_comments';
	static $TOURNAMENT = 'tournaments';
	static $COURT = 'courts';
	static $GROUP = 'groups';
	static $COURT_COMS = 'court_comments';
	static $COURT_EDIT_HIST = 'court_editions_history';
	static $COURT_MODIF_HIST = 'court_modifications_history';
	static $PAIR = 'pairs';
	static $MATCH = 'match';
	static $SURFACES = 'surfaces';
	static $USER_TYPE = 'users_type';

	//EVERY ARGUMENT DEPENDING OF THE OBJECT
	static $ARGUMENTS = array(
			self::USER => array('title', 'last_name', 'first_name', 'address', 'phone_number', 'e_mail_address', 'ranking', 'creation_date', 'password', 'user_type_fk', 'method_of_payement'), //user : method_of_payement
			self::USER_HIST => array('history_action', 'user_fk'),
			self::USER_COMS => array('comment_text', 'user_fk'),
			self::TOURNAMENT => array('name', 'day'),
			self::COURT => array('court_real_id', 'court_address', 'is_club', 'graphical_zone', 'map_number', 'court_instructions', 'map_picture', 'sattelite_picture', 'is_lend', 'court_state', 'answer_mean', 'surface_fk', 'owner_user_fk', 'staff_user_fk'),
			self::GROUP => array('first', 'second', 'third', 'court_fk', 'tournament_fk', 'staff_user_fk'),
			self::COURT_COMS => array('court_comment_text', 'court_fk'),
			self::COURT_EDIT_HIST => array('action', 'court_fk'),
			self::COURT_MODIF_HIST => array('action', 'court_fk'),
			self::PAIR => array('user_j1_fk', 'user_j2_fk', 'group_fk', 'tournament_fk', 'solo'), //pair : solo field added
			self::MATCH => array('set_one_results', 'set_two_results', 'set_three_results', 'set_four_results', 'set_five_results', 'winner_pair_fk', 'looser_pair_fk'),
			self::SURFACES => array('surface_type'),
			self::USER_TYPE => array('user_type_rule') );
}

function create($data, $type, $db){

	//INSERT THE NEW OBJECT IN THE PROPER TABLE OF THE DATABASE
	$query = "INSERT INTO ".$type." (";
	for($i = 0; $i < count(DatabaseHandler::ARGUMENTS[$type]); $i++) { //get the arguments array and take the sub-array according to the type
		$query .= DatabaseHandler::ARGUMENTS[$type][$i];

		if($i != count(DatabaseHandler::ARGUMENTS[$type])-1){
			$query .= ", "; //if last one, not put ',' after the variable (cauz it's the last one)
		}
	}

	$query .= ") VALUES ("; //should look like 'INSERT INTO xxxxx (a, b, c, ... ) VALUES (

	for($i = 0; $i < count($data); $i++) { //match datas to arguments
		if(is_string($data[DatabaseHandler::ARGUMENTS[$type][$i]])){ //put the ' if it's a string
			$query .= "'".$data[DatabaseHandler::ARGUMENTS[$type][$i]]."'";
		}else{
			$query .= $data[DatabaseHandler::ARGUMENTS[$type][$i]];
		}

		if($i != count($data)-1){
			$query .= ", "; //if last one, not put ',' after the variable (cauz it's the last one)
		}
	}
	$query .= ")"; //should look like 'INSERT INTO xxxxx (a, b, c, ...) VALUES (xa, xb, xc, ...)

	$st = $db->prepare($query); //use the string as a query
	$result = $st->execute(); //execute the query (cross fingers)

	return $result;
}

function edit($data, $type, $db){
	//UPDATE tutorials_tbl SET tutorial_title="Learning JAVA" WHERE tutorial_id=3
	//return objet modifié
	//$id = $object->getId();


	$query = "UPDATE ".$type." SET ";
	for($i = 0, $l = count($data); $i < $l; $i++) { //make the select conditions
		// exemple  =>  title = 'coucou bonjour à tous'
		$query .= DatabaseHandler::ARGUMENTS[$type][$i]." = ";
		if(is_string($data[DatabaseHandler::ARGUMENTS[$type][$i]])){ //put the ' if it's a string
			$query .= "'".$data[DatabaseHandler::ARGUMENTS[$type][$i]]."'";
		}else{
			$query .= $data[DatabaseHandler::ARGUMENTS[$type][$i]];
		}

		if($i != $l-1) $query .= ', '; //if last one, not put ',' after the variable (cauz it's the last one)
	}
	$query .= ' WHERE id'.$type.' = :id';

	$st = $db->prepare($query); //use the string as a query
	//$st->bindParam('id'.$type, $id ,PDO::PARAM_INT); //récup sous forme d'entier l'ID et le lie à notre variable
	$result = $st->execute(); //execute the query (cross fingers)
	return $result;
}

function getPlayerById($id, $db){
	$st = $db->prepare('SELECT * FROM users WHERE idusers = '.$id);
	$st->execute();
	return $st->fetch(PDO::FETCH_ASSOC);
}

function getSoloPlayers($idTournament, $db){
	$st1 = $db->prepare('SELECT * FROM pairs WHERE solo = 1 AND tournament_fk = '.$idTournament);
	$st1->execute();
	$players = array();

	$solos = $st1->fetchAll(PDO::FETCH_ASSOC);
	/*while ($row = $st1->fetch(PDO::FETCH_ASSOC)) { //get all row selected
		$courts[] = $row;
	}*/

	foreach ($solos as $key => $value) {
		$st2 = $db->prepare('SELECT * FROM users WHERE idusers = '.$value['user_j1_fk']);
		$st2->execute();

		//$players = $st2->fetchAll(PDO::FETCH_ASSOC);
		$players[] = $st2->fetchAll(PDO::FETCH_ASSOC);
	}

	//$st3 = $db->prepare('DELETE FROM pairs WHERE solo=1 AND tournament_fk='.$idTournament);
	//$st3->execute();

	return $players;

}

function addGroupFkPair($idPair, $idGroup, $db){
	$query = "UPDATE pairs SET group_fk=".$idGroup." WHERE idpairs=".$idPair;
	$st = $db->prepare($query); //use the string as a query
	$result = $st->execute(); //execute the query (cross fingers)

	return $result;
}

function editPairs($listPairs, $db){
	for($i = 0, $l = count($listPairs); $i < $l; ++$i) {
		edit($listPairs[i], DatabaseHandler::PAIR, $db);
	}
}

function insertPairs($listPairs, $db){
	foreach ($listPairs as $key => $value) {
		createGen($value, 'pairs', $db);
	}
}

function createGen($data, $type, $db){
	$query = "INSERT INTO ".$type." (";
	$queryData = " VALUES (";

	$nbArg = 0;
	foreach ($data as $key => $value) {
		$nbArg++;
		$query .= $key;
		if (is_string($value)){
			$queryData .= "'".$value."'";
		}else{
			$queryData .= $value;
		}

		if($nbArg < count($data)){
			$query .= ', ';
			$queryData .= ", ";
		}
	}
	$query .= ' )';
	$queryData .= " )";

	$final = $query.$queryData;
	$st = $db->prepare($final); //use the string as a query
	$result = $st->execute(); //execute the query (cross fingers)

	return $db->lastInsertId('groups_idgroups_seq');
}

function insertGroups($listGroups, $db){
	for($i = 0, $l = count($listGroups); $i < $l; ++$i) {
		create($listGroups[i], DatabaseHandler::GROUP, $db);
	}
}

function insertGroup($group, $db){
	//create($group, DatabaseHandler::GROUP, $db);
	$id = createGen($group, "groups", $db);
	return $id;
}

function getLeaderByIdSoloPair($id,$idTournament, $db){
	$st = $db->prepare('SELECT leader FROM pairs WHERE user_j1_fk='.$id.' AND solo=1 AND tournament_fk='.$idTournament);
	$st->execute();

	$result = $st->fetch(PDO::FETCH_ASSOC);

	return $result;
}

function getPairs($idTournament, $db){

	$st = $db->prepare('SELECT * FROM pairs WHERE tournament_fk='.$idTournament.' AND solo=0 AND group_fk IS NULL');
	$st->execute();

	$result = $st->fetchAll(PDO::FETCH_ASSOC);

	return $result;
}

function getAllCourts($idTournament, $pluie, $db){
	$query = "SELECT * FROM courts WHERE tournament_fk = ".$idTournament;

	if($pluie){
		$query .= " AND isindoor = 1";
	}

	$st1 = $db->prepare($query);
	$st1->execute();
	$courts = array();

	while ($row = $st1->fetch(PDO::FETCH_ASSOC)) { //get all row selected
		$courts[] = $row;
	}
	return $courts;
}
function getAllTournaments($db){
	$st1 = $db->prepare('SELECT * FROM tournaments');
	$st1->execute();
	$tournaments = array();

	while ($row = $st1->fetch(PDO::FETCH_ASSOC)) { //get all row selected
		$tournaments[] = $row;
	}
	return $tournaments;
}
