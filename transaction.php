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

  $sql = "select cashfree_transactions.cf_token,(select phone_num from user where user_id=cashfree_transactions.customer_id) as customer_id ,cashfree_transactions.order_status,cashfree_transactions.webhook_response,cashfree_transactions.order_amount,cashfree_transactions.payment_time,bills.bill_no,bills.bill_id from cashfree_transactions left join bills on cashfree_transactions.bill_id=bills.bill_id where bills.store_id='".$_SESSION['store_id']."' order by cashfree_transactions.id desc";
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
                    <h1>Transactions</h1>
                    <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                        <ol class="breadcrumb pt-0">
                            <li class="breadcrumb-item">
                                <a href="#">Home</a>
                            </li>
                           
                            <li class="breadcrumb-item active" aria-current="page">Transactions</li>
                        </ol>
                    </nav>
                    <div class="separator mb-5"></div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <table class="data-table data-table-transaction">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Amount</th>
                                        <th>Payment Time</th>
                                        <th>Bill ID</th>
                                        <th>Customer ID</th>
                                        <th>Payment Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
<?php if ($result->num_rows > 0) {
    // output data of each row
    $i=1;
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
        if($row['order_status']!=3){
        ?>
        
    
    <tr>
        <td><?php echo $i++ ?></td>
        <td><?php echo $amount;?></td>
        <td><?php echo $trans_date ?></td>
        <td><a href="bill_details.php?id=<?php echo $row['bill_id'] ?>&trans_id=<?php echo $row['id'] ?>"><?php echo $row['bill_no'] ?></a></td>
        <td><?php echo $phone_num; ?></td>
        <td><?php echo $order_status; ?></td>
        <td><a class="btn btn-info text-white" onclick='viewtransaction("<?php echo $transaction_id ?>","<?php echo $trans_date ?>","<?php echo $amount ?>","<?php echo $order_status ?>","<?php echo $color ?>")'>View</a></td>
</tr>
    <?php } } } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="showTransaction" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" id="transactionbackground">
        <h5 class="modal-title text-white" id="transactiondheading"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
       <p> Transaction ID : <span id="trans_id"></span></p>
       <p>Amount : <span id="amount"></span></p>
       <p>Payment Time : <span id="payment_time"></span></p>
       <p>Payment Status : <span id="payment_status"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


 <?php include "./includes/footer.php"; ?>
    
    <script src="js/vendor/jquery-3.3.1.min.js"></script>
    <script src="js/vendor/bootstrap.bundle.min.js"></script>
    <script src="js/vendor/perfect-scrollbar.min.js"></script>
    <script src="js/vendor/datatables.min.js"></script>
    <script src="js/dore.script.js"></script>
    <script src="js/scripts.js"></script>
    
    <script>
       function viewtransaction(trans_id,payment_time,amount,payment_status,color)
       {
        $('#showTransaction').modal('show');
        $('#trans_id').html(trans_id);
        $('#payment_time').html(payment_time);
        $('#amount').html(amount);
        $('#payment_status').html(payment_status);
        $('#transactiondheading').html(payment_status);
        $('#transactionbackground').css('background-color',color);
       }

    </script>

</body>

</html>