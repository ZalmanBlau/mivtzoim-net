<?php 
  require_once("../includes.php");
  use Cartalyst\Sentinel\Native\Facades\Sentinel;
  use Illuminate\Database\Capsule\Manager as Capsule;
  Sentinel::logout(null, true);
  session_unset();
?>

<h3> You've been logged out. </h3>