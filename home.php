<?php
/**
 * Php file containing the controllers related to the
 * homepage and the utilisation of a normal user
 * (user with no account)
 */

use Silex\Provider\FormServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


//This file has every controllers for the basic (non connected) user, in other words everything accessible from the homepage
#renvoie sur le /test si on quelqu'un essaie d'acceder au site
$app->match('/', function (Request $request) use ($app) {
  return $app->redirect('/test');
});


#Crée la homepage
$app->get('/test', function() use($app) {
    if($app['session']->get('staff') == null && $app['session']->get('owner')==null){
      return $app['twig']->render('test.twig' , array('staff' =>  false,
                                                      'owner' => false,
                                                      'superuser' => false ));
    } elseif ($app['session']->get('staff') != null && $app['session']->get('owner')==null) {
      $val = $app['session']->get('staff');
      $su = false;
      $tmp = $app['session']->get('superuser');
      if($tmp == null){
        $su = false;
      } else {
        $su = $tmp['superuser'];
      }
      return $app['twig']->render('test.twig' , array('staff' =>  $val['staff'],
                                                      'owner' => false,
                                                      'superuser' => $su ));
    } elseif ($app['session']->get('staff') == null && $app['session']->get('owner')!=null) {
      # code...
      $val = $app['session']->get('owner');
      return $app['twig']->render('test.twig' , array('staff' =>  false,
                                                      'owner' => $val['owner'],
                                                      'superuser'=>false ));
    } else {
      $val = $app['session']->get('staff');
      $val2 = $app['session']->get('owner');
      $su = false;
      $tmp = $app['session']->get('superuser');
      if($tmp == null){
        $su = false;
      } else {
        $su = $tmp['superuser'];
      }
      return $app['twig']->render('test.twig', array('staff' =>  $val['staff'],
                                                     'owner' => $val2['owner'],
                                                     'superuser' => $su ));
    }
});

$tmp = $app['session']->get('superuser');
if($tmp == null){
  $su = false;
} else {
  $su = $tmp['superuser'];
}
$app->get('/contact',  function() use($app) {
  return $app['twig']->render('contact.twig', array('staff' => $app['session']->get('staff'),
                                                    'owner' => $app['session']->get('owner'),
                                                    'superuser' => $su ));
});

$tmp = $app['session']->get('superuser');
if($tmp == null){
  $su = false;
} else {
  $su = $tmp['superuser'];
}
$app->get('/faq',  function() use($app) {
  if($app['session']->get('staff') == null && $app['session']->get('owner')==null){
      return $app['twig']->render('faq.twig' , array('staff' =>  false,
                                                      'owner' => false,
                                                      'superuser' => false ));
    } elseif ($app['session']->get('staff') != null && $app['session']->get('owner')==null) {
      $val = $app['session']->get('staff');
      $su = false;
      $tmp = $app['session']->get('superuser');
      if($tmp == null){
        $su = false;
      } else {
        $su = $tmp['superuser'];
      }
      return $app['twig']->render('faq.twig' , array('staff' =>  $val['staff'],
                                                      'owner' => false,
                                                      'superuser' => $su ));
    } elseif ($app['session']->get('staff') == null && $app['session']->get('owner')!=null) {
      # code...
      $val = $app['session']->get('owner');
      return $app['twig']->render('faq.twig' , array('staff' =>  false,
                                                      'owner' => $val['owner'],
                                                      'superuser'=>false ));
    } else {
      $val = $app['session']->get('staff');
      $val2 = $app['session']->get('owner');
      $su = false;
      $tmp = $app['session']->get('superuser');
      if($tmp == null){
        $su = false;
      } else {
        $su = $tmp['superuser'];
      }
      return $app['twig']->render('faq.twig', array('staff' =>  $val['staff'],
                                                     'owner' => $val2['owner'],
                                                     'superuser' => $su ));
    }
});


