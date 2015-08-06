<?php

session_start();

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "base3acces";

try {
  $db_connexion = new PDO("mysql:host=$db_host;dbname=$db_name;",$db_user,$db_pass);

} catch (PDOException $e) {
  echo $e->getMessage();
}
