<?php

echo "<br>Mean: ".$mean."</br>";
echo "Mode: ".$mode."</br>";
echo "Percent complete: ".$p_com."</br>" ;
?>
<table class="table table-bordered table-striped table-condensed table-responsive">
 <thead>
   <tr>
     <th>
       Name
     </th>
     <th>
       Email
     </th>
     <th>
       Course Progress
     </th>
     <th>
       Enrolled course date
     </th>
     <th>
       Last login
     </th>
   <?php
//Loop through available units and echo the column name
 for ($i=0; $i <$number_of_units ; $i++) {
$f = $i+1;
   echo '<th>Unit '.$f.'</th>';
 }

 ?>
	</tr>
	</thead>
	<tbody>

    <?php
	//Calcualte when the student was last in the course
    foreach ($students as $key => $student) {
      $enroll_date = strtotime($student->enrolled_course_date);
      $current_time = time();
      $time_diff = $current_time - $enroll_date;
      //seconds
      $time_diff = $time_diff/60;
	  //minutes
      $time_diff = $time_diff/60;
	  //hours
	  //Round to closest whole number
      $time_diff = round($time_diff/24);
	  //If course progress is less than 1 then student has not started the course
      if ($student->course_progress < 1) {
        $stnt =  "Has not started since ".$time_diff." Days ago";
      }else {
     $stnt =  "Started ".$time_diff." Days ago";
      }
	  
//Calculate when the student last logged in
  $ll = $student->last_login ;
  $lld = $current_time - $ll;
  //seconds
  $lld = $lld/60;
  //minutes
  $lld = $lld/60;
  //hours 
  //Round to closest whole number
  $lld = round($lld/24);
  //If $lld is less than one then it hasn't been 24 hours
  if($lld < 1){
	$ll_stanement = "Today";
  }else{
	$ll_stanement = $lld." Days ago";
  }

//Display Report Data
      echo "<tr>";
        echo "<td>".$student->name."</td>";
        echo "<td>".$student->email."</td>";
        echo "<td>".$student->course_progress."</td>";
        echo "<td>".$stnt."</td>";
        echo "<td>".$ll_stanement."</td>";

        for ($i=0; $i <$number_of_units ; $i++) {
          $f = $i+1;
          $x ='Unit_'.$f;
          echo "<td>".$student->$x."</td>";
        }

      echo "</tr>";

    }
     ?>
	</tbody>
</table>
