<?php
require_once('init.php');
//Get Report
function readReport($course_id){
	//Plugin directory
	$plugindirpath = dirname(__DIR__);
	$plugindirpath = $plugindirpath.'/';
	//Reports path
	$dir = $plugindirpath.'assets/reports/';

	$units = getUnitsAndMenuOrder($course_id);
	$number_of_units = count($units);

	//Bucket
	$a = array();
	// Open a directory, and read its contents
	if (is_dir($dir)){
	  if ($dh = opendir($dir)){
		while (($file = readdir($dh)) !== false){
			$a[] = $file;
		}
		closedir($dh);

		//Get ids of json files
		foreach ($a as $file){
			$file = substr($file,strpos($file, '-')+1);
			$file = substr($file,0,strpos($file, '.'));
			$course_report_ids[] = $file;
		}

		$sap_this_course_reports = array();
		foreach ($course_report_ids as $key => $f){
			$f = intval($f);
			if ($f == $course_id){
				$sap_this_course_reports[] = $key;
			}
		}

		//Remove all files not for this report
		$all_files_for_this_course = array();
		foreach ($sap_this_course_reports as $key => $value) {
			if(array_key_exists($value, $a)) {
				$all_files_for_this_course[] = $a[$value];
			}
		}

		if(empty($all_files_for_this_course)){		}

		//Check for most recent file for course
		$course_report_timestamps = array();
		foreach ($all_files_for_this_course as $file){
			$file = substr($file, 0, strpos($file, '-'));
			$course_report_timestamps[] = $file;
		}

		if(empty($course_report_timestamps)){
			//Return No reports Menu.
			$path = $plugindirpath.'views/no-reports.php';
			$a ='No reports yet';
			ob_start();
			include($path);
			$contactStr = ob_get_clean();
			return $contactStr;
		}
		
		//Get the highest value. Getting the most recent timestamp
		$a = max($course_report_timestamps);
		//Genertate URL to the json file and read it
		$jsonurl = $dir.$a."-".$course_id.".json";
		$json = file_get_contents($jsonurl);
		$a = json_decode($json);
		//Pull Values for the report
		$mean = $a->Mean;
		$mode = $a->Mode;
		$p_com = $a->p_complete;
		$number_of_units;
		$students = $a->students;
		$path = $plugindirpath.'views/display-report.php';
		ob_start();
		include($path);
		$contactStr = ob_get_clean();
		return $contactStr;
		}
	}
}//End readReport

/// Getting course IDS
function getCourseIds(){
	$myposts = get_posts(array(
				'showposts' => -1,
				'post_type' => 'course',
				'orderby'   => 'ID',
				'order'     => 'ASC',
				));
	$courses = array();
	foreach($myposts as $course){
		$ID = $course->ID;
		$courses[] = $ID;
	}
	return $courses;
}//End getCourseIds

/// Getting unit IDS and menu order
function getUnitsAndMenuOrder($CourseId){
	$units_and_menu_order = array();

	$a = new Course($CourseId);
	$a = $a->get_units();
	foreach ($a as $unit) {
		$unit = $unit['post'];
		$id = $unit->ID;
		$menu_order = $unit->menu_order;
		$units_and_menu_order[$id] = $menu_order;
	}
	return $units_and_menu_order;
}//End getUnitsAndMenuOrder

function getCompleationData($allStudentIds = array(),$course_id, $unit_ids){
	//Removing menu order -- just Unit IDS
	$unit_ids = array_keys($unit_ids);
	//get # of units
	$number_of_units = count($unit_ids);
	//Define arrays
	$class_progress_raw = array();
	$class_progress_output = array();

	//getting all course and unit progess
	foreach ($allStudentIds as $studentId) {
		$class_progress_raw[$studentId] = Student_Completion::get_completion_data($studentId,$course_id);
	}

	//Document course progess
	//Document unit progess
	foreach ($class_progress_raw as $key => $student_progress) {
		$student_course_progress = $student_progress['course_progress'];
		$student_units_progress = $student_progress['unit'];
		//course progress
		if(!empty($student_course_progress)){
			$class_progress_output[$key] = array('course_progress' => $student_course_progress);
		} else {
			$class_progress_output[$key] = array('course_progress' => '0');
		}

		//unit progress
		if(!empty($student_units_progress)){
			$x = 1;
			foreach ($student_units_progress as $unit_progress) {
				$class_progress_output[$key]['Unit_'.$x]= $unit_progress['unit_progress'];
				$x++;
			}
		} else {
			for($x = 1; $x <= $number_of_units; $x++) {
			$class_progress_output[$key]['Unit_'.$x]= 0;
			}
		}
	}
	$class_progress = $class_progress_output;
	return $class_progress;
}//End getCompleationData

//Get last login
function get_last_login($allStudentIds){
	$last_logins = array();
	//Go through all students and grab their latest_activity
		foreach ($allStudentIds as $id) {
			$a = get_user_meta($id, 'latest_activity',true);
			$last_logins[$id] = $a;
		}
  return $last_logins;
}//End get_last_login

