<?php
  session_start();
  require_once(__DIR__ . '/vendor/autoload.php');
  require_once(__DIR__ . "/config.php");

  function projectAutoload($class_name) {
    require_once __DIR__ . "/resources/library/" . $class_name . '.class.php';
  }

  spl_autoload_register('projectAutoload');
  
  require_once(__DIR__ . "/resources/library/mergeInitializer.class.php");
  require_once("login_config.php");
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>

