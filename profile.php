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

  $sql = "select id,bill_id,review,bill_date,bill_time,(select name from stores where store_id=bills.store_id) as store_name,user_id,rating,status,bill_uri from bills where store_id='".$_SESSION['store_id']."' order by id desc";
  $result = $conn->query($sql);
  
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
                    <h1>Profile</h1>
                    <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                        <ol class="breadcrumb pt-0">
                            <li class="breadcrumb-item">
                                <a href="#">Home</a>
                            </li>
                           
                            <li class="breadcrumb-item active" aria-current="page">Profile</li>
                        </ol>
                    </nav>
                    <div class="separator mb-5"></div>
                    <div class="row">
                        <div class="col-md-3">

                        </div>
                        <div class="col-md-6">
                        <div class="card mb-4">
                        <div class="card-body">
                            <?php if($_REQUEST['error']=="pasword"){ echo '<h5 class="mb-4" style="color:red" align="center">New and confirm password must be same</h5>'; }
                            if($_REQUEST['success']=="true"){ echo '<h5 class="mb-4" style="color:green" align="center">Password updated successfully</h5>'; }
                            if($_REQUEST['error']=="update"){ echo '<h5 class="mb-4" style="color:red" align="center">Something went wrong please try again later</h5>'; } ?>

                            <form method="post" action="update_password.php">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Old Password</label>
                                    <input type="password" required class="form-control" id="old_password"
                                        name="oldpassword" placeholder="Old Password">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">New Password</label>
                                    <input type="password" required class="form-control" id="new_password"
                                        name="newpassword" placeholder="New Password">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Confirm Password</label>
                                    <input type="password" required class="form-control" id="confirm_password"
                                        name="confirmpassword" placeholder="Confirm Password">
                                        <small style="color :red" id="error_message"><small>
                                </div>

                                <button type="submit" class="btn btn-primary mb-0">Submit</button>
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
        $('#confirm_password').on('change',function(){
            let newpass=$('#new_password').val();
            let confirmpass=$('#confirm_password').val();
            if(newpass!=confirmpass)
            {
                $('#error_message').html("New and confirm password must be same" );
            }
            else {
                $('#error_message').html("");
                }
        })
    </script>

</body>

</html>