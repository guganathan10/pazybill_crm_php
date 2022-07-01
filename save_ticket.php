<?php 
session_start();
if (!isset($_SESSION['store_id']) && $_SESSION['store_id'] == '')
{
    header('Location: login.php');
}
require_once("./config/db.php");
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
  $date=date("d-m-Y")+" "+date("h:i:sa");
  $sql_comment="select name from stores where store_id='".$_SESSION['store_id']."'";
$result_comment = $conn->query($sql_comment);
$row_comment = $result_comment->fetch_assoc();
$comment[0]=array("date"=>$date,"replaied_by"=>$row_comment['name'],"comment"=>$_POST['issue']);
$comment=json_encode($comment);
  $sql = "insert into support_queries values ('','".$_POST['issue_in']."','','".$_SESSION['store_id']."','".$_POST['issue']."',now(),now(),'open','$comment')";
  $result = $conn->query($sql);
  if($result)
  {
    header('Location:ticket.php?stat=success');
  }else
  {
    header('Location:ticket.php?stat=failed');
  }

?>