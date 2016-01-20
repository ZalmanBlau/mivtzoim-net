<?php 
require_once(__DIR__."/../../includes.php");
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Database\Capsule\Manager as Capsule;

class DataMerge {
  private $form_data;
  private $mysqli;

  private $name_dealer;
  private $place_dealer;
  private $zip_dealer;
  private $address_dealer;

  public function __construct(){
    $this->mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $this->mysqli2 = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  }

  public function handle_form($form_data){
    $this->form_data = $form_data;

    $this->name_fields();
    $this->place_fields();
    $this->zip_fields();
    $this->address_fields();
  }

  public function compare_data(){
    $test_data = $this->test_data();
    // $zips = array(7920, 11413, 19064, 20111, 20120, 20121, 20124, 20151, 20152, 20170, 20171, 20189, 20190, 20191, 20194, 20905, 22003, 22009, 22015, 22027, 22030, 22031, 22032, 22033, 22038, 22039, 22040, 22041, 22042, 22043, 22044, 22046, 22060, 22066, 22079, 22101, 22102, 22106, 22116, 22124, 22150, 22151, 22152, 22153, 22180, 22181, 22182, 22201, 22202, 22203, 22204, 22205, 22206, 22207, 22209, 22213, 22219, 22301, 22302, 22303, 22304, 22305, 22306, 22307, 22308, 22309, 22310, 22311, 22312, 22314, 22315, 22320, 22580, 22903, 24090, 33446, 33498, 52655,84045);
    $zips = array(22101, 22102, 22103, 22106, 22180);
    $zipcode_list = DataMerge::join_zips($zips);

    $results = $this->run_tests($test_data, $zips);
    return array($results, $zipcode_list);
  }

  public static function join_zips($zips){
    $zipcode_count = count($zips);
    $zipcode_list = join(", ", array_slice($zips, 0, $zipcode_count - 2));
    return $zipcode_list .= " and " . $zips[$zipcode_count -1];
  }

  ##############################################################
  ##############################################################
  ################     Method Setters!!!     ###################
  ##############################################################
  ##############################################################

  // Names... 

  private function name_fields(){
    $form_data = $this->form_data;
    if(($index = array_search("Full Name", $form_data["columns"]))){
      if($form_data["name_side"] == "true" || $form_data["name_side"] == ""){
        $this->name_dealer = array("method" => "right_name_split", "index" => $index);
      }
      else{
        $this->name_dealer = array("method" => "left_name_split", "index" => $index);
      }
    }
    else{
      $first_n_index = array_search("First Name", $form_data["columns"]);
      $last_n_index = array_search("Last Name", $form_data["columns"]);
      $this->name_dealer = array("method" => "separate_names", "first_n_index" => $first_n_index, "last_n_index" => $last_n_index);
    }
  }


  // Places...

  private function place_fields(){
    $form_data = $this->form_data;
    if(($index = array_search("City/State", $form_data["columns"]))){
      if($form_data["place_side"] == "true" || $form_data["place_side"] == "" && $index != $original_position){
        $this->place_dealer = array("method" => "right_state_split", "index" => $index);
      }
      else{
        $this->place_dealer = array("method" => "left_state_split", "index" => $index);
      }
    }
    else{
      $city_index = array_search("City", $form_data["columns"]);
      $state_index = array_search("State", $form_data["columns"]);
      $this->place_dealer = array("method" => "separate_places", "city_index" => $city_index, "state_index" => $state_index);
    }
  }

  // Zip Code...

  private function zip_fields(){
    $form_data = $this->form_data;
    if(($index = array_search("Zip Code", $form_data["columns"]))){
      $this->zip_dealer = array("method" => "zip_code", "index" => $index);
    }
    elseif(($index = array_search("Zip 5", $form_data["columns"]))){
      $this->zip_dealer = array("method" => "zip_code", "index" => $index);
    }
  }

  // Address...

  private function address_fields(){
    $form_data = $this->form_data;
    $index = array_search("Address", $form_data["columns"]);
    $this->address_dealer = array("method" => "address", "index" => $index);
  }

  ##############################################################
  ##############################################################
  ################      Data Grabbers!!!     ###################
  ##############################################################
  ##############################################################

  // Names...

