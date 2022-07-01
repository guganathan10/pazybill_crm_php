<?php 
require_once("./config/db.php");
$conn = new mysqli($servername, $username, $password, $database);
if(isset($_REQUEST['id']) && $_REQUEST['id']!='')
{
    $sql="update store_bank_info set status=".$_REQUEST['status']." where store_bank_id='".$_REQUEST['id']."'";
    echo $conn->query($sql);
    if($conn->query($sql)===TRUE)
    {
        header('Location:bank_account.php?success=true');
    }else
    {
        header('Location:bank_account.php?error=update');
    }
    
}else
{
    header('Location:bank_account.php');
}

?>