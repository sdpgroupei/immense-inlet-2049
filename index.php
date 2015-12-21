<?php

/**
 * Main php file of the web Application
 * Contains part of the controllers and the
 * require statement that include others php files
 *
 */
require('../vendor/autoload.php');



#Les import + fuseau horaire
date_default_timezone_set('Europe/Brussels');
use Silex\Provider\FormServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;



// set up some variabes of the app
$app = new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

$app->before(function (Request $request) use ($app) {
    $app['twig']->addGlobal('active', $request->get("_route"));
});

$app->register(new FormServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'translator.domains' => array(),
));
$app->register(new Silex\Provider\SwiftmailerServiceProvider());
$from='sdpgroupei@gmail.com';
$app['swiftmailer.options'] = array(
    'host' => 'smtp.gmail.com',
    'port' => 465,
    'username' => 'sdpgroupei@gmail.com',
    'password' => 'groupeisdp',
    'encryption' => 'ssl',
    'auth_mode' => 'login');

$dbopts = parse_url(getenv('HEROKU_POSTGRESQL_BRONZE_URL'));
$app->register(new Herrera\Pdo\PdoServiceProvider(),
  array(
    'pdo.dsn' => 'pgsql:dbname='.ltrim($dbopts["path"],'/').';host='.$dbopts["host"],
    'pdo.port' => $dbopts["port"],
    'pdo.username' => $dbopts["user"],
    'pdo.password' => $dbopts["pass"]
  )
);


//SESSION gestion
$app->register(new Silex\Provider\SessionServiceProvider());



#Fonction pour supprimer un cours de la DB
$app->get('/deletecourt/{id}', function($id) use($app) {
	$val1 = $app['session']->get('staff');
	$val2 = $app['session']->get('owner');

	if($val1!=null || $val2!=null){


		$st = $app['pdo']->prepare('DELETE FROM courts where idcourts = :i');

		$st->execute(array(
			'i'=> $id
			));
		}
		#redirige vers la page de gestion des admins si on est connecté comme admin
		if($val1!=null){
			return $app->redirect('/admingestion');
		}
		#redirige vers la page de gestion des owner si on est connecté comme owner
		if($val2!=null){
			return $app->redirect('/ownergestion');
		}
		return $app->redirect('/test');
})->bind('deletecourt');


// delete a tournament
$app->get('/deletetournament/{id}', function($id) use($app) {
	$val1 = $app['session']->get('staff');

	if($val1!=null){
		$rt = $app['pdo']->prepare('DELETE FROM groups WHERE tournament_fk = :i');
		$st = $app['pdo']->prepare('DELETE FROM tournaments where idtournaments = :i');

		$rt->execute(array('i'=>$id));
		$st->execute(array(
			'i'=> $id
			));
		}
		#redirige vers la page de gestion des admins si on est connecté comme admin
		if($val1!=null){
			return $app->redirect('/edittournament');
		}
		return $app->redirect('/test');
})->bind('deletetournament');


// set a leader
$app->get('/setleader/{id}', function($id) use($app) {
	$val1 = $app['session']->get('staff');

	if($val1!=null){
		$rt = $app['pdo']->prepare('SELECT group_fk FROM pairs WHERE idpairs=:id');
		$rt->execute(array('id'=>$id));
		$row=$rt->fetch(PDO::FETCH_NUM);
		$st = $app['pdo']->prepare('UPDATE groups set leader=:l WHERE idgroups =:id');
		$st->execute(array('l'=>$id,'id'=>$row[0]));
		}
		#redirige vers la page de gestion des admins si on est connecté comme admin
		if($val1!=null){
			return $app->redirect('/admingroupgestion/'.$row[0]);
		}
		return $app->redirect('/test');
})->bind('setleader');

// split a pair
$app->get('/splitpair/{id}', function($id) use($app) {
	$val1 = $app['session']->get('staff');

	if($val1!=null){
		$rt2=$app['pdo']->prepare('SELECT tournament_fk,user_j1_fk,user_j2_fk FROM pairs WHERE idpairs=:id');
		$rt2->execute(array('id'=>$id));
		$row=$rt2->fetch(PDO::FETCH_NUM);
		$tournament=$row[0];
		$user1=$row[1];
		$user2=$row[2];
		$st2=$app['pdo']->prepare('INSERT INTO pairs(user_j1_fk,tournament_fk,solo) VALUES(:i1,:t,1)');
		$st2->execute(array('i1'=>$user1,
							't'=>$tournament));
		$st2->execute(array('i1'=>$user2,
							't'=>$tournament));
		$st4=$app['pdo']->prepare('UPDATE groups SET first=null where first=:id');
		$st4->excute(array('id'=>$id));
		$st5=$app['pdo']->prepare('UPDATE groups SET second=null where second=:id');
		$st5->excute(array('id'=>$id));
		$st6=$app['pdo']->prepare('UPDATE groups SET third=null where third=:id');
		$st6->excute(array('id'=>$id));
		$st7=$app['pdo']->prepare('UPDATE groups SET leader=null where leader=:id');
		$st7->excute(array('id'=>$id));
		$st3=$app['pdo']->prepare('DELETE FROM pairs WHERE idpairs=:id');
		$st3->execute(array('id'=>$id));
	}
	#redirige vers la page de gestion des admins si on est connecté comme admin
	if($val1!=null){
		return $app->redirect('/adminpairgestion');
	}
	return $app->redirect('/test');
})->bind('splitpair');



