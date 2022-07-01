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

  $sql = "select * from store_bank_info where store_id='".$_SESSION['store_id']."' order by s_no desc";
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
                    <h1>Account Details</h1>
                    <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                        <ol class="breadcrumb pt-0">
                            <li class="breadcrumb-item">
                                <a href="#">Home</a>
                            </li>
                           
                            <li class="breadcrumb-item active" aria-current="page">Account Details</li>
                        </ol>
                    </nav>
                    <div class="separator mb-5"></div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h2 class="m-3">List</h2>
                                        </div>
                                        <div class="col-md-6">
                                            <a class="btn btn-info text-white m-3" style="float:right" href="add_bank.php">Add New +</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-stripped" id="bank_accounts">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Bank Name</th>
                                                <th>Account Number</th>
                                                <th>IFSC</th>
                                                <th>Branch Name</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php  $i=1;
                                            if ($result->num_rows > 0) {
                                            while($row = $result->fetch_assoc()) { ?>
                                            <tr>
                                                <td><?php echo $i++ ?></td>
                                                <td><?php echo $row['bank_name'] ?></td>
                                                <td><?php echo $row['account_number'] ?></td>
                                                <td><?php echo $row['IFSC'];?></td>
                                                <td><?php echo $row['branch_name']; ?></td>
                                                <?php if($row['status']==1){ ?>
                                                <td><span style='color:green'>Active</span></td>
                                                <td><a href="deactive_account.php?id=<?php echo $row['store_bank_id'] ?>&status=0" class="btn btn-secondary text-white">Deactivate</a></td>
                                                <?php }else{
                                                    ?>
                                                    <td><span style='color:red'>Inactive</span></td>
                                                    <td><a href="deactive_account.php?id=<?php echo $row['store_bank_id'] ?>&status=1" class="btn btn-primary text-white">Activate</a></td>
                                                    <?php
                                                } ?>
                                                
                                            </tr>
                                            <?php }}   ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
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
        $('#bank_accounts').DataTable();
    </script>
    

</body>

</html>