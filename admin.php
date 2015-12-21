<?php
/**
 * Php file containing the controllers related to the
 * admin page and the utilisation of a simple admin
 *
 */
use Silex\Provider\FormServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

//This file has every controllers of the admin


#Fonction utilisé pour créer un compte Admin

$app->match('/7d4c5c8894e016e772e9b7acab04d2b775ad4e7861d162ca5a48de7a7151a6a2', function (Request $request) use ($app) {

    $formad = $app['form.factory']->createBuilder('form')
        ->add('name', 'text', array(
            'constraints' => array(new Assert\NotBlank()),
            'attr' => array( 'placeholder' => 'Nom'),
            'label' => false
        ))
        ->add('firstname', 'text', array(
            'constraints' => array(new Assert\NotBlank()),
            'attr' => array( 'placeholder' => 'Prénom'),
            'label' => false
        ))
        ->add('email', 'email', array(
            'attr' => array( 'placeholder' => 'E-mail'),
            'label' => false,
            'constraints' => new Assert\Email()
        ))
        ->add('address', 'text', array(
            'constraints' => array(new Assert\NotBlank()),
            'attr' => array( 'placeholder' => 'Adresse (e.g. Nom de rue - Numéro + numéro de boîte  - Code postal - Localité)'),
            'label' => false
        ))
        ->add('phone', 'text', array(
            'constraints' => array(new Assert\NotBlank()),
            'attr' => array( 'placeholder' => 'Numéro de téléphone (e.g. 0499/ 45 12 23)'),
            'label' => false
        ))
        ->add('pass1', 'password', array(
            'constraints' => array(new Assert\NotBlank()),
            'attr' => array( 'placeholder' => 'Mot de passe'),
            'label' => false
        ))
        ->add('pass', 'password', array(
            'constraints' => array(new Assert\NotBlank()),
            'attr' => array( 'placeholder' => 'Confirmation mot de passe'),
            'label' => false
        ))
        ->getForm();



    $formad->handleRequest($request);

    if ($formad->isSubmitted() ) {
        $data = $formad->getData();
        if(strcmp($data['pass1'], $data['pass']) == 0){

        try {

            $st = $app['pdo']->prepare('SELECT count(*) FROM users WHERE e_mail_address=:m');
            $st->execute(array('m' => $data['email']));
            if($st->fetchColumn()==0){
            $encyptedpassword = sha1($data['pass1']);
            $st = $app['pdo']->prepare('INSERT INTO users ( last_name, first_name , e_mail_address, address , phone_number,user_type_fk , password) VALUES (:ln, :fn, :m, :ad,:ph, (SELECT MAX(iduser_type) FROM users_type WHERE user_type_rule = :t), :p)');

            $st->execute( array(
                'ln' => $data['name'],
                'fn' => $data['firstname'],
                'm' => $data['email'],
                'ad'=> $data['address'],
                'ph'=> $data['phone'],
                'p' => $encyptedpassword,
                't' => 'staff'
            ));
          }
          else{
            return $app->redirect('/error');
          }

      } catch (Exception $e) {

        return $app->redirect('/error');

      }

        // redirect somewhere
        return $app->redirect('/succ');
      } else {
        return $app->redirect('/error');
      }
    }


    // display the form
    return $app['twig']->render('adminreg.twig', array('formad' => $formad->createView() ));
});


// controller concerning the FAQ for the admin pages
$app->match('/faqadmin', function (Request $request) use ($app) {
  $val2 = $app['session']->get('staff');
  if($val2 == null){
    return $app['twig']->render('illegal.twig', array('group' => false));
  } else{
    if($val2['staff'] == false){
      return $app['twig']->render('illegal.twig', array('group' => false));
    }
  }

  $tmp = $app['session']->get('superuser');
  if($tmp == null){
    $su = false;
  } else {
    $su = $tmp['superuser'];
  }
  return $app['twig']->render('faqadmin.twig', array('superuser' => $su ));
});


// controller for editing and creating a new tournament
$app->match('/edittournament', function (Request $request) use ($app) {
	$st=$app['pdo']->prepare('SELECT name,day,idtournaments as id FROM tournaments');
	$st->execute(array());
	$tournaments=array();
	while($row=$st->fetch(PDO::FETCH_ASSOC)) {
		$tournaments[]=$row;
	}
  $val2 = $app['session']->get('staff');
  if($val2 == null){
    return $app['twig']->render('illegal.twig', array('group' => false));
  } else{
    if($val2['staff'] == false){
      return $app['twig']->render('illegal.twig', array('group' => false));
    }
  }

  $formpair = $app['form.factory']->createBuilder('form')
      ->add('name', 'text', array(
          'constraints' => array(new Assert\NotBlank()),
          'attr' => array( 'placeholder' => 'Nom du tournoi'),
          'label' => false
      ))
      ->add('year', 'integer', array(
          'attr' => array( 'placeholder' => 'Année'),
          'label' => false,
		      'required' => false
      ))
      ->add('month', 'integer', array(
          'attr' => array( 'placeholder' => 'mois'),
          'label' => false,
		      'required' => false
      ))
      ->add('day', 'integer', array(
          'attr' => array( 'placeholder' => 'Jour'),
          'label' => false,
		      'required' => false
      ))
      ->getForm();

  $formpair->handleRequest($request);

  if ($formpair->isSubmitted() ) {
    $data = $formpair->getData();
    $month = '';
    if(strlen($data['month']) == 1){
      $month = '0'.$data['month'];
    } else {
      $month = $data['month'];
    }
    $day = '';
    if(strlen($data['day']) == 1){
      $day= '0'.$data['day'];
    } else {
      $day = $data['day'];
    }

    $date = $data['year'].'-'.$month.'-'.$day;
    $app['monolog']->addDebug('day tournament : '.$date);
    $st = $app['pdo']->prepare('INSERT INTO tournaments (name, day) values (:n, :d)');

    $st->execute( array(
      'n' => $data['name'],
      'd' => $date
    ));

    // redirect somewhere
    return $app->redirect('/edittournament');
  }

	$tmp = $app['session']->get('superuser');
	if($tmp == null){
		$su = false;
	} else {
		$su = $tmp['superuser'];
	}
  // display the form
  return $app['twig']->render('settournament.twig', array('formpair' => $formpair->createView(),
														'tournaments'=>$tournaments,
														'superuser' => $su));
});



