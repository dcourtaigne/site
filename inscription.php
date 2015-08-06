<?php
require_once ('includes/db.inc.php');
include ('includes/user.inc.php');

if(!empty($_SESSION["user_session"])){
  header("Location:profile.php");
}

if(isset($_POST['btn-login'])){
  $username = $_POST['txt_username'];
  $usermail = $_POST['txt_email'];
  $userpass = $_POST['txt_password'];

  if(empty($username)){
    $errors[] = "Veuillez indiquer un pseudo";
  } elseif(empty($usermail)){
    $errors[] = "Veuillez indiquer votre e-mail";
  } elseif(!preg_match('/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-]+$/',$usermail)){
    $errors[] = "Veuillez indiquer un e-mail valide";
  } elseif (empty($userpass)){
    $errors[] = "Veuillez indiquer un mot de passe";
  } elseif (strlen($userpass) < 8){
    $errors[] = "Le mot de passe indiqué n'est pas assez long (8 caractères minimum)";
  } else{
    $sql = 'SELECT user_name, user_email FROM users WHERE user_name=:user OR user_email=:email';
      try {
        $statement = $db_connexion->prepare($sql);
        $statement->execute(array(':user'=>$username , ':email'=>$usermail));
        $row = $statement->fetch();

        if($row["user_name"] == $username){
          $errors[] = "le pseudo a déjà été utilisé";
        }elseif ($row["user_email"] == $usermail){
          $errors [] = "Cette adresse email est déjà associé à un compte";
        }else{
          if(user_register($username, $usermail, $userpass, $db_connexion)){
            header("Location:inscription.php?success");
          }
        }
      }catch (Exception $e) {
          echo $e->getMessage();
      }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Inscription</title>
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" type="text/css"  />
<link rel="stylesheet" href="style.css" type="text/css"  />
</head>
<body>
<div class="container">
        <div class="form-container col-md-4 col-md-offset-3">
        <form method="post">
            <h2>Inscrivez-vous</h2><hr />
            <?php
            if(isset($errors)){
              foreach($errors as $error){
                ?>
                <div class="alert alert-danger"><?php echo $error ?></div>
                <?php
              }
            }
            else if (isset($_GET["success"])){
                    ?>
                <div class="alert alert-success">
                  Inscription avec succés! <a href="index.php">Connectez-vous</a>
                </div>
                <?php
            }
            ?>
            <div class="form-group">
                <input type="text" class="form-control" name="txt_username" placeholder="Username" required />
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="txt_email" placeholder="E-mail" required />
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="txt_password" placeholder="Your Password" required />
            </div>
            <div class="clearfix"></div><hr />
            <div class="form-group">
                <button type="submit" name="btn-login" class="btn btn-block btn-primary">
                    <i class="glyphicon glyphicon-log-in"></i>&nbsp;Inscrivez-vous
                </button>
            </div>
            <br />
            <label>vous avez un compte? <a href="inscription.php">Connectez-vous</a></label>
        </form>
       </div>
</div>

</body>
</html>