$app->get('/deletepair/{id}', function($id) use($app) {
	$val1 = $app['session']->get('staff');

	if($val1!=null){
		$st4=$app['pdo']->prepare('UPDATE groups SET first=null where first=:id');
		$st4->excute(array('id'=>$id));
		$st5=$app['pdo']->prepare('UPDATE groups SET second=null where second=:id');
		$st5->excute(array('id'=>$id));
		$st6=$app['pdo']->prepare('UPDATE groups SET third=null where third=:id');
		$st6->excute(array('id'=>$id));
		$st7=$app['pdo']->prepare('UPDATE groups SET leader=null where leader=:id');
		$st7->excute(array('id'=>$id));
		$st3=$app['pdo']->prepare('DELETE FROM pairs WHERE idpairs=:id');
		$st3->execute(array('id'=>$id));
	}
	#redirige vers la page de gestion des admins si on est connecté comme admin
	if($val1!=null){
		return $app->redirect('/adminpairgestion');
	}
	return $app->redirect('/test');
})->bind('deletepair');

$app->get('/deleteresult/{id}', function($id) use($app) {
	$val1 = $app['session']->get('staff');
	if($val1!=null){
		$st = $app['pdo']->prepare('DELETE FROM match where idmatch = :i');

		$st->execute(array(
			'i'=> $id
			));
		}
		#redirige vers la page de gestion des admins si on est connecté comme admin
		if($val1!=null){
			return $app->redirect('/adminresults');
		}
		return $app->redirect('/test');
})->bind('deleteresult');


#Fonction permetant de retirer des cours dans la DB
$app->get('/removefromcourt/{id}', function($id) use($app) {
  $val1 = $app['session']->get('staff');
  $val2 = $app['session']->get('owner');

  if(($val1!=null) || ($val2!=null)){
    $st = $app['pdo']->prepare('UPDATE courts SET staff_user_fk =0  WHERE idcourts = :c ');

  $st->execute(array(
    'c'=> $id
  ));
  }

   if($val1!=null){
            return $app->redirect('/admingestion');
        }
   if($val2!=null){
            return $app->redirect('/ownergestion');
        }

   else{
            return $app->redirect('/test');
        }

})
->bind('removefromcourt');



require 'home.php'; // controllers related to the homepage
require 'admin.php'; // controllers related to the admin management pages
require 'miscellaneous.php';// controllers used often like error or succes pages
require 'knockoff.php';// controllers related to the knockoff tournament



//other controllers without a category

#Fonction permettant de créer la page de gestion pour les owner
$app->match('/ownergestion', function (Request $request) use ($app) {
  $val2 = $app['session']->get('owner');
  $uemail = $val2['email'];

  $st = $app['pdo']->prepare('SELECT idcourts, court_address,court_instructions, court_state,surface_type,name, isindoor FROM surfaces,courts,tournaments WHERE surface_fk=idsurfaces AND tournament_fk=idtournaments AND owner_user_fk = (SELECT MAX(idusers) FROM users WHERE e_mail_address = :e)');
  $st->execute(array(
    'e'=>$uemail
  ));

  $courts = array();
  while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
    $courts[] = $row;
  }

  $rt = $app['pdo']->prepare('SELECT idtournaments,name,day FROM tournaments WHERE day>(NOW()-interval\'1 day\')');
  $rt->execute(array());
  $displayTournaments=array();
  while ($row = $rt->fetch(PDO::FETCH_NUM)) {
	$displayTournaments[$row[0]]=$row[1].'('.$row[2].')';
  }

  $formowner = $app['form.factory']->createBuilder('form')
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
            'choices' => array('Concrete'=> 'Béton', 'Grass' => 'Herbe', 'Synthetic'=>'Synthetique' , 'Clay'=>'Terre Battue')
          ))
          ->add('dispo', 'choice', array(
            'choices' => $displayTournaments
          ))
          ->add('indoor', 'choice', array(
            'choices' => array('1' => 'Terrain couvert', '2'=>'Terrain extérieur')
          ))
          ->getForm();

  $formowner->handleRequest($request);
  if ($formowner->isSubmitted()) {
      $data = $formowner->getData();

      try {

          $st3 = $app['pdo']->prepare('INSERT INTO courts ( court_address, court_state, court_instructions, owner_user_fk, surface_fk, is_club, is_lend, isindoor , tournament_fk)VALUES (:ca, :cs, :ci, (SELECT MAX(idusers) FROM users WHERE e_mail_address = :e ),(SELECT MAX(idsurfaces) FROM surfaces WHERE surface_type = :st ), false , false, :indoor, :disp)');
          $st3->execute( array(
            'ca' => $data['addresscourt'],
            'cs' => $data['courtstate'],
            'ci' => $data['courtinstructions'],
            'e' => $uemail,
            'st' => $data['surface'],
            'indoor'=>  $data['indoor'],
            'disp'=> $data['dispo']

          ));



    } catch (Exception $e) {
      return $app->redirect('/error');
    }
      return $app->redirect('/ownergestion');
  }

  $owner_array = $app['session']->get('owner');
  if($owner_array == null){
    return $app['twig']->render('illegal.twig', array('group' => false));
  } else {
    if ($owner_array['owner'] == false) {
        return $app['twig']->render('illegal.twig', array('group' => false));
    } else {
        return $app['twig']->render('courtowner.twig' , array('courts'=>$courts, 'formowner' => $formowner->createView()));
    }
  }
});

