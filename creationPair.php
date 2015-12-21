<?php

require_once('DatabaseHandler.php');

/*
  Retourne $nb pair qui ont le même rank ou un rank qui se rapproche
*/
function getXPairByRank($pairs, $pair, $nb){

  $result = [];
  if($nb < count($pairs)){
    array_push($result, $pair);
    $rankPair = getRankOfPair($pair);

    while(count($result) < $nb){
      $tmp = getPairSameRank($pairs, $rankPair);
      array_push($result, $tmp);
      $key = array_search($tmp, $pairs);
      unset($pairs[$key]);

    }

    return $result;
  }else{
    return $pairs;
  }
}
/*
  Retourne une paire de la liste $pairs qui possède le même rank que $rank
*/
function getPairSameRank($pairs, $rank){
  $result = NULL;
  while(is_null($result)){
    foreach ($pairs as $p ){
      if(getRankOfPair($p) == $rank){
          $result = $p;
      }
    }
    $rank--;
  }
  return $result;
}
/*
  Retourne le rank d'une paire. CAD le rank le plus haut de la paire
*/
function getRankOfPair($pair){
  if($pair[0]['rank'] >= $pair[1]['rank']){
    return $pair[0]['rank'];
  }else{
    return $pair[1]['rank'];
  }
}
/*
  Transforme un tableau dans la structure de pair de la DB
*/
function transformPairsToDb($pairs, $idTournament, $db){
  $result = array();

  foreach ($pairs as $key => $value) {
    $tmp = array();
    $tmp['user_j1_fk'] = $value[0][0]['idusers'];
    $tmp['user_j2_fk'] = $value[1][0]['idusers'];
    $tmp['tournament_fk'] = $idTournament;
    $tmp['solo'] = 0;

    $leader1 = getLeaderByIdSoloPair($value[0][0]['idusers'],$idTournament, $db);
    $leader2 = getLeaderByIdSoloPair($value[1][0]['idusers'],$idTournament, $db);

    if(($leader1['leader'] == 1) || ($leader2['leader'] == 1)){
      $tmp['leader'] = 1;
    }else{
      echo " FALSE ";
      $tmp['leader'] = 0;
    }

    $result[] = $tmp;
  }

  return $result;
}
/*
  Retourne la premiere pair de $pairs qui souhaite etre leader de groupe
*/
function getLeaderOfGroup($pairs){
  foreach ($pairs as $key => $value) {
    if($value['leader'] == 1){
      return $value['idpairs'];
    }
  }
  return NULL;
}
/*
  Fonction qui assemble les pairs solo en semble et qui cree les groupes et leur assigne un terrain
*/
function creerTournoi($nbPairByGroup, $idTournament, $pluie, $dbh){
  // var_dump($nbPairByGroup);
  // var_dump($idTournament);
  // var_dump($pluie);
  // echo "-----------------players    ";
  $players = getSoloPlayers($idTournament, $dbh);
  // print_r(count($players));

  // echo "-----------------pairs    ";
  $pairs = makingPair($players);
  // print_r(count($pairs));

  // echo "-----------------insert    ";
  insertPairs(transformPairsToDb($pairs, $idTournament, $dbh) , $dbh);

  // echo "-----------------cours :    ";
  $cours = getAllCourts($idTournament, $pluie, $dbh);
  // print_r(count($cours));

  // echo "-----------------pairs :    ";
  $pairs = getPairs($idTournament, $dbh);
  // print_r(count($pairs));

  // echo "-----------------groups :    ";
  $nbGroup = 0; //n-1
  $group = [];
  while(!empty($pairs)){
    // echo "[GROUP START NUM=".$nbGroup."]";
    // echo "pairs = ".count($pairs);
    $group[$nbGroup]['pairs'] = getXPairByRank($pairs, array_shift($pairs), $nbPairByGroup);

    $group[$nbGroup]['leader'] = getLeaderOfGroup($group[$nbGroup]['pairs']);

    // echo "COUNT___________".count($group[$nbGroup]['pairs'])."__________COUNT";
    // var_dump($group[$nbGroup]['pairs']);
    //var_dump($group[$nbGroup]['leader']);
    // echo "pairs = ".count($pairs);
    $key = array_search($group[$nbGroup]['pairs'][1], $pairs);
    unset($pairs[$key]);
    /*foreach ($group[$nbGroup]['pairs'] as $p) {
      $key = array_search($p, $pairs);
      unset($pairs[$key]);
    }*/
    // echo "pairs = ".count($pairs);
    // echo "[GROUP END NUM = ".$nbGroup."]";
    $nbGroup++;
  }
  // var_dump(count($group));
  // die('GROUPS');
  // echo "-----------------groups and cours :    ";
  set_time_limit(0);
  foreach ($group as $k => $g) {
    $courtTmp = getNearCours($cours, $g['pairs'], $dbh);

    $key = array_search($courtTmp, $cours);
    unset($cours[$key]);

    $group[$k]['court_fk'] = $courtTmp;

    $groupTmp = [
      'court_fk' => $courtTmp['idcourts'],
      'tournament_fk' => $idTournament
    ];

    if(!is_null($g['leader'])){
      $groupTmp['leader'] = $g['leader'];
    }

    $idGroup = insertGroup($groupTmp, $dbh);

    $group[$k]['group_id'] = $idGroup;

    foreach ($g['pairs'] as $kPair => $valuePair) {
      addGroupFkPair($valuePair['idpairs'], $idGroup, $dbh);
      $group[$k]['pairs'][$kPair]['joueur1'] = getPlayerById($valuePair['user_j1_fk'],$dbh);
      $group[$k]['pairs'][$kPair]['joueur2'] = getPlayerById($valuePair['user_j2_fk'],$dbh);
    }

  }

  return $group;

}
/*
  Retourne la distance entre les deux joueurs de la pair et un court
*/
function getDistancePairOfCour($cour, $pair, $db){
  $total = 0;
  $user1 = getPlayerById($pair['user_j1_fk'],$db);
  $user2 = getPlayerById($pair['user_j2_fk'],$db);
  $total += getDistance($cour['court_address'], $user1['address']);
  $total += getDistance($cour['court_address'], $user2['address']);
  return $total;
}
/*
  Retourne la distance total des joueurs d'un groupe et un court
*/
function getDistanceTotalOfGroup($cour, $group, $db){

  $total = 0;
  foreach ($group as $p) {
    $total += getDistancePairOfCour($cour, $p, $db);
  }
  return $total;
}
/*
  Retoune le court le plus près pour tous les joueurs d'un groupe
*/
function getNearCours($cours, $group, $db){
  $courtMin = end($cours);
  $distCourtMin = getDistanceTotalOfGroup($courtMin,$group, $db);

  foreach ($cours as $c) {
    $tmp = getDistanceTotalOfGroup($c,$group, $db);

    if($tmp < $distCourtMin){
      $distCourtMin = $tmp;
      $courtMin = $c;
    }
  }
  return $courtMin;
}
/*
  Retourne tous les joueurs dont le rank est égal à $rank
*/
function getPlayerClassement($players, $rank){
  $playersSameClassement = [];
  foreach ($players as $p) {
    if($rank == $p['rank']){
      array_push($playersSameClassement,$p);
    }
  }
  if(!empty($playersSameClassement)){
    return $playersSameClassement;
  }else{
    return NULL;
  }
}