// news page controller
 $app->get('/niews', function() use($app) {

  $st = $app['pdo']->prepare('SELECT author, message FROM niews ORDER BY niewsId DESC LIMIT 20 OFFSET 0');
  $st->execute();

  $niews = array();
  while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
    $niews[] = $row;
  }

  if($app['session']->get('staff') == null && $app['session']->get('owner')==null){
      return $app['twig']->render('niews.twig' , array('niews'=>$niews,
													  'staff' =>  false,
                                                      'owner' => false,
                                                      'superuser' => false ));
    } elseif ($app['session']->get('staff') != null && $app['session']->get('owner')==null) {
      $val = $app['session']->get('staff');
      $su = false;
      $tmp = $app['session']->get('superuser');
      if($tmp == null){
        $su = false;
      } else {
        $su = $tmp['superuser'];
      }
      return $app['twig']->render('niews.twig' , array('niews'=>$niews,
													  'staff' =>  $val['staff'],
                                                      'owner' => false,
                                                      'superuser' => $su ));
    } elseif ($app['session']->get('staff') == null && $app['session']->get('owner')!=null) {
      # code...
      $val = $app['session']->get('owner');
      return $app['twig']->render('niews.twig' , array('niews'=>$niews,
													  'staff' =>  false,
                                                      'owner' => $val['owner'],
                                                      'superuser'=>false ));
    } else {
      $val = $app['session']->get('staff');
      $val2 = $app['session']->get('owner');
      $su = false;
      $tmp = $app['session']->get('superuser');
      if($tmp == null){
        $su = false;
      } else {
        $su = $tmp['superuser'];
      }
      return $app['twig']->render('niews.twig', array('niews'=>$niews,
													 'staff' =>  $val['staff'],
                                                     'owner' => $val2['owner'],
                                                     'superuser' => $su ));
    }
});

// controller for the connection page
$app->match('/connect', function (Request $request) use ($app) {
  $formadmin = $app['form.factory']->createBuilder('form')
      ->add('email', 'email', array(
          'attr' => array( 'placeholder' => 'E-mail'),
          'label' => false,
          'constraints' => new Assert\Email()
      ))
      ->add('password', 'password', array(
          'constraints' => array(new Assert\NotBlank(),
          ),
          'attr' => array( 'placeholder' => 'Mot de passe'),
          'label' => false
      ))
      ->getForm();

    $formadmin->handleRequest($request);
    if ($formadmin->isSubmitted()) {
        $data = $formadmin->getData();
        // TODO check DB
        $encyptedpassword = sha1($data['password']);
        $st = $app['pdo']->prepare('SELECT (user_type_fk) FROM users WHERE e_mail_address = :e AND password = :p');
        $st->execute(array(
          'e'=> $data['email'],
          'p'=>   $encyptedpassword ));

        $res = array();
        $isStaff = false;
        $isOwner = false;
        $superuser = false;
        if($row = $st->fetch(PDO::FETCH_ASSOC)) {
          if($row['user_type_fk'] == 3){
            $isStaff =true;

          }elseif ($row['user_type_fk'] == 2) {
            $isOwner = true;
          }
          elseif ($row['user_type_fk'] == 4) {
            $superuser = true;
          }
        }

        if($isStaff){
           $app['session']->set('staff', array('staff'=>true, 'email' => $data['email'] ));
          return $app->redirect('/ok');
        }
        if($isOwner){
          $app['session']->set('owner', array('owner'=>true, 'email' => $data['email']));
          return $app->redirect('/ok');
        }
        if($superuser){
          $app['session']->set('superuser', array('superuser'=>true, 'email' => $data['email']));
          $app['session']->set('staff', array('staff'=>true, 'email' => $data['email'] ));
          return $app->redirect('/ok');
        }
    }

    if($app['session']->get('staff') == null && $app['session']->get('owner')==null){
      return $app['twig']->render('connect.twig' , array('formadmin' => $formadmin->createView(),
                                                      'staff' =>  false,
                                                      'owner' => false ));
    } elseif ($app['session']->get('staff') != null && $app['session']->get('owner')==null) {

      $val = $app['session']->get('staff');
      return $app['twig']->render('connect.twig' , array('formadmin' => $formadmin->createView(),
                                                      'staff' =>  $val['staff'],
                                                      'owner' => false ));
    } elseif ($app['session']->get('staff') == null && $app['session']->get('owner')!=null) {

      $val = $app['session']->get('owner');
      return $app['twig']->render('connect.twig', array('formadmin' => $formadmin->createView(),
                                                        'staff' =>  false,
                                                        'owner' => $val['owner'] ));
    } else {
      $val = $app['session']->get('staff');
      $val2 = $app['session']->get('owner');
      return $app['twig']->render('connect.twig', array('formadmin' => $formadmin->createView(),
                                                        'staff' =>  $val['staff'],
                                                        'owner' => $val2['owner'] ));
    }
});

