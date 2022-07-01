<?php
session_start();
require_once("./config/db.php");
$conn = new mysqli($servername, $username, $password, $database);
$id=$_REQUEST['id'];
$comment=$_REQUEST['comment'];
$date=date("d-m-Y")+" "+date("h:i:sa");
$sql="select name from stores where store_id='".$_SESSION['store_id']."'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$sql_ticket="select * from support_queries where id='".$id."'";
$result_ticket = $conn->query($sql_ticket);
$row_ticket = $result_ticket->fetch_assoc();
$decoded_comment=json_decode($row_ticket['comments'],true);
$comment_array=array("date"=>$date,"replaied_by"=>$row['name'],"comment"=>$comment);
array_push($decoded_comment,$comment_array);
$decoded_comment=json_encode($decoded_comment);
$squ_update="UPDATE support_queries SET comments = '$decoded_comment' WHERE id='".$id."'";
$result = $conn->query($squ_update);
  if($result)
  {
    $response=array("code"=>200,"message"=>"Comment Added");
    echo json_encode($response);
  }else
  {
    $response=array("code"=>200,"message"=>"There is some error");
    echo json_encode($response);
  }
?>