/*
  Crée des pairs tant qu'il y a des gens dans le tableau
  IL NE FAUT PAS UN NOMBRE IMPAIR DE JOUEUR
  VERIFIER QUE LE JOUEUR EST INSCRIT
*/
function makingPair($soloPlayers){
  $pairs = [];
  while(count($soloPlayers) > 1){
    $first = array_shift($soloPlayers);
    $rankP = $first['rank'];
    do{
      $listesPlayers = getPlayerClassement($soloPlayers, $rankP);
      $rankP--;
    }while(!$listesPlayers);
    $pair = trouverPartenaire($first,$listesPlayers);
    array_push($pairs,$pair);
    $key = array_search($pair[1], $soloPlayers);
    unset($soloPlayers[$key]);
  }
  return $pairs;
}

/*
  Retourne la pair avec le partenaire du joueur passé en argument et celui-ci
*/
function trouverPartenaire($player, $soloPlayers){
  //$first =  array_shift($soloPlayers);
  $procheP = $soloPlayers[0];
  $distanceMin = getDistance($player['address'],$procheP['address']);
  foreach($soloPlayers as $p){
    $tmp = getDistance($player['address'], $p['address']);
    if($tmp < $distanceMin){
      $procheP = $p;
      $distanceMin = $tmp;
    }
  }
  return [$player,$procheP];
}

/*
  Consulte l'API GOOGLE pour calculer la distance entre deux adresses
  status = OK
  status = INVALID_REQUEST
*/
function getDistance($from, $to){
  $from = urlencode($from);
  $to = urlencode($to);
  $data = file_get_contents("http://maps.googleapis.com/maps/api/distancematrix/json?origins=$from&destinations=$to&language=en-EN&sensor=false");
  $dataPHP = json_decode($data,true);

  $distance = 0;
  if($dataPHP["status"] == "OK"){
    /*foreach($dataPHP->rows[0]->elements as $road) {
        $distance += $road->distance->value;
    }*/
    $distance += $dataPHP['rows'][0]['elements'][0]['distance']['value'];
  }
  return $distance;

}