// gestion of the court by a saff member
$app->match('/admingestion', function(Request $request) use($app) {
	$courts = array();
	$rt=$app['pdo']->prepare('SELECT idsurfaces,surface_type FROM surfaces');
	$rt->execute();
	$displaySurfaces=array();
	while ($row = $rt->fetch(PDO::FETCH_NUM)) {
		$displaySurfaces[$row[0]]=$row[1];
	}
	$rt2=$app['pdo']->prepare('SELECT idtournaments,name,day FROM tournaments');
	$rt2->execute();
	$displayTournaments = array();
    while ($row = $rt2->fetch(PDO::FETCH_ASSOC)) {
      $displayTournaments[$row['idtournaments']] = $row['name'].' ('.$row['day'].')';
    }
	$formcourt = $app['form.factory']->createBuilder('form')
    ->add('address', 'text', array(
        'label' => false,
				'required' => false,
				'attr' => array('placeholder' => 'Adresse')
    ))
    ->add('state', 'text', array(
			  'label'=> false,
			  'required' => false,
			  'attr' => array('placeholder' => 'Etat du terrain')
    ))
		->add('surface', 'choice', array(
        'choices' => $displaySurfaces,
			  'label'=> false,
			  'required' => false,
			  'placeholder' => 'Surface'
    ))
    ->add('email', 'text', array(
        'label' => false,
				'required' => false,
				'attr' => array('placeholder' => 'Adresse email du propriétaire')
    ))
    ->add('isindoor', 'choice', array(
	      'choices' => array('0'=> 'terrain exterieur', '1' => 'terrain couvert'),
			  'label'=> false,
			  'required' => false,
			  'placeholder' => 'Intérieur/Extérieur'
    ))
		->add('tournament', 'choice', array(
				'choices' => $displayTournaments,
	      'label' => false,
				'required' => false,
				'placeholder' => 'Tournoi'
    ))
    ->getForm();

	$formcourt->handleRequest($request);
	if ($formcourt->isSubmitted()) {
		$data = $formcourt->getData();
		$reqsql='';
		$search = array();
		// only search or specified field
		if($data['address']!='')
		{
			$reqsql .= ' AND court_address LIKE :ca';
			$search['ca'] = '%'.$data['address'].'%';
		}
		if($data['state']!='')
		{
			$reqsql .= ' AND court_state LIKE :cs';
			$search['cs'] = '%'.$data['state'].'%';
		}
		if($data['surface']!='')
		{
			$reqsql .= ' AND surface_fk=:su';
			$search['su'] = $data['surface'];
		}
		if($data['email']!='')
		{
			$reqsql .= ' AND e_mail_address LIKE :e';
			$search['e'] = '%'.$data['email'].'%';
		}
		if($data['isindoor']!='')
		{
			$reqsql .= ' AND isindoor=:i';
			$search['i'] = $data['isindoor'];
		}
		if($data['tournament']!='')
		{
			$reqsql .= ' AND idtournaments=:t';
			$search['t'] = $data['tournament'];
		}

		$st = $app['pdo']->prepare('SELECT idcourts, court_address, court_instructions, court_state, surface_type, e_mail_address, name, isindoor FROM surfaces, users, courts,tournaments WHERE owner_user_fk = idusers AND tournament_fk = idtournaments AND surface_fk = idsurfaces'.$reqsql);
		$st->execute($search);

		$st2 = $app['pdo']->prepare('SELECT st.last_name as sl, st.first_name as sf FROM users st,courts co WHERE co.staff_user_fk=st.idusers AND co.idcourts=:id');

		while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
			$app['monolog']->addDebug('court');
			$st2->execute(array('id'=>$row['idcourts']));
			$row2=$st2->fetch(PDO::FETCH_ASSOC);
			if($row2==false)
			{
				$row['isStaff']=false;
			}else
			{
				$row['isStaff']=true;
				$row['sl']=$row2['sl'];
				$row['sf']=$row2['sf'];
			}
			$courts[] = $row;
		}
	}else
	{
		$data = $formcourt->getData();
		$st = $app['pdo']->prepare('SELECT idcourts, court_address, court_instructions, court_state, surface_type, e_mail_address, name, isindoor FROM surfaces, users, courts,tournaments WHERE owner_user_fk = idusers AND tournament_fk = idtournaments AND surface_fk = idsurfaces');
		$st->execute();

		$st2 = $app['pdo']->prepare('SELECT st.last_name as sl, st.first_name as sf FROM users st,courts co WHERE co.staff_user_fk=st.idusers AND co.idcourts=:id');

		while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
			$app['monolog']->addDebug('court');
			$st2->execute(array('id'=>$row['idcourts']));
			$row2=$st2->fetch(PDO::FETCH_ASSOC);
			if($row2==false)
			{
				$row['isStaff']=false;
			}else
			{
				$row['isStaff']=true;
				$row['sl']=$row2['sl'];
				$row['sf']=$row2['sf'];
			}
			$courts[] = $row;
		}
	}
	$val2 = $app['session']->get('staff');
	$uemail = $val2['email'];
	$staff_array = $app['session']->get('staff');
	if($staff_array == null){
		return $app['twig']->render('illegal.twig', array('group' => false));
	} else {
		if ($staff_array['staff'] == false) {
			return $app['twig']->render('illegal.twig', array('group' => false));
		} else {
			$su = false;
			$tmp = $app['session']->get('superuser');
			if($tmp == null){
				$su = false;
			} else {
				$su = $tmp['superuser'];
			}
			return $app['twig']->render('gestionadmin.twig', array('courts'=>$courts,
																	'superuser'=> $su,
																	'formcourt'=> $formcourt->createView()));
		}
	}
});


// displaying an historic of all the match result
$app->match('/adminresults', function(Request $request) use($app) {
	$results = array();
	// search form
	$rt = $app['pdo']->prepare('SELECT idtournaments,name,day FROM tournaments WHERE day>(NOW()-interval\'1 day\')');
    $rt->execute(array());
	$displayTournaments=array();
	while ($row = $rt->fetch(PDO::FETCH_NUM)) {
		$displayTournaments[$row[0]]=$row[1].'('.$row[2].')';
	}
	$formresult = $app['form.factory']->createNamedBuilder('formresult','form')
        ->add('pair1', 'integer', array(
            'attr' => array( 'placeholder' => 'N° d\'une paire'),
            'label' => false,
			'required' => false
        ))
		->add('pair2', 'integer', array(
            'attr' => array( 'placeholder' => 'N° de la 2ème paire'),
            'label' => false,
			'required' => false
        ))
		->add('tournament', 'choice', array(
			'choices' => $displayTournaments,
            'placeholder' => 'Choisissez un tournoi',
            'label' => false,
			'required' => false
        ))
        ->getForm();
	$formresult->handleRequest($request);
	if ($formresult->isSubmitted()) {
		$data = $formresult->getData();
		$reqsql='';
		$search = array();
		// only search or specified field
		if($data['pair1']!='' and $data['pair2'])
		{
			$reqsql .= ' AND ((m.pair1_id=:p1 AND m.pair2_id=:p2) OR (m.pair1_id=:p2 AND m.pair2_id=:p1))';
			$search['p1'] = $data['pair1'];
			$search['p2'] = $data['pair2'];
		}
		elseif($data['pair1']!='')
		{
			$reqsql .= ' AND (m.pair1_id=:p OR m.pair2_id=:p)';
			$search['p'] = $data['pair1'];
		}
		elseif($data['pair2']!='')
		{
			$reqsql .= ' AND (m.pair1_id=:p OR m.pair2_id=:p)';
			$search['p'] = $data['pair2'];
		}
		if($data['tournament']!='')
		{
			$reqsql .= ' AND tournament_id=:t';
			$search['t']= $data['tournament'];
		}
		$st2=$app['pdo']->prepare('SELECT m.idmatch as id,m.set_one_results as s1,m.set_two_results as s2,m.set_three_results as s3,m.set_four_results as s4,m.set_five_results as s5,m.pair1_id as i1,m.pair2_id as i2,m.group_id as g,t.name as tn,t.day as td from match m,tournaments t WHERE m.tournament_id=t.idtournaments'.$reqsql);
		$st2->execute($search);
		while ($row = $st2->fetch(PDO::FETCH_ASSOC)) {
			$results[] = $row;
		}
	}else{

		// get the pairs
		$st=$app['pdo']->prepare('SELECT m.idmatch as id,m.set_one_results as s1,m.set_two_results as s2,m.set_three_results as s3,m.set_four_results as s4,m.set_five_results as s5,m.pair1_id as i1,m.pair2_id as i2,m.group_id as g,t.name as tn,t.day as td from match m,tournaments t WHERE m.tournament_id=t.idtournaments'.$reqsql);
		$st->execute();


		while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
			$results[] = $row;
		}
	}
	return $app['twig']->render('adminresult.twig', array('results'=>$results,
                                                          'formresult'=>$formresult->createView(),
                                                          'superuser'=>$su,
														  'staff'=>$app['session']->get('staff')
            ));
});

