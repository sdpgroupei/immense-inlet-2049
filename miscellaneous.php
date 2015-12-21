<?php
/**
 * Php file containing the controllers related to the
 * other pages
 */

use Silex\Provider\FormServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


#fonction appeler quand l'utilisateur essaie de lancer une fonctionnalit� non implement�
$app->get('/notimplemented', function() use($app) {



  return $app['twig']->render('notimplemented.twig');
});

# Gestion des erreurs et de la page d'erreur
$app->get('/error', function() use($app) {


  if($app['session']->get('staff') == null && $app['session']->get('owner')==null){
    return $app['twig']->render('success.twig',array('status'=> 'Erreur',
                                                    'staff' =>  false,
                                                    'owner' => false,
                                                    'superuser'=>false,
                                                    'tournament' => false
    ));
  } elseif ($app['session']->get('staff') != null && $app['session']->get('owner')==null) {

    $val = $app['session']->get('staff');
    $su = false;
    if($app['session']->get('superuser') != null){
      $tmp = $app['session']->get('superuser');
      $su = $temp['superuser'];

    }
    return $app['twig']->render('success.twig',array('status'=> 'Erreur',
                                                    'staff' =>  $val['staff'],
                                                    'owner' => false,
                                                    'superuser' => $su,
                                                    'tournament' => false
    ));
  }elseif ($app['session']->get('staff') == null && $app['session']->get('owner')!=null) {
    
    $val = $app['session']->get('owner');
    return $app['twig']->render('success.twig',array('status'=> 'Erreur',
                                                    'staff' =>  false,
                                                    'owner' => $val['owner'],
                                                    'superuser'=> false,
                                                    'tournament' => false
    ));
  }
   else {
    $val = $app['session']->get('staff');
    $val2 = $app['session']->get('owner');
    $su = false;
    if($app['session']->get('superuser') != null){
      $tmp = $app['session']->get('superuser');
      $su = $temp['superuser'];
    }
    return $app['twig']->render('success.twig',array('status'=> 'Erreur',
                                                    'staff' =>  $val['staff'],
                                                    'owner' => $val2['owner'],
                                                    'superuser' => $su,
                                                    'tournament' => false
    ));
  }

});

#fonction permettant de deco un utilisateur
$app->get('/disconnect', function() use($app) {

  $app['session']->set('staff', array('staff'=>false));
  $app['session']->set('owner', array('owner'=>false));
  $app['session']->set('superuser', array('superuser'=>false));

  return $app->redirect('/test');
});


# Fonction appel� quand l'ajout a la DB est un succes
$app->get('/succ', function() use($app) {

  if($app['session']->get('staff') == null && $app['session']->get('owner')==null){
    return $app['twig']->render('success.twig',array('status'=> 'Succes',
                                                    'staff' =>  false,
                                                    'owner' => false,
                                                    'superuser' =>false,
                                                    'tournament' => false
    ));
  } elseif ($app['session']->get('staff') != null && $app['session']->get('owner')==null) {
    # code...
    $val = $app['session']->get('staff');
    $su = false;
    if($app['session']->get('superuser') != null){
      $tmp = $app['session']->get('superuser');
      $su = $temp['superuser'];
    }
    return $app['twig']->render('success.twig',array('status'=> 'Succes',
                                                    'staff' =>  $val['staff'],
                                                    'owner' => false,
                                                    'superuser' => $su,
                                                    'tournament' => false
    ));
  }elseif ($app['session']->get('staff') == null && $app['session']->get('owner')!=null) {
    # code...
    $val = $app['session']->get('owner');
    return $app['twig']->render('success.twig',array('status'=> 'Succes',
                                                    'staff' =>  false,
                                                    'owner' => $val['owner'],
                                                    'superuser' =>false,
                                                    'tournament' => false
    ));
  }
   else {
    $val = $app['session']->get('staff');
    $val2 = $app['session']->get('owner');
    $su = false;
    if($app['session']->get('superuser') != null){
      $tmp = $app['session']->get('superuser');
      $su = $temp['superuser'];
    }
    return $app['twig']->render('success.twig',array('status'=> 'Succes',
                                                    'staff' =>  $val['staff'],
                                                    'owner' => $val2['owner'],
                                                    'superuser' => $su,
                                                    'tournament' => false
    ));
  }
});


#Fonction appeler si la connexion c'est bien pass�.
$app->get('/ok', function() use($app) {
  if($app['session']->get('staff') == null && $app['session']->get('owner')==null){
    return $app['twig']->render('success.twig',array('status'=> 'Connexion',
                                                    'staff' =>  false,
                                                    'owner' => false,
                                                    'superuser' =>false,
                                                    'tournament' => false
    ));
  } elseif ($app['session']->get('staff') != null && $app['session']->get('owner')==null) {
    # code...
    $val = $app['session']->get('staff');
    $su = false;
    $tmp = $app['session']->get('superuser');
    if($tmp == null){
      $su = false;

    } else {
      $su = $tmp['superuser'];

    }

    return $app['twig']->render('success.twig',array('status'=> 'Connexion',
                                                    'staff' =>  $val['staff'],
                                                    'owner' => false,
                                                    'superuser' => $su,
                                                    'tournament' => false
    ));
  }elseif ($app['session']->get('staff') == null && $app['session']->get('owner')!=null) {
    # code...
    $val = $app['session']->get('owner');
    return $app['twig']->render('success.twig',array('status'=> 'Connexion',
                                                    'staff' =>  false,
                                                    'owner' => $val['owner'],
                                                    'superuser',
                                                    'tournament' => false
    ));
  }
   else {
    $val = $app['session']->get('staff');
    $val2 = $app['session']->get('owner');
    $su = false;
    $tmp = $app['session']->get('superuser');
    if($tmp == null){
      $su = false;

    } else {
      $su = $tmp['superuser'];

    }


    return $app['twig']->render('success.twig',array('status'=> 'Connexion',
                                                    'staff' =>  $val['staff'],
                                                    'owner' => $val2['owner'],
                                                    'superuser' => $su,
                                                    'tournament' => false
    ));
  }


});