// tournament registration controller
$app->match('/test-tournament', function (Request $request) use ($app) {
	$rt = $app['pdo']->prepare('SELECT idtournaments,name,day FROM tournaments WHERE day>(NOW()-interval\'1 day\')');
    $rt->execute(array());
	$displayTournaments=array();
	while ($row = $rt->fetch(PDO::FETCH_NUM)) {
		$displayTournaments[$row[0]]=$row[1].'('.$row[2].')';
	}
    $formpair = $app['form.factory']->createBuilder('form')
        ->add('yourname', 'text', array(
            'constraints' => array(new Assert\NotBlank()),
            'attr' => array( 'placeholder' => 'Nom'),
            'label' => false
        ))
        ->add('yourfirstname', 'text', array(
            'constraints' => array(new Assert\NotBlank()),
            'attr' => array( 'placeholder' => 'Prénom'),
            'label' => false
        ))
        ->add('youremail', 'email', array(
            'attr' => array( 'placeholder' => 'E-mail'),
            'label' => false,
            'constraints' => new Assert\Email()
        ))
        ->add('pairname', 'text', array(
            'constraints' => array(new Assert\NotBlank()),
            'attr' => array( 'placeholder' => 'Nom de votre partenaire'),
            'label' => false
        ))
        ->add('pairfirstname', 'text', array(
            'constraints' => array(new Assert\NotBlank()),
            'attr' => array( 'placeholder' => 'Prénom de votre partenaire'),
            'label' => false
        ))
        ->add('pairemail', 'email', array(
            'attr' => array( 'placeholder' => 'E-mail de votre partenaire'),
            'label' => false,
            'constraints' => new Assert\Email()
        ))
        ->add('tournament', 'choice', array(
          'choices' => $displayTournaments
        ))
		->add('leader', 'choice', array(
			'choices' => array('1'=>'oui','0'=>'non'),
			'label' => false,
			'placeholder' => 'devenir une paire responsable?'
		))
		->add('wishes', 'textarea', array(
            'attr' => array( 'placeholder' => 'Demandes éventuelles'),
            'label' => false,
			'required' => false
        ))
        ->getForm();

       $formpair->handleRequest($request);

        if ($formpair->isSubmitted()) {
            $data = $formpair->getData();
			$app['monolog']->debug('value of checkbox = '.$data['leader']);
			if($data['leader']==true)
			{
				$app['monolog']->debug('c est bien true');
			}
			if($data['leader']==false)
			{
				$app['monolog']->debug('c est bien false');
			}
            if (strlen($data['yourname']) > 30) {
              return $app['twig']->render('tournament.twig' , array('formpair' => $formpair->createView(),
                                                                'erroryourname' => true,
                                                                'erroryourfirstname' => false,
                                                                'erroryouremail' => false,
                                                                'errorpairname' => false,
                                                                'errorpairfirstname' => false,
                                                                'errorpairemail' => false ));
            } else if (strlen($data['yourfirstname']) > 30) {
              return $app['twig']->render('tournament.twig' , array('formpair' => $formpair->createView(),
                                                                'erroryourname' => false,
                                                                'erroryourfirstname' => true,
                                                                'erroryouremail' => false,
                                                                'errorpairname' => false,
                                                                'errorpairfirstname' => false,
                                                                'errorpairemail' => false ));
            } else if (strpos($data['youremail'],'@') == false) {
              return $app['twig']->render('tournament.twig' , array('formpair' => $formpair->createView(),
                                                                'erroryourname' => false,
                                                                'erroryourfirstname' => false,
                                                                'erroryouremail' => true,
                                                                'errorpairname' => false,
                                                                'errorpairfirstname' => false,
                                                                'errorpairemail' => false ));
            } else if (strlen($data['pairname']) > 30) {
              return $app['twig']->render('tournament.twig' , array('formpair' => $formpair->createView(),
                                                                'erroryourname' => false,
                                                                'erroryourfirstname' => false,
                                                                'erroryouremail' => false,
                                                                'errorpairname' => true,
                                                                'errorpairfirstname' => false,
                                                                'errorpairemail' => false ));
            } else if (strlen($data['pairfirstname']) > 30) {
              return $app['twig']->render('tournament.twig' , array('formpair' => $formpair->createView(),
                                                                'erroryourname' => false,
                                                                'erroryourfirstname' => false,
                                                                'erroryouremail' => false,
                                                                'errorpairname' => false,
                                                                'errorpairfirstname' => true,
                                                                'errorpairemail' => false ));
            } else if (strpos($data['pairemail'],'@') == false) {
              return $app['twig']->render('tournament.twig' , array('formpair' => $formpair->createView(),
                                                                'errorname' => false,
                                                                'errorfirstname' => false,
                                                                'erroremail' => false,
                                                                'errorpairname' => false,
                                                                'errorpairfirstname' => false,
                                                                'errorpairemail' => true ));
            }

            # we check if the user is already in the database as a player.
            try {
              $st = $app['pdo']->prepare("SELECT users.idusers FROM users WHERE users.last_name = :ul AND users.first_name = :uf AND users.e_mail_address = :ue");
              $st->execute(array(
                'ul' => $data['yourname'],
                'uf' => $data['yourfirstname'],
                'ue' => $data['youremail'] ));

              if (!($row = $st->fetch(PDO::FETCH_ASSOC))) {
                return $app['twig']->render('success.twig', array('status'=> 'Erreur',
                                                                  'staff' =>  false,
                                                                  'owner' => false,
                                                                  'superuser' => false,
                                                                  'tournament' => true ));
              }
            } catch (Exception $e) {
              return $app->redirect('/error');
            }

            # we check if the other user of the pair is already in the database as a player.
            try {
              $st = $app['pdo']->prepare("SELECT users.idusers FROM users WHERE users.last_name = :ul AND users.first_name = :uf AND users.e_mail_address = :ue");
              $st->execute(array(
                'ul' => $data['pairname'],
                'uf' => $data['pairfirstname'],
                'ue' => $data['pairemail'] ));

              if (!($row = $st->fetch(PDO::FETCH_ASSOC))) {
                return $app['twig']->render('success.twig', array('status'=> 'Erreur',
                                                                  'staff' =>  false,
                                                                  'owner' => false,
                                                                  'superuser' => false,
                                                                  'tournament' => true ));
              }
            } catch (Exception $e) {
              return $app->redirect('/error');
            }

            #$st = $app['pdo']->prepare('INSERT INTO pairs (user_j1_fk, user_j2_fk, tournament_fk,solo) values ((select MAX(idusers) from users where  e_mail_address = :e), (select MAX(idusers) from users where  e_mail_address = :pe), (select MAX(idtournaments) from tournaments where name = :tn and EXTRACT(year from day)=:td),0 )');
            $st = $app['pdo']->prepare('INSERT INTO pairs (user_j1_fk, user_j2_fk, tournament_fk,solo,wishes,leader) values ((select MAX(idusers) from users where  e_mail_address = :e), (select MAX(idusers) from users where  e_mail_address = :pe),:t,0,:w,:l )');


            $body='Vous avez correctement été inscrit au tournoi en tant que pair. <br> Veuillez vérifier que vos données sont correctes:'.
                '<br> Nom : '.$data['yourname'].'<br> Prénom : '.$data['yourfirstname'].'<br> Adresse Mail : '.$data['youremail'].'<br> Nom de votre partenaire: '.
                $data['pairname'].'<br> Prénom de votre partenaire : '.$data['pairfirstname'].'<br> Adresse Mail de votre partenaire: '.$data['pairemail'].
                '<br> Jour : '.$data['tournament'];

            $message = \Swift_Message::newInstance()
                ->setSubject('inscription')
                ->setFrom('sdpgroupei@gmail.com')
                ->setTo(array($data['youremail']))
                ->setBody($body,'text/html');

            $app['mailer']->send($message);
			if($data['leader']==1)
			{
				$leader=1;
			}else
			{
				$leader=0;
			}
            $st->execute( array(
              'e' => $data['youremail'],
              'pe' => $data['pairemail'],
              't' => $data['tournament'],
			  'w' => $data['wishes'],
			  'l' => $leader
            ));
            $app['session']->set('tournoiMail', array('email' => $data['youremail'] ));

            // redirect somewhere
            return $app->redirect('/pay');
        }

        // display the form
        return $app['twig']->render('tournament.twig' , array('formpair' => $formpair->createView(),
                                                                'erroryourname' => false,
                                                                'erroryourfirstname' => false,
                                                                'erroryouremail' => false,
                                                                'errorpairname' => false,
                                                                'errorpairfirstname' => false,
                                                                'errorpairemail' => false ));
});



