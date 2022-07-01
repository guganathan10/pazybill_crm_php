<?php
session_start();
require_once("./config/db.php");
$conn = new mysqli($servername, $username, $password, $database);

if($_POST['newpassword']==$_POST['confirmpassword'])
{
    $sql="select * from store_login where store_id='".$_SESSION['store_id']."'";
                            $result = $conn->query($sql);
                            if($result->num_rows===1)
                            {
                                $row = $result -> fetch_assoc();
                                $hashed_password=$row['password'];
                                if( hash_equals($hashed_password, crypt($_POST['oldpassword'], $hashed_password))){
                                   $sql_update="update store_login set password='".crypt($_POST['newpassword'])."' where store_id='".$_SESSION['store_id']."'";
                                   $result = $conn->query($sql_update);
                                   if($result)
                                   {
                                    header('Location:profile.php?success=true');
                                   }else
                                   {
                                    header('Location:profile.php?error=update');
                                   }
                                }
                               
                            }
}else
{
    header('Location:profile.php?error=pasword');
}
?>