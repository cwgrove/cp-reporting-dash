<?php





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
      echo "<tr>";
        echo "<td>".$student->name."</td>";
        echo "<td>".$student->email."</td>";
        echo "<td>".$student->course_progress."</td>";
        echo "<td>".$student->enrolled_course_date."</td>";
        echo "<td>".$student->last_login."</td>";

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