#Fonction utilisé pour créer un compte superuser.
$app->match('/382132701c4733c3402706cfdd3c8fc7f41f80a88dce5428d145259a41c5f12f', function (Request $request) use ($app) {
    // some default data for when the form is displayed the first time

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
                't' => 'superuser'
            ));
          }
          else{
            return $app->redirect('/error');
          }

      } catch (Exception $e) {
        //TODO
        return $app->redirect('/error');

      }

        // redirect somewhere
        return $app->redirect('/succ');
      } else {
        return $app->redirect('/error');
      }
    }


    // display the form
    return $app['twig']->render('superuser.twig' , array('formad' => $formad->createView()
                                                       )
                                                    );
});

#fonction pour gerer les payement
$app->match('/pay',  function(Request $request) use($app) {
  $paymentinfo = false;
  $formpay = $app['form.factory']->createBuilder('form')
  ->add('yourpaymethod', 'choice', array(
          'choices' => array('surPlace'=> 'Sur place', 'virement' => 'Par virement', 'paypal' => 'Paypal')
        ))
  ->getForm();
  $formpay->handleRequest($request);

  $Tm = $app['session']->get('tournoiMail');
  $Tmail = $Tm['email'];

 //TODO add request payment method to db
  if ($formpay->isSubmitted()) {
            $data = $formpay->getData();

            // request to put payment method in db pairs
            $st = $app['pdo']->prepare('UPDATE  pairs set payment = (:p) where idpairs =
              (SELECT max(idpairs) from pairs where user_j1_fk =
                (SELECT max(idusers) from users where e_mail_address = :m)
              )
            ');
              $st->execute( array(
                'p'=> $data['yourpaymethod'],
                'm'=> $Tmail
              ));

            if($data['yourpaymethod']=='virement'){
              $paymentinfo = 'virement';
              $body='Pour finaliser votre inscription au tournoi, vous avez indiqué vouloir payer par virement.<br>
              Veuillez payer le montant dû au moins cinq jours ouvrables avant le tournoi sur le numéro de compte: BE46 1430 7568 2636.<br><br>
              En espérant vous voir en forme au tournoi,<br>
              Le staff Asmae';

              $message = \Swift_Message::newInstance()
              ->setSubject('Paiement')
              ->setFrom('sdpgroupei@gmail.com')
              ->setTo($Tmail)
              ->setBody($body,'text/html');

              $app['mailer']->send($message);
            }

            if($data['yourpaymethod']=='paypal'){
              $paymentinfo = 'paypal';
              $body='Pour finaliser votre inscription au tournoi, vous avez indiqué vouloir payer via Paypal.<br>
              Pour payer, veuillez cliquer sur le bouton "Faire un don" en-dessous.<br><br>
              <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                                <input type="hidden" name="cmd" value="_s-xclick">
                                <input type="hidden" name="hosted_button_id" value="BBGJBR473E4FC">
                                <input type="image" src="https://www.paypalobjects.com/fr_FR/BE/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal, le réflexe sécurité pour payer en ligne">
                                <img alt="" border="0" src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
              </form>
              <br><br>
              En espérant vous voir en forme au tournoi,<br>
              Le staff Asmae';

              $message = \Swift_Message::newInstance()
              ->setSubject('Paiement')
              ->setFrom('sdpgroupei@gmail.com')
              ->setTo($Tmail)
              ->setBody($body,'text/html');

              $app['mailer']->send($message);
            }

            if($data['yourpaymethod']=='surPlace'){
              $body='Pour finaliser votre inscription au tournoi, vous avez indiqué vouloir payer sur place.<br>
              Veuillez ne pas oublier de payer le montant dû, au moins 5 jours ouvrable avant le tournoi.
              Dans le cas contraire vous ne serez pas inscrit au tournoi.<br><br>
              En espérant vous voir en forme au tournoi,<br>
              Le staff Asmae';

              $message = \Swift_Message::newInstance()
              ->setSubject('Paiement')
              ->setFrom('sdpgroupei@gmail.com')
              ->setTo($Tmail)
              ->setBody($body,'text/html');

              $app['mailer']->send($message);
              return $app->redirect('/succ');
            }
        }

        return $app['twig']->render('formpay.twig', array(
          'formpay' => $formpay->createView(),
          'paymentinfo'=> $paymentinfo
        ));
});

#Fonction appeler si le payement c'est bien passé.
$app->get('/paysuccess', function() use($app) {
    return $app['twig']->render('paysuccess.twig');
});




$app->run();