// display all the pairs
$app->match('/adminpairsgestion', function(Request $request) use($app) {
	$pairs = array();
	$solo = array();
	// search form
	$rt = $app['pdo']->prepare('SELECT idtournaments,name,day FROM tournaments WHERE day>(NOW()-interval\'1 day\')');
    $rt->execute(array());
	$displayTournaments=array();
	while ($row = $rt->fetch(PDO::FETCH_NUM)) {
		$displayTournaments[$row[0]]=$row[1].'('.$row[2].')';
	}
	$formplayer = $app['form.factory']->createNamedBuilder('formplayer','form')
        ->add('lastname', 'text', array(
            'attr' => array( 'placeholder' => 'Nom du joueur'),
            'label' => false,
			'required' => false
        ))
		->add('firstname', 'text', array(
            'attr' => array( 'placeholder' => 'Prénom du joueur'),
            'label' => false,
			'required' => false
        ))
		->add('rank', 'text', array(
            'attr' => array( 'placeholder' => 'Classement du joueur'),
            'label' => false,
			'required' => false
        ))
		->add('tournament', 'choice', array(
			'choices' => $displayTournaments,
            'placeholder' => 'Choisissez un tournoi',
            'label' => false,
			'required' => false
        ))
        ->getForm();
	$formplayer->handleRequest($request);
	if ($formplayer->isSubmitted()) {
		$data = $formplayer->getData();
		$reqsql='';
		$reqsql_solo='';
		$search = array();
		// only search or specified field
		if($data['lastname']!='')
		{
			$reqsql .= ' AND (u1.last_name LIKE :ln OR u2.last_name LIKE :ln)';
			$reqsql_solo .= ' AND u1.last_name LIKE :ln';
			$search['ln'] = '%'.$data['lastname'].'%';
		}
		if($data['firstname']!='')
		{
			$reqsql .= ' AND (u1.first_name LIKE :fn OR u2.first_name LIKE :fn)';
			$reqsql_solo .= ' AND u1.first_name LIKE :fn';
			$search['fn']= '%'.$data['firstname'].'%';
		}
		if($data['rank']!='')
		{
			$reqsql .= ' AND (u1.ranking=:r OR u2.ranking=:r)';
			$reqsql_solo .= ' AND u1.ranking LIKE :r';
			$search['r']= $data['rank'];
		}
		if($data['tournament']!='')
		{
			$reqsql .= ' AND tournament_fk=:t';
			$reqsql_solo .= ' AND tournament_fk=:t';
			$search['t']= $data['tournament'];
		}
		// get the pairs
		$st2=$app['pdo']->prepare('SELECT p.idpairs As id,u1.first_name As fn1, u1.last_name as ln1, u1.ranking as rk1, u2.first_name as fn2, u2.last_name as ln2, u2.ranking as rk2,t.name as tn,t.day as td,p.wishes as w from users as u1, users as u2, pairs as p, tournaments as t where p.user_j1_fk = u1.idusers AND p.user_j2_fk = u2.idusers AND p.solo=0 AND t.idtournaments=p.tournament_fk'.$reqsql);
		$st2->execute($search);
		while ($row = $st2->fetch(PDO::FETCH_ASSOC)) {
			$pairs[] = $row;
		}
		// get the solo players
		$st2=$app['pdo']->prepare('SELECT p.idpairs As id,u1.first_name As fn1, u1.last_name as ln1, u1.ranking as rk1,t.name as tn,t.day as td,p.wishes as w from users as u1, pairs as p, tournaments as t where p.user_j1_fk = u1.idusers AND p.solo=1 AND t.idtournaments=p.tournament_fk'.$reqsql_solo);
		$st2->execute($search);
		while ($row = $st2->fetch(PDO::FETCH_ASSOC)) {
			$solo[] = $row;
		}
	}else{

		// get the pairs
		$st = $app['pdo']->prepare('SELECT p.idpairs As id,u1.first_name As fn1, u1.last_name as ln1, u1.ranking as rk1, u2.first_name as fn2, u2.last_name as ln2, u2.ranking as rk2,t.name as tn,t.day as td,p.wishes as w from users as u1, users as u2, pairs as p, tournaments as t where (p.user_j1_fk = u1.idusers AND p.user_j2_fk = u2.idusers AND p.solo=0 AND t.idtournaments=p.tournament_fk)');
		$st->execute();


		while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
			$pairs[] = $row;
		}

		// get the solo players
		$st = $app['pdo']->prepare('SELECT p.idpairs As id,u1.first_name As fn1, u1.last_name as ln1, u1.ranking as rk1,t.name as tn,t.day as td,p.wishes as w from users as u1, pairs as p, tournaments as t where (p.user_j1_fk = u1.idusers AND p.solo=1 AND t.idtournaments=p.tournament_fk)');
		$st->execute();



		while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
			$solo[] = $row;
		}
	}
	$staff_array = $app['session']->get('staff');
	if($staff_array == null){
		return $app['twig']->render('illegal.twig', array('group' => false));
	} else {
		if ($staff_array['staff'] == false) {
			return $app['twig']->render('illegal.twig', array('group' => false));
		} else {
      $su = false;
      $tmp = $app['session']->get('superuser');
      if($tmp == null){
        $su = false;

      } else {
        $su = $tmp['superuser'];

      }
			return $app['twig']->render('adminpairsgetsion.twig', array('pairs'=>$pairs,'solo'=>$solo,
                                                          'formplayer'=>$formplayer->createView(),
                                                          'superuser'=>$su
            ));
		}
	}
});


