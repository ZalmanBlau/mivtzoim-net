<?php
require_once(__DIR__ . "/../../includes.php");
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Database\Capsule\Manager as Capsule;

class Columnify {
  public $file_path;
  // $csv_columns and $sample_rows will be mutated as columns are found.
  // This serves to save consumption on columns that are already identified
  // $original_columns will be used as refrence when the column position is
  // being stored. 
  public $csv_columns;
  public $sample_rows;
  public $original_columns;
  public $original_rows;
  public $column_positions = array();
  // $form_option has something resembling a 'name-space' for different
  // catogories. This will make it easier to sort in the user verification form.
  public $form_options = array(
    "name" => array(
      "Full Name" => "",
      "First Name" => "",
      "Last Name" => ""
      ),
    "zip" => array(
      "Zip Code" => "",
      "Zip 5" => "",
      "Zip 4" => ""
      ),
    "address" => array(
      "Address" => ""
      ),
    "place" => array(
      "City" => "",
      "State" => "",
      "City/State" => ""
      ),
    "unknown" => array(
      "Other" => ""
      )
    );

  private $file_extension;
  public $zip;
  public $address;
  public $name;
  public $city;
  public $state;

  public function __construct($file_name){
    ###### to-do #############
    // Conversion / compatibilty for xls, xlsx and odt
    // for now we're coding for csv files, eventually 
    // we'll have a method that checks and returns the converted file

    // the above proccess will be initiated once the find method is called
    // __construct will do a regex on the file extension and return an error if its invalid
    ##########################
    return $this->file_path = __DIR__ . "/../.." . $file_name;
  }

  public function find(){
    $csv_sample = $this->csv_sample();
    $this->original_columns = $this->csv_columns = $csv_sample[0];
    $this->original_rows = $this->sample_rows = array_slice($csv_sample, 1);
    
    $this->zip = new ZipColumnify($this);
    $this->zip->find();
    $this->address = new AddressColumnify($this);
    $this->address->find();
    $this->name = new NameColumnify($this);
    $this->name->find();
    $this->city = new CityStateColumnify($this, "city");
    $city_result = $this->city->find();
    $this->state = new CityStateColumnify($this, "state");
    $state_result = $this->state->find();

    // if state state or city were not found, the remaining columns
    // will go through a more compex analysis to find possible
    // mixed result "city/state".
    if(!$state_result && !$city_result){
      eval(\Psy\sh());
      $this->state->find_mixed();
    }
    
    return $this->format_columns();
  }

  private function csv_sample(){
    $doc = PHPExcel_IOFactory::load($this->file_path);
    $sheet = $doc->setActiveSheetIndex(0);

    $rows = array();
    for($i = 1; $i < 7; $i++){
      array_push($rows, array());
      $cellIterator = $sheet->getRowIterator($i)->current()->getCellIterator();
      foreach($cellIterator as $cell){
        array_push($rows[$i-1], $cell->getValue());
      }
    }
    return $rows;
  }

  private function eliminate_column($index){
    // Deletes givin column from sample_rows & csv_columns.
    // This will usualy be eliminate a column that's found.
    unset($this->csv_columns[$index]);

    for($i = 0; $i < count($this->sample_rows); $i++){
      unset($this->sample_rows[$i][$index]);
    }
  }

  private function eliminate_cells($position){
    for($i = 0; $i < count($this->original_rows); $i++){
      unset($this->original_rows[$i][$position]);
    }
  }

  public function store_column($new_name, $position, $category){
    // $state_side holds a value of null or 'right' and 'left'. 
    // This becomes relevent if the city and states columns are mixed.
    $this->eliminate_column($position);
    
    return $this->column_positions[$new_name] = array(
      "position" => $position,
      "category" => $category,
      "checkbox" => array(
        "text" => null, 
        "yes_checked?" => null,
        "no_checked?" => null,
        "hidden?" => "invisible"
        )
      );
  }

  private function unknown_columns(){
    $unknowns = $this->csv_columns;
    foreach($unknowns as $i=>$column){
      if($this->not_empty($i)){
        $this->store_column("Other$i", $i, "unknown");
      }
      else{
        $this->eliminate_cells($i);
      }
    }
  }

  private function not_empty($position){
    $cell = $this->sample_rows[0][$position];
    return preg_match("/\w/", $cell);
  }

  private function format_columns(){
    $this->unknown_columns();
    $columns_info = $this->column_positions;

    if(array_key_exists("City/State", $columns_info)){
      $columns_info["City/State"]["checkbox"]["text"] = "Cities on Left?";
      $columns_info["City/State"]["checkbox"]["hidden?"] = null;
      // if state is on right and city is on left.

      // P.s. could have made it more straight forward, but
      // when it comes to parsing it's more relevant that the 
      // state is on the right or left then vice versa. For the user however it's
      // the reverse. 
      if($this->state->split_method == "right()"){
        $columns_info["City/State"]["checkbox"]["yes_checked?"] = "checked";
      }
      else{
        $columns_info["City/State"]["checkbox"]["no_checked?"] = "checked";
      }
    }

    if(array_key_exists("Full Name", $columns_info)){
      $columns_info["Full Name"]["checkbox"]["text"] = "First Name on Left?";
      $columns_info["Full Name"]["checkbox"]["hidden?"] = null;
      // if last name is on right, and first name is on left. 

      // P.s. Same goes for names. Just like above, it's more relevant that
      // that the last name on the right or left then vice versa. 
      if($this->name->split_method == "right()"){
        $columns_info["Full Name"]["checkbox"]["yes_checked?"] = "checked";
      }
      else{
        $columns_info["Full Name"]["checkbox"]["no_checked?"] = "checked";
      }
    }

    // Sort columns in order of real position. 
    uasort($columns_info, "self::column_cmp");
    return $columns_info;
  }

  private static function column_cmp($a, $b){
    if($a["position"] == $b["position"]){
      return 0;
    }
    return $a["position"] < $b["position"] ? -1 : 1;
  }


}

?>