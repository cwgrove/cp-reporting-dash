<?php
/*
Plugin Name: CP Reporting Dash
Plugin URI: http://sapphirebd.com/
Version: 0.1
Author: Sapphire BD
Description:
*/


$dirpath = plugin_dir_path( __FILE__);
require_once($dirpath.'/includes/init.php');
//require_once($dirpath.'/includes/functions.php');


/** Step 2 (from text above). */
add_action( 'admin_menu', 'cprd_menu' );
/** Step 1. */
function cprd_menu() {
	add_options_page( 'CP Reporting Options', 'CP reporting dash', 'manage_options', 'cpim', 'cprd_menu_options' );
}
/** Step 3. */
function cprd_menu_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	$course_id = htmlspecialchars($_GET["course_id"]);

	$gen = htmlspecialchars($_GET["gen_rep"]);
	if (!empty($gen)){

	 generateReport($course_id);
	}



if (!empty($course_id)){
$b = readReport($course_id);
}

//Plugin directory
$plugindirpath = dirname(__DIR__);
$plugindirpath = $plugindirpath.'/cp-reporting-dash';
//Reports path
$dir = $plugindirpath.'/assets/reports/';
$path = $plugindirpath.'/views/pick-course.php';
//echo $path;
//$a ='No reports yet';
$form = getCourses();

ob_start();
include($path);
$contactStr = ob_get_clean();
print $contactStr;



echo '<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

<form action="#" method="get">
  <input type="hidden" name="page" value="cpim">
	<input type="hidden" name="course_id" value="'.$course_id.'">
	<input type="hidden" name="gen_rep" value="'.$course_id.'">
<button id="singlebutton" name="singlebutton" class="btn btn-primary">Button</button>
<hr/>
</form>


';


?><pre> <?php print_r($b); ?> </pre> <?php

}
?>