// controller for the page that allow to search a pair and delete a pair
$app->match('/adminpairgestion', function(Request $request) use($app) {
	$pairs=array();
	$solos=array();
	$errortournament=false;
	$pair=false;
	$solo=false;
	// search form
	$rt = $app['pdo']->prepare('SELECT idtournaments,name,day FROM tournaments WHERE day>(NOW()-interval\'1 day\')');
    $rt->execute(array());
	$displayTournaments=array();
	while ($row = $rt->fetch(PDO::FETCH_NUM)) {
		$displayTournaments[$row[0]]=$row[1].'('.$row[2].')';
	}
	$formcreation = $app['form.factory']->createNamedBuilder('formcreation','form')
		->add('first', 'integer', array(
            'attr' => array( 'placeholder' => 'N° du premier joueur solo'),
            'label' => false,
			'required' => false
        ))
		->add('second', 'integer', array(
            'attr' => array( 'placeholder' => 'N° du deuxième joueur solo'),
            'label' => false,
			'required' => false
        ))->getForm();
	$formcreation->handleRequest($request);
	$formplayer = $app['form.factory']->createNamedBuilder('formplayer','form')
		->add('solo', 'choice', array(
			'choices' => array('pair'=>'paires','solo'=>'joueurs solo'),
            'placeholder' => 'paires ou joueurs solo?',
			'required' => false,
            'label' => false
        ))
		->add('id', 'integer', array(
            'attr' => array( 'placeholder' => 'N° de la paire'),
            'label' => false,
			'required' => false
        ))
        ->add('lastname', 'text', array(
            'attr' => array( 'placeholder' => 'Nom du joueur'),
            'label' => false,
			'required' => false
        ))
		->add('firstname', 'text', array(
            'attr' => array( 'placeholder' => 'Prénom du joueur'),
            'label' => false,
			'required' => false
        ))
		->add('rank', 'text', array(
            'attr' => array( 'placeholder' => 'Classement du joueur'),
            'label' => false,
			'required' => false
        ))
		->add('tournament', 'choice', array(
			'choices' => $displayTournaments,
            'placeholder' => 'Choisissez un tournoi',
            'label' => false,
			'required' => false
        ))
        ->getForm();
	$formplayer->handleRequest($request);
	if ($formplayer->isSubmitted() or $formcreation->isSubmitted()) {
		$datacreation = $formcreation->getData();
		$dataplayer = $formplayer->getData();
		if($datacreation['first']!='' and $datacreation['second']!='')
		{
			$rt2=$app['pdo']->prepare('SELECT tournament_fk,user_j1_fk FROM pairs WHERE idpairs=:id');
			$rt2->execute(array('id'=>$datacreation['first']));
			$row=$rt2->fetch(PDO::FETCH_NUM);
			$t1=$row[0];
			$user1=$row[1];
			$rt2->execute(array('id'=>$datacreation['second']));
			$row=$rt2->fetch(PDO::FETCH_NUM);
			$t2=$row[0];
			$user2=$row[1];
			if($t1!=$t2)
			{
				$errortournament=true;
			}
			else
			{
				$tournament=$t1;
				$st2=$app['pdo']->prepare('INSERT INTO pairs(user_j1_fk,user_j2_fk,tournament_fk,solo) VALUES(:i1,:i2,:t,0)');
				$st2->execute(array('i1'=>$user1,
									'i2'=>$user2,
									't'=>$tournament));
				$st3=$app['pdo']->prepare('DELETE FROM pairs WHERE idpairs=:id');
				$st3->execute(array('id'=>$datacreation['first']));
				$st3->execute(array('id'=>$datacreation['second']));
			}
			$pair=true;
			// get the pairs
			$st1=$app['pdo']->prepare('SELECT p.idpairs As id,u1.first_name As fn1, u1.last_name as ln1, u1.ranking as rk1, u2.first_name as fn2, u2.last_name as ln2, u2.ranking as rk2,t.name as tn,t.day as td,p.wishes as w from users as u1, users as u2, pairs as p, tournaments as t where p.user_j1_fk = u1.idusers AND p.user_j2_fk = u2.idusers AND p.solo=0 AND t.idtournaments=p.tournament_fk AND t.day>(NOW()-interval\'1 day\')');
			$st1->execute(array());
			while ($row = $st1->fetch(PDO::FETCH_ASSOC)) {
				$pairs[] = $row;
			}
			$solo=true;
			// get the solo players
			$st2=$app['pdo']->prepare('SELECT p.idpairs As id,u1.first_name As fn1, u1.last_name as ln1, u1.ranking as rk1,t.name as tn,t.day as td,p.wishes as w from users as u1, pairs as p, tournaments as t where p.user_j1_fk = u1.idusers AND p.solo=1 AND t.idtournaments=p.tournament_fk AND t.day>(NOW()-interval\'1 day\')');
			$st2->execute(array());
			while ($row = $st2->fetch(PDO::FETCH_ASSOC)) {
				$solos[] = $row;
			}
		} else {
			$reqsql='';
			$reqsql_solo='';
			$search = array();
			// only search or specified field
			if($dataplayer['id']!='')
			{
				$reqsql .= ' AND idpairs=:id';
				$reqsql_solo .= ' AND idpairs=:id';
				$search['id']= $dataplayer['id'];
			}
			if($dataplayer['lastname']!='')
			{
				$reqsql .= ' AND (u1.last_name LIKE :ln OR u2.last_name LIKE :ln)';
				$reqsql_solo .= ' AND u1.last_name LIKE :ln';
				$search['ln'] = '%'.$dataplayer['lastname'].'%';
			}
			if($dataplayer['firstname']!='')
			{
				$reqsql .= ' AND (u1.first_name LIKE :fn OR u2.first_name LIKE :fn)';
				$reqsql_solo .= ' AND u1.first_name LIKE :fn';
				$search['fn']= '%'.$dataplayer['firstname'].'%';
			}
			if($dataplayer['rank']!='')
			{
				$reqsql .= ' AND (u1.ranking=:r OR u2.ranking=:r)';
				$reqsql_solo .= ' AND u1.ranking LIKE :r';
				$search['r']= $dataplayer['rank'];
			}
			if($dataplayer['tournament']!='')
			{
				$reqsql .= ' AND tournament_fk=:t';
				$reqsql_solo .= ' AND tournament_fk=:t';
				$search['t']= $dataplayer['tournament'];
			}
			if($dataplayer['solo']=='pair')
			{
				$pair=true;
				// get the pairs
				$st2=$app['pdo']->prepare('SELECT p.idpairs As id,u1.first_name As fn1, u1.last_name as ln1, u1.ranking as rk1, u2.first_name as fn2, u2.last_name as ln2, u2.ranking as rk2,t.name as tn,t.day as td,p.wishes as w from users as u1, users as u2, pairs as p, tournaments as t where p.user_j1_fk = u1.idusers AND p.user_j2_fk = u2.idusers AND p.solo=0 AND t.idtournaments=p.tournament_fk AND t.day>(NOW()-interval\'1 day\')'.$reqsql);
				$st2->execute($search);
				while ($row = $st2->fetch(PDO::FETCH_ASSOC)) {
					$pairs[] = $row;
				}
			}elseif($dataplayer['solo']=='solo')
			{
				$solo=true;
				// get the solo players
				$st2=$app['pdo']->prepare('SELECT p.idpairs As id,u1.first_name As fn1, u1.last_name as ln1, u1.ranking as rk1,t.name as tn,t.day as td,p.wishes as w from users as u1, pairs as p, tournaments as t where p.user_j1_fk = u1.idusers AND p.solo=1 AND t.idtournaments=p.tournament_fk AND t.day>(NOW()-interval\'1 day\')'.$reqsql_solo);
				$st2->execute($search);
				while ($row = $st2->fetch(PDO::FETCH_ASSOC)) {
					$solos[] = $row;
				}
			}else
			{
				$pair=true;
				// get the pairs
				$st2=$app['pdo']->prepare('SELECT p.idpairs As id,u1.first_name As fn1, u1.last_name as ln1, u1.ranking as rk1, u2.first_name as fn2, u2.last_name as ln2, u2.ranking as rk2,t.name as tn,t.day as td,p.wishes as w from users as u1, users as u2, pairs as p, tournaments as t where p.user_j1_fk = u1.idusers AND p.user_j2_fk = u2.idusers AND p.solo=0 AND t.idtournaments=p.tournament_fk AND t.day>(NOW()-interval\'1 day\')'.$reqsql);
				$st2->execute($search);
				while ($row = $st2->fetch(PDO::FETCH_ASSOC)) {
					$pairs[] = $row;
				}
				$solo=true;
				// get the solo players
				$st2=$app['pdo']->prepare('SELECT p.idpairs As id,u1.first_name As fn1, u1.last_name as ln1, u1.ranking as rk1,t.name as tn,t.day as td,p.wishes as w from users as u1, pairs as p, tournaments as t where p.user_j1_fk = u1.idusers AND p.solo=1 AND t.idtournaments=p.tournament_fk AND t.day>(NOW()-interval\'1 day\')'.$reqsql_solo);
				$st2->execute($search);
				while ($row = $st2->fetch(PDO::FETCH_ASSOC)) {
					$solos[] = $row;
				}
			}
		}
	} else {
		$pair=true;
		// get the pairs
		$st1=$app['pdo']->prepare('SELECT p.idpairs As id,u1.first_name As fn1, u1.last_name as ln1, u1.ranking as rk1, u2.first_name as fn2, u2.last_name as ln2, u2.ranking as rk2,t.name as tn,t.day as td,p.wishes as w from users as u1, users as u2, pairs as p, tournaments as t where p.user_j1_fk = u1.idusers AND p.user_j2_fk = u2.idusers AND p.solo=0 AND t.idtournaments=p.tournament_fk AND t.day>(NOW()-interval\'1 day\')');
		$st1->execute(array());
		while ($row = $st1->fetch(PDO::FETCH_ASSOC)) {
			$pairs[] = $row;
		}
		$solo=true;
		// get the solo players
		$st2=$app['pdo']->prepare('SELECT p.idpairs As id,u1.first_name As fn1, u1.last_name as ln1, u1.ranking as rk1,t.name as tn,t.day as td,p.wishes as w from users as u1, pairs as p, tournaments as t where p.user_j1_fk = u1.idusers AND p.solo=1 AND t.idtournaments=p.tournament_fk AND t.day>(NOW()-interval\'1 day\')');
		$st2->execute(array());
		while ($row = $st2->fetch(PDO::FETCH_ASSOC)) {
			$solos[] = $row;
		}
	}
	$staff_array = $app['session']->get('staff');
	if($staff_array == null){
		return $app['twig']->render('illegal.twig', array('group' => false));
	} else {
		if ($staff_array['staff'] == false) {
			return $app['twig']->render('illegal.twig', array('group' => false));
		} else {
      $su = false;
      $tmp = $app['session']->get('superuser');
      if($tmp == null){
        $su = false;

      } else {
        $su = $tmp['superuser'];

      }
			return $app['twig']->render('adminpaircreation.twig', array('pairs'=>$pairs,'solos'=>$solos,
                                                          'formplayer'=>$formplayer->createView(),
														  'errortournament'=>$errortournament,
													     'formcreation'=>$formcreation->createView(),
														'pair'=>$pair,'solo'=>$solo,
                                                          'superuser'=>$su ));
		}
	}
});

