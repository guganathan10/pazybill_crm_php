<?php 
session_start();
error_reporting(1);
if (!isset($_SESSION['store_id']) && $_SESSION['store_id'] == '')
{
    header('Location: login.php');
}
require_once("./config/db.php");
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
function gen_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
        mt_rand( 0, 0xffff ),
        mt_rand( 0, 0x0fff ) | 0x4000,
        mt_rand( 0, 0x3fff ) | 0x8000,
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
    
}
if(isset($_POST['account_number']))
{
    $accountnumber=$_POST['account_number'];
    $bankname=$_POST['bank_name'];
    $accountname=$_POST['account_holder_number'];
    $ifsc=$_POST['ifsc_code'];
    $branchname=$_POST['branch_name'];
    $uuid=gen_uuid();
    $store_id=$_SESSION['store_id'];
    $sql_bank="insert into store_bank_info (store_id,bank_name,IFSC,branch_name,account_number,account_name,store_bank_id,created_no) values('$store_id','$bankname','$ifsc','$branchname','$accountnumber','$accountname','$uuid',now());";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>PazyBill | Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" href="font/iconsmind-s/css/iconsminds.css" />
    <link rel="stylesheet" href="font/simple-line-icons/css/simple-line-icons.css" />
    <link rel="stylesheet" href="css/vendor/dataTables.bootstrap4.min.css" />
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" href="css/vendor/datatables.responsive.bootstrap4.min.css" />
    <link rel="stylesheet" href="css/vendor/bootstrap.min.css" />
    <link rel="stylesheet" href="css/vendor/bootstrap.rtl.only.min.css" />
    <link rel="stylesheet" href="css/vendor/perfect-scrollbar.css" />
    <link rel="stylesheet" href="css/vendor/component-custom-switch.min.css" />
    <link rel="stylesheet" href="css/main.css" />
</head>

<body id="app-container" class="menu-default show-spinner">
 <?php include "./includes/header.php" ?>
   <?php include "./includes/sidebar.php" ?>
    <main>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <h1>Account</h1>
                    <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                        <ol class="breadcrumb pt-0">
                            <li class="breadcrumb-item">
                                <a href="#">Home</a>
                            </li>
                           
                            <li class="breadcrumb-item active" aria-current="page">Account</li>
                        </ol>
                    </nav>
                    <div class="separator mb-5"></div>
                    <div class="row">
                        <div class="col-md-3">

                        </div>
                        <div class="col-md-6">
                           
                        <div class="card mb-4">
                        <div class="card-body">
                        <?php
                            if(isset($_POST['account_number']))
                            {
                                if ($conn->query($sql_bank) === TRUE) 
                                { 
                                    ?>
                                    <div class="alert alert-success rounded" role="alert">
                                            Account details added
                                        </div>
                                       <?php 
                                            } 
                                            else 
                                            { 
                                        ?>
                                        <div class="alert alert-danger rounded" role="alert">
                                            Could not add your account information
                                        </div>
                                       <?php
                                }
                           }
                            ?>
                            <form method="post" action="add_bank.php">
                               
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Account Number</label>
                                    <input type="text" required class="form-control" id="account_number"
                                        name="account_number" placeholder="Account Number">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Account Name</label>
                                    <input type="text" required class="form-control" id="account_holder_number"
                                        name="account_holder_number" placeholder="Account Name">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Bank Name</label>
                                    <input type="text" required class="form-control" id="bank_name"
                                        name="bank_name" placeholder="Bank Name">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">IFSC Code</label>
                                    <input type="text" required class="form-control" id="ifsc_code"
                                        name="ifsc_code" placeholder="IFSC Code">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Branch Name</label>
                                    <input type="text" required class="form-control" id="confirm_password"
                                        name="branch_name" placeholder="Branch Name">
                                </div>
                               

                                <button type="submit" class="btn btn-primary mb-0">Submit</button>
                                <a class="btn btn-warning mb-0" href="bank_account.php">Cancel</a>
                            </form>
                        </div>
                    </div>
                        </div>
                        <div class="col-md-3">

                        </div>
                    </div>
                   
                </div>
            </div>

        </div>
    </main>

 <?php include "./includes/footer.php"; ?>
    
    <script src="js/vendor/jquery-3.3.1.min.js"></script>
    <script src="js/vendor/bootstrap.bundle.min.js"></script>
    <script src="js/vendor/perfect-scrollbar.min.js"></script>
    <script src="js/vendor/datatables.min.js"></script>
    <script src="js/dore.script.js"></script>
    <script src="js/scripts.js"></script>
    <script>
       
    </script>

</body>
                    
</html>