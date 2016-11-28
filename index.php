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
$course_id = 18;
$b = readReport($course_id);
echo '<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">';


//echo ($b);
?><pre> <?php print_r($b); ?> </pre> <?php

}
?>
