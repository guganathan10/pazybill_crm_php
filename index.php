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

  $sql = "select count(*) as bill_count from bills where store_id='".$_SESSION['store_id']."'";
  $result = $conn->query($sql);
  $row = $result -> fetch_assoc();
  $bill_count=$row['bill_count'];

  $sql_cancel = "select count(*) as bill_count from bills where store_id='".$_SESSION['store_id']."' and status='cancelled'";
  $result_cancel = $conn->query($sql_cancel);
  $row_calcel = $result_cancel -> fetch_assoc();
  $bill_count_cancel=$row_calcel['bill_count'];

  $sql_unscanned = "select count(*) as unscanned from bills where user_id is null and store_id='".$_SESSION['store_id']."'  and status =1";
  $result_unscanned = $conn->query($sql_unscanned);
  $row_unscanned = $result_unscanned -> fetch_assoc();
  $unscanned_count=$row_unscanned['unscanned'];

  $sql_scanned_today = "select count(*) as scanned from bills where user_id is not null and store_id='".$_SESSION['store_id']."' and date(bill_date)=date(now()) and order_status=1 and status =1 ";
  $result_scanned_today = $conn->query($sql_scanned_today);
  $row_scanned_today = $result_scanned_today -> fetch_assoc();
  $scanned_count_today=$row_scanned_today['scanned'];

  $sql_bills_today = "select count(*) as scanned from bills where user_id is not null and store_id='".$_SESSION['store_id']."' and date(bill_date)=date(now()) and status =1 ";
  $result_bills_today = $conn->query($sql_bills_today);
  $row_bills_today = $result_bills_today -> fetch_assoc();
  $bills_count_today=$row_bills_today['scanned'];
  

  $sql_scanned = "select count(*) as scanned from bills where user_id is not null and store_id='".$_SESSION['store_id']."'  and status =1";
  $result_scanned = $conn->query($sql_scanned);
  $row_scanned = $result_scanned -> fetch_assoc();
  $scanned_count=$row_scanned['scanned'];

  $rating_sql="select sum(rating)/count(rating) as rating from bills where store_id='".$_SESSION['store_id']."' and rating<>0 and status=1";
  $result_rating = $conn->query($rating_sql);
  $row_rating = $result_rating -> fetch_assoc();
  $rating=$row_rating['rating'];

  $rating_sql="select sum(rating)/count(rating) as rating from bills where store_id='".$_SESSION['store_id']."' and rating<>0 and status=1";
  $result_rating = $conn->query($rating_sql);
  $row_rating = $result_rating -> fetch_assoc();
  $rating=$row_rating['rating'];

  $device_sql='select count(*) as count from store_device where status="active" and store_id="'.$_SESSION['store_id'].'"';
  $result_device = $conn->query($device_sql);
  $row_device = $result_device -> fetch_assoc();
  $device=$row_device['count'];


  $customer_sql='select count(*) as count from bills where store_id="'.$_SESSION['store_id'].'" and user_id is not null and status =1 group by user_id';
  $result_customer = $conn->query($customer_sql);
  $row_customer = $result_customer -> num_rows;
  $customer=$row_customer;


  $customer_sql_today='select count(*) from bills where store_id="'.$_SESSION['store_id'].'" and date(bill_date)=date(now()) and user_id is not null and status =1 group by user_id';
  $result_customer_today = $conn->query($customer_sql_today);
  $row_customer_today = $result_customer_today -> num_rows;
  $customer_today=$row_customer_today;

  $payment_sql_recent='select cashfree_transactions.order_amount as amount,cashfree_transactions.created_at as created_at,cashfree_transactions.order_status,bills.bill_no as bill_no,cashfree_transactions.bill_id as bill_id
  from cashfree_transactions 
  left join bills 
  ON cashfree_transactions.bill_id=bills.bill_id 
  where store_id="'.$_SESSION['store_id'].'" order by cashfree_transactions.id desc';
  $result_payment_recent = $conn->query($payment_sql_recent);
  $row_payment_recent = $result_payment_recent -> fetch_assoc();
  $payment_recent=$row_payment_recent['amount']; 
  $payment_status_recent=$row_payment_recent['order_status']; 

  $payment_sql_today='select sum(cashfree_transactions.order_amount) as amount,cashfree_transactions.bill_id 
  from cashfree_transactions 
  left join bills 
  ON cashfree_transactions.bill_id=bills.bill_id 
  where bills.bill_date=date(now()) and store_id="'.$_SESSION['store_id'].'" and cashfree_transactions.order_status=1';
  $result_payment_today = $conn->query($payment_sql_today);
  $row_payment_today = $result_payment_today -> fetch_assoc();
  $payment_today=$row_payment_today['amount'];  

  $payment_sql_total='select sum(cashfree_transactions.order_amount) as amount,cashfree_transactions.bill_id 
  from cashfree_transactions 
  left join bills 
  ON cashfree_transactions.bill_id=bills.bill_id 
  where store_id="'.$_SESSION['store_id'].'" and date(payment_time)=date(now()) and cashfree_transactions.order_status=1';
  $result_payment_total = $conn->query($payment_sql_total);
  $row_payment_total = $result_payment_total -> fetch_assoc();
  $payment_total=$row_payment_total['amount'];  

  $payment_sql_today_count='select count(cashfree_transactions.order_amount) as amount,cashfree_transactions.bill_id 
  from cashfree_transactions 
  left join bills 
  ON cashfree_transactions.bill_id=bills.bill_id 
  where bills.bill_date=date(now()) and store_id="'.$_SESSION['store_id'].'" and cashfree_transactions.order_status=1';
  $result_payment_today_count = $conn->query($payment_sql_today_count);
  $row_payment_today_count = $result_payment_today_count -> fetch_assoc();
  $payment_today_count=$row_payment_today_count['amount']; 
  
  $created_at=explode(" ",$row_payment_recent['created_at']);
  $timestamp = strtotime($created_at[0]);
  $new_date = date("d M Y", $timestamp);
  $new_date = $new_date." ".$created_at[1];
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

    <link rel="stylesheet" href="css/vendor/bootstrap.min.css" />
    <link rel="stylesheet" href="css/vendor/bootstrap-stars.css" />
    <link rel="stylesheet" href="css/main.css" />
    <style>
        small, .small
        {
            font-size:40% !important;
        }
        </style>
