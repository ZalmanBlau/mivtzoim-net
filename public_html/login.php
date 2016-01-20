<?php 
  require_once("../includes.php");
  use Cartalyst\Sentinel\Native\Facades\Sentinel;
  use Illuminate\Database\Capsule\Manager as Capsule;
  $_SESSION["flash"] = "Sign in to Mivtzoim.net";

  if(!empty($_POST)){
    $credentials = [
      'email'    => $_POST["email"],
      'password' => $_POST["password"]
    ];

    if(($user = Sentinel::authenticate($credentials, true))){
      Sentinel::login($user, true);

      $_SESSION["flash"] = "Welcome on Board!";
      set_session(); 
      header('Location:'.$_SESSION["login_redirect"]);
      die();
    }
    else{
      $_SESSION["flash"] = "The email and password entered don't match. Please try again.";
    }
  }
?>

<h1><?= $_SESSION["flash"] ?></h1>

<form action="login.php" method="post">
  <div> 
    <label for="email"> Email </label>
    <input type="email" name="email" id="email" required>
  </div>
  <div>
    <label for="password"> Password </label>
    <input type="password" name="password" id="password" required>
  </div>
  <div>
    <input type="submit" value="submit">
  </div>
</form>