<?php
require_once 'includes/db.inc.php';
include ('includes/user.inc.php');

if (isset($_GET["logout"]) && $_GET["logout"]=='true'){
  user_logout($_SESSION["user_session"]);
  header("Location:index.php");
}

if(!isset($_SESSION["user_session"])){
  header("Location:index.php");
}