// display all the groups
$app->match('/admingroupsgestion/{id}', function (Request $request,$id=13) use ($app) {

  $tournament_id=$id;
  $rt = $app['pdo']->prepare('SELECT idtournaments,name,day FROM tournaments WHERE day>(NOW()-interval\'1 day\')');
  $rt->execute();
  $tournament_names=array();
	while($row=$rt->fetch(PDO::FETCH_NUM))
	{
		$tournament_names[$row[0]]=$row[1].' ('.$row[2].')';
	}
	$rtbis = $app['pdo']->prepare('SELECT name FROM tournaments where idtournaments=:t');
  $rtbis->execute(array('t'=>$tournament_id));
	$row=$rtbis->fetch(PDO::FETCH_NUM);
	$tournament_name=$row[0];
	// the tournament form
	if($tournament_id==-1){
		$formtournament = $app['form.factory']->createNamedBuilder('formtournament')
			->add('name', 'choice', array(
			'choices' => $tournament_names,
      'label' => false,
			'placeholder' => 'Choisissez un tournoi'
			))
			->getForm();
	} else {
		$formtournament = $app['form.factory']->createNamedBuilder('formtournament')
			->add('name', 'choice', array(
			'choices' => $tournament_names,
			'label' => false,
			'preferred_choices' => array($tournament_name)
			))
			->getForm();
	}
	$app['monolog']->addDebug('name tournament : '.$tournament_name);
	$formtournament->handleRequest($request);
    if($formtournament->isSubmitted()){
		// get the id of the selected tournament and reload the page with it
        $data=$formtournament->getData();
        $app['monolog']->addDebug('row tournament: '.$data['name']);
        return $app->redirect('/admingroupsgestion/'.$data['name']);
    }
	// get all the groups for this tournament
  $st = $app['pdo']->prepare('SELECT idgroups,court_fk,leader FROM groups WHERE tournament_fk=:t');
  $st->execute(array('t'=>$tournament_id));

  $groups = array();
  $st2 = $app['pdo']->prepare('SELECT p.idpairs As id,u1.first_name As fn1, u1.last_name as ln1, u1.ranking as rk1, u2.first_name as fn2, u2.last_name as ln2, u2.ranking as rk2,p.wishes as w from users as u1, users as u2, pairs as p where (p.user_j1_fk = u1.idusers AND p.user_j2_fk = u2.idusers AND p.group_fk=:g)');
  $st3 = $app['pdo']->prepare('SELECT court_address FROM courts WHERE idcourts=:id');
  while ($row = $st->fetch(PDO::FETCH_NUM)) {
        $app['monolog']->addDebug('row : '.$row[0]);
		// get all the pairs for the group
        $st2->execute(array('g'=>$row[0]));
        $group = array();
        while ($row2 = $st2->fetch(PDO::FETCH_ASSOC)){
            $app['monolog']->addDebug('idpairs : '.$row2['id']);
            $group[]=$row2;
        }
		// get the court for the group
		if($row[1]==''){
			$groups[]=array('id'=>$row[0],'court'=>$row[1],'leader'=>$row[2],'list'=>$group);
		}else{
			$st3->execute(array('id'=>$row[1]));
			$row3=$st3->fetch(PDO::FETCH_NUM);
			$groups[]=array('id'=>$row[0],'court'=>$row3[0],'leader'=>$row[2],'list'=>$group);
		}
    }

    $app['monolog']->addDebug('groups size : ' . count($groups));

    $staff_array = $app['session']->get('staff');
    if($staff_array == null){
        return $app['twig']->render('illegal.twig', array('group' => true));
    } else {
        if ($staff_array['staff'] == false) {
            return $app['twig']->render('illegal.twig', array('group' => true));
        } else {
          $su = false;
          $tmp = $app['session']->get('superuser');
          if($tmp == null){
            $su = false;
          } else {
            $su = $tmp['superuser'];
          }
          return $app['twig']->render('admingroupsgestion.twig', array('groups'=>$groups,
                                                'formtournament'=>$formtournament->createView(),
                                                'tournament_id' => $tournament_id,
						                                    'tournament_name' => $tournament_name,
                                                'superuser'=>$su ));
        }
    }

})->bind('admingroupsgestion');

// display one group
$app->match('/admingroupgestion/{id}', function (Request $request,$id) use ($app) {
	$app['monolog']->addDebug('data pair : ' . $data['pair']);
	$app['monolog']->addDebug('data court : ' . $data['court']);
	$rt=$app['pdo']->prepare('SELECT tournament_fk,court_fk,leader FROM groups WHERE idgroups=:i');
    $rt->execute(array(
 	   'i'=>$id));
    $row = $rt->fetch(PDO::FETCH_NUM);
	$id_tournament=$row[0];
	$court_id=$row[1];
	$leader=$row[2];
	// get the court of the group
	$courtrq=$app['pdo']->prepare('SELECT court_address FROM courts WHERE idcourts=:i');
	$courtrq->execute(array('i'=>$court_id));
	$row = $courtrq->fetch(PDO::FETCH_NUM);
	$court_ad=$row[0];
	// get all the pairs of the group
	$st2 = $app['pdo']->prepare('SELECT p.idpairs As id,u1.first_name As fn1, u1.last_name as ln1, u1.ranking as rk1, u2.first_name as fn2, u2.last_name as ln2, u2.ranking as rk2,p.wishes as w,p.leader as l from users as u1, users as u2, pairs as p where (p.user_j1_fk = u1.idusers AND p.user_j2_fk = u2.idusers AND p.group_fk=:g)');
    $st2->execute(array('g'=>$id));
    $group = array();
    while ($row2 = $st2->fetch(PDO::FETCH_ASSOC)){
		if($row2['l']==1)
		{
			$row2['leader']=true;
		}else
		{
			$row2['leader']=false;
		}
        $group[]=$row2;
    }
	$courtsql = $app['pdo']->prepare('SELECT idcourts,court_address FROM courts WHERE tournament_fk=:t');
    $courtsql->execute(array('t'=>$id_tournament));
    $displaycourts=array();
	while ($row = $courtsql->fetch(PDO::FETCH_NUM)) {
		$displaycourts[$row[0]]=$row[1];
	}
	// form to add a pair or change the court
    $formgroup = $app['form.factory']->createNamedBuilder('formgroup','form')
        ->add('pair', 'integer', array(
            'attr' => array( 'placeholder' => 'N° de la pair à ajouter'),
            'label' => false,
			'required' => false
        ))
		->add('court', 'choice', array(
			'choices' => $displaycourts,
			'placeholder' => "Choisissez un nouveau court",
			'label' => false,
			'required'=> false
		))
        ->getForm();
	$formgroup->handleRequest($request);
	if ($formgroup->isSubmitted()) {
		$data = $formgroup->getData();
		try {
			if($data['pair']!=''){
				$st3 = $app['pdo']->prepare('UPDATE pairs SET group_fk=:g WHERE idpairs=:i');
				$st3->execute( array(
						'g' => $id,
						'i' => $data['pair']
					));
			}
			if($data['court']!=''){
				$rt3=$app['pdo']->prepare('UPDATE groups set court_fk=:c WHERE idgroups=:i');
				$rt3->execute(array('c'=>$data['court'],
									'i'=>$id));
			}
		} catch (Exception $e) {
			return $app->redirect('/error');
		}
		return $app->redirect('/admingroupgestion/'.$id);
	}
	//$app['monolog']->addDebug('groups size : ' . count($groups));
    $staff_array = $app['session']->get('staff');
    if($staff_array == null and false){
        return $app['twig']->render('illegal.twig', array('group' => true));
    } else {
        if ($staff_array['staff'] == false and false) {
            return $app['twig']->render('illegal.twig', array('group' => true));
        } else {
          $su = false;
          $tmp = $app['session']->get('superuser');
          if($tmp == null){
            $su = false;

          } else {
            $su = $tmp['superuser'];

          }
            return $app['twig']->render('admingroupgestion.twig' , array('list'=>$group,
									'formgroup'=>$formgroup->createView(),
									'id' => $id,
									'tournament_id' => $id_tournament,
									'court' => $court_ad,
									'leader' => $leader,
                  'superuser'=>$su
                ));
        }
    }
})->bind('admingroupgestion');