// group search controller for a player registered in a tournament
$app->match('/test-group', function (Request $request) use ($app) {

	if($app['session']->get('staff') == null && $app['session']->get('owner')==null){
      $staf= false;
	  $owner=false;
	  $superuser=false;
    } elseif ($app['session']->get('staff') != null && $app['session']->get('owner')==null) {
      $val = $app['session']->get('staff');
      $su = false;
      $tmp = $app['session']->get('superuser');
      if($tmp == null){
        $su = false;
      } else {
        $su = $tmp['superuser'];
      }
	   $staf=$val['staff'];
	   $owner=false;
	   $superuser=$su;
    } elseif ($app['session']->get('staff') == null && $app['session']->get('owner')!=null) {
      # code...
      $val = $app['session']->get('owner');
	   $staf=false;
	   $owner=$val['owner'];
	   $superuser=false;
    } else {
      $val = $app['session']->get('staff');
      $val2 = $app['session']->get('owner');
      $su = false;
      $tmp = $app['session']->get('superuser');
      if($tmp == null){
        $su = false;
      } else {
        $su = $tmp['superuser'];
      }
	  $staf=$val['staff'];
	  $owner=$val['owner'];
	  $superuser=false;
    }
	$rt = $app['pdo']->prepare('SELECT idtournaments, name, day FROM tournaments WHERE day>(NOW()-interval\'1 day\')');
  $rt->execute();
  $displayTournaments=array();
  while ($row = $rt->fetch(PDO::FETCH_NUM)) {
    $displayTournaments[$row[0]] = $row[1].' ('.$row[2].')';
  }

  $formplayer = $app['form.factory']->createBuilder('form')
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
      ->add('tournament', 'choice', array(
          'choices' => $displayTournaments,
          'label' => false,
      ))
      ->getForm();

  $formplayer->handleRequest($request);

  if ($formplayer->isSubmitted()) {
    $data = $formplayer->getData();

    if (strlen($data['name']) > 30) {
      return $app['twig']->render('group.twig', array('formplayer' => $formplayer->createView(),
                                                      'issubmit' => false,
                                                      'errorname' => true,
                                                      'errorfirstname' => false,
                                                      'erroremail' => false,
													  'staff' =>  $val['staff'],
                                                      'owner' => $val2['owner'],
                                                      'superuser' => $su));
    } else if (strlen($data['firstname']) > 30) {
      return $app['twig']->render('group.twig', array('formplayer' => $formplayer->createView(),
                                                      'issubmit' => false,
                                                      'errorname' => false,
                                                      'errorfirstname' => true,
                                                      'erroremail' => false,
													'staff' =>  $val['staff'],
                                                     'owner' => $val2['owner'],
                                                     'superuser' => $su));
    } else if (strpos($data['email'],'@') == false) {
      return $app['twig']->render('group.twig', array('formplayer' => $formplayer->createView(),
                                                      'issubmit' => false,
                                                      'errorname' => false,
                                                      'errorfirstname' => false,
                                                      'erroremail' => true,
													  'staff' =>  $val['staff'],
                                                     'owner' => $val2['owner'],
                                                     'superuser' => $su));
    }

    try {
      $st = $app['pdo']->prepare("SELECT groups.idgroups, pairs.idpairs, courts.court_address, tournaments.day FROM users, pairs, courts, groups, tournaments WHERE tournaments.idtournaments= :tr AND users.last_name = :ul AND users.first_name = :uf AND users.e_mail_address = :ue AND (users.idusers = pairs.user_j1_fk OR users.idusers = pairs.user_j2_fk) AND pairs.group_fk = groups.idgroups AND groups.court_fk = courts.idcourts AND pairs.tournament_fk = tournaments.idtournaments");
      $st->execute(array(
        'tr' => $data['tournament'],
        'ul' => $data['name'],
        'uf' => $data['firstname'],
        'ue' => $data['email'] ));

      $names = array();
      while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
        $names[] = $row;
      }
    } catch (Exception $e) {
      return $app->redirect('/error');
    }
    // redirect somewhere
    return $app['twig']->render('group.twig' , array('formplayer' => $formplayer->createView(),'issubmit' => true, 'result' => $names,
													'staff' =>  $val['staff'],
                                                     'owner' => $val2['owner'],
                                                     'superuser' => $su));
  }
  // display the form
  return $app['twig']->render('group.twig' , array('formplayer' => $formplayer->createView(),'issubmit' => false,
													'staff' =>  $val['staff'],
                                                     'owner' => $val2['owner'],
                                                     'superuser' => $su));
});