//Get enroll Date
function get_enroll_date($allStudentIds,$course_id){
	$enrolled_course_date = array();
	//Go through all students and get the datat they enrolled the course
	foreach ($allStudentIds as $id) {
		$a = get_user_meta($id, 'enrolled_course_date_'.$course_id,true);
		$enrolled_course_date[$id] = $a;
	}
	return $enrolled_course_date;
}//End get_enroll_date

//Caclulate students course progress
function CalcCourseStats($student_full_data_array, $allStudentIds)
{
	$mean_s = 0;
	$mode_s = 0;
	$per_complete = 0;
	$x = array();
	$num_of_completed = 0;
	//Go through Stuent array and pull out their course progress
	foreach ($student_full_data_array as $key => $value) {
		$x[] = intval($value['course_progress']);
		//If course completion is 100% then add to the number of complete
		if ($value['course_progress'] == 100){
			$num_of_completed++;
		}
	}

	//Average
	$mean_s = round(array_sum($x) / count($x));
	//calc $mode_s number of values that are the same
	$mode_s = array_count_values($x);

	//Gets the maximum count then returns the key.
	$mode = array_keys($mode_s, max($mode_s));
	$mode = $mode[0];

	//Checks how many of the values are 100% || $per_complete_scalculates
	$number_of_enrolled_students = count($allStudentIds);
	//Calculate decimal percent value
	$per_complete = $num_of_completed/$number_of_enrolled_students;
	//Convert decimal to percent and round
	$per_complete = round($per_complete * 100);
	//Returns array of the Mean, Mode and % Complete
	$x = array('Mean' => $mean_s, 'Mode'=> $mode, 'p_complete' =>$per_complete);
	return $x;
}//End CalcCourseStats

//Generates the report by passing in the Course ID you want to report on
function generateReport($course_id){
	//Get all student ids for couse
	$allStudentIds = Course::get_course_students_ids($course_id);
	//Get units and menue order
	$unit_ids =  getUnitsAndMenuOrder($course_id);
	//Get compleation data
	$class_progress = getCompleationData($allStudentIds,$course_id, $unit_ids);
	//Get last login & enroll Date
	$login_dates = get_dates($allStudentIds,$course_id);
	//Gets students Name and Email
	$student_name_and_email = getnameandEmail($allStudentIds);
	//cobine user data
	$student_course_data_array = combineData($login_dates,$class_progress);
	//Combine User Data
	$student_full_data_array = combineData($student_course_data_array,$student_name_and_email);
	//Class Stats
	$total_class_stats = CalcCourseStats($student_full_data_array,$allStudentIds);
	//Build the Report by passing in the students data
	buildReport($total_class_stats,$student_full_data_array,$course_id);
}//End generateReport

//Build report with class info and Student Data
function buildReport($total_class_stats,$student_full_data_array,$course_id){
	//Plugin directory
	$plugindirpath = dirname(__DIR__);
	$plugindirpath = $plugindirpath.'/';
	$dir = $plugindirpath.'assets/reports/';

	//Final Combo Data
	$n = array('students' => $student_full_data_array);
	$h = $total_class_stats + $n;
	$h = json_encode($h);
	//Get current Timestamp
	$time = time();
	//Open json file, write the content, close the file, return the encoded json
	$myfile = fopen($dir.$time."-".$course_id.".json", "w") or die("Unable to open file!");
	fwrite($myfile, $h);
	fclose($myfile);
	return $h;
}//End buildReport

//combines array values with same keys
function combineData($x,$y){
	$f =  array();
	foreach ($x as $key => $value) {
		$f[$key] = $value + $y[$key];
	}
	return $f;
}//End combineData

//Get students name and email and return the array
function getnameandEmail($allStudentIds){
	$u = array();
	foreach ($allStudentIds as $id) {
		$x = get_userdata($id);
		$ue = $x->user_email;
		$fn = $x->first_name;
		$ln = $x->last_name;
		$u[$id] = array('name' => $fn.' '.$ln,'email'=>$ue);
	}
	return $u;
}//End getnameandEmail

//Get last login + enrolled_course_date_
function get_dates($allStudentIds,$course_id){
	$last_logins = array();
	foreach ($allStudentIds as $id) {
		$a = get_user_meta($id, 'latest_activity',true);
		$b = get_user_meta($id, 'enrolled_course_date_'.$course_id,true);
		$last_logins[$id] = array('last_login' => $a,'enrolled_course_date' =>$b );
	}
	return $last_logins;
}//End get_dates

//Get all courses
function getCourses(){
	$myposts = get_posts(array(
		'showposts' => -1,
		'post_type' => 'course',
		'orderby'   => 'ID',
		'order'     => 'ASC',
		));

	$courses = array();
	//Go through the course IDs and grab the Titles
	foreach($myposts as $course){
		$c = array();
		$ID = $course->ID;
		$title = $course->post_title;
		$c[] = $ID;
		$c[] = $title;
		$courses[] = $c;
	}
	
	//Creates drop down menu for course selection
	$form = '<select id="selectbasic" name="course_id" class="form-control">';
	foreach($courses as $course){
		$form .='<option value="'.$course[0].'">'.$course[1].'</option>';
	}
	$form .= '</select>';
	return $form;
}//End getCourses
 ?>
