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
if(isset($_REQUEST['id']))
{
    $sql = "update bills set status=0 where bill_id='".$_REQUEST['id']."'";
    $result = $conn->query($sql);
    header('Location: bills.php');
}
else
{
    header('Location: index.php');
}
  
 // $result = $conn->query($sql);

?>