<?php
//Checks if coursepress is active
class Report {
	__contstuct($id){
		if ( !is_plugin_active( 'coursepress/coursepress.php' ) ) {
		return "coursepress Pro needs to be active";
	}
}
?>