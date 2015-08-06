<?php
function user_register($username, $usermail, $userpass, $db_connexion){
  try {
    $newpass = md5($userpass);//fonction de cryptage du mot de passe, MD5 est une fonction de cryptage symétrique (une autre est sha1())
    $statement = $db_connexion->prepare('INSERT INTO `users`(`id_user`, `user_name`, `user_email`, `user_pass`)
                                         VALUES (NULL,:user,:email,:pass);');
    $statement->execute(array(':user'=>$username , ':email'=>$usermail , ':pass'=>$newpass));
    return $statement;
    } catch (Exception $e) {
      echo $e->getMessage();
    }
}

function user_login($username, $usermail, $userpass, $db_connexion){

  try {
    $sql = 'SELECT * FROM users where user_name=:user OR user_email=:email';
    $statement = $db_connexion->prepare($sql);
    $statement->execute(array(':user'=>$username , ':email'=>$usermail));
    $userRow = $statement->fetch();

    if($statement->rowCount() > 0){
      if($userRow['user_pass'] == md5($userpass)){
        $_SESSION["user_session"] = $userRow['id_user'];
        return true;
      }else{
        return false;
      }
    }
  } catch (Exception $e) {
    echo $e->getMessage();
  }
}

function user_logout($sessionID){
  session_destroy();
  unset($sessionID);
  return true;
}

function getUserInfo($db_connexion){
  $userID =$_SESSION['user_session'];
  try {
    $sql = 'SELECT * FROM users where id_user=:user';
    $statement = $db_connexion->prepare($sql);
    $statement->execute(array(':user'=>$userID));
    $userInfo="";
    while($userRow = $statement->fetch()){
      $userInfo .= "<li>Pseudo: ".$userRow['user_name']."</li>";
      $userInfo .= "<li>E-mail: ".$userRow['user_email']."</li>";
      $userInfo .= getAddress($db_connexion, $userRow, $userID);
      $userInfo .= getPhone($db_connexion, $userRow, $userID);
    }
    return $userInfo;
  } catch (Exception $e) {
    echo $e->getMessage();
  }
}

function getAddress($db_connexion, $userRow, $userID){
    if(empty($userRow['user_address']) && empty($userRow['user_ville']) && $userRow['user_codepostal'] == "0" ){
      $userFullAddress = "<li>Adresse: Aucune adresse renseignée dans le profil</li>";
    }else{
      $userFullAddress = "<li>Adresse: ".$userRow['user_address'].", ".$userRow['user_codepostal']." ".$userRow['user_ville']."</li>";
    }
    return $userFullAddress;
}

function getPhone($db_connexion, $userRow, $userID){
    if($userRow['user_tel'] == "0" ){
      $userFullAddress = "<li>Telephone: Aucun numéro de téléphone renseigné dans le profil</li>";
    }else{
      $userFullAddress = "<li>Telelphone: ".$userRow['user_tel']."</li>";
    }
    return $userFullAddress;
}

function getPhoto($dirTarget){
  $userID =$_SESSION['user_session'];
  $file = "photo_user_".$userID.".jpg";
  $fileDefault = "default.jpg";
  $filePath = $dirTarget."/".$file;
  $filePathDefault = $dirTarget."/".$fileDefault;
  if (!file_exists($filePath)){
    return "<img src='".$filePathDefault."'>";
  }else{
    return "<img src='".$filePath."'>";
  }
}

function modifierProfil($db_connexion){
  if(isset($_GET['action']) && $_GET['action'] == 'modifier'){
    $userID =$_SESSION['user_session'];
    try {
      $sql = 'SELECT * FROM users where id_user=:user';
      $statement = $db_connexion->prepare($sql);
      $statement->execute(array(':user'=>$userID));
      $userModForm="<div class='form-group col-md-4'><form action='' method='POST'>";
      while($userRow = $statement->fetch()){
        $userModForm .= "<label>Pseudo</label><input type='text' class='form-control' name='name' value='".$userRow['user_name']."'>";
        $userModForm .= "<label>E-mail</label><input type='email'class='form-control' name='email' value='".$userRow['user_email']."'>";
        $userModForm .= "<label>Adresse</label><input type='text' class='form-control' name='address' value='".$userRow['user_address']."'>";
        $userModForm .= "<label>Code Postal</label><input type='text' class='form-control' name='codepostal' value='".$userRow['user_codepostal']."'>";
        $userModForm .= "<label>Ville</label><input type='text' class='form-control' name='ville' value='".$userRow['user_ville']."'>";
        $userModForm .= "<label>Telephone</label><input type='text' class='form-control' name='tel' value='".$userRow['user_tel']."'>";
        $userModForm .= "<input type='submit' name='submit' value='Envoyer'>";
      }
      $userModForm .= "</form></div>";
      return $userModForm;
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }
}

function uploadPhoto(){
  if(isset($_GET['action']) && $_GET['action'] == 'uploadphoto'){
    $formPhoto="<div><form enctype='multipart/form-data' action='profile.php' method='post'>
                <input type='hidden' name='MAX_FILE_SIZE' value='300000' />
                Envoyez ce fichier : <input name='avatar' type='file' />
                <input type='submit' value='Envoyer la photo' />
                </form></div>";
    return $formPhoto;
  }
}
