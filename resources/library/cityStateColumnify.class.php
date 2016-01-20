<?php
require_once(__DIR__ . "/../../includes.php");

class CityStateColumnify {
  public $parent;
  private $name_pattern;
  private $column_name;
  private $city_pattern = "/^\W*(city|town(?:ship)|municipality|village)\W*?$/i";
  private $state_pattern = "/^\W*state\W*$/i";
  // proporties for find_mixed
  private $combo_name_pattern = "/^\W*(state)\W*?(city|town(?:ship)?|municipality|village)?\W*(state)?\W*$/i";
  public $cell_pattern = "/^(\w+).*(?: |,)(\w+)/";
  private $split_method;


  public function __construct($parent, $mode){
    // Parent should always be of class Columnify. 
    $this->parent = $parent;
    // $mode should be = to a string: either 'city' or 'state'
    if(strtolower($mode) == "city"){
      $this->name_pattern = $this->city_pattern;
      $this->column_name = "City";
    }
    else{
      $this->name_pattern = $this->state_pattern;
      $this->column_name = "State";
    }
  }

  public function find(){
    return $this->name_match() ? true : $this->cell_match(); 
  }

  // Find mix when no state or city found, 
  // and their's the possibility that they are mixed. 
  
  public function find_mixed(){
    $this->column_name_search() ? true : $this->like_query();
  }



  private function name_match(){
    foreach($this->parent->csv_columns as $i=>$column){
      preg_match($this->name_pattern, $column, $matches);
      if($matches){
        return $this->parent->store_column($this->column_name, $i, "place");
      }
    }
  }

  private function cell_match() {
    $results = 0;

    foreach($this->parent->sample_rows[0] as $i=>$cell){ 
      $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

      $cell1 = strtolower((str_replace("'", "\'", $cell)));
      $cell2 = strtolower((str_replace("'", "\'", $this->parent->sample_rows[1][$i])));
      $cell3 = strtolower((str_replace("'", "\'", $this->parent->sample_rows[2][$i])));
      
      $select_statement = "SELECT COUNT($this->column_name) FROM us_places WHERE";
      $cells = array_unique(array($cell1, $cell2, $cell3));

      foreach($cells as $k=>$place){
        $select_statement .= " $this->column_name = '$place' OR";
      }
     
      $select_statement = preg_replace("/OR$/", " ", $select_statement);
      
      if(($result = $mysqli->query($select_statement))){
        $count = intval($result->fetch_array(MYSQLI_ASSOC)["COUNT($this->column_name)"]);
        if($count >= $k){
         return $this->parent->store_column($this->column_name, $i, "place");
        } 
      }
    }
    return false;
  }
  

  #############################################################
  #############################################################
  ########    Mixed (find_mix) Helper Methods!    #############
  #############################################################
  #############################################################
  // Checks columns names for city/state.
  private function column_name_search(){
    foreach($this->parent->csv_columns as $i=>$column){
      preg_match($this->combo_name_pattern, $culumn, $matches);
      if(count($matches) >= 3){
        return $this->parent->store_column("City/State", $i);
      }
    }
    return false;
  } 

  // Feeds the next method the data it needs for analysis.
  // Seperated for DRY reasons.
  private function like_query(){
    foreach($this->parent->sample_rows[0] as $i=>$cell){
      if($this->possible_state($cell)){
        return $this->parent->store_column("City/State", $i);
      }
    }
    return false;
  }

  // Using the data fed to it by the previous method, splits and compares
  // the first and last string of each cell against the database. 
  // Then compares the 2nd half to confirm that's it a city.

  private function possible_state($cell){
    $split_cell = explode(" ", $cell);
    $s_cell_count = count($split_cell) - 1;
    $query1 = " state_right = '$split_cell[$s_cell_count]'";
    $query2 = " state_left = '$split_cell[0]'";

    if(($result = $this->standard_search($query1, "state"))){
      if($this->possible_city($result, $cell)){
        eval(\Psy\sh());
        return true;
      }
      elseif(($result = $this->standard_search($query2, "state"))){
        return $this->possible_city($result, $cell, false);
      }
    }
  }

  // Compares possible states fed by previous method and confrims
  // validity by comparing it's other half against a list of cities.

  private function possible_city($possible_states, $query, $right_side = true){
    eval(\Psy\sh());
    foreach($possible_states as $k=>$possibly){
      $pattern = $right_side ? "/(\w+) $possibly$/i"  : "/^$possibly (\w+)/i";
      if(preg_match($pattern, $query, $matches)){
        return $this->standard_search("city = '$matches[1]'", "city"); 
      }
    }
  }


  ###############################################################
  // Next to method's seem unnessery now. Keeping them until product is complete.
  ###############################################################
  // Feeds the next method the data it needs for analysis. 
  // Seperated for DRY reasons.

  // private function has_sides(){
  //   for($i = 0; $i < count($this->parent->sample_rows); $i++){
  //     if(($this->split_method = $this->get_sides($i))){
  //       return $this->parent->store_column("City/State", $i, "place");
  //     }
  //   }
  //   return false;
  // }

  //  // Finds city/state by indentifying charactersitcs of a double data
  // // cell. (aka ',' or '-'). Then by confirming results against database

  // private function get_sides($index = 0){
  //   $cell1 = $this->parent->sample_rows[0][$index]; 
  //   $cell2 = $this->parent->sample_rows[1][$index]; 
  //   $cell3 = $this->parent->sample_rows[2][$index]; 

  //   $uniques = count(array_unique(array($cell1, $cell2, $cell3)));

  //   preg_match($this->cell_pattern, $cell1, $matches1);
  //   preg_match($this->cell_pattern, $cell2, $matches2);
  //   preg_match($this->cell_pattern, $cell3, $matches3);

  //   $s_on_right = "state = '$matches1[2]' OR state = '$matches2[2]' OR state = '$matches3[2]'";
  //   $c_on_left = "city = '$matches1[1]' OR city = '$matches2[1]' OR city = '$matches3[1]'";
    
  //   $s_on_left = "state = '$matches1[1]' OR state = '$matches2[1]' OR state = '$matches3[1]'";
  //   $c_on_right = "city =  '$matches1[2]' OR city = '$matches2[2]' OR city = '$matches3[2]'";
    
  //   if(count($matches1) == 3){
  //     $r_state_boolean = $this->count_records($s_on_right, "state") == ($uniques - 1);
  //     $l_city_boolean = $this->count_records($s_on_right, "state") == ($uniques - 1);
      
  //     if($l_city_boolean && $r_state_boolean){
  //       return "right()"; 
  //     }
  //     elseif($this->count_records($s_on_left, "state") == ($uniques - 1)){
  //       return $this->count_records($s_on_left, "state") == ($uniques - 1) ? "left()" : false;
  //     }
  //   }
  // }



  // Fulfills commen search needs
  // Standard search is for regurler searches when you need the actaul results back
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

  // count_records is for times when you just need to know the total
  // number of items that match
  private function count_records($terms, $column){
    $select_statement = "SELECT COUNT($column) FROM us_places WHERE $terms";

    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $result = $mysqli->query($select_statement);

    return $result ? intval($result->fetch_array(MYSQLI_ASSOC)["COUNT($column)"]) : 0;
  }

}
?>