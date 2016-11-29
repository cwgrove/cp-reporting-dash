
<?php
//require_once('init.php');
?>



<button id="singlebutton" name="singlebutton" class="btn btn-primary">Button</button>
<hr/>


<form action='#' method="get" class="form-horizontal">
<fieldset>

<!-- Form Name -->
<legend>Form Name</legend>

<!-- Multiple Radios -->
<div class="form-group">

  <input type="hidden" name="page" value="cpim">

  <label class="col-md-4 control-label" for="radios">Multiple Radios</label>
  <div class="col-md-4">

<?php echo $form; ?>




  </div>
</div>



<div class="form-group">
  <label class="col-md-4 control-label" for="button1id">Double Button</label>
  <div class="col-md-8">
    <button id="button1id" name="button1id" class="btn btn-success">Run Report</button>
    <button id="button2id" name="button2id" class="btn btn-danger">View Report</button>
  </div>
</div>

</fieldset>
</form>

<?php

$plugindirpath = get_site_url()."/wp-content/plugins/cp-reporting-dash/includes/";

$dir = $plugindirpath;


?>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $("#button1id").click(function(){
        $.post("<?php echo $dir; ?>functions.php?action=generateReport",
        {
          course_id: 2,
        },
        function(data,status){
            console.log("sssss");
        });
    });
});
</script>
