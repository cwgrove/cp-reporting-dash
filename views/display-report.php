<?php

//$student->last_login;











echo "Mean".$mean."</br>";
echo "Mode".$mode."</br>";
echo "Percent Compleate".$p_com."</br>" ;

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

 for ($i=0; $i <$number_of_units ; $i++) {
$f = $i+1;
   echo '<th>Unit '.$f.'</th>';
 }

 ?>
	</tr>
	</thead>


	<tbody>

    <?php
    foreach ($students as $key => $student) {
/// calc enrollment time
      $enroll_date = strtotime($student->enrolled_course_date);
      $current_time = time();
      $time_diff = $current_time - $enroll_date;
      //seconds
      $time_diff = $time_diff/60;
      $time_diff = $time_diff/60;
      $time_diff = $time_diff/24;
      $time_diff = round($time_diff);

      if ($student->course_progress < 1) {
        $stnt =  "Has not started since ".$time_diff." Days ago";
      }else {
     $stnt =  "Started ".$time_diff." Days ago";
      }

///calc dasys since logged in

  $ll = $student->last_login ;
  $lld = $current_time - $ll;
  $lld = $lld/60;
  $lld = $lld/60;
  $lld = round($lld/24);
  if($lld < 1){
  $ll_stanement = "Today";
  }
  $ll_stanement = $lld." Days ago";



      echo "<tr>";
        echo "<td>".$student->name."</td>";
        echo "<td>".$student->email."</td>";
        echo "<td>".$student->course_progress."</td>";
        //echo "<td>".$student->enrolled_course_date."</td>";
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
