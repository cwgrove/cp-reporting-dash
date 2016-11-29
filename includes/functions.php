<?php

require_once('init.php');
//Get Report
function readReport($course_id){

	//Plugin directory
	$plugindirpath = dirname(__DIR__);
	$plugindirpath = $plugindirpath.'/';
	//Reports path
	$dir = $plugindirpath.'assets/reports/';

/*	if($course_id === 0 ){
	//Return No reports Menu.
	$path = $plugindirpath.'views/no-reports.php';
	$a ='No reports yet';
	ob_start();
	include($path);
	$contactStr = ob_get_clean();
				return $contactStr;

	}*/


	$units = getUnitsAndMenuOrder($course_id);

	$number_of_units = count($units);


////Buc
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

///Remove all files not for this report
	$all_files_for_this_course = array();
foreach ($sap_this_course_reports as $key => $value) {

if(array_key_exists($value, $a)) {
	$all_files_for_this_course[] = $a[$value];
}
}

if(empty($all_files_for_this_course)){



}






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

		$a = max($course_report_timestamps);


		$jsonurl = $dir.$a."-".$course_id.".json";
		$json = file_get_contents($jsonurl);
		$a = json_decode($json);

		$mean = $a->Mean;
		$mode = $a->Mode;
		$p_com = $a->p_compleate;

		$number_of_units;
		$students = $a->students;

		$path = $plugindirpath.'views/display-report.php';
		ob_start();
		include($path);
		$contactStr = ob_get_clean();
		return $contactStr;
		//$a
	//	return $students;
  }

}
}





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

  }


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
}





function getCompleationData($allStudentIds = array(),$course_id, $unit_ids)
{

//Removing menu order -- just Unit IDS
$unit_ids = array_keys($unit_ids);
//get # of units
$number_of_units = count($unit_ids);

$class_progress_raw = array();

$class_progress_output = array();

//getting all course and unit progess
    foreach ($allStudentIds as $studentId) {
      $class_progress_raw[$studentId] = Student_Completion::get_completion_data($studentId,$course_id);
    }

//Document course progess
#//Document unit progess

foreach ($class_progress_raw as $key => $student_progress) {

      $student_course_progress = $student_progress['course_progress'];

      $student_units_progress = $student_progress['unit'];

  //course progress
  if(!empty($student_course_progress)){
      $class_progress_output[$key] = array('course_progress' => $student_course_progress);
  }
  else {
    $class_progress_output[$key] = array('course_progress' => '0');
  }

  //unit progress
  if(!empty($student_units_progress)){
    $x = 1;
    foreach ($student_units_progress as $unit_progress) {
  $class_progress_output[$key]['Unit_'.$x]= $unit_progress['unit_progress'];
  $x++;
    }
  }
  else {
    for($x = 1; $x <= $number_of_units; $x++) {
      $class_progress_output[$key]['Unit_'.$x]= 0;
    }
}

}

$class_progress = $class_progress_output;
return $class_progress;
}

//Get all student ids for couse
$allStudentIds = Course::get_course_students_ids($course_id);



//Get last login
function get_last_login($allStudentIds){
  $last_logins = array();
  foreach ($allStudentIds as $id) {
  $a = get_user_meta($id, 'latest_activity',true);
  $last_logins[$id] = $a;
  }
  return $last_logins;
}


//Get enroll Date
function get_enroll_date($allStudentIds,$course_id){
  $enrolled_course_date = array();
  foreach ($allStudentIds as $id) {
  $a = get_user_meta($id, 'enrolled_course_date_'.$course_id,true);
  $enrolled_course_date[$id] = $a;
  }
  return $enrolled_course_date;
}



function CalcCourseStats($student_full_data_array, $allStudentIds)
{
  $mean_s = 0;
  //$meadian_s = 0;
  $mode_s = 0;
  $per_compleate_s = 0;
  $x = array();
    foreach ($student_full_data_array as $key => $value) {
      $x[] = intval($value['course_progress']);

    }
//Median
//$meadian_s = round(count($meadian_s) / 2);
//$meadian_s = $array[$meadian_s-1];

//Average
$mean_s = round(array_sum($x) / count($x));
//   //calc $mode_s number of values that are the same
$mode_s = array_count_values($x);

//Gets the maximum count then returns the key.
$mode = array_keys($mode_s, max($mode_s));
$mode = $mode[0];

//Checks how many of the values are 100% || $per_compleate_scalculates
$num_of_compleated = $x['100'];
$number_of_enrolled_students = count($allStudentIds);
$per_compleate = $num_of_compleated/$number_of_enrolled_students;

  $mean = $mean_s;
//  $meadian = $meadian_s;
  $mode = $mode;
  $per_compleate = $per_compleate;

   $x = array('Mean' => $mean, 'Mode'=> $mode, '% compleate' =>$per_compleate);
  return $x;
}


function generateReport($course_id){


	//Get all student ids for couse
	$allStudentIds = Course::get_course_students_ids($course_id);

	//Get units and menue order
	$unit_ids =  getUnitsAndMenuOrder($course_id);
	//Get compleation data
	$class_progress = getCompleationData($allStudentIds,$course_id, $unit_ids);
	//Get last login & enroll Date
	$login_dates = get_dates($allStudentIds,$course_id);

	$student_name_and_email = getnameandEmail($allStudentIds);


	//cobine user data
	$student_course_data_array = combineData($login_dates,$class_progress);

	$student_full_data_array = combineData($student_course_data_array,$student_name_and_email);

	//Class Stats
	$total_class_stats = CalcCourseStats($student_full_data_array,$allStudentIds);

	buildReport($total_class_stats,$student_full_data_array,$course_id);
	//return true;
}



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

$time = time();

//$course_id ='18';

$myfile = fopen($dir.$time."-".$course_id.".json", "w") or die("Unable to open file!");
fwrite($myfile, $h);
fclose($myfile);
  return $h;
}

//combines array values with same keys
function combineData($x,$y){
    $f =  array();
  foreach ($x as $key => $value) {
    $f[$key] = $value + $y[$key];
  }
return $f;
}



function getnameandEmail($allStudentIds)
{
  $u = array();
  foreach ($allStudentIds as $id) {
  $x = get_userdata($id);
  $ue = $x->user_email;
  $fn = $x->first_name;
  $ln = $x->last_name;

  $u[$id] = array('name' => $fn.' '.$ln,'email'=>$ue);
  }
return $u;
}
//Get last login + enrolled_course_date_
function get_dates($allStudentIds,$course_id){
  $last_logins = array();
  foreach ($allStudentIds as $id) {
  $a = get_user_meta($id, 'latest_activity',true);
  $b = get_user_meta($id, 'enrolled_course_date_'.$course_id,true);
  $last_logins[$id] = array('last_login' => $a,'enrolled_course_date' =>$b );
  }
  return $last_logins;
}



function getCourses(){
		$myposts = get_posts(array(
			'showposts' => -1,
			'post_type' => 'course',
			'orderby'   => 'ID',
			'order'     => 'ASC',
			));

	$courses = array();
	foreach($myposts as $course){
		$c = array();
		$ID = $course->ID;
		$title = $course->post_title;
		$c[] = $ID;
		$c[] = $title;

		$courses[] = $c;
		}
$form = '<select id="selectbasic" name="course_id" class="form-control">';
	foreach($courses as $course){
     $form .='<option value="'.$course[0].'">'.$course[1].'</option>';
	}
$form .= '</select>';
return $form;
	}





 ?>
