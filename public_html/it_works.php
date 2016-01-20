<h1> It Works! </h1>
<?php 
  require_once(__DIR__ . "/../includes.php");
  
  $initializer->merge_data();
  header("Location: /public_html/mergeresult.php/");
?>