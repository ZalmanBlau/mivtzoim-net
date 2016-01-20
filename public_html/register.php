<?php 
  require_once("../includes.php");
  use Cartalyst\Sentinel\Native\Facades\Sentinel;
  $_SESSION["flash"] = "Create your Mivtzoim.net Account";

  if(!empty($_POST)){
    $credentials = [
      'email'    => $_POST["email"],
      'password' => $_POST["password"],
      'first name' => $_POST["first_name"],
      'last name' => $_POST["last_name"],
      'chabad_name' => $_POST["chabad_name"]
    ];
    if(register_valid()){
      $user = Sentinel::register($credentials, true);
      Sentinel::login($user, true);

      $_SESSION["flash"] = "Welcome on Board!";
      header('Location:'.$_SESSION["login_redirect"]);
    }
    else{
      $_SESSION["flash"] = "Email already taken. Please try again.";
    }
  }
?>

<h1><?= $_SESSION["flash"] ?></h1>

<form action="register.php" method="post">
  <div> 
    <label for="email"> Email </label>
    <input type="email" name="email" id="email" required>
  </div>

  <div>
    <label for="password"> Password </label>
    <input type="password" name="password" id="password" required>
  </div>

  <div>
    <label for="first_name"> First Name </label>
    <input type="first_name" name="first_name" id="first_name" required>
  </div>

  <div>
    <label for="last_name"> Last Name </label>
    <input type="last_name" name="last_name" id="last_name" required>
  </div>

  <div>
    <label for="chabad_name"> Chabad Center </label>
    <input type="chabad_name" name="chabad_name" id="chabad_name" required>
  </div>

  <div>
    <input type="submit" value="submit">
  </div>
</form>