  private function right_name_split($row){
    $data = $row[$this->name_dealer["index"]];
    $first_name = join(" ", array_slice($name_arr, 0, count($name_arr)-2));
    $last_name = preg_replace("/\W| /", "", $name_arr[count($name_arr)-1]);

    return array($first_name, $last_name);
  }

  private function left_name_split($row){
    $data = $row[$this->name_dealer["index"]];
    $name_arr = explode(" ",$data);
    $first_name = join(" ", array_slice($name_arr, 1));
    $last_name = preg_replace("/\W| /", "", $name_arr[0]);

    return array($first_name, $last_name);
  }

  private function separate_names($row){
    $row[$this->name_dealer["index"]];
    
    $first_name = $row[$this->name_dealer["first_n_index"]];
    $last_name = $row[$this->name_dealer["last_n_index"]];

    return array($first_name, $last_name);
  }

  // Places...

  private function right_state_split($row){
    $data = $row[$this->place_dealer["index"]];
    preg_match(State::cell_pattern(), $place_data, $places);

    $city_test = $places[1];
    $state_test = $places[2];
    $search_settings = array(
      "city" => array(
          "test" => $city_test,
          "cal_side" => "city_left",
          "cmp_method" => "left_regex",
        ),
      "state" => array(
          "test" => $state_test,
          "cal_side" => "state_right",
          "cmp_method" => "right_regex",
        )
      );
    
    return $this->place_split_search($search_settings, $data);
  }

  private function left_state_split($row){
    $data = $row[$this->place_dealer["index"]];
    preg_match(State::cell_pattern(), $place_data, $places);

    $city_test = $places[2];
    $state_test = $places[1];
    $search_settings = array(
      "city" => array(
          "test" => $city_test,
          "cal_side" => "city_right",
          "cmp_method" => "right_regex",
        ),
      "state" => array(
          "test" => $state_test,
          "cal_side" => "state_left",
          "cmp_method" => "left_regex",
        )
      );
    
    return $this->place_split_search($search_settings, $data);
  }

  private function separate_places($row){
    $city = $row[$this->place_dealer["city_index"]];
    $state = $row[$this->place_dealer["state_index"]];
    return array($state, $city);
  }

  // Zip Code...

  private function zip_code($row){
    $full_zip = $row[$this->zip_dealer["index"]];
    $zip5 = explode("-", $full_zip)[0];
    return array($full_zip, $zip5);
  }

  // Address...

  private function address($row){
    $full_address = $row[$this->address_dealer["index"]];
    $num = explode(" ", $full_address)[0];
    return array ($full_address, $num);
  }







  ################################################
  //  _state_split helpers
  ################################################
  
  private function place_split_search($settings, $data){

    $city_call_side = $settings["city"]["call_side"];
    $state_call_side = $settings["state"]["call_side"];
    $city_cmp = $settings["city"]["cmp_method"];
    $state_cmp = $settings["state"]["cmp_method"];
    $city_test = $settings["city"]["test"];
    $state_test = $settings["state"]["test"];
    $response = array();

    // Getting all states who compare to $state_test
    if(($states = $this->standard_search("$state_call_side = '$state_test'", "state"))){  
      // Checking which one matches the real data
      foreach($states as $state){
        if(call_user_func(array($this, $state_cmp), $city, $data)){
          // This is the right state. 
          array_push($response, $state);
          break;
        }
      }  
      $response[0] ? '' : array_push($response, false);
    }  
    // Getting all city who's names end matches $city_test
    if(($cities = $this->standard_search("city_call_side = '$city_test'", "city"))){ 
      // Checking which one matches the real data
      foreach($cities as $city){
        if(call_user_func(array($this, $city_cmp), $city, $data)){
          // This is the right city. 
          array_push($response, $city);
          break;
        }
      }
      // If no city is found
      $response[1] ? '' : array_push($response, false);
    }
    return $response;
  }

  // place_split_search helpers

  private function right_regex($place, $data){
    return preg_match("/$place\W*$/i", $data);
  }

  private function left_regex(){
    return preg_match("/^\W*$place/i", $data);
  }


  ##############################################################
  ##############################################################
  ################      Data Mergers!!!      ###################
  ##############################################################
  ##############################################################

