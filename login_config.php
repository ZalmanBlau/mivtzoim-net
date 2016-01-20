<?php 
require_once('config.php');
session_start();
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => DB_HOST,
    'database'  => DB_NAME,
    'username'  => DB_USER,
    'password'  => DB_PASSWORD,
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
]);

$capsule->bootEloquent();

$_SESSION["login_redirect"] = !$_SESSION["login_redirect"] ? "login_success.php" : $_SESSION["login_redirect"];


function set_session(){
  $user_id = Sentinel::getUser()->getUserId();
  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  $result = $mysqli->query("SELECT `file_path` FROM `mivtzoim_user_files` WHERE `mivtzoim_user_id` = $user_id");
  if($result){
    $_SESSION["file_path"] = $result->fetch_array(MYSQLI_ASSOC)["file_path"];
  }
}

function register_valid(){
  $credentials = [
    "email" => $_POST["email"]
  ];

  if(!Sentinel::getUserRepository()->findByCredentials($credentials)){
    return true;
  }
  return false;
}

  
?>