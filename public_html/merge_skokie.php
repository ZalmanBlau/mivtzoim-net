<?php
/**
	Chabad of Skokie Merge from old data 

	New Contact if Last Name + Address # + Zip code match
	Moved Contact if Address # + Zip code match

**/
// B"H
### note ###
require_once(__DIR__ . '/../includes.php');
include(__DIR__ . "/../config.php");
###

### change back ###
// require("/home/mivtzoim/public_html/admin/config.php");

###

ini_set('max_execution_time',86400); // in seconds (24 hours)
ini_set('memory_limit','128M');
ini_set('max_input_time',1800); // in seconds
ini_set('post_max_size', '64M');
ini_set('upload_max_filesize', '64M');


$now = time();

$start = microtime(true);

// connect to db
error_reporting(E_ERROR | E_WARNING | E_PARSE);

$input_data = array();
$new_data = array();
$moved_data = array();
$existing_data = array();

$input_test_strs = array(); // for finding new contacts
$input_test_strs2 = array(); // for finding moved contacts

$num_new = 0; // contacts in Mivtzoim.net DB not in old spreadsheets i.e. new SOURCE
$num_moved = 0; // contacts in Mivtzoim.net DB with SAME source but different LAST NAME
$num_existing = 0; // contacts in spreadsheet + Mivtzoim.net DB

$csv_row = 1;
### change back ###

// if (($handle = fopen("../documents/skokie_1_5_2015_data.csv", "r")) !== FALSE) {
if (($handle = fopen("../documents/skokie_1_5_2015_data.csv", "r")) !== FALSE) {

###################
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    	/**
			1 Name
			2 Address
			4 Zipcode
    	**/

    	// last_name + address_num + zip_code

    	$name_arr = explode(" ",$data[1]);
    	$last_name = trim($name_arr[count($name_arr)-1]);
		$address_arr = explode(" ",$data[2]);
		$address_num = trim($address_arr[0]);
		$zipcode = trim($data[4]);

		// Finding new contacts
		$test_str = $last_name . "_" . $address_num . "_" . $zipcode;
		array_push($input_test_strs,$test_str);

		// Finding moved contacts
		$test_str2 = $address_num . "_" . $zipcode;
		array_push($input_test_strs2,$test_str2);


    	$csv_row++;

    }
    fclose($handle);
}


$zips = array("60076","60077","60203");

foreach ( $zips as $zip ){
	
	$result = $mysqli->query("SELECT * FROM `res_master` WHERE `zip` = '" . $zip . "'");
	while ( $row = $result->fetch_array(MYSQLI_ASSOC)){

		/**
		New Contact if Last Name + Address # + Zip code match
		Moved Contact if Address # + Zip code match
		**/

		$address_arr = explode(" ",$row["address"]);
		$address_num = trim($address_arr[0]);

		// Finding new contacts
		$test_str = $row["last_name"] . "_" . $address_num . "_" . $row["zip"];

		// Finding moved contacts
		$test_str2 = $address_num . "_" . $row["zip"];

		if ( !in_array($test_str,$input_test_strs) ){
			// New contact		
			$text_result = "New Contact (Not in your spreadsheet)";
			$num_new++;		
	    	array_push($new_data,
	    		array(
	    			"first_name"=>$row["first_name"],
	    			"last_name"=>$row["last_name"],
	    			"address"=>$row["address"],
	    			"zip"=>$row["zip"],
	    			"jewishness"=>$row["jewishness"],
	    			"result"=>$text_result
	    		)
	    	);

		} else { // existing contact

			$text_result = "Not new Contact (already in your spreadsheet)";
			$num_existing++;

	    	array_push($existing_data,
	    		array(
	    			"first_name"=>$row["first_name"],
	    			"last_name"=>$row["last_name"],
	    			"address"=>$row["address"],
	    			"zip"=>$row["zip"],
	    			"jewishness"=>$row["jewishness"],
	    			"result"=>$text_result
	    		)
	    	);

		}

	}



}


$zipcode_list = ""; // zip code list for email

foreach ( $zips as $zip ){
	$zip = trim($zip);	
	$i++;
	$zipcode_list .= $zip;			
	if ( $i == count($zips)-1){			
		$zipcode_list .= " and ";		
	} elseif ( $i < count($zips) ){
		$zipcode_list .= ", ";
	}
}

?>


<h1>Merge Chabad of Skokie Data list (2013) with current Mivtzoim.net data</h1>

<p><a href="skokie_1_5_2015_data.csv">Source spreadsheet</a></p>

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