  private function test_data(){
    $input_test_strs = array();
    $input_test_strs2 = array();
    $zip_codes = array();
 
    $doc = PHPExcel_IOFactory::load(__DIR__ . "/../.." . $_SESSION['file_path']);
    $sheet = $doc->setActiveSheetIndex(0)->toArray();
    $sheet_count = count($sheet);

    for($i = 1; $i < $sheet_count; $i++){
      $data = $sheet[$i];
      /**
      1 Name
      2 Address
      4 Zipcode
      **/

      // last_name + address_num + zip_code
      $name_arr = call_user_func(array($this, $this->name_dealer["method"]), $data);
      $last_name = $name_arr[1];
      $address_arr = call_user_func(array($this, $this->address_dealer["method"]), $data);
      $address_num = trim($address_arr[1]);
      $zip_arr = call_user_func(array($this, $this->zip_dealer["method"]), $data);
      $zipcode = trim($zip_arr[1]);

      // Finding new contacts
      $test_str = $last_name . "_" . $address_num . "_" . $zipcode;
      array_push($input_test_strs, $test_str);

      // Finding moved contacts
      $test_str2 = $address_num . "_" . $zipcode;
      array_push($input_test_strs2, $test_str2);

      // Recording Zip Code.
      array_push($zip_codes, $zipcode);

    }

    $zipcodes = array_values(array_unique($zip_codes));
    return array($input_test_strs, $input_test_strs2, $zipcodes);
  }

  private function run_tests($tests_data, $zips){
    $chabad_id = Sentinel::getUser()->getUserId();
    $input_test_strs = $tests_data[0];
    $input_test_strs2 = $tests_data[1];

    $new_data = array();
    $existing_data = array();

    foreach ($zips as $zip){
      $result = $this->mysqli->query("SELECT * FROM `res_master_tmp` WHERE `zip` = '" . $zip . "'");
      while($row = $result->fetch_array(MYSQLI_ASSOC)){
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
          $row_id = $row["id"];
          $this->mysqli2->query("INSERT INTO `users_mivtzoim_data` (`res_master_tmp_id`, `users_id`, `new_data?`) VALUES ($row_id, $chabad_id, 1)");
        } else { // existing contact
          $row_id = $row["id"];
          $this->mysqli2->query("INSERT INTO `users_mivtzoim_data` (`res_master_tmp_id`, `users_id`, `new_data?`) VALUES ($row_id, $chabad_id, 0)");
        }
      }
    }
  }

  public static function past_results(){
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    $chabad_id = Sentinel::getUser()->getUserId();
    $chabad_data = array("new" => array(), "existing" => array());
    $rounds = 0;
    $true_val = 1;
    $data_status = "new";
    $text_status = "New Contact (Not in your spreadsheet)";
    $zips = array();

    while($rounds < 2){
      $SQL = "SELECT * FROM `res_master_tmp` INNER JOIN `users_mivtzoim_data` ON res_master_tmp.`id` = users_mivtzoim_data.`res_master_tmp_id` WHERE users_mivtzoim_data.`users_id` = ? AND users_mivtzoim_data.`new_data?` = ?;";
      
      if($stmt = $mysqli->prepare($SQL)){
        $stmt->bind_param("ii", $chabad_id, $true_val);
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
          array_push($chabad_data[$data_status],
            array(
              "first_name"=>$row["first_name"],
              "last_name"=>$row["last_name"],
              "address"=>$row["address"],
              "zip"=>$row["zip"],
              "jewishness"=>$row["jewishness"],
              "result"=>$text_status
            )
          );
          array_push($zips, $row["zip"]);
        }
      }
      $stmt->close();
      $rounds++;
      $true_val = 0;
      $data_status = "existing";
      $text_status = "Not new Contact (already in your spreadsheet)";
    }
    $zips = array_values(array_unique($zips));
    $zips = DataMerge::join_zips($zips);
    return array($chabad_data, $zips);
  }


  ##############################################################
  ##############################################################
  ###############      Database Searchers     ##################
  ##############################################################
  ##############################################################
  private function standard_search($terms, $column){
    $select_statement = "SELECT $column FROM us_places WHERE $terms";

    $result = $this->mysqli->query($select_statement);

    if($results){
      $data = $result->fetch_array(MYSQLI_ASSOC)["$column"];
      return gettype($data) == "array" ? $data : array($data);
    }
    
    return false;
  }
}
?>