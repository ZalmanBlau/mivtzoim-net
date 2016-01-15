$(document).ready(function(){
  console.log("document loaded")
  $(function() {
    $( "#dialog" ).hide();
    $( "#name_dialog" ).hide();
    $( "#place_dialog" ).hide();
  });

  var ignore_prevent = false;

  $(" #verification_form").submit(function(event){
    if(ignore_prevent){
      // Do nothing.
    }
    else if($("option:selected[value='Full Name']")[0] && $("option:selected[value='City/State']")[0]){
      event.preventDefault();
      $( "#dialog" ).dialog({
        title: "2 More Questions"
      });
      $( "#place_dialog" ).show();
      $( "#name_dialog" ).show();
    }
    else if($("option:selected[value='City/State']")[0]){
      event.preventDefault();
      $( "#dialog" ).dialog({
        title: "1 More Question"
      });
      $( "#place_dialog" ).show();
      // 
    }
    else if($("option:selected[value='Full Name']")[0]){
      event.preventDefault();
      $( "#dialog" ).dialog({
        title: "1 More Question"
      });
      $( "#name_dialog" ).show();
      // 
    }
  });

  $( "#dialog" ).bind('dialogclose', function(event) {
    $( "#place_dialog" ).hide();
    $( "#name_dialog" ).hide();
  });

  $("#dialog_submit").click(function(){
    ignore_prevent = true;
    $('#verification_form').submit( function(eventObj) {
      $('<input />').attr('type', 'hidden')
          .attr('name', "name_side")
          .attr('value', $('input[name=name_side]:checked').val())
          .appendTo('#verification_form');
          $('<input />').attr('type', 'hidden')
          .attr('name', "place_side")
          .attr('value', $('input[name=place_side]:checked').val())
          .appendTo('#verification_form');
        });
      $('#verification_form').submit();
  })
});