</head>

<body id="app-container" class="menu-default show-spinner">
<?php include "./includes/header.php" ?>
   <?php include "./includes/sidebar.php" ?>
    <main>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <h1>Dashboard</h1>
                    <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                        <ol class="breadcrumb pt-0">
                            <li class="breadcrumb-item">
                                <a href="#">Home</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                        </ol>
                    </nav>
                    <div class="separator mb-5"></div>
                </div>
            </div>
            
            <h3>Payment</h3>
            <div class="row">
                <div class="col-xl-12 col-lg-12 mb-4">
                    <div class="row">
                        <div class="col-4 mb-4">
                            <div class="card ">
                                <div class="card-body">
                                    <div class="row">
                                    <a href="bill_details.php?id=<?php echo $row_payment_recent['bill_id'] ?>">
                                        <div class="col-md-12">
                                            <?php 
                                            if($payment_status_recent==1)
                                            {  ?>
                                               <p class="lead color-theme-1 mb-1 value" style="color:green">₹ <?php setlocale(LC_MONETARY, 'en_IN'); echo money_format('%!i',$payment_recent); ?></p>
                                            
                                                <?php   }
                                            else
                                            {
                                                ?>
                                                <p class="lead color-theme-1 mb-1 value" style="color:red">₹ <?php setlocale(LC_MONETARY, 'en_IN'); echo money_format('%!i',$payment_recent); ?></p>
                                            
                                                <?php  } 
                                            ?>
                                            <p>Bill No: <?php echo $row_payment_recent['bill_no'] ?></p>
                                            <p>Time: <?php echo $new_date?></p>
                                            <p class="mb-0 label text-small"><b>Recent Payment </b></p>
                                        </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 mb-4">
                            <div class="card ">
                                <div class="card-body">
                                <div class="row">
                                        <div class="col-md-12">
                                            <p class="lead color-theme-1 mb-1 value">₹ <?php setlocale(LC_MONETARY, 'en_IN'); echo money_format('%!i',$payment_today+0); ?></p>
                                            <p>No of Paid Bills: <?php echo $scanned_count_today; ?></p>
                                            <p>Total Bills: <?php echo $bills_count_today; ?></p>
                                            <p class="mb-0 label text-small"><b>Today Payment</b></p>
                                        </div>
                                      
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                     
                       
                        <div class="col-4 mb-4">
                            <div class="card ">
                                <div class="card-body">
                                <div class="row">
                                        <div class="col-md-12">
                                        <p class="lead color-theme-1 mb-1 value">₹ <?php setlocale(LC_MONETARY, 'en_IN'); echo money_format('%!i',$payment_total); ?> </p>
                                        <p>Settlement Date</p>
                                        <p>Transaction Info</p>
                                        <p class="mb-0 label text-small"><b>Previous Settlement</b></p>
                                                
                                        </div>
                                        
                                    </div>
                                    
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
               
            </div>
            <h3>Bills</h3>
            <div class="row">
                <div class="col-xl-12 col-lg-12 mb-4">
                    <div class="row">
                        <div class="col-3 mb-4">
                            <div class="card ">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-10">
                                            <p class="lead color-theme-1 mb-1 value"><?php echo $bill_count; ?></p>
                                            <p class="mb-0 label text-small">No of Bills Generated</p>
                                        </div>
                                        <div class="col-md-2">
                                                <i class="color-theme-1 iconsminds-add-file" style="font-size:30px; margin-left :-10px"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3 mb-4">
                            <div class="card ">
                                <div class="card-body">
                                <div class="row">
                                        <div class="col-md-10">
                                            <p class="lead color-theme-1 mb-1 value"><?php echo $scanned_count; ?></p>
                                            <p class="mb-0 label text-small">No of Bills Scanned Bills</p>
                                        </div>
                                        <div class="col-md-2">
                                                <i class="color-theme-1 iconsminds-qr-code" style="font-size:30px;margin-left :-10px"></i>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-3 mb-4">
                            <div class="card ">
                                <div class="card-body">
                                <div class="row">
                                        <div class="col-md-10">
                                        <p class="lead color-theme-1 mb-1 value"><?php echo $unscanned_count; ?></p>
                                    <p class="mb-0 label text-small">No of Unscanned Bills</p>
                                    
                                        </div>
                                        <div class="col-md-2">
                                                <i class="color-theme-1 iconsminds-letter-open" style="font-size:30px;margin-left :-10px"></i>
                                        </div>
                                    </div>
                                    
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-3 mb-4">
                            <div class="card ">
                                <div class="card-body">
                                <div class="row">
                                        <div class="col-md-10">
                                        <p class="lead color-theme-1 mb-1 value"><?php echo $bill_count_cancel; ?></p>
                                    <p class="mb-0 label text-small">No of Cancelled Bills</p>
                                    
                                        </div>
                                        <div class="col-md-2">
                                                <i class="color-theme-1 iconsminds-close" style="font-size:30px;margin-left :-10px"></i>
                                        </div>
                                    </div>
                                    
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
               
            </div>
            <h3>Customer</h3>
            <div class="row">
            <div class="col-md-6 mb-4">
                    <div class="card  mb-4"">
                        <div class="card-body text-center">
                        <i class="iconsminds-add-user color-theme-1" style="font-size:40px"></i>
                            <p class="lead color-theme-1 mb-1 value"><?php echo $customer_today ?></p>
                            <p class="mb-0 label text-small">Today's  Customer Count </p>
                        </div>
                    </div>
                </div>
            
                <div class="col-md-6 mb-4">
                <div class="card mb-4">
                        <div class="card-body text-center">
                            <i class="iconsminds-male-female color-theme-1" style="font-size:40px"></i>
                            <p class="lead color-theme-1 mb-1 value"><?php echo $customer ?></p>
                            <p class="mb-0 label text-small">Total Customers Count</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                <h3>Device</h3>
                    <div class="card ">
                        <div class="card-body">
                            <p class="lead color-theme-1 mb-1 value"><?php echo $device; ?></p>
                            <p class="mb-0 label text-small">No of Devices available on Store</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h3>Store Rating</h3>
                    <div class="card ">
                        <div class="card-body">
                            <p class="lead color-theme-1 mb-1 value"><?php echo number_format((float)$rating, 1, '.', ''); ?>/5</p>
                            <p class="mb-0 label text-small">No of Devices available on Store</p>
                    </div>
                </div>
            </div>
    </main>

   <?php include "./includes/footer.php" ?>

    <script src="js/vendor/jquery-3.3.1.min.js"></script>
    <script src="js/vendor/bootstrap.bundle.min.js"></script>
    <script src="js/vendor/Chart.bundle.min.js"></script>
    <script src="js/vendor/jquery.barrating.min.js"></script>
    <script src="js/dore.script.js"></script>
    <script src="js/scripts.js"></script>
   
</body>

</html>