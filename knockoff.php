<?php
/**
 * Php file containing the controllers related to the
 * knockoff and the utilisation of a super user
 *
 */

use Silex\Provider\FormServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


//----------------------------------------------------------------------
//              knockoff controllers
//----------------------------------------------------------------------


/*
 * knocktree display for the user simple admin.
 * they are allowed to choose the knocktree for a specific tournament
 * and to modified it in a live version (javascript) then they can either save
 * their changes or not
 */

$app->match('/knockoff', function (Request $request) use ($app) {



  // check if staff member
  $supuser = $app['session']->get('staff');
  if($supuser == null){
    return $app['twig']->render('illegal.twig', array('group' => false));
  }else {
    if ($supuser['staff'] == false) {
          return $app['twig']->render('illegal.twig', array('group' => false));
        }
  }

  $perfecttree = false;
  $issubmit = false;


  // request selecting the available tournament
  $st = $app['pdo']->prepare('SELECT idtournaments, name, day FROM tournaments');
  $st->execute();

  $tournamentvalue = array();
  while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
    $tournamentvalue[] = $row;
  }

  $renderingarray = array();
  foreach ($tournamentvalue as $value) {

    $renderingarray[$value['idtournaments']] = $value['name']  .'('. $value['day'].')';
  }

  $formtournament = $app['form.factory']->createBuilder('form')
      ->add('idtournaments', 'choice', array(
            'choices' => $renderingarray,
            'label' => false
      ))
      ->getForm();

  // variables needed in the twig
  $tournament_fk = 0;
  $treeinfo = array();
  $treesize = 16;
  $count = 0;
  $idtree = 0;
  $courtinfo = array();
  $court = array();
  $newcount = 0;


  // hidden form saving the tree state
  $formtree = $app['form.factory']->createBuilder('form')
      ->add('_16_1', 'hidden')
      ->add('_16_2', 'hidden')
      ->add('_16_3', 'hidden')
      ->add('_16_4', 'hidden')
      ->add('_16_5', 'hidden')
      ->add('_16_6', 'hidden')
      ->add('_16_7', 'hidden')
      ->add('_16_8', 'hidden')
      ->add('_16_9', 'hidden')
      ->add('_16_10', 'hidden')
      ->add('_16_11', 'hidden')
      ->add('_16_12', 'hidden')
      ->add('_16_13', 'hidden')
      ->add('_16_14', 'hidden')
      ->add('_16_15', 'hidden')
      ->add('_16_16', 'hidden')
      ->add('_8_1', 'hidden')
      ->add('_8_2', 'hidden')
      ->add('_8_3', 'hidden')
      ->add('_8_4', 'hidden')
      ->add('_8_5', 'hidden')
      ->add('_8_6', 'hidden')
      ->add('_8_7', 'hidden')
      ->add('_8_8', 'hidden')
      ->add('_4_1', 'hidden')
      ->add('_4_2', 'hidden')
      ->add('_4_3', 'hidden')
      ->add('_4_4', 'hidden')
      ->add('_2_1', 'hidden')
      ->add('_2_2', 'hidden')
      ->add('win', 'hidden')
      ->add('treesize', 'hidden')
      ->add('tournament_fk', 'hidden')
      ->add('id', 'hidden')
      ->add('save', 'submit')
      ->getForm();

  // set handler for the hidden form that save the tree state
  $formtree->handleRequest($request);
  if($formtree->isSubmitted() && $formtree->get('save')->isClicked()){
    $data = $formtree->getData();
    $st1 = $app['pdo']->prepare('INSERT INTO knockofftree (
      _16_1 ,
      _16_2  ,
      _16_3  ,
      _16_4  ,
      _16_5  ,
      _16_6  ,
      _16_7  ,
      _16_8  ,
      _16_9  ,
      _16_10  ,
      _16_11  ,
      _16_12  ,
      _16_13  ,
      _16_14  ,
      _16_15  ,
      _16_16  ,
      _8_1 ,
      _8_2  ,
      _8_3  ,
      _8_4  ,
      _8_5  ,
      _8_6  ,
      _8_7  ,
      _8_8  ,
      _4_1 ,
      _4_2  ,
      _4_3  ,
      _4_4  ,
      _2_1 ,
      _2_2  ,
      winner,
      treesize,
      idamdin,
      tournament_fk) values (
        :_16_1 ,
        :_16_2  ,
        :_16_3  ,
        :_16_4  ,
        :_16_5  ,
        :_16_6  ,
        :_16_7  ,
        :_16_8  ,
        :_16_9  ,
        :_16_10  ,
        :_16_11  ,
        :_16_12  ,
        :_16_13  ,
        :_16_14  ,
        :_16_15  ,
        :_16_16  ,
        :_8_1 ,
        :_8_2  ,
        :_8_3  ,
        :_8_4  ,
        :_8_5  ,
        :_8_6  ,
        :_8_7  ,
        :_8_8  ,
        :_4_1 ,
        :_4_2  ,
        :_4_3  ,
        :_4_4  ,
        :_2_1 ,
        :_2_2  ,
        :win,
        :size,
        (SELECT idusers FROM users where e_mail_address = :e),
        :tournament_fk
      ) ' );
      $user = $app['session']->get('staff');

    $st1->execute(array(
      '_16_1'=> $data['_16_1'] ,
      '_16_2' => $data['_16_2'] ,
      '_16_3' => $data['_16_3'] ,
      '_16_4' => $data['_16_4'] ,
      '_16_5' => $data['_16_5'] ,
      '_16_6' => $data['_16_6'],
      '_16_7' => $data['_16_7'] ,
      '_16_8' => $data['_16_8'] ,
      '_16_9' => $data['_16_9'] ,
      '_16_10' => $data['_16_10'] ,
      '_16_11' => $data['_16_11'] ,
      '_16_12'=> $data['_16_12']  ,
      '_16_13' => $data['_16_13'] ,
      '_16_14' => $data['_16_14'] ,
      '_16_15' => $data['_16_15'] ,
      '_16_16' => $data['_16_16'] ,
      '_8_1'=> $data['_8_1'] ,
      '_8_2' => $data['_8_2'] ,
      '_8_3' => $data['_8_3'] ,
      '_8_4' => $data['_8_4'] ,
      '_8_5' => $data['_8_5'] ,
      '_8_6' => $data['_8_6'],
      '_8_7' => $data['_8_7'] ,
      '_8_8' => $data['_8_8'] ,
      '_4_1'=> $data['_4_1'] ,
      '_4_2' => $data['_4_2'] ,
      '_4_3' => $data['_4_3'] ,
      '_4_4' => $data['_4_4'] ,
      '_2_1'=> $data['_2_1'] ,
      '_2_2' => $data['_2_2'] ,
      'win'=> $data['win'],
      'size' => $data['treesize'],
      'e'=> $user['email'],
      'tournament_fk'=> $data['tournament_fk']
    ));


    // update the knockoff id of the corresponding court repartition

    $st9 = $app['pdo']->prepare('UPDATE knockoffcourt
      set idcourt = (SELECT idtree from knockofftree order by idtree DESC LIMIT 1 OFFSET 0)
      where idcourt = :id
    ');

    $st9->execute(array('id' => $data['id']));

    // return on the homepage  after saving the tree state
    return $app->redirect('/test');
  }

  // handler for the selection of the tournament
  $formtournament->handleRequest($request);
  if($formtournament->isSubmitted()){
    $data = $formtournament->getData();
    // set le tournament_fk
    $tournament_fk = $data['idtournaments'];

    $issubmit = true;

    $st5 = $app['pdo']->prepare('SELECT
      _16_1 ,
      _16_2  ,
      _16_3  ,
      _16_4  ,
      _16_5  ,
      _16_6  ,
      _16_7  ,
      _16_8  ,
      _16_9  ,
      _16_10  ,
      _16_11  ,
      _16_12  ,
      _16_13  ,
      _16_14  ,
      _16_15  ,
      _16_16  ,
      _8_1 ,
      _8_2  ,
      _8_3  ,
      _8_4  ,
      _8_5  ,
      _8_6  ,
      _8_7  ,
      _8_8  ,
      _4_1 ,
      _4_2  ,
      _4_3  ,
      _4_4  ,
      _2_1 ,
      _2_2  ,
      winner,
      treesize,
      idtree
      from knockofftree where tournament_fk = :tournament
       order by idtree DESC LIMIT 1 OFFSET 0');
    $st5->execute(array(
      'tournament'=> $tournament_fk,
    ));

    // fetching the most recent tree state
    while ($row3 = $st5->fetch(PDO::FETCH_ASSOC)) {
      $treeinfo[] = $row3;
      $count++;
    }

    // should be only one tree selected
    if($count == 1) {
      $treesize = $treeinfo[0]['treesize'];
      $treeinfo = $treeinfo[0];
      $idtree = $treeinfo['idtree'];
    }

    $st6 = $app['pdo']->prepare('SELECT idcourts, court_address from courts, knockoffcourt where _8_1 = idcourts OR _8_2 = idcourts OR _8_3 = idcourts OR _8_4 = idcourts OR  _8_7 = idcourts OR _8_8 = idcourts OR final = idcourts
       AND idcourt = :id
      ');
    $st6->execute(array(
      'id'=> $idtree,
    ));

    while ($row6 = $st6->fetch(PDO::FETCH_ASSOC)) {
      $court[] = $row6;

    }


    $st7 = $app['pdo']->prepare('SELECT
      _8_1 ,
      _8_2  ,
      _8_3  ,
      _8_4  ,
      _8_5  ,
      _8_6  ,
      _8_7  ,
      _8_8  ,
      _4_1 ,
      _4_2  ,
      _4_3  ,
      _4_4  ,
      _2_1 ,
      _2_2  ,
      final,
      idcourt from knockoffcourt where idcourt = :id
      ');
    $st7->execute(array(
      'id'=> $idtree,
    ));


    while ($row7 = $st7->fetch(PDO::FETCH_ASSOC)) {
      $courtinfo[] = $row7;
      $newcount++;
    }

    if($newcount == 1){
      $courtinfo = $courtinfo['0'];
    }







    $tmp = $app['session']->get('superuser'); # we check if the user is connected as a superuser.
    if($tmp == null){
      $su = false;
    } else {
      $su = $tmp['superuser'];
    }
    return $app['twig']->render('knockoffuser.twig', array(
          'treesize'=> $treesize,
          'treeinfo'=> $treeinfo,
          'count'=> $count,
          'formtree'=> $formtree->createView(),
          'formtournament' => $formtournament->createView(),
          'issubmit'=> $issubmit,
          'tournament_fk' => $tournament_fk,
          'idtree'=> $idtree,
          'courtinfo' => $courtinfo,
          'court' => $court,
          'newcount'=> $newcount,
          'superuser' => $su ));
  }

  $tmp = $app['session']->get('superuser'); # we check if the user is connected as a superuser.
  if($tmp == null){
    $su = false;
  } else {
    $su = $tmp['superuser'];
  }
  return $app['twig']->render('knockoffuser.twig', array(
        'treesize'=> $treesize,
        'treeinfo'=> $treeinfo,
        'count'=> $count,
        'formtree'=> $formtree->createView(),
        'formtournament' => $formtournament->createView(),
        'issubmit'=> $issubmit,
        'tournament_fk' => $tournament_fk,
        'idtree'=> $idtree,
        'courtinfo' => $courtinfo,
        'court'=> $court,
        'newcount'=> $newcount,
        'superuser' => $su ));
});


/*
 * generate the knockoff for a speficied tournament and for a speficied
 * number of selected players from the groups
 * Attention : only done by a super user
 */
$app->match('/knockoffsuperuser', function (Request $request) use ($app) {

  // check if superuser
  $supuser = $app['session']->get('superuser');
  if($supuser == null){
    return $app['twig']->render('illegal.twig', array('group' => false));
  }else {
    if ($supuser['superuser'] == false) {
          return $app['twig']->render('illegal.twig', array('group' => false));
        }
  }

  // request selecting available tournament
  $st = $app['pdo']->prepare('SELECT idtournaments, name, day FROM tournaments');
  $st->execute();

  $tournamentvalue = array();
  while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
    $tournamentvalue[] = $row;
  }

  $renderingarray = array();
  foreach ($tournamentvalue as $value) {
    # code...
    $renderingarray[$value['idtournaments']] = $value['name']  .' ('. $value['day'].')';
  }




  // global variables needed for the twig
  $issubmit = false;
  $perfecttree = false;
  $treesize = 16;
  $tomanypairs = false;

  // form generation to select a tournament
  $formknockoff = $app['form.factory']->createBuilder('form')
      ->add('top', 'choice', array(
            'choices' => array( '1' => '1','2'=>'2', '3' =>'3'),
            'label' => false
      ))
      ->add('indoor', 'choice', array(
            'choices' => array( '1' => 'Terrain Couvert','0'=>'Terrain Extérieur'),
            'label'=>false
      ))
      ->add('idtournaments', 'choice', array(
            'choices' => $renderingarray,
            'label' => false
      ))
      ->getForm();

      // hidden form generation to save the tree state
      $formtree = $app['form.factory']->createBuilder('form')
          ->add('_16_1', 'hidden')
          ->add('_16_2', 'hidden')
          ->add('_16_3', 'hidden')
          ->add('_16_4', 'hidden')
          ->add('_16_5', 'hidden')
          ->add('_16_6', 'hidden')
          ->add('_16_7', 'hidden')
          ->add('_16_8', 'hidden')
          ->add('_16_9', 'hidden')
          ->add('_16_10', 'hidden')
          ->add('_16_11', 'hidden')
          ->add('_16_12', 'hidden')
          ->add('_16_13', 'hidden')
          ->add('_16_14', 'hidden')
          ->add('_16_15', 'hidden')
          ->add('_16_16', 'hidden')
          ->add('_8_1', 'hidden')
          ->add('_8_2', 'hidden')
          ->add('_8_3', 'hidden')
          ->add('_8_4', 'hidden')
          ->add('_8_5', 'hidden')
          ->add('_8_6', 'hidden')
          ->add('_8_7', 'hidden')
          ->add('_8_8', 'hidden')
          ->add('_4_1', 'hidden')
          ->add('_4_2', 'hidden')
          ->add('_4_3', 'hidden')
          ->add('_4_4', 'hidden')
          ->add('_2_1', 'hidden')
          ->add('_2_2', 'hidden')
          ->add('win', 'hidden')
          ->add('treesize', 'hidden')
          ->add('tournament_fk', 'hidden')
          ->add('_8_1_court', 'hidden')
          ->add('_8_2_court', 'hidden')
          ->add('_8_3_court', 'hidden')
          ->add('_8_4_court', 'hidden')
          ->add('_8_5_court', 'hidden')
          ->add('_8_6_court', 'hidden')
          ->add('_8_7_court', 'hidden')
          ->add('_8_8_court', 'hidden')
          ->add('_4_1_court', 'hidden')
          ->add('_4_2_court', 'hidden')
          ->add('_4_3_court', 'hidden')
          ->add('_4_4_court', 'hidden')
          ->add('_2_1_court', 'hidden')
          ->add('_2_2_court', 'hidden')
          ->add('final', 'hidden')
          ->add('save', 'submit')
          ->getForm();

  /*
   * handler for the hiden form that is set with the finish edit Button
   * and saved with the save button
   */

  $formtree->handleRequest($request);
  if($formtree->isSubmitted() && $formtree->get('save')->isClicked()){
    $data = $formtree->getData();
    $st1 = $app['pdo']->prepare('INSERT INTO knockofftree (
      _16_1 ,
      _16_2  ,
      _16_3  ,
      _16_4  ,
      _16_5  ,
      _16_6  ,
      _16_7  ,
      _16_8  ,
      _16_9  ,
      _16_10  ,
      _16_11  ,
      _16_12  ,
      _16_13  ,
      _16_14  ,
      _16_15  ,
      _16_16  ,
      _8_1 ,
      _8_2  ,
      _8_3  ,
      _8_4  ,
      _8_5  ,
      _8_6  ,
      _8_7  ,
      _8_8  ,
      _4_1 ,
      _4_2  ,
      _4_3  ,
      _4_4  ,
      _2_1 ,
      _2_2  ,
      winner,
      treesize,
      tournament_fk,
      idamdin) values (
        :_16_1 ,
        :_16_2  ,
        :_16_3  ,
        :_16_4  ,
        :_16_5  ,
        :_16_6  ,
        :_16_7  ,
        :_16_8  ,
        :_16_9  ,
        :_16_10  ,
        :_16_11  ,
        :_16_12  ,
        :_16_13  ,
        :_16_14  ,
        :_16_15  ,
        :_16_16  ,
        :_8_1 ,
        :_8_2  ,
        :_8_3  ,
        :_8_4  ,
        :_8_5  ,
        :_8_6  ,
        :_8_7  ,
        :_8_8  ,
        :_4_1 ,
        :_4_2  ,
        :_4_3  ,
        :_4_4  ,
        :_2_1 ,
        :_2_2  ,
        :win,
        :size,
        :tournament_fk,
        (SELECT idusers FROM users where e_mail_address = :e)
      )');
      $user = $app['session']->get('superuser');

    $st1->execute(array(
      '_16_1'=> $data['_16_1'] ,
      '_16_2' => $data['_16_2'] ,
      '_16_3' => $data['_16_3'] ,
      '_16_4' => $data['_16_4'] ,
      '_16_5' => $data['_16_5'] ,
      '_16_6' => $data['_16_6'],
      '_16_7' => $data['_16_7'] ,
      '_16_8' => $data['_16_8'] ,
      '_16_9' => $data['_16_9'] ,
      '_16_10' => $data['_16_10'] ,
      '_16_11' => $data['_16_11'] ,
      '_16_12'=> $data['_16_12']  ,
      '_16_13' => $data['_16_13'] ,
      '_16_14' => $data['_16_14'] ,
      '_16_15' => $data['_16_15'] ,
      '_16_16' => $data['_16_16'] ,
      '_8_1'=> $data['_8_1'] ,
      '_8_2' => $data['_8_2'] ,
      '_8_3' => $data['_8_3'] ,
      '_8_4' => $data['_8_4'] ,
      '_8_5' => $data['_8_5'] ,
      '_8_6' => $data['_8_6'],
      '_8_7' => $data['_8_7'] ,
      '_8_8' => $data['_8_8'] ,
      '_4_1'=> $data['_4_1'] ,
      '_4_2' => $data['_4_2'] ,
      '_4_3' => $data['_4_3'] ,
      '_4_4' => $data['_4_4'] ,
      '_2_1'=> $data['_2_1'] ,
      '_2_2' => $data['_2_2'] ,
      'win'=> $data['win'],
      'size'=> $data['treesize'],
      'tournament_fk' => $data['tournament_fk'],
      'e'=> $user['email']
    ));

    // inserting the court selected for the knockoff
    $st9 = $app['pdo']->prepare('INSERT INTO knockoffcourt (
      _8_1 ,
      _8_2  ,
      _8_3  ,
      _8_4  ,
      _8_5  ,
      _8_6  ,
      _8_7  ,
      _8_8  ,
      _4_1 ,
      _4_2  ,
      _4_3  ,
      _4_4  ,
      _2_1 ,
      _2_2  ,
      idcourt,
      final
      ) values (

        :_8_1 ,
        :_8_2  ,
        :_8_3  ,
        :_8_4  ,
        :_8_5  ,
        :_8_6  ,
        :_8_7  ,
        :_8_8  ,
        :_4_1 ,
        :_4_2  ,
        :_4_3  ,
        :_4_4  ,
        :_2_1 ,
        :_2_2  ,
        (SELECT idtree from knockofftree order by idtree DESC LIMIT 1 OFFSET 0),
        :final

      )');

    $st9->execute(array(
      '_8_1'=> $data['_8_1_court'] ,
      '_8_2' => $data['_8_2_court'] ,
      '_8_3' => $data['_8_3_court'] ,
      '_8_4' => $data['_8_4_court'] ,
      '_8_5' => $data['_8_5_court'] ,
      '_8_6' => $data['_8_6_court'],
      '_8_7' => $data['_8_7_court'] ,
      '_8_8' => $data['_8_8_court'] ,
      '_4_1'=> $data['_4_1_court'] ,
      '_4_2' => $data['_4_2_court'] ,
      '_4_3' => $data['_4_3_court'] ,
      '_4_4' => $data['_4_4_court'] ,
      '_2_1'=> $data['_2_1_court'] ,
      '_2_2' => $data['_2_2_court'] ,
      'final'=> $data['final'] ,

    ));


    // saving the tree state redirecing to homepage
    return $app->redirect('/test');
  }

  // handler request for the tournament choice
  $formknockoff->handleRequest($request);
  if ($formknockoff->isSubmitted()) {
    $data = $formknockoff->getData();
	$rt=$app['pdo']->prepare('SELECT idgroups FROM groups WHERE tournament_fk=:t');
	$rt->execute(array('t'=>$data['idtournaments']));
	$rt2=$app['pdo']->prepare('SELECT winner_pair_fk,pair1_id,pair2_id,number_set_1,number_set_2 from match where group_id=:g');
	$rt3=$app['pdo']->prepare('UPDATE groups SET first=:f,second=:s,third=:t WHERE idgroups=:g');
	while($row=$rt->fetch(PDO::FETCH_NUM))
	{
		$results=array();
		$sets=array();
		$rt2->execute(array('g'=>$row[0]));
		while($row2=$rt2->fetch(PDO::FETCH_NUM))
		{
			if(array_key_exists($row2[0], $results))
			{
				$results[$row2[0]]+=1;
			}else
			{
				$results[$row2[0]]=1;
			}
			if(array_key_exists($row2[1], $results))
			{
				$sets[$row2[1]]+=intval($row2[3]);
			}else
			{
				$results[$row2[1]]=0;
				$sets[$row2[1]]=intval($row2[3]);
			}
			if(array_key_exists($row2[2], $results))
			{
				$sets[$row2[2]]+=intval($row2[4]);
			}else
			{
				$results[$row2[2]]=0;
				$sets[$row2[2]]=intval($row2[4]);
			}
		}
		$max1value=max($results);
		$max1key=array_keys($results, $max1value)[0];
		$app['monolog']->debug('max1key '.$max1key);
		unset($results[$max1key]);
		$max2value=max($results);
		$max2key=array_keys($results, $max2value)[0];
		$app['monolog']->debug('max2key '.$max2key);
		unset($results[$max2key]);
		$max3value=max($results);
		$max3key=array_keys($results, $max3value)[0];
		$app['monolog']->debug('max3key '.$max3key);
		unset($results[$max3key]);
		if($max1value>$max2value)
		{
			$first=$max1key;
			if($max2value>$max3value or $sets[$max2key]>=$sets[$max3key])
			{
				$second=$max2key;
				$third=$max3key;
			}else
			{
				$second=$max3key;
				$third=$max2key;
			}
		}elseif($max2value>$max3value)
		{
			if($sets[$max1key]>=$sets[$max2key])
			{
				$first=$max1key;
				$second=$max2key;
			}else
			{
				$first=$max2key;
				$second=$max1key;
			}
			$third=$max3key;
		}
		else
		{
			$setsbests=array();
			$setsbests[$max1key]=$max1value;
			$setsbests[$max2key]=$max2value;
			$setsbests[$max3key]=$max3value;
			asort($setsbests);
			end($setsbests);
			$first = key($setsbests);
			unset($setsbests[$first]);
			end($setsbests);
			$second = key($setsbests);
			unset($setsbests[$second]);
			end($setsbests);
			$third = key($setsbests);
			unset($setsbests[$third]);
		}
		if($data['top']<3)
		{
			$third=null;
			if($data['top']<2)
			{
				$second=null;
			}
		}
		$rt3->execute(array('f'=>$first,
							's'=>$second,
							't'=>$third,
							'g'=>$row[0]));
	}
    $issubmit = true;
    $tournament_fk = $data['idtournaments'];

    // number of total players : number of player per group * nbrofgroup
    $nbrofgroup = 0;
    $st = $app['pdo']->prepare('SELECT idgroups FROM groups where tournament_fk = :tournament');
    $st->execute(array(
      'tournament' => $tournament_fk
    ));

    // count the numbre of groups
    while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
      $nbrofgroup++;
    }

    // global variables nneded for the twig
    $typeoftree = $data['top']*$nbrofgroup;
    $idpairs = array();
    $availablecourts = array();
    $tomanypairs = false;

    // set the correct value for the treesize
    if($typeoftree <= 2){
      $treesize = 2;
    } elseif ($typeoftree <= 4) {
      # code...
      $treesize = 4;
    } elseif ($typeoftree <= 8) {
      # code...
      $treesize = 8;
    } elseif ($typeoftree <= 16) {
      # code...
      $treesize = 16;
    } else {
      $tomanypairs = true;
    }


      if($data['indoor'] == 1){
        // select all courts that are indoor or outdoor as specified in the form
        $st1 = $app['pdo']->prepare('SELECT idcourts, court_address FROM courts where isindoor = :indoor AND tournament_fk = :tournament');
        $st1->execute(array(
          'indoor'=> $data['indoor'],
          'tournament' => $tournament_fk
        ));
      } else {
        // select all courts that are indoor or outdoor as specified in the form
        $st1 = $app['pdo']->prepare('SELECT idcourts, court_address FROM courts where tournament_fk = :tournament ');
        $st1->execute(array(
          'tournament' => $tournament_fk
        ));
      }


      // fetch the result and places it in availablecourts array
      while ($row1 = $st1->fetch(PDO::FETCH_ASSOC)) {
        $availablecourts[] = $row1;
      }

      // if the number of player can make a tree shape of tournament
      if($typeoftree == 2 || $typeoftree == 4 ||$typeoftree == 8 ||$typeoftree == 16){
        $perfecttree = true;
      }else {
        // error nbr of player must match 2,4, 8, 16
        // otherwise cannot build the tree
        $perfecttree = false;
      }


      switch ($data['top']) {
        case 1:
          $st = $app['pdo']->prepare('SELECT first FROM groups where tournament_fk = :tournament');
          $st->execute(array(
            'tournament' => $tournament_fk
          ));

          // get all pairid that are first of its group
          while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
            $idpairs[] = $row;
          }

          break;
        case 2:
          $st = $app['pdo']->prepare('SELECT first, second FROM groups where tournament_fk = :tournament');
          $st->execute(array(
            'tournament'=> $tournament_fk
          ));

          // get all pairid that are first or second of its group
          while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
            $idpairs[] = $row;
          }
          break;
        case 3:
          $st = $app['pdo']->prepare('SELECT first, second, third FROM groups where tournament_fk = :tournament');
          $st->execute(array(
            'tournament' => $tournament_fk
          ));

          // get all pairid that are first or second of its group
          while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
            $idpairs[] = $row;
          }
          break;

        default:
          // error only first second or third can be taken for knockoff
          break;
      }

    $tmp = $app['session']->get('superuser'); # we check if the user is connected as a superuser.
    if($tmp == null){
      $su = false;
    } else {
      $su = $tmp['superuser'];
    }
    return $app['twig']->render('knockofftree.twig', array(
          'idpairs'=> $idpairs,
          'typeoftree'=> $typeoftree,
          'issubmit'=> $issubmit,
          'nbrofgroup'=> $nbrofgroup,
          'top'=> $data['top'],
          'availablecourts'=> $availablecourts,
          'perfecttree'=> $perfecttree,
          'formtree'=> $formtree->createView(),
          'treesize'=> $treesize,
          'tournament' => $tournament_fk,
          'tomanypairs' => $tomanypairs,
          'superuser' => $su ));
  }

  $tmp = $app['session']->get('superuser'); # we check if the user is connected as a superuser.
  if($tmp == null){
    $su = false;
  } else {
    $su = $tmp['superuser'];
  }
  return $app['twig']->render('knockofftree.twig', array(
        'formknockoff'=> $formknockoff->createView(),
        'idpairs'=>$idpairs,
        'typeoftree'=>$typeoftree,
        'issubmit'=> $issubmit,
        'nbrofgroup'=>$nbrofgroup,
        'top'=> $data['top'],
        'availablecourts'=> $availablecourts,
        'perfecttree'=> $perfecttree,
        'formtree'=>$formtree->createView(),
        'treesize'=> $treesize,
        'tournament' => $tournament_fk,
        'tomanypairs' => $tomanypairs,
        'superuser' => $su ));
});

