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

  $sql = "select user_id,(select phone_num from user where user.user_id=bills.user_id) as phone_num,count(*) as purchases,(select if(dob<>'1970-01-01',DATE_FORMAT(FROM_DAYS(DATEDIFF(now(),dob)), '%Y')+0,'no data') from user where bills.status=1 and user.user_id=bills.user_id) as age from bills where store_id='".$_SESSION['store_id']."' and user_id is not null group by user_id";
  $result = $conn->query($sql);

  function mask_mobile_no($number)
{
    if($number!='')
    return substr($number, 0, 2) . 'XXXXXXXX' ;
    else
    return
    'Deleted User';
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
                    <h1>Customer</h1>
                    <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                        <ol class="breadcrumb pt-0">
                            <li class="breadcrumb-item">
                                <a href="#">Home</a>
                            </li>
                           
                            <li class="breadcrumb-item active" aria-current="page">Customer</li>
                        </ol>
                    </nav>
                    <div class="separator mb-5"></div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <table class="data-table data-table-customer">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Customer Mobile</th>
                                        <th>No of Purchase</th>
                                        <th>Age Group</th>
                                        <th>Rating</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $i=1;
                                    if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $sql_rating='select sum(rating)/count(*) as rating from bills where user_id="'.$row['user_id'].'" and store_id="'.$_SESSION['store_id'].'" and rating<>0';
        $result_rating = $conn->query($sql_rating);
        $row_rating = $result_rating -> fetch_assoc();
   ?>
                                    <tr>
                                        <td><?php echo $i++ ?></td>
                                        <td><?php echo mask_mobile_no($row['phone_num']) ?></td>
                                        <td><?php echo $row['purchases'] ?></td>
                                        <?php 
                                        if($row['age']=='no data')
                                        { ?>
                                            <td><?php echo "Unknown" ?></td>
                                        <?php }else
                                        {
                                        if($row['age']<20) 
                                            { 
                                        ?>
                                            <td><?php echo "Below 21" ?></td>
                                        <?php } 
                                        else if($row['age']>20 && $row['age']<=30)
                                        { ?>
                                                <td><?php echo "21-30"; ?></td>
                                                <?php } 
                                        else if($row['age']>30 && $row['age']<=40)
                                        { ?>
                                                <td><?php echo "31-40"; ?></td>
                                                <?php } 
                                        else if($row['age']>40 && $row['age']<=50)
                                        { ?>
                                                <td><?php echo "41-50"; ?></td>
                                                <?php } 
                                        else if($row['age']>50 && $row['age']<=60)
                                        { ?>
                                                <td><?php echo "51-60"; ?></td>
                                                <?php } 
                                        else if($row['age']>60 && $row['age']<=70)
                                        { ?>
                                                <td><?php echo "61-70"; ?></td>
                                                <?php } 
                                        else if($row['age']>70 )
                                        { ?>
                                                <td><?php echo "Above 70"; ?></td>
                                    <?php } }?> 
                                    
                                            <td><?php echo number_format((float)$row_rating['rating'], 1, '.', ''); ?></td>
                                        
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

</body>

</html>