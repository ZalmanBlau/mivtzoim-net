<?php
require_once(__DIR__ . "/../../includes.php");

class ZipColumnify {
  public $parent;  
  private $current_column = 0;
  private $name_pattern = "/^(zip)(?: |-)*(?:(code)(?: |-)*)?(?:4|5)?(?: |-)*(code)?$/i";
  private $zip_pattern1 = "/^(\d{5})(?:-(\d{4}))?$/";
  private $zip_pattern2 = "/^(\d{4})$/";

  public function __construct($parent){
    $this->parent = $parent;
  }

  public function find(){
    if($this->name_match()){
      // call again incase the zipcodes last 4 digits are in a different column
      $this->name_match();
    }
    else{
      // check cell data incase name didn't match but zip exist.
      $this->cell_match();
    }  
  }

  private function name_match(){
    $csv_columns = array_slice($this->parent->csv_columns, $this->current_column);

    foreach($csv_columns as $i=>$column){
      preg_match($this->name_pattern, $column, $matches);
      $this->current_column++;
      
      if($matches && $this->store_name()){
        return true;
      }
    }
  }

  private function cell_match() {
    $results = 0;
    
    foreach($this->parent->sample_rows[0] as $i=>$cell){  
      if($this->zip5_match($cell)){
        $results++;
        for($k = 1; $k < 3; $k++){
          if(!$this->zip5_match($this->parent->sample_rows[$k][$i])) {
            $results = 0;
            break;
          }
          $results++;
        }
        if($results == 3){ 
          return $this->parent->store_column("Zip Code", $i, "zip");
        }
      }
    }
  }

  private function store_name(){
    $i = $this->current_column - 1;
    $cell = $this->parent->sample_rows[0][$i];
    
    if($this->zip4_match($cell)){
      return $this->parent->store_column("Zip4", $i, "zip");
    }
    elseif($this->zip5_match($cell)){
      return $this->parent->store_column("Zip Code", $i, "zip");
    }
  }


  private function zip5_match($cell){
    preg_match($this->zip_pattern1, $cell, $matches);
    return $matches;
  }

  private function zip4_match($cell){
    preg_match($this->zip_pattern2, $cell, $matches);
    return $matches; 
  }
}

?>
