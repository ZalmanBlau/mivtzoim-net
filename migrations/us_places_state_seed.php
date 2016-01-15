<?php
require_once(__DIR__ . '/../includes.php');

$file = "../documents/states.csv";
$handle = fopen($file, "r");
$i = 0;

$sql_statement = "INSERT IGNORE INTO `us_places` (`state`) VALUES ";

while(($data = fgetcsv($handle, 30, ",")) !== FALSE){
  if($i != 0){
    $sql_statement .= " ('$data[0]'), ('$data[1]'),";
  }
  $i++;
}
  
fclose($handle);

$sql_statement = preg_replace("/,$/", " ", $sql_statement);

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$mysqli->query($sql_statement);
eval(\Psy\sh());
?>