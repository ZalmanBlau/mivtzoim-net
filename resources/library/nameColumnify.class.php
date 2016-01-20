<?php
require_once(__DIR__ . "/../../includes.php");

class NameColumnify {
  public $parent;
  public $split_method;
  private $iterations;
  private $matched_columns = array();
  private $name_pattern = "/^\W*(?!.*company|corporation)(?:(first|last)|.*)\W*name\W*(?:(first|last)|.*)\W*$/i";
  private $cell_pattern = "/^mrs?\W*/i";
  private $comma_split = "/^(.*),+((?:\W*\w+\W*)+)$/";
  private $right_split = "/^(.+)+[ -]((?:\w|')+\W*)$/";
  private $left_split = "/^((?:\w|')+)[ -](.+)+$/";
  

  public function __construct($parent){
    // Parent should always be of class Columnify. 
    $this->parent = $parent;
  }

  public function find(){
    return $this->name_match() ? true : $this->cell_match(); 
  }

  
  private function name_match(){
    $finds = 0;

    foreach($this->parent->csv_columns as $i=>$column){
      $this->iterations++;

      preg_match($this->name_pattern, $column, $matches);
      $matches = array_map('strtolower', $matches);

      if(count($matches) >= 2){
        if($matches[1] == "first"){
          $this->parent->store_column("First Name", $i, "name");
          $finds++;
        }
        elseif($matches[1] == "last"){
          $this->parent->store_column("Last Name", $i, "name");
          $finds++;
        }
      }
      elseif(count($matches) == 1){
        array_push($this->matched_columns, array($column, $i));
      }
    }

    if($finds == 2 || $finds == 1 && $i == (count($csv_columns) - 1)){
      return true;
    }
    elseif($this->matched_columns){
      return $this->store_fullname();
    }

    return false;
  }

  private function cell_match() {
    $results = 0;
    foreach($this->parent->sample_rows[0] as $i=>$cell){  
      if(preg_match($this->cell_pattern, $cell)){
        return $this->parent->store_column("Full Name", $i, "name");
      }
    }
  }

  private function store_fullname(){
    foreach($this->matched_columns as $column){
      $c_index = $column[1];
      $cell = $this->parent->sample_rows[0][$c_index];

      if(preg_match($this->comma_split, $cell)){
        $this->split_method = "comma_split()";
        return $this->parent->store_column("Full Name", $c_index, "name");
      }
    }
    $this->split_method = "right_split()";
    $c_index = $this->matched_columns[0][1];
    return $this->parent->store_column("Full Name", $c_index, "name");
  }

}
?>