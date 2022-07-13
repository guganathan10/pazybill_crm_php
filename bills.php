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

  $sql = "select id,bill_id,bill_info,user_id,bill_no,order_status,order_amount,(select phone_num from user where user.user_id=bills.user_id) as phone_num,review,bill_date,bill_time,(select name from stores where store_id=bills.store_id) as store_name,user_id,rating,status,bill_uri from bills where store_id='".$_SESSION['store_id']."' order by id desc";
  $result = $conn->query($sql);
  function mask_mobile_no($number)
  {
    return substr($number, 0, 2) . 'XXXXXXXX' ;
  }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>PazyBill | Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" href="font/iconsmind-s/css/iconsminds.css" />
    <link rel="stylesheet" href="font/simple-line-icons/css/simple-line-icons.css" />
    <link rel="stylesheet" href="css/vendor/dataTables.bootstrap4.min.css" />
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
                    <h1>Bills</h1>
                    <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                        <ol class="breadcrumb pt-0">
                            <li class="breadcrumb-item">
                                <a href="#">Home</a>
                            </li>
                           
                            <li class="breadcrumb-item active" aria-current="page">Bills</li>
                        </ol>
                    </nav>
                    <div class="separator mb-5"></div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <table class="data-table data-table-bills">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Bill Id</th>
                                        <th>Bill Date</th>
                                        <th>Customer Id</th>
                                        <th>Amount</th>
                                        <th>Rating</th>
                                        <th>Payment</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    <?php
                                    $i=1;
                                    if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        setlocale(LC_MONETARY, 'en_IN');
        $bill_info=json_decode($row['bill_info'],true);
   ?>
                                    <tr>
                                        <td><?php echo $i++ ?></td>
                                        <?php if($row['bill_no']==''){ ?>
                                        <td><?php  
                                        echo $bill_info['bill_no'];
                                        ?></td>
                                        <?php }else{ ?>
                                            <td><?php echo $row['bill_no'] ?></td>
                                            <?php } ?>
                                        <td><?php echo $row['bill_date']." ".$row['bill_time'] ?></td>
                                        <?php if($row['phone_num']!='') {?>
                                        <td><?php echo mask_mobile_no($row['phone_num']) ?></td>
                                        <?php }else{
                                            if($row['user_id']=='')
                                            {
                                                echo '<td>Unscanned</td>';
                                            }else
                                            {
                                                echo '<td>Deleted User</td>';
                                            }
                                        } ?>
                                        
                                        <td><?php if($row['order_amount']!=''){echo '₹ '.money_format('%!i', $row['order_amount']); } else{ echo '₹ '.money_format('%!i', $bill_info['amt']); }?></td>
                                        <td><?php echo $row['rating'] ?></td>
                                        <?php if($row['order_status']==1 ){ ?>
                                        <td>Paid</td>
                                        <?php } else { ?>
                                            <td>Direct</td>
                                        <?php } ?>
                                       
                                        <?php if($row['status']==1 ){ ?>
                                        <td>Active</td>
                                        <?php } else { ?>
                                            <td>Cancelled</td>
                                        <?php } ?>
                                        <td>
                                            <a href="bill_details.php?id=<?php echo $row['bill_id'] ?>"target=_blank class="btn btn-outline-primary "><i class="simple-icon-eye"></i></a>
                                            <?php if($row['status']==1 ){ ?>
                                            <a onclick="confirm_cancel('<?php echo $row['bill_id'] ?>')" class="btn btn-outline-danger "><i class="iconsminds-delete-file"></i></a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php 
                                     }
                                    }
                                  
                                    $conn->close();  ?>
                                </tbody>
                            </table>
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
       function confirm_cancel(id)
       {
            let result = confirm('Are you sure you want to delete?');
            let message = result ? window.location.href='cancel_bill.php?id='+id : console.log('stopped');
       }
    </script>

</body>

</html>