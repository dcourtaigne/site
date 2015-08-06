<?php
require_once ('includes/db.inc.php');
include ('includes/user.inc.php');
?>
<?php
  if(empty($_SESSION["user_session"])){
  header("Location:index.php");
  }

  $user_id = $_SESSION['user_session'];
  try {
    $statement = $db_connexion->prepare("SELECT * FROM users where id_user=:userid");
    $statement->bindparam(":userid", $user_id);
    $statement->execute();
    $rowUser = $statement->fetch();
  } catch (PDOException $e) {
      echo $e->getMessage();
  }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Bienvenu sur votre profil <?php print($rowUser['user_name']); ?></title>
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" type="text/css"  />
<link rel="stylesheet" href="style.css" type="text/css"  />
</head>
<body>
<div class="container">
  <div class="header">
    <div class="left"><a href="logout.php?logout=true">Déconnexion</a></div>
  </div>
</div>
<h1>Bienvenu sur votre profil <?php print($rowUser['user_name']); ?></h1>
<div>
  <ul class="col-md-2">
    <li><a href="profile.php?action=modifier">Modifier votre profil</a></li>
    <li><a href="profile.php?action=uploadphoto">Ajouter une photo</a></li>
    <li><a href="profile.php?action=supprimer">Supprimer votre profil</a></li>
  </ul>
  <?php

  $action = (isset($_GET['action']))?$_GET['action']:'';

  switch($action){
    case 'modifier':
      echo modifierProfil($db_connexion);
      break;
    case 'uploadphoto':
      echo uploadPhoto();
      break;
    case 'supprimer':
      # code...
      break;
    default:

      break;
  }
    echo getPhoto('upload');
  ?>
  <ul class="col-md-4">
    <?php echo getUserInfo($db_connexion);?>
  </ul>
</div>


<?php
if (isset($_POST['submit'])){
  if(!empty($_POST['name']) && !empty($_POST['email']) && strlen($_POST['codepostal'])<=5 && strlen($_POST['tel'])<=10){
    var_dump($_POST);
    $userID =$_SESSION['user_session'];
    $sql="UPDATE `users` SET `user_name`=:user,`user_email`=:email,`user_address`=:address,
                             `user_codepostal`=:codepostal,`user_ville`=:ville,`user_tel`=:tel WHERE `id_user`=".$userID;
    $statement = $db_connexion->prepare($sql);
    if($statement->execute(array(':user'=>$_POST['name'], ':email'=>$_POST['email'], ':address'=>$_POST['address'],
                              ':codepostal'=>$_POST['codepostal'],':ville'=>$_POST['ville'],':tel'=>$_POST['tel']))){
     header('location:profile.php');
    }
  }
}

if (isset($_FILES['avatar'])){
  $filename = $_FILES['avatar']['tmp_name'];
  $destination = 'upload/';
  $userID =$_SESSION['user_session'];
  $extensions = array('.png', '.gif', '.jpg', '.jpeg','.PNG');
  $extension = strrchr($_FILES['avatar']['name'], '.');

  $fichier = "photo_user_".$userID.$extension;
  //echo $fichier; exit();

  $taille_maxi = 300000;
  $taille = filesize($_FILES['avatar']['tmp_name']);


  if(!in_array($extension, $extensions)) $erreur = "le fichier n'est pas une image";
  if($taille>$taille_maxi) $erreur = 'Le fichier est trop gros...';

  if(!isset($erreur)){
    if(move_uploaded_file ($filename,$destination.$fichier)){
    header('location:profile.php');
    }else{
      echo "Le fichier n'a pas pu être téléchargé";
    }
  }else{
    echo $erreur;
  }

}

?>

</body>
</html>


