<?php 
  require_once("../includes.php");
  use Cartalyst\Sentinel\Native\Facades\Sentinel;
  use Illuminate\Database\Capsule\Manager as Capsule;
  Sentinel::getUser();

  if(!Sentinel::check()){
    $_SESSION["login_redirect"] = "/public_html/mergeresult.php";
    header("location: /public_html/login.php");
    die();
  }
  // $data_merge_result var inherited from mergeInitializer file.
  $data_merge_result = DataMerge::past_results();
  $new_data = $data_merge_result[0]["new"];
  $existing_data = $data_merge_result[0]["existing"];
  $zipcode_list = $data_merge_result[1];

  $num_new = count($new_data);
  $num_existing = count($existing_data);
?>

<h1>Data merge with Mivtzoim.net Contacts</h1>

<p><a href=<?=$_SESSION["file_path"]?>>Source spreadsheet</a></p>

<p>Zip codes: <?= $zipcode_list; ?></p>

<p># New Contacts: <strong><?= number_format($num_new); ?></strong>  - these are contacts in Mivtzoim.net DB not found in your spreadsheet

<!--
<p># Moved Contacts: <strong><?= number_format($num_moved); ?></strong>  - these are contacts in both Mivtzoim.net DB and your spreadsheet but have  spreadsheet
-->

<p># Existing Contacts: <strong><?= number_format($num_existing); ?></strong>  - these are contacts in Mivtzoim.net DB AND your spreadsheet</p>

<h2>New Data Sample</h2>
<table cellpadding="5" cellspacing="5">
  <thead>
    <tr>
      <th>First Name</th>
      <th>Last Name</th>
      <th>Address</th>
      <th>Zip</th>
      <th>Jewishness</th>
      <th>Result</th>
    </tr>
  </thead>
  <tbody>
  <?php 
  $i=0;
  foreach ( $new_data as $key=>$row ){ 
    if ( $i<=100){
  ?>
    <tr>
      <td><?= $row["first_name"]; ?></td>
      <td><?= $row["last_name"]; ?></td>
      <td><?= $row["address"]; ?></td>
      <td><?= $row["zip"]; ?></td>
      <td><?= $row["jewishness"]; ?></td>
      <td><?= $row["result"]; ?></td>           
    </tr>
  <?php 
    }
    $i++;
  } 
  ?>
  </tbody>
</table>

<!--
<h2>Moved Data Sample</h2>
<table cellpadding="5" cellspacing="5">
  <thead>
    <tr>
      <th>First Name</th>
      <th>Last Name</th>
      <th>Address</th>
      <th>Zip</th>
      <th>Jewishness</th>
      <th>Result</th>
    </tr>
  </thead>
  <tbody>
  <?php 
  /**
  $i=0;
  foreach ( $moved_data as $key=>$row ){ 
    if ( $i<=100){
  ?>
    <tr>
      <td><?= $row["first_name"]; ?></td>
      <td><?= $row["last_name"]; ?></td>
      <td><?= $row["address"]; ?></td>
      <td><?= $row["zip"]; ?></td>
      <td><?= $row["jewishness"]; ?></td>
      <td><?= $row["result"]; ?></td>           
    </tr>
  <?php 
    }
    $i++;
  } 
  **/
  ?>
  </tbody>
</table>
-->


<h2>Existing Data Sample</h2>
<table cellpadding="5" cellspacing="5">
  <thead>
    <tr>
      <th>First Name</th>
      <th>Last Name</th>
      <th>Address</th>
      <th>Zip</th>
      <th>Jewishness</th>
      <th>Result</th>
    </tr>
  </thead>
  <tbody>
  <?php 
  $i=0;
  foreach ( $existing_data as $key=>$row ){ 
    if ( $i<=100){
  ?>
    <tr>
      <td><?= $row["first_name"]; ?></td>
      <td><?= $row["last_name"]; ?></td>
      <td><?= $row["address"]; ?></td>
      <td><?= $row["zip"]; ?></td>
      <td><?= $row["jewishness"]; ?></td>
      <td><?= $row["result"]; ?></td>           
    </tr>
  <?php 
    }
    $i++;
  } 
  ?>
  </tbody>
</table>

<?php

$time_elapsed = microtime(true) - $start;

echo "<p>Runtime: " . $time_elapsed. " seconds</p>";

?>


