<?php 
  require_once(__DIR__."/../../includes.php");


  class mergeInitializer {
    public function columnify($csv_file){
      $this->columnify = new Columnify($csv_file);
      return $this->columnify;
    }

    public function merge_data(){
      $this->dataMerge = new DataMerge();
      $this->dataMerge->handle_form($_POST);
      return $this->data_merge_result = $this->dataMerge->compare_data();
    }

  }

  
  $initializer = new mergeInitializer();
?>