// search among the groups
$app->match('/adminsearchgroup/{id}', function(Request $request,$id) use($app) {
	// the search form
	$formplayer = $app['form.factory']->createNamedBuilder('formplayer','form')
        ->add('lastname', 'text', array(
            'attr' => array( 'placeholder' => 'Nom du joueur'),
            'label' => false,
			'required' => false
        ))
		->add('firstname', 'text', array(
            'attr' => array( 'placeholder' => 'Pr�nom du joueur'),
            'label' => false,
			'required' => false
        ))
		->add('rank', 'text', array(
            'attr' => array( 'placeholder' => 'Classement du joueur'),
            'label' => false,
			'required' => false
        ))
        ->getForm();
	$formplayer->handleRequest($request);
	if ($formplayer->isSubmitted()) {
		$data = $formplayer->getData();
		$st = $app['pdo']->prepare('SELECT idgroups,court_fk,leader FROM groups WHERE tournament_fk=:t');
		$st->execute(array('t'=>$id));
		$groups = array();
		$reqsql='';
		$search = array();
		// only search for specified field
		if($data['lastname']!='')
		{
			$reqsql .= ' AND (u1.last_name LIKE :ln OR u2.last_name LIKE :ln)';
			$search['ln'] = '%'.$data['lastname'].'%';
		}
		if($data['firstname']!='')
		{
			$reqsql .= ' AND (u1.first_name LIKE :fn OR u2.first_name LIKE :fn)';
			$search['fn']= '%'.$data['firstname'].'%';
		}
		if($data['rank']!='')
		{
			$reqsql .= ' AND (u1.ranking=:r OR u2.ranking=:r)';
			$search['r']= $data['rank'];
		}
		$st2=$app['pdo']->prepare('SELECT p.idpairs As id,u1.first_name As fn1, u1.last_name as ln1, u1.ranking as rk1, u2.first_name as fn2, u2.last_name as ln2, u2.ranking as rk2 from users as u1, users as u2, pairs as p where p.user_j1_fk = u1.idusers AND p.user_j2_fk = u2.idusers AND p.group_fk=:g'.$reqsql);
		$st3 = $app['pdo']->prepare('SELECT court_address FROM courts WHERE idcourts=:id');
		// for all the groups
		while ($row = $st->fetch(PDO::FETCH_NUM)) {
			$app['monolog']->addDebug('row : '.$row[0]);
			$search['g']=$row[0];
			// get all the pairs for the group that match with the request
			$st2->execute($search);
			$group = array();
			while ($row2 = $st2->fetch(PDO::FETCH_ASSOC)){
				$app['monolog']->addDebug('idpairs : '.$row2['id']);
				$group[]=$row2;
			}
			// if there is
			if(count($group)!=0){
				if($row[1]==''){
					$groups[]=array('id'=>$row[0],'court'=>$row[1],'leader'=>$row[2],'list'=>$group);
				}else{
					$st3->execute(array('id'=>$row[1]));
					$row3=$st3->fetch(PDO::FETCH_NUM);
					$groups[]=array('id'=>$row[0],'court'=>$row3[0],'leader'=>$row[2],'list'=>$group);
				}
			}
		}
	}
    $staff_array = $app['session']->get('staff');
    if($staff_array == null and false){
        return $app['twig']->render('illegal.twig', array('group' => true));
    } else {
        if ($staff_array['staff'] == false and false) {
            return $app['twig']->render('illegal.twig', array('group' => true));
        } else {
          $su = false;
          $tmp = $app['session']->get('superuser');
          if($tmp == null){
            $su = false;

          } else {
            $su = $tmp['superuser'];

          }
            return $app['twig']->render('adminsearchgroup.twig' , array('groups'=>$groups,
                                                'formplayer'=>$formplayer->createView(),
												'tournament_id'=>$id,
                        'superuser'=>$su
                      ));
        }
    }
})->bind('adminsearchgroup');

// management of the court by the admin
$app->get('/courtadmin', function() use($app) {
  $val2 = $app['session']->get('staff');
  $uemail = $val2['email'];
  $app['monolog']->addDebug('email ' . $uemail);

  $st = $app['pdo']->prepare('SELECT idcourts, court_address, court_instructions, court_state, surface_type, e_mail_address, name, isindoor FROM surfaces, users, courts, tournaments WHERE tournament_fk= idtournaments AND owner_user_fk = idusers AND surface_fk = idsurfaces AND staff_user_fk = (SELECT MAX(idusers) FROM users where e_mail_address = :e)');

  $st->execute(array(
    'e'=>$uemail
  ));

  $courts = array();
  while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
    $courts[] = $row;
  }

  $staff_array = $app['session']->get('staff');
  if($staff_array == null){
    return $app['twig']->render('illegal.twig', array('group' => false));
  } else {
    if ($staff_array['staff'] == false) {
        return $app['twig']->render('illegal.twig', array('group' => false));
    } else {
      $su = false;
      $tmp = $app['session']->get('superuser');
      if($tmp == null){
        $su = false;

      } else {
        $su = $tmp['superuser'];

      }
        return $app['twig']->render('courtadmin.twig' , array('courts'=>$courts,
                                                              'superuser'=>$su
      ));
    }
  }
});

// entering the results of a match (done by an admin)
$app->match('/test-match-result', function (Request $request) use ($app) {
  //SELECT idtournaments,name,day FROM tournaments WHERE day>(NOW()-interval\'1 day\')
	$rt = $app['pdo']->prepare('SELECT idtournaments,name,day FROM tournaments WHERE day>(NOW()-interval\'1 day\')');
    $rt->execute(array());
	$displayTournaments=array();
	while ($row = $rt->fetch(PDO::FETCH_NUM)) {
		$displayTournaments[$row[0]]=$row[1].'('.$row[2].')';
	}
    $formresult = $app['form.factory']->createBuilder('form')
        ->add('pair1', 'text', array(
            'constraints' => array(new Assert\NotBlank()),
            'attr' => array( 'placeholder' => 'Numéro de la première pair'),
            'label' => false
        ))
        ->add('pair2', 'text', array(
            'constraints' => array(new Assert\NotBlank()),
            'attr' => array( 'placeholder' => 'Numéro de la deuxième pair'),
            'label' => false
        ))
        ->add('set11', 'choice', array(
              'choices' => array('0'=> '0', '1' => '1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7')
        ))
        ->add('set12',  'choice', array(
              'choices' => array('0'=> '0', '1' => '1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7')
        ))
        ->add('set21',  'choice', array(
              'choices' => array('0'=> '0', '1' => '1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7')
        ))
        ->add('set22',  'choice', array(
              'choices' => array('0'=> '0', '1' => '1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7')
        ))->add('set31',  'choice', array(
              'choices' => array('0'=> '0', '1' => '1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7')
        ))->add('set32',  'choice', array(
              'choices' => array('0'=> '0', '1' => '1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7')
        ))->add('set41',  'choice', array(
              'choices' => array('0'=> '0', '1' => '1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7')
        ))->add('set42',  'choice', array(
              'choices' => array('0'=> '0', '1' => '1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7')
        ))->add('set51',  'choice', array(
              'choices' => array('0'=> '0', '1' => '1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7')
        ))->add('set52',  'choice', array(
              'choices' => array('0'=> '0', '1' => '1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7')
        ))->add('groupeid', 'text', array(
            'constraints' => array(new Assert\NotBlank()),
            'attr' => array( 'placeholder' => 'N° du Groupe'),
            'label' => false
        ))->add('tournament', 'choice', array(
          'choices' => $displayTournaments
        ))
        ->getForm();

        $formresult->handleRequest($request);
        if ($formresult->isSubmitted()) {
            $data = $formresult->getData();
			$count=0;
			if($data['set11']!=$data['set12'])
			{
				$count+=($data['set11']-$data['set12'])/(abs($data['set11']-$data['set12']));
			}
			if($data['set21']!=$data['set22'])
			{
				$count+=($data['set21']-$data['set22'])/(abs($data['set21']-$data['set22']));
			}
			if($data['set31']!=$data['set32'])
			{
				$count+=($data['set31']-$data['set32'])/(abs($data['set31']-$data['set32']));
			}
			if($data['set41']!=$data['set42'])
			{
				$count+=($data['set41']-$data['set42'])/(abs($data['set41']-$data['set42']));
			}
			if($data['set51']!=$data['set52'])
			{
				$count+=($data['set51']-$data['set52'])/(abs($data['set51']-$data['set52']));
			}
			if($count>0)
			{
				$winner=$data['pair1'];
				$loser=$data['pair2'];
			}
			else
			{
				$winner=$data['pair1'];
				$loser=$data['pair2'];
			}
            $st = $app['pdo']->prepare('INSERT INTO match (set_one_results, set_two_results, set_three_results, set_four_results, set_five_results, tournament_id, group_id, pair1_id, pair2_id,winner_pair_fk,looser_pair_fk,number_set_1,number_set_2) values (:on, :tw, :th, :fo, :fi, :t, :gi, :p1, :p2,:w,:l,:n1,:n2)');
            $st->execute(array(
                'on' => $data['set11'].'/'.$data['set12'],
                'tw' => $data['set21'].'/'.$data['set22'],
                'th' => $data['set31'].'/'.$data['set32'],
                'fo' => $data['set41'].'/'.$data['set42'],
                'fi' => $data['set51'].'/'.$data['set51'],
				't' => $data['tournament'],
                'gi' => $data['groupeid'],
                'p1' => $data['pair1'],
                'p2' => $data['pair2'],
				'w' => $winner,
				'l' => $loser,
				'n1'=> intval($data['set11'])+intval($data['set21'])+intval($data['set31'])+intval($data['set41'])+intval($data['set51']),
				'n2'=> intval($data['set12'])+intval($data['set22'])+intval($data['set32'])+intval($data['set42'])+intval($data['set52']),
				));

            // redirect somewhere
            return $app->redirect('/test-match-result');
        }

    $staff_array = $app['session']->get('staff');
    if($staff_array == null){
        return $app['twig']->render('illegal.twig', array('group' => false));
    } else {
        if ($staff_array['staff'] == false) {
            return $app['twig']->render('illegal.twig', array('group' => false));
        } else {
          $su = false;
          $tmp = $app['session']->get('superuser');
          if($tmp == null){
            $su = false;

          } else {
            $su = $tmp['superuser'];

          }
            return $app['twig']->render('match-result.twig', array('formresult' => $formresult->createView(),
                                                                    'superuser'=>$su
            ));
        }
    }
});

