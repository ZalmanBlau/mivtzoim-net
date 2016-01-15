<?php
require_once(__DIR__ . "/../../includes.php");

class AddressColumnify {
  public $parent;
  private $address_pattern = "/^(?:\d|-)+(?: \w+)+/i";

  public function __construct($parent){
    // Parent should always be of class Columnify. 
    $this->parent = $parent;
  }

  public function find(){
    return $this->cell_match(); 
  }

  private function cell_match() {
    $results = 0;
    
    foreach($this->parent->sample_rows[0] as $i=>$cell){  
      if(preg_match($this->address_pattern, $cell)){
        $results++;
        for($k = 1; $k < 3; $k++){
          $new_cell = $this->parent->sample_rows[$k][$i];
          if(!preg_match($this->address_pattern, $new_cell)) {
            $results = 0;
            break;
          }
          $results++;
        }
        if($results == 3){ 
          return $this->parent->store_column("Address", $i, "address");
        }
      }
    }
  }

}
?>