
<?php
//require_once('init.php');

//generateReport(2);
?>




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
