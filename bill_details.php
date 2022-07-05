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

  $sql = "select webhook_response,order_amount,payment_time,(select phone_num from user where user_id=customer_id) as customer_id,order_status from cashfree_transactions where bill_id='".$_REQUEST['id']."' order by id desc";
  $result = $conn->query($sql);
  function mask_mobile_no($number)
{
    return substr($number, 0, 2) . 'XXXXXXXX' ;
}
$sql_bills = "select * from bills where bill_id='".$_REQUEST['id']."' order by id desc";
$result_bills = $conn->query($sql_bills);
$row_bills = $result_bills->fetch_assoc();
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
    <link rel="stylesheet" href="css/vendor/bootstrap-stars.css" />
    <link rel="stylesheet" href="css/vendor/perfect-scrollbar.css" />
    <link rel="stylesheet" href="css/vendor/component-custom-switch.min.css" />
    <link rel="stylesheet" href="css/main.css" />
    <style> .card{
    display: flex;
    flex-direction: coloumn;
    }</style>
</head>

<body id="app-container" class="menu-default show-spinner">
 <?php include "./includes/header.php" ?>
   <?php include "./includes/sidebar.php" ?>
    <main>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <h1>Bill Details</h1>
                    <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                        <ol class="breadcrumb pt-0">
                            <li class="breadcrumb-item">
                                <a href="index.php">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="bills.php">Bill</a>
                            </li>
                           
                            <li class="breadcrumb-item active" aria-current="page">Details</li>
                        </ol>
                    </nav>
                    <div class="separator mb-5"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-8">
                    <?php if($row_bills['status']==1){ ?>
                        <p> <b>Bill Status : </b> Active</p>
                    <?php }else{ ?>
                        <p> <b>Bill Status : </b> Cancelled</p>
                    <?php } ?>
                    <?php if($row_bills['review']!='' && $row_bills['review']!='None'){ ?>
                    <p><b>Review :</b> <?php echo $row_bills['review'] ?></p>
                    <?php } ?>
                    <p><b>Payment Status : </b>  <?php if($row_bills['order_status']==0)
                                { 
                                    echo "Failed"; 
                                }
                                else if($row_bills['order_status']==1)
                                { 
                                    echo "Success";
                                }else if($row_bills['order_status']==2)
                                { 
                                    echo "Pending";
                                }else
                                {
                                    echo "No Payment Initation"; 
                                }
                                 ?></p>
                    <?php if($row_bills['rating']!=0){ ?>
                    <p><b>Rating :</b> <select class="rating " data-current-rating="<?php echo $row_bills['rating'] ?>" data-readonly="true">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select> </p>
                    <?php } ?>
                   
                    <iframe src="<?php echo $row_bills['bill_uri'] ?>#toolbar=0&navpanes=0" width="100%" height="850px"></iframe>
                </div>
                <div class="col-4">
                <h3>Payments</h3>
                    <?php  if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) { 
                        $webhook=json_decode($row['webhook_response'],true);
                        $amount=$row['order_amount']; 
                        $trans_date=$row['payment_time'];
                        $phone_num=mask_mobile_no($row['customer_id']);
                        $transaction_id='';
                        $color='';
                        if($row['order_status']==1)
                        {
                            $row['order_status']='Success';
                            $transaction_id=$webhook['cf_payment_id'];
                            $color='green';
                        }elseif($row['order_status']==2)
                        {
                            $row['order_status']='Pending';
                            if($webhook['data']['payment']['cf_payment_id']!='')
                            {
                                $transaction_id=$webhook['data']['payment']['cf_payment_id'];
                            }else if($webhook['cf_payment_id']!='')
                            {
                                $transaction_id=$webhook['cf_payment_id'];
                               
                            }
                            
                            $color='orange';
                        }
                        elseif($row['order_status']==0)
                        {
                            $row['order_status']='Failed';
                            if($webhook['data']['payment']['cf_payment_id']!='')
                            {
                                $transaction_id=$webhook['data']['payment']['cf_payment_id'];
                            }else if($webhook['cf_payment_id']!='')
                            {
                                $transaction_id=$webhook['cf_payment_id'];
                            }
                            $color='red';
                        }
                        $order_status=$row['order_status'];
                        if($order_status!=3){
                        ?>
                   
                            <div class="card m-3" style='border-style: solid;solid;border-width: 7px;border-top-color: <?php echo $color ?>;border-right:none;border-bottom:none;border-left:none'>
                          
                        <div class="card-head">
                            <h2 class="ml-3 pt-3">
                                <?php echo $order_status ?>
                            </h2>
                            
                        </div>
                        <div class="card-body">
                        <div >
                            <p>Transaction Id : <?php echo $transaction_id ?></p>
                            <p>Amount: <?php echo $amount ?></p>
                            <p>Payment Time : <?php echo $trans_date ?></p>
                            <p> Customer : <?php echo $phone_num ?></p>
                            </div>
                        </div>
                       
                    </div>
                    <?php } } } ?>
                </div>
            </div>

          
        </div>
    </main>


 <?php include "./includes/footer.php"; ?>
    
    <script src="js/vendor/jquery-3.3.1.min.js"></script>
    <script src="js/vendor/bootstrap.bundle.min.js"></script>
    <script src="js/vendor/perfect-scrollbar.min.js"></script>
    <script src="js/vendor/datatables.min.js"></script>
    <script src="js/vendor/jquery.barrating.min.js"></script>
    <script src="js/dore.script.js"></script>
    <script src="js/scripts.js"></script>

</body>

</html>