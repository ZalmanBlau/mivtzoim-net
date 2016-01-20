<?php 
  require_once("../includes.php");
  use Cartalyst\Sentinel\Native\Facades\Sentinel;
  Sentinel::getUser();
  $start = microtime(true);

  if(!Sentinel::check()){
    $_SESSION["login_redirect"] = "/public_html/verify_columns.php";
    header("location: /public_html/login.php");
    die();
  }
  
  $parser = $initializer->columnify($_SESSION["file_path"]);
  $columns_info = $parser->find();
  $form_options = $parser->form_options;
  $original_rows = $parser->original_rows;

  $tablehead = "<thead><tr>";

  // Creating table header (aka the verification form).
  foreach($columns_info as $name=>$data){

    $options_copy = $form_options;
    $position = $data["position"];
    $yes_checked = $data["checkbox"]["yes_checked?"];
    $no_checked = $data["checkbox"]["no_checked?"];
    $should_hide = $data["checkbox"]["hidden?"];
    $checkbox_text = $data["checkbox"]["text"];
    $category = $data["category"];

    if($category != "unknown"){   
      // Deviding the current category from the rest of the options.
      // The program will display these first. 
      $category_options = $options_copy[$category];
      unset($category_options[$name]);
      unset($options_copy[$category]);

      $tablehead .= "<th><select name='columns[$position]'> <option value='$name' selected> $name </option>";

      // current category options.
      foreach($category_options as $option=>$empty_value){
        $tablehead .= "<option value='$option'> $option </option>";
      }
    }
    else{
      $tablehead .= "<th><select name='columns[$position]'> <option value='' selected> Select </option>";
    }

    foreach($options_copy as $category){
      foreach($category as $option=>$empty_value){
        $tablehead .= "<option value='$option'> $option </option>";
      }
    }
    $tablehead .= "</select>";
    
  }

  $tablehead .= "</tr></thead>";

  // Displaying a sample of the users data (5 spreadsheet rows to be precise).
  $spreadsheet = "";
  foreach($original_rows as $row){
    $spreadsheet .= "<tr>";
    foreach($row as $cell){
      $spreadsheet .= "<td> $cell </td>";
    }
    $spreadsheet .= "</tr>";
  }
?>


<!-- Start html formatting. -->

<script src="js/verify_form.js"></script>

<div class="main-content">
   <div class="page-content">
      <!-- /section:settings.box -->
      <div class="page-content-area">
         <!-- for range selector -->
         <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
         
         <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-3" style="padding:10px;font-size:18px;">
            </div>
            <div class="col-xs-12 col-sm-8 col-md-9" style="padding:10px;">
            </div>
         </div>

         <form action="it_works.php" id="verification_form" method="post">
           <div class="tabbable">
              <div class="tab-content">
                 <div id="home" class="tab-pane fade in active" style="overflow-x:scroll;">
                    <div style="display:inline-block;margin-right: 5px;margin-bottom: 10px;">
                       
                    </div>
                   
                    <div id="home_data">
                       <div id="res_table_wrapper" class="dataTables_wrapper form-inline no-footer">
                          <div class="row">
                             <div class="col-xs-6">
                              </div>
                             <div class="col-xs-6">
                                <div id="verify-submit-btn" class="dataTables_filter">
                                    <input type="Submit" value="Submit" id="verify_submit" class="btn btn-primary btn-lg">
                                </div>
                             </div>
                          </div>
                          <table id="res_table" class="table table-striped table-bordered table-hover dataTable no-footer" role="grid" aria-describedby="res_table_info">
                             <?php 
                                  echo $tablehead . $spreadsheet;
                                  
                              ?>
                          </table>
                          <div class="row">
                             <div class="col-xs-6">
                                <div class="dataTables_info" id="res_table_info" role="status" aria-live="polite"></div>
                             </div>
                             <div class="col-xs-6">
                                
                             </div>
                          </div>
                       </div>
                       <script>
                          $('[data-rel="tooltip"]').tooltip({placement: 'bottom'});
                       </script>
                    </div>
                 </div>
                 <div id="messages" class="tab-pane fade" style="overflow-x:scroll;">
                    <div id="bus_table_wrapper" class="dataTables_wrapper form-inline no-footer">
                       <div class="row">
                          <div class="col-xs-6">
                             
                          </div>
                          <div class="col-xs-6">
                             
                          </div>
                       </div>
                       
                       <div class="row">
                          <div class="col-xs-6">
                            
                          </div>
                          <div class="col-xs-6">
                            
                          </div>
                       </div>
                    </div>
                 </div>
              </div>
           </div>
           <!-- hidden by default -->
           <div id="dialog">
            <div id="name_dialog">
              <h3> First Name is on the Left? </h3>
              <label for='name_side_true'>Yes</label>
              <input type='radio' name='name_side' value='true' id='name_side_true'>
              <label for='name_side_false'>No</label>
              <input type='radio' name='name_side' value='false' id='name_side_false'>
            </div>

            <div id="place_dialog">
              <h3> City is on the Left? </h3>
              <label for='place_side_true'>Yes</label>
              <input type='radio' name='place_side' value='true' id='place_side_true'>
              <label for='place_side_false'>No</label>
              <input type='radio' name='place_side' value='false' id='place_side_false'>
            </div>
            <input type="button" value="Submit" id="dialog_submit">
          </div>
         </form>

      </div>
   </div>
   <!-- /.page-content-area -->
</div>



 












              


                            
                        