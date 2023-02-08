<?php
$flash_message = 'message-text';
// database connection
include('db.php');
?>

<?php
  // GET ALL THE CANDIDATES JAMB REG NUMBERS FROM JAMB RESULT TABLE
  $olevel = "SELECT reg_number FROM primary_table";
  $olevel = $connect_db->query($olevel);
  if ($olevel->num_rows > 0) {
    // output data of each row 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Result | Calculation</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.1/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="icon" type="image/png" href="images/favicon.png"/>
</head>
<body>
<div class="container">

<table class="table table-bordered table-responsive">
  <thead>
    <tr>
      <th> reg_number </th>
      <th> name </th>
      <th> utme_agg </th>
      <th> olevel_score </th>
      <th> course </th>
      <th> utme_subjects </th>
      <th> olevel_result </th>
      <th> result_count </th>
    </tr>
  </thead>
  <tbody>
    <?php

      while($olevel_row = $olevel->fetch_assoc()) { 
        $regno = $olevel_row["reg_number"];//'202210013577GA';

        # get result count
        #SELECT count(distinct year, exam_number, type) as result_count_3, reg_number FROM secondary_table GROUP BY reg_number;

        # GET Candidates O'level Result
        $utmesubjects = "SELECT DISTINCT reg_number,subject,grade,year,type,exam_number,score FROM secondary_table WHERE reg_number = '$regno'";
        $utmesub = $connect_db->query($utmesubjects);
        if (!empty($utmesub) && $utmesub->num_rows > 0) {
          // code...
          $utmesubArray = array();
          while($utmesub_row = $utmesub->fetch_assoc()) {
             //array_push($utmesubArray, $utmesub_row['subject'].'|'.$utmesub_row['score']);
             array_push($utmesubArray, $utmesub_row['subject'].'|'.$utmesub_row['score']);
           }
        }
        //$utmesub_row['score']
        @$olevel_detail = json_encode($utmesubArray);
        //$olevel_detail = trim($olevel_detail, '[]');
        //$olevel_detail = str_replace("[","",$olevel_detail);
        //$olevel_detail = str_replace("]","",$olevel_detail);
        $olevel_detail = str_replace('"'," ",$olevel_detail);

        $get_main_result = "SELECT
            primary_table.name,
            primary_table.reg_number,
            primary_table.su1 AS s1,
            primary_table.su2 AS s2,
            primary_table.su3 AS s3,
            primary_table.eng AS eng,
            primary_table.agg AS utmescore,
            SUM(secondary_table.score) AS points,
            primary_table.course AS course,
            result_count_3.reg_number,
            result_count_3.result_count as result_count_3
            
            
            FROM secondary_table
            INNER JOIN primary_table
            ON
            primary_table.reg_number = secondary_table.reg_number
            JOIN result_count_3
            ON
            result_count_3.reg_number = primary_table.reg_number
            WHERE primary_table.reg_number = '$regno' 
            AND secondary_table.subject IN (primary_table.su1, primary_table.su2, primary_table.su3, 'ENG') 
            ORDER BY points";

            $main_result = $connect_db->query($get_main_result);

            while($main_result_row = $main_result->fetch_assoc()) { ?>
              <tr>
                <td> <?php echo $olevel_row["reg_number"];?> </td>
                <td> <?php echo $main_result_row["name"];?> </td>
                <td> <?php echo $main_result_row["utmescore"];?> </td>
                <td> <?php echo $main_result_row["points"];?> </td>
                <td> <?php echo $main_result_row["course"];?> </td>
                <td> <?php echo $main_result_row["s1"]."|".$main_result_row["s2"]."|".$main_result_row["s3"];?> </td>
                <td> <?php echo $olevel_detail;?> </td>
                <td> <?php echo $main_result_row["result_count_3"];?> </td>
              </tr>
      <?php }
    ?>
  <?php }  
  }else {
      $flash_message = "No Data";
  } ?>
  </tbody>
</table>

</div>
</body>
