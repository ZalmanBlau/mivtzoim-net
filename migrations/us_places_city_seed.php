<?php

require_once(__DIR__ . '/../includes.php');

$file = "../documents/national_places.csv";
$handle = fopen($file, "r");

$sql_statement = "INSERT IGNORE INTO `us_places` (`city`) VALUES ";

while(($data = fgetcsv($handle, 1000, ",")) !== FALSE){
  preg_match("/(.+) .+$/", $data[3], $matches);
  if($matches[1] !== NULL){
    $city = str_replace("'", "\'", $matches[1]);
    $sql_statement .= " ('$city'),";
  }
}
  
fclose($handle);

$sql_statement = preg_replace("/,$/", " ", $sql_statement);

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
eval(\Psy\sh());
$mysqli->query($sql_statement);

?>