// possibility of adding a news
$app->match('/niewsfeed', function (Request $request) use ($app) {
    // some default data for when the form is displayed the first time

    $formniews = $app['form.factory']->createBuilder('form')
        ->add('author', 'text', array(
            'constraints' => array(new Assert\NotBlank()),
            'attr' => array( 'placeholder' => 'Votre Nom'),
            'label' => false
        ))
        ->add('message', 'textarea', array(
            'constraints' => array(new Assert\NotBlank()),
            'attr' => array( 'placeholder' => 'News'),
            'label' => false
        ))
        ->getForm();



    $formniews->handleRequest($request);

    if ($formniews->isSubmitted() ) {
        $data = $formniews->getData();

        try {
          $st = $app['pdo']->prepare('INSERT INTO niews ( author, message) VALUES (:a, :m)');

          $st->execute( array(
            'a' => $data['author'],
            'm' => $data['message'],
          ));
      } catch (Exception $e) {
        return $app->redirect('/error');
      }

    return $app->redirect('/niews');
    }

    $staff_array = $app['session']->get('staff');
    if($staff_array == null){
        return $app['twig']->render('illegal.twig', array('group' => false));
    } else {
        if ($staff_array['staff'] == false) {
            return $app['twig']->render('illegal.twig', array('group' => false));
        } else {
          $su = false;
          $tmp = $app['session']->get('superuser');
          if($tmp == null){
            $su = false;

          } else {
            $su = $tmp['superuser'];

          }
            return $app['twig']->render('niewsfeed.twig' , array('formniews' => $formniews->createView(),
                                                                  'superuser'=>$su
          ));
        }
    }
});

// management page for the news
$app->get('/niewsgestion', function() use($app) {

  $st = $app['pdo']->prepare('SELECT niewsId, author, message FROM niews ORDER BY niewsId DESC LIMIT 20 OFFSET 0');
  $st->execute();

  $niews = array();
  while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
    $niews[] = $row;
  }

  $staff_array = $app['session']->get('staff');
  if($staff_array == null){
    return $app['twig']->render('illegal.twig', array('group' => false));
  } else {
    if ($staff_array['staff'] == false) {
      return $app['twig']->render('illegal.twig', array('group' => false));
    } else {
      $su = false;
      $tmp = $app['session']->get('superuser');
      if($tmp == null){
        $su = false;
      } else {
        $su = $tmp['superuser'];
      }
      return $app['twig']->render('niewsgestion.twig' , array('niews' => $niews,
                                                              'superuser'=> $su ));
    }
  }
});

// delete a news
$app->get('/deleteniews/{id}', function($id) use($app) {

  $st = $app['pdo']->prepare('DELETE FROM niews where niewsId = :i');

  $st->execute(array(
    'i'=> $id
  ));

  return $app->redirect('/niewsgestion');
})
->bind('deleteniews');

// page that allows the autogeneration of the groups for a tournament
$app->match('/groupgeneration', function (Request $request) use ($app) {

    require_once('DatabaseHandler.php');
    $result = getAllTournaments($app['pdo']);

    $displayTournaments = array();
    foreach ($result as $r) {
      $displayTournaments[$r['idtournaments']] = $r['name'].' ('.$r['day'].')';
    }

    $formgroup = $app['form.factory']->createBuilder('form')
            ->add('nbr', 'integer', array(
                'constraints' => array(new Assert\NotBlank()),
                'attr' => array( 'placeholder' => 'Nombre de paires par groupe'),
                'label' => 'Nombre de paires par groupe'
            ))
            ->add('tournament', 'choice', array(
              'choices' => $displayTournaments,
              'label' => 'Tournoi'
            ))
            ->add('pluie', 'choice', array(
              'choices' => array(true => 'Oui', false => 'Non'),
              'label' => 'Temps pluvieux ?'
            ))
            ->getForm();

    $formgroup->handleRequest($request);
    if ($formgroup->isSubmitted()) {
        $data = $formgroup->getData();

        try {
          require_once('creationPair.php');
          $result = creerTournoi($data['nbr'],$data['tournament'], $data['pluie'], $app['pdo']);
          // echo "<pre>";
          // print_r($result);
          // echo "</pre>";
          // die();
          return $app['twig']->render('groupgenerationdisplay.twig', array('resultGroup' => $result ,'superuser'=> $su));
      } catch (Exception $e) {
        error_log("GROUP GENERATION CATCH : ".$e);
        return $app->redirect('/error');
      }
        //return $app->redirect('/test');
  }

  $staff_array = $app['session']->get('staff');
  if($staff_array == null){
    return $app['twig']->render('illegal.twig', array('group' => false));
  } else {
    if ($staff_array['staff'] == false) {
        return $app['twig']->render('illegal.twig', array('group' => false));
    } else {
      $su = false;
      $tmp = $app['session']->get('superuser');
      if($tmp == null){
        $su = false;
      } else {
        $su = $tmp['superuser'];
      }
    	return $app['twig']->render('groupgeneration.twig', array('formgroup' => $formgroup->createView(),
                                                                'superuser'=> $su ));
    }
  }
});

#fonction pour supprimer une pair d'un groupe
$app->get('/deletegrouppairs/{id}', function($id) use($app) {

  $rt=$app['pdo']->prepare('SELECT group_fk FROM pairs WHERE idpairs=:i');
  $rt->execute(array(
	'i'=>$id));
  $row = $rt->fetch(PDO::FETCH_NUM);

  $st = $app['pdo']->prepare('UPDATE pairs SET group_fk=NULL where idpairs = :i');

  $st->execute(array(
    'i'=> $id
  ));

  return $app->redirect('/admingroupgestion/'.$row[0]);
})
->bind('deletegrouppairs');




#fonction pour supprimer un group
$app->get('/deletegroup/{id}', function($id) use($app) {

  //TODO check si connect comme admin
  $rt=$app['pdo']->prepare('SELECT tournament_fk FROM groups WHERE idgroups=:i');
  $rt->execute(array(
	'i'=>$id));
  $row = $rt->fetch(PDO::FETCH_NUM);

  $st = $app['pdo']->prepare('DELETE FROM groups where idgroups = :i');

  $st->execute(array(
    'i'=> $id
  ));

  return $app->redirect('/admingroupsgestion/'.$row[0]);
})
->bind('deletegroup');