// court owner inscription controller
$app->match('/test-owner', function (Request $request) use ($app) {
	$rt = $app['pdo']->prepare('SELECT idtournaments,name, day FROM tournaments WHERE day>(NOW()-interval\'1 day\')');
  $rt->execute(array());
	$displayTournaments=array();
	while ($row = $rt->fetch(PDO::FETCH_NUM)) {
		$displayTournaments[$row[0]]=$row[1].'('.$row[2] .')';
	}
    $formowner = $app['form.factory']->createBuilder('form')
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
            ->add('addresscourt', 'text', array(
                'constraints' => array(new Assert\NotBlank()),
                'attr' => array( 'placeholder' => 'Adresse de votre terrain (e.g. Nom de rue - Numéro + numéro de boîte  - Code postal - Localité)'),
                'label' => false
            ))
            ->add('courtstate', 'text', array(
                'constraints' => array(new Assert\NotBlank()),
                'attr' => array( 'placeholder' => 'Etat du terrain (e.g. Excellent - Bon - Mauvais)'),
                'label' => false
            ))
            ->add('courtinstructions', 'textarea', array(
                'attr' => array( 'placeholder' => 'Autres remarques à propos du terrain'),
                'label' => false
            ))
            ->add('surface', 'choice', array(
              'choices' => array('Concrete'=> 'Béton', 'Grass' => 'Herbe', 'Synthetic'=>'Synthétique' , 'Clay'=>'Terre Battue')
            ))
            ->add('indoor', 'choice', array(
              'choices' => array('1'=> 'Terrain couvert', '0' => 'Terrain extérieur')
            ))
            ->add('dispo', 'choice', array(
              'choices' => $displayTournaments
            ))
            ->getForm();

    $formowner->handleRequest($request);

    if ($formowner->isSubmitted()) {
        $data = $formowner->getData();

        if (strlen($data['name']) > 30) {
          return $app['twig']->render('owner.twig' , array('formowner' => $formowner->createView(),
                                                            'errorname' => true,
                                                            'errorfirstname' => false,
                                                            'errorpassword' => false,
                                                            'erroremail' => false,
                                                            'errorage' => false,
                                                            'errorinstructions' => false ));
        } else if (strlen($data['firstname']) > 30) {
          return $app['twig']->render('owner.twig' , array('formowner' => $formowner->createView(),
                                                            'errorname' => false,
                                                            'errorfirstname' => true,
                                                            'errorpassword' => false,
                                                            'erroremail' => false,
                                                            'errorage' => false,
                                                            'errorinstructions' => false ));
        } else if ($data['pass'] != $data['pass1']) {
          return $app['twig']->render('owner.twig' , array('formowner' => $formowner->createView(),
                                                            'errorname' => false,
                                                            'errorfirstname' => false,
                                                            'errorpassword' => true,
                                                            'erroremail' => false,
                                                            'errorage' => false,
                                                            'errorinstructions' => false ));
        } else if (strpos($data['email'],'@') == false) {
          return $app['twig']->render('owner.twig' , array('formowner' => $formowner->createView(),
                                                            'errorname' => false,
                                                            'errorfirstname' => false,
                                                            'errorpassword' => false,
                                                            'erroremail' => true,
                                                            'errorage' => false,
                                                            'errorinstructions' => false ));
        } else if ($data['age'] < 0 and $data['age'] > 99) {
          return $app['twig']->render('owner.twig' , array('formowner' => $formowner->createView(),
                                                            'errorname' => false,
                                                            'errorfirstname' => false,
                                                            'errorpassword' => false,
                                                            'erroremail' => false,
                                                            'errorage' => true,
                                                            'errorinstructions' => false ));
        } else if (strlen($data['courtinstructions']) > 1000) {
          return $app['twig']->render('owner.twig' , array('formowner' => $formowner->createView(),
                                                            'errorname' => false,
                                                            'errorfirstname' => false,
                                                            'errorpassword' => false,
                                                            'erroremail' => false,
                                                            'errorage' => false,
                                                            'errorinstructions' => true ));
        }

        if(strcmp($data['pass1'], $data['pass']) == 0){
        try {

        $st = $app['pdo']->prepare('SELECT count(*) FROM users WHERE e_mail_address=:m');
          $st->execute(array('m' => $data['email']));
          if($st->fetchColumn()==0){

			$st1 = $app['pdo']->prepare('INSERT INTO users ( last_name, first_name , e_mail_address, address , phone_number, user_type_fk, password)VALUES (:ln, :fn, :m, :ad,:ph,(SELECT iduser_type FROM users_type WHERE user_type_rule = :o), :p)');

			$body='Vous avez correctement été inscrit en tant que propriétaire de terrain. <br> Veuillez vérifier que vos données sont correctes:'.
            '<br> Nom : '.$data['name'].'<br> Prénom : '.$data['firstname'].'<br> Mot De Passe : '.$data['pass1'].'<br> Adresse Mail : '.
            $data['email'].'<br> Adresse : '.$data['address'].'<br> Numéro de téléphone : '.$data['phone'].'<br> Etat du terrain : '.
            $data['courtstate'].'<br> Surface : '.$data['surface'].'<br> Adresse du terrain : '.$data['addresscourt'].'<br> Remarque éventuelle : '.
            $data['courtinstructions'];

			$message = \Swift_Message::newInstance()
            ->setSubject('inscription')
            ->setFrom('sdpgroupei@gmail.com')
            ->setTo(array($data['email']))
            ->setBody($body,'text/html');

			$app['mailer']->send($message);

			$encyptedpassword = sha1($data['pass1']);

			$st1->execute( array(
          'ln' => $data['name'],
          'fn' => $data['firstname'],
          'm' => $data['email'],
          'ad'=> $data['address'],
          'ph'=> $data['phone'],
          'p' =>  $encyptedpassword,
          'o'=> 'owner' ));

         $st3 = $app['pdo']->prepare('INSERT INTO courts ( court_address, court_state, court_instructions, owner_user_fk, surface_fk, is_club, is_lend, isindoor, tournament_fk )VALUES (:ca, :cs, :ci, (SELECT MAX(idusers) FROM users WHERE e_mail_address = :e ),(SELECT MAX(idsurfaces) FROM surfaces WHERE surface_type = :st ), false , false, :indoor, :t)');
         $st3->execute( array(
            'ca' => $data['addresscourt'],
            'cs' => $data['courtstate'],
            'ci' => $data['courtinstructions'],
            'e' => $data['email'],
            'st' => $data['surface'],
            'indoor'=>  $data['indoor'],
            't'=> $data['dispo'] ));
		}
      } catch (Exception $e) {
        return $app->redirect('/error');
      }
        return $app->redirect('/succ');
    } else {
      return $app->redirect('/error');
    }
  }
    return $app['twig']->render('owner.twig', array('formowner' => $formowner->createView(),
                                                    'errorname' => false,
                                                    'errorfirstname' => false,
                                                    'errorpassword' => false,
                                                    'erroremail' => false,
                                                    'errorage' => false,
                                                    'errorinstructions' => false ));
});


