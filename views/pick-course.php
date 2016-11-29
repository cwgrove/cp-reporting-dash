
<?php
//require_once('init.php');
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

<!-- Button -->
<div class="form-group">
  <label class="col-md-4 control-label" for="singlebutton">Single Button</label>
  <div class="col-md-4">
    <button id="singlebutton" name="singlebutton" class="btn btn-primary">Button</button>
  </div>
</div>

</fieldset>
</form>