#Fonction pour ajouté un group
$app->get('/addgroup/{id}', function($id) use($app) {

  $st = $app['pdo']->prepare('INSERT INTO groups(tournament_fk) VALUES (:id)');

  $st->execute(array(
    'id'=> $id
  ));

  return $app->redirect('/admingroupsgestion/'.$id);
})
->bind('addgroup');



#Fonction permetant l'ajout de cours dans la DB
$app->get('/adtocourt/{court}', function($court) use($app) {
  $val2 = $app['session']->get('staff');
  $uemail = $val2['email'];
  if($val2!=null){
    $st = $app['pdo']->prepare('UPDATE courts SET staff_user_fk= (SELECT MAX(idusers) FROM users WHERE e_mail_address = :e ) WHERE idcourts = :c ');
  }
  $st->execute(array(
    'e'=> $uemail,
    'c'=> $court
  ));

  return $app->redirect('/courtadmin');
})
->bind('adtocourt');


// The two following functions come from http://stackoverflow.com/questions/4249432/export-to-csv-via-php
function array2csv(array &$array)
{
   if (count($array) == 0) {
     return null;
   }
   ob_start();
   $df = fopen("php://output", 'w');
   fputcsv($df, array_keys(reset($array)));
   foreach ($array as $row) {
      fputcsv($df, $row);
   }
   fclose($df);
   return ob_get_clean();
}


function download_send_headers($filename) {
    // disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

    // force download
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");

    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
}


$app->get('/export-db', function() use($app) {

	$staff_array = $app['session']->get('staff');
	if($staff_array == null){
    return $app['twig']->render('illegal.twig', array('group' => false));
  } else {
   	if ($staff_array['staff'] == false) {
     	return $app['twig']->render('illegal.twig', array('group' => false));
  	} else {
		  $su = false;
      $tmp = $app['session']->get('superuser');
      if($tmp == null){
        $su = false;
      } else {
        $su = $tmp['superuser'];
      }
		  return $app['twig']->render('test-db.twig', array('superuser' => $su));
		}
	}

});


$app->get('/export-db-user', function() use($app) {

	$staff_array = $app['session']->get('staff');
	if($staff_array == null){
    return $app['twig']->render('illegal.twig', array('group' => false));
  } else {
   	if ($staff_array['staff'] == false) {
     	return $app['twig']->render('illegal.twig', array('group' => false));
  	} else {

		  $st = $app['pdo']->prepare('SELECT first_name, last_name, address FROM users WHERE user_type_fk = 1 ORDER BY address');
	    $st->execute();
	    $niews = array();
	    while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
	      $niews[] = $row;
	    }

	    download_send_headers("info_utilisateurs_" . date("Y-m-d") . ".csv");
	    echo array2csv($niews);
	    die();

		  $su = false;
      $tmp = $app['session']->get('superuser');
      if($tmp == null){
        $su = false;
      } else {
        $su = $tmp['superuser'];
      }
		  return $app['twig']->render('test-db.twig', array('superuser' => $su));
		}
	}

});


$app->get('/export-db-owner', function() use($app) {

	$staff_array = $app['session']->get('staff');
	if($staff_array == null){
    return $app['twig']->render('illegal.twig', array('group' => false));
  } else {
   	if ($staff_array['staff'] == false) {
     	return $app['twig']->render('illegal.twig', array('group' => false));
  	} else {

		  $st = $app['pdo']->prepare('SELECT first_name, last_name, address FROM users WHERE user_type_fk = 2 ORDER BY address');
	    $st->execute();

	    $niews = array();
	    while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
	      $niews[] = $row;
	    }

	    download_send_headers("info_proprietaires_" . date("Y-m-d") . ".csv");
	    echo array2csv($niews);
	    die();

		  $su = false;
      $tmp = $app['session']->get('superuser');
      if($tmp == null){
        $su = false;
      } else {
        $su = $tmp['superuser'];
      }
		  return $app['twig']->render('test-db.twig', array('superuser' => $su ));
		}
	}
});


$app->match('/courthistory', function(Request $request) use($app) {

	$staff_array = $app['session']->get('staff');
	if($staff_array == null){
    return $app['twig']->render('illegal.twig', array('group' => false));
  }
 	if ($staff_array['staff'] == false) {
   	return $app['twig']->render('illegal.twig', array('group' => false));
	}

	$rt = $app['pdo']->prepare('SELECT courts.idcourts, courts.court_address FROM courts');
  $rt->execute();
  $displayCourts=array();
  while ($row = $rt->fetch(PDO::FETCH_NUM)) {
    $displayCourts[$row[0]] = $row[1];
  }

  $formcourt = $app['form.factory']->createBuilder('form')
      ->add('adress', 'choice', array(
          'choices' => $displayCourts,
          'label' => false
      ))
      ->getForm();

  $formcourt->handleRequest($request);

  if ($formcourt->isSubmitted()) {
    $data = $formcourt->getData();

	  try {
	  	$ccc = $data['adress'];
	  	$res = $app['pdo']->prepare('SELECT idcourt_comments, court_comment_text FROM court_comments WHERE court_fk = :ca');
		  $res->execute(array('ca' => $data['adress']));
		  $cc = array();
		  while ($row = $res->fetch(PDO::FETCH_NUM)) {
		    $cc[] = $row;
		  }
	  } catch (Exception $e) {
    	return $app->redirect('/error');
    }

    $su = false;
    $tmp = $app['session']->get('superuser');
    if($tmp == null){
      $su = false;
    } else {
      $su = $tmp['superuser'];
    }
	  return $app['twig']->render('courthistory.twig', array('formcourt' => $formcourt->createView(),
	  																												'issubmit' => true,
	  																												'result' => $cc,
	  																												'superuser' => $su ));
  }

  $su = false;
  $tmp = $app['session']->get('superuser');
  if($tmp == null){
    $su = false;
  } else {
    $su = $tmp['superuser'];
  }
  return $app['twig']->render('courthistory.twig', array('formcourt' => $formcourt->createView(),
  																												'issubmit' => false,
  																												'superuser' => $su ));
});


$app->match('/createcourthistory', function(Request $request) use($app) {

	$staff_array = $app['session']->get('staff');
	if($staff_array == null){
    return $app['twig']->render('illegal.twig', array('group' => false));
  }
 	if ($staff_array['staff'] == false) {
   	return $app['twig']->render('illegal.twig', array('group' => false));
	}

	$rt = $app['pdo']->prepare('SELECT courts.idcourts, courts.court_address FROM courts');
  $rt->execute();
  $displayCourts=array();
  while ($row = $rt->fetch(PDO::FETCH_NUM)) {
    $displayCourts[$row[0]] = $row[1];
  }

  $formcourtcreate = $app['form.factory']->createBuilder('form')
  		->add('courtinstructions', 'textarea', array(
                'attr' => array( 'placeholder' => 'Remarques à propos du terrain'),
                'label' => false
      ))
      ->add('adress', 'choice', array(
          'choices' => $displayCourts,
          'label' => false
      ))
      ->getForm();

  $formcourtcreate->handleRequest($request);

  if ($formcourtcreate->isSubmitted()) {
    $data = $formcourtcreate->getData();

    $stt = $app['pdo']->prepare('INSERT INTO court_comments values ((SELECT count(*) FROM court_comments)+1,:n, :d)');
    $stt->execute(array('n' => $data['courtinstructions'],
      									'd' => $data['adress'] ));

    // redirect somewhere
    return $app->redirect('/createcourthistory');
  }

  return $app['twig']->render('createcourthistory.twig', array('formcourtcreate' => $formcourtcreate->createView(),
  																												'superuser' => $su ));
});


$app->get('/deletecourthistory/{id}', function($id) use($app) {

  $st = $app['pdo']->prepare('DELETE FROM court_comments where idcourt_comments = :i');

  $st->execute(array(
    'i'=> $id
  ));

  return $app->redirect('/courthistory');
})
->bind('deletecourthistory');