// inscription to a tournament with no pair
$app->match('/alone', function (Request $request) use ($app) {

	$rt = $app['pdo']->prepare('SELECT idtournaments,name,day FROM tournaments WHERE day>(NOW()-interval\'1 day\')');
    $rt->execute(array());
	$displayTournaments=array();
	while ($row = $rt->fetch(PDO::FETCH_NUM)) {
		$displayTournaments[$row[0]]=$row[1].'('.$row[2].')';
	}
	$formalone = $app['form.factory']->createBuilder('form')
      ->add('yourname', 'text', array(
          'constraints' => array(new Assert\NotBlank()),
          'attr' => array( 'placeholder' => 'Nom'),
          'label' => false
      ))
      ->add('yourfirstname', 'text', array(
          'constraints' => array(new Assert\NotBlank()),
          'attr' => array( 'placeholder' => 'Prénom'),
          'label' => false
      ))
      ->add('youremail', 'email', array(
          'attr' => array( 'placeholder' => 'E-mail'),
          'label' => false,
          'constraints' => new Assert\Email()
      ))
      ->add('tournament', 'choice', array(
        'choices' => $displayTournaments,
		'label' => false
      ))
	  ->add('leader', 'choice', array(
		'choices' => array('1'=>'oui','0'=>'non'),
		'label' => false,
		'placeholder' => 'devenir une paire responsable?'
	  ))
	  ->add('wishes', 'textarea', array(
            'attr' => array( 'placeholder' => 'Demandes éventuelles'),
            'label' => false,
			'required' => false
       ))
      ->getForm();

      $formalone->handleRequest($request);
      if ($formalone->isSubmitted() ) {
        $data = $formalone->getData();

        if (strlen($data['yourname']) > 30) {
          return $app['twig']->render('alone.twig' , array('formalone' => $formalone->createView(),
                                                            'errorname' => true,
                                                            'errorfirstname' => false,
                                                            'erroremail' => false ));
        } else if (strlen($data['yourfirstname']) > 30) {
          return $app['twig']->render('alone.twig' , array('formalone' => $formalone->createView(),
                                                            'errorname' => false,
                                                            'errorfirstname' => true,
                                                            'erroremail' => false ));
        } else if (strpos($data['youremail'],'@') == false) {
          return $app['twig']->render('alone.twig' , array('formalone' => $formalone->createView(),
                                                            'errorname' => false,
                                                            'errorfirstname' => false,
                                                            'erroremail' => true ));
        }

        $st = $app['pdo']->prepare('INSERT INTO pairs (user_j1_fk, tournament_fk, solo,wishes,leader)
        values ((select MAX(idusers) from users where e_mail_address = :e),
        :t,:s,:w,:l )');

        $body='Vous avez correctement été inscrit au tournoi en solo. <br> Veuillez vérifier que vos données sont correctes:'.
        '<br> Nom : '.$data['yourname'].'<br> Prénom : '.$data['yourfirstname'].'<br> Adresse Mail : '.$data['youremail'].
        '<br> Jour : '.$data['tournament'];

        $message = \Swift_Message::newInstance()
        ->setSubject('inscription')
        ->setFrom('sdpgroupei@gmail.com')
        ->setTo(array($data['youremail']))
        ->setBody($body,'text/html');

        $app['mailer']->send($message);
		if($data['leader']==1)
		{
			$leader=1;
		}else
		{
			$leader=0;
		}
        $st->execute( array(
              'e' => $data['youremail'],
              't' => $data['tournament'],
              's'=> 1,
			  'w'=> $data['wishes'],
			  'l'=> $leader));

        $app['session']->set('tournoiMail', array('email' => $data['youremail'] ));

        return $app->redirect('/pay');
      }
      return $app['twig']->render('alone.twig' , array('formalone' => $formalone->createView(),
                                                        'errorname' => false,
                                                        'errorfirstname' => false,
                                                        'erroremail' => false ));
});

// inscription as a player 
$app->match('/test-player', function (Request $request) use ($app) {
    $formplayer2 = $app['form.factory']->createBuilder('form')
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
        ->add('ranking', 'text', array(
            'constraints' => array(new Assert\NotBlank()),
            'attr' => array( 'placeholder' => 'Votre classement'),
            'label' => false
        ))
        ->add('gender', 'choice', array(
          'choices' => array('Male'=> 'Homme', 'female' => 'Femme')
        ))
        ->add('age', 'integer', array(
                    'constraints' => array(new Assert\NotBlank()),
                    'attr' => array( 'placeholder' => 'Votre age'),
                    'label' => false
        ))
        ->getForm();

    $formplayer2->handleRequest($request);

    if ($formplayer2->isSubmitted()) {
        $data = $formplayer2->getData();

        if (strlen($data['name']) > 30) {
          return $app['twig']->render('player.twig' , array('formplayer2' => $formplayer2->createView(),
                                                            'errorname' => true,
                                                            'errorfirstname' => false,
                                                            'erroremail' => false,
                                                            'errorage' => false ));
        } else if (strlen($data['firstname']) > 30) {
          return $app['twig']->render('player.twig' , array('formplayer2' => $formplayer2->createView(),
                                                            'errorname' => false,
                                                            'errorfirstname' => true,
                                                            'erroremail' => false,
                                                            'errorage' => false ));
        } else if (strpos($data['email'],'@') == false) {
          return $app['twig']->render('player.twig' , array('formplayer2' => $formplayer2->createView(),
                                                            'errorname' => false,
                                                            'errorfirstname' => false,
                                                            'erroremail' => true,
                                                            'errorage' => false ));
        } else if ($data['age'] < 0 and $data['age'] > 99) {
          return $app['twig']->render('player.twig' , array('formplayer2' => $formplayer2->createView(),
                                                            'errorname' => false,
                                                            'errorfirstname' => false,
                                                            'erroremail' => false,
                                                            'errorage' => true ));
        }

        try {
          $st = $app['pdo']->prepare('SELECT count(*) FROM users WHERE e_mail_address=:m');
          $st->execute(array('m' => $data['email']));
          if($st->fetchColumn()==0){
          $st = $app['pdo']->prepare('INSERT INTO users ( last_name, first_name , e_mail_address, address , phone_number, ranking, gender, user_type_fk, age) VALUES (:ln, :fn, :m, :ad,:ph, :r, :g, (SELECT MAX(iduser_type) FROM users_type WHERE user_type_rule = :t), :a)');

          $body='Vous avez correctement été inscrit en tant que joueur du tournoi asmae. <br> Veuillez vérifier que vos données sont correctes:'.
                '<br> Nom : '.$data['name'].'<br> Prénom : '.$data['firstname'].'<br> Adresse Mail : '.$data['email'].'<br> Adresse : '.$data['address'].
                '<br> Numéro de téléphone : '.$data['phone'].'<br> Classement : '.$data['ranking'].'<br> Genre : '.$data['gender'].
                '<br> Si toutes ces informations sont correctes,veuillez vous inscrire au tournoi : <br> https://immense-inlet-2049.herokuapp.com/test-tournament';
          $message = \Swift_Message::newInstance()
            ->setSubject('inscription')
            ->setFrom('sdpgroupei@gmail.com')
            ->setTo(array($data['email']))
            ->setBody($body,'text/html');

          $app['mailer']->send($message);

          $st->execute( array(
            'ln' => $data['name'],
            'fn' => $data['firstname'],
            'm' => $data['email'],
            'ad'=> $data['address'],
            'ph'=> $data['phone'],
            'r' => $data['ranking'],
            'g' => $data['gender'],
            't' => 'player',
            'a' => $data['age'] ));
          } else {
            return $app->redirect('/error');
          }
      } catch (Exception $e) {
        return $app->redirect('/error');
      }
        return $app->redirect('/succ');
    }

    return $app['twig']->render('player.twig' , array('formplayer2' => $formplayer2->createView(),
                                                      'errorname' => false,
                                                      'errorfirstname' => false,
                                                      'erroremail' => false,
                                                      'errorage' => false ));
});