/*
 * knockoff gestion for super user
 * it allows the super user to see all state of the knockoff tree
 * for a specified tournament and he has the ability of deleting
 * certain states
 */

$app->match('/knockoffgestion', function (Request $request) use ($app) {

 // check if superuser
 $supuser = $app['session']->get('superuser');
 if($supuser == null){
   return $app['twig']->render('illegal.twig', array('group' => false));
 }else {
   if ($supuser['superuser'] == false) {
         return $app['twig']->render('illegal.twig', array('group' => false));
       }
 }

 // global varib needed for twig
 $perfecttree = false;
 $issubmit = false;

 // request getting all available tournament
 $st = $app['pdo']->prepare('SELECT idtournaments, name, day FROM tournaments');
 $st->execute();

 $tournamentvalue = array();
 while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
   $tournamentvalue[] = $row;
 }

 $renderingarray = array();
 foreach ($tournamentvalue as $value) {

   $renderingarray[$value['idtournaments']] = $value['name'] .'('. $value['day'].')';
 }

 // form generation for seleting the tournament
 $formtournament = $app['form.factory']->createBuilder('form')
     ->add('idtournaments', 'choice', array(
           'choices' => $renderingarray,
           'label' => false ))
     ->getForm();

  // global variables for the twig
 $tournament_fk = 0;
 $treeinfo = array();
 $treesize = 16;
 $count = 0;

 // reuest handler for the tournament id specification
 $formtournament->handleRequest($request);
 if($formtournament->isSubmitted() ){
   $data = $formtournament->getData();
   // set le tournament_fk
   $tournament_fk = $data['idtournaments'];
   // variable needed in the twig
   $issubmit = true;

   // request selecting all the field for the tree to be displayed
   $st5 = $app['pdo']->prepare('SELECT
     _16_1 ,
     _16_2  ,
     _16_3  ,
     _16_4  ,
     _16_5  ,
     _16_6  ,
     _16_7  ,
     _16_8  ,
     _16_9  ,
     _16_10  ,
     _16_11  ,
     _16_12  ,
     _16_13  ,
     _16_14  ,
     _16_15  ,
     _16_16  ,
     _8_1 ,
     _8_2  ,
     _8_3  ,
     _8_4  ,
     _8_5  ,
     _8_6  ,
     _8_7  ,
     _8_8  ,
     _4_1 ,
     _4_2  ,
     _4_3  ,
     _4_4  ,
     _2_1 ,
     _2_2  ,
     winner,
     treesize,
     idamdin,
     idtree,
     e_mail_address
     from knockofftree , users where tournament_fk = :tournament AND idamdin = idusers
      order by idtree');
   $st5->execute(array(
     'tournament'=> $tournament_fk,
   ));

   // get the data
   while ($row3 = $st5->fetch(PDO::FETCH_ASSOC)) {
     $treeinfo[] = $row3;
     $count++;
   }

   // get the tree size
   $treesize = $treeinfo[0]['treesize'];

   $tmp = $app['session']->get('superuser'); # we check if the user is connected as a superuser.
   if($tmp == null){
      $su = false;
    } else {
      $su = $tmp['superuser'];
    }
   return $app['twig']->render('knockoffgestion.twig', array(
         'treesize'=> $treesize,
         'treeinfo'=>$treeinfo,
         'count'=>$count,
         'formtournament' => $formtournament->createView(),
         'issubmit'=> $issubmit,
         'tournament_fk' => $tournament_fk,
         'superuser' => $su
         ));
 }

 $tmp = $app['session']->get('superuser'); # we check if the user is connected as a superuser.
 if($tmp == null){
    $su = false;
  } else {
    $su = $tmp['superuser'];
  }
 return $app['twig']->render('knockoffgestion.twig', array(
       'treesize'=> $treesize,
       'treeinfo'=>$treeinfo,
       'count'=>$count,
       'formtournament' => $formtournament->createView(),
       'issubmit'=> $issubmit,
       'tournament_fk' => $tournament_fk,
       'superuser' => $su
       ));

});

// delete the knockofftree specified by the id
$app->get('/deleteknockofftree/{id}', function($id) use($app) {
  // check if superuser
  $supuser = $app['session']->get('superuser');
  if($supuser == null){
    return $app['twig']->render('illegal.twig', array('group' => false));
  }else {
    if ($supuser['superuser'] == false) {
          return $app['twig']->render('illegal.twig', array('group' => false));
        }
  }



  $st = $app['pdo']->prepare('DELETE FROM knockofftree where idtree = :i');

  $st->execute(array(
    'i'=> $id
  ));

  return $app->redirect('/knockoffgestion');
})
->bind('deleteknockofftree');
