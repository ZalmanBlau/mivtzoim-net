<?php 
class DataMerge {
  private $columnify;
  private $form_data;
  private $spreadsheet;

  private $name_dealer;
  private $place_dealer;
  private $zip_dealer;
  private $address_dealer;

  public function __construct($columnify){
    $this->columnify = $columnify;
  }

  public function handle_form($form_data){
    $this->form_data = $form_data;

    $this->name_fields();
    $this->place_fields();
    $this->zip_fields();
    $this->address_dealer();
  }

  ##############################################################
  ##############################################################
  ################     Method Setters!!!     ###################
  ##############################################################
  ##############################################################

  private function name_fields(){
    $form_data = $this->form_data;
    if(($index = array_search("Full Name", $form_data["columns"]))){
      if($form_data["name_side"] == "true" || $form_data["name_side"] == ""){
        $this->name_fields = array("method" => "right_name_split", "index" => $index);
      }
      else{
        $this->name_fields = array("method" => "left_name_split", "index" => $index);
      }
    }
    else{
      $first_n_index = array_search("First Name", $form_data["columns"]);
      $last_n_index = array_search("Last Name", $form_data["columns"]);
      $this->name_fields = array("method" => "separate_names", "first_n_index" => $first_n_index, "last_n_index" => $last_n_index);
    }
  }

  private function place_fields(){
    $form_data = $this->form_data;
    if(($index = array_search("City/State", $form_data["columns"]))){
      if($form_data["place_side"] == "true" || $form_data["place_side"] == ""){
        $this->name_fields = array("method" => "right_state_split", "index" => $index);
      }
      else{
        $this->name_dealer = array("method" => "left_state_split", "index" => $index);
      }
    }
    else{
      $city_index = array_search("City", $form_data["columns"]);
      $state_index = array_search("State", $form_data["columns"]);
      $this->place_dealer = array("method" => "separate_places", "city_index" => $city_index, "state_index" => $state_index);
    }
  }

  private function zip_fields(){
    if(($index = array_search("Zip Code", $form_data["columns"]))){
      $this->zip_dealer = array("method" => "zip_code", "index" => $index)
    }
    elseif(($index = array_search("Zip 5", $form_data["columns"]))){
      $this->zip_dealer = array("method" => "zip_code", "index" => $index)
    }
  }

  private function address_fields(){
    $index = array_search("Address", $form_data["columns"])
    $this->address_dealer = array("method" => "address", "index" => $index);
  }

  ##############################################################
  ##############################################################
  ################      Data Grabbers!!!     ###################
  ##############################################################
  ##############################################################

  // Names...

  private function right_name_split($row){
    $places = preg_match($this->columnify->state->cell_pattern, 
                          $row[$this->place_dealer["index"]], $matches);
    
    $city_test = $places[0];
    $state_test = $placesp[1];
    if($states = ($this->standard_search("state_right LIKE '$state_test'", "state"))){
      foreach($states as $state){
        if
      }
    }
      
    foreach(){

    }
  }

  private function left_name_split(){
    
  }

  private function separate_names(){

  }

  // Places

  private function right_state_split(){

  }

  private function left_state_split(){
    
  }

  private function separate_places(){
    
  }

  // Zip Code

  private function zip_code(){

  }

  // Address

  private function address(){

  }

  ##############################################################
  ##############################################################
  ################      Data Mergers!!!      ###################
  ##############################################################
  ##############################################################



  ##############################################################
  ##############################################################
  ###############      Database Searchers     ##################
  ##############################################################
  ##############################################################
  private function standard_search($terms, $column){
    $select_statement = "SELECT $column FROM us_places WHERE $terms";

    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $result = $mysqli->query($select_statement);

    if($results){
      $data = $result->fetch_array(MYSQLI_ASSOC)["$column"];
      return gettype($data) == "array" ? $data : array($data);
    }
    
    return false;
  }
}
?>