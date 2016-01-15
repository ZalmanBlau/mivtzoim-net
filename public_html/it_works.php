<h1> It Works! </h1>
<?php 
  require_once(__DIR__ . "/../includes.php");
  
  // dataMerge was initiated together with columnify
  // and has access to it and it's methods. 
  eval(\Psy\sh());
  $data_merge->handle_form($_POST);
?>