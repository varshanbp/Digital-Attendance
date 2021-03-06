<?php
require_once('../db-conn.php');
require_once('../authenticator.php');

echo "<!DOCTYPE html>
<html>
<title>DA Data Processing :: Digtal Attendance by JSSV</title>
<meta name='viewport' content='width=device-width, initial-scale=1'>
<link rel='stylesheet' href='../../digi-attend/css/w3.css'>";

date_default_timezone_set('Asia/Calcutta');

if(isset($_POST["sem"])) {
  $sem=$_POST["sem"];
  $usem=explode("-",$sem);
  $csem=date('n');
  if($csem<=11&&$csem>=6)
    $sem=$usem[0];
  else
    $sem=$usem[1];
} else {
  include('../header.php');
  echo "<p class='w3-xxxlarge' style='text-align: center;'>FATAL ERROR CONTACT DBA</p>";
  include('../footer.php');
  exit();
}

$day=date('d');
$month=date('m_');
$year=date('y_');
$q_tbnm=$month.$year.$sem;


$stu_qry1="SELECT * FROM `$tbname2` WHERE sem='$sem' ORDER BY regno ASC";
$res_stu1=mysqli_query($sql_conn2,$stu_qry1);

$res_cnt=mysqli_num_rows($res_stu1);

echo "<div class='w3-xxlarge'>STUDENTS OF SEMESTER ".$sem."</div>
<div class='w3-container w3-blue'><p>INFO! : ONLY MARK ABSENTEES, \"PRESENT\" STUDENTS MARKING AUTOMATED</p></div>
<iframe class='w3-container w3-border' name='if2' style='width:100%;height:100%;'></iframe>";
echo "<table class='w3-table w3-striped w3-bordered w3-card-4'>
      <tr class='w3-red'><th>Serial No</th><th>Name</th><th>Register No</th>";
if(date('G')<=11)
  echo "<th>Morning Attendance</th>";
else
  echo "<th>Afternoon Attendance</th>";
echo "<th>Submit/Cancel</th></tr>";

$i=1;

while($list_res=mysqli_fetch_assoc($res_stu1)) {
  echo "<form action='att-pdb.php' method='POST' id='myForm' target='if2'>";
  echo "<tr><td>".$i."</td><td>".$list_res['name']."</td><td><input type='text' value=".$list_res['regno']."
   name='regno' style='border:0;' readonly></td>";
  echo "<input type='text' value=".$sem." name='sem' hidden readonly>";

  $reg=$list_res['regno'];

  $stu_qry4="SELECT `".date('d')."` FROM `$q_tbnm` WHERE regnum='$reg'";
  $res_stu4=mysqli_query($sql_conn2,$stu_qry4) or die("error");
  $list_res4=mysqli_fetch_assoc($res_stu4);

  if(date('G')<=11) {
    if($list_res4["$day"]==NULL) {
      $sql_qry4="UPDATE `$q_tbnm` SET `$day`='P' WHERE `regnum`='$reg'";
      mysqli_query($sql_conn2, $sql_qry4) or die("FATAL ERROR CONTACT DEVELOPER".mysqli_error($sql_conn2));
    }
    echo "<!--td><input class='w3-radio' type='radio' name='mast' value='P' >
    <label class='w3-validate'>PRESENT</label></td-->
    <td><input class='w3-radio' type='radio' name='mast' value='A' >
    <label class='w3-validate'>ABSENT</label></td>";
  } else {
    /*$stu_qry4="SELECT `".date('d')."` FROM `$q_tbnm` WHERE regnum='$reg'";
    $res_stu4=mysqli_query($sql_conn2,$stu_qry4);
    $list_res4=mysqli_fetch_row($res_stu4);*/
    if((strlen($list_res4[0])==0)) {
      $sql_qry4="UPDATE `$q_tbnm` SET `$day`=NULL WHERE `regnum`='$reg'";
      mysqli_query($sql_conn2, $sql_qry4) or die("FATAL ERROR CONTACT DEVELOPER".mysqli_error($sql_conn2));
    } else {
      $att_value=$list_res4[0]."P";
      $sql_qry4="UPDATE `$q_tbnm` SET `$day`='$att_value' WHERE `regnum`='$reg'";
      mysqli_query($sql_conn2, $sql_qry4) or die("FATAL ERROR CONTACT DEVELOPER".mysqli_error($sql_conn2));
    }

    echo "<!--td><input class='w3-radio' type='radio' name='aast' value='P' >
    <label class='w3-validate'>PRESENT</label></td-->
    <td><input class='w3-radio' type='radio' name='aast' value='A' >
    <label class='w3-validate'>ABSENT</label></td>";
  }

//  mysqli_query($sql_conn2, $sql_qry4) or die("FATAL ERROR CONTACT DEVELOPER".mysqli_error($sql_conn2));

  echo "<td><input class='w3-btn w3-light-green' type='submit' />
  <input class='w3-btn w3-red' type='reset' /></td></tr>";

  echo "</form>";
  $i++;
}
echo "<table><br></form>";
echo "</table><div style='text-align:center;' class='w3-xxxlarge'>--------------- END OF LIST ---------------</div>";

 ?>
