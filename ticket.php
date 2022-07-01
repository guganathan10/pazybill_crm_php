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

  $sql = "select * from support_queries where store_id='".$_SESSION['store_id']."'";
  $result = $conn->query($sql);
  
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Pazybill | Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" href="font/iconsmind-s/css/iconsminds.css" />
    <link rel="stylesheet" href="font/simple-line-icons/css/simple-line-icons.css" />

    <link rel="stylesheet" href="css/vendor/bootstrap.min.css" />
    <link rel="stylesheet" href="css/vendor/bootstrap.rtl.only.min.css" />
    <link rel="stylesheet" href="css/vendor/perfect-scrollbar.css" />
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" href="css/vendor/select2.min.css" />
    <link rel="stylesheet" href="css/vendor/select2-bootstrap.min.css" />
    <link rel="stylesheet" href="css/vendor/jquery.contextMenu.min.css" />
    <link rel="stylesheet" href="css/vendor/component-custom-switch.min.css" />
    <link rel="stylesheet" href="css/main.css" />
    <style>
        .bottom-fixed 
        {
            position: absolute;
            bottom: 0;
            margin-left:10px;
        }
        .bottom-fixed > input
        {
            margin:10px;
        }

    </style>
</head>

<body id="app-container" class="menu-sub-hidden show-spinner right-menu">
  
<?php include "./includes/header.php" ?>
   <?php include "./includes/sidebar.php" ?>
    <main>
        <div class="container-fluid">
            <div class="row app-row">
                <div class="col-12">
                    <div class="mb-2">
                        <h1>Ticket</h1>
                        <div class="top-right-button-container">
                            <button type="button" class="btn btn-primary btn-lg top-right-button mr-1"
                                data-toggle="modal" data-backdrop="static" data-target="#exampleModalRight">ADD
                                NEW</button>

                            <div class="modal fade modal-right" id="exampleModalRight" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalRight" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Add New</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form action="save_ticket.php" method="post">
                                        <div class="modal-body">
                                          
                                                <div class="form-group">
                                                    <label>Issue in</label>
                                                    <select class="form-control" name="issue_in">
                                                        <option label="&nbsp;">&nbsp;</option>
                                                        <option value="Device">Device</option>
                                                        <option value="CRM">CRM</option>
                                                    </select>
                                                </div>
                                               

                                                <div class="form-group">
                                                    <label>Issue</label>
                                                    <textarea placeholder="" name="issue" class="form-control" rows="2"></textarea>
                                                </div>

                                                
                                            
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-primary"
                                                data-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                          
                        </div>

                      
                    </div>

                    <div class="mb-2">
                       
                        <div class="collapse d-md-block" id="displayOptions">
                            <div class="d-block d-md-inline-block">
                                
                               
                            </div>
                        </div>
                    </div>
                    <div class="separator mb-5"></div>

                    <div class="list disable-text-selection" data-check-all="checkAll">
                    <?php
                                    $i=1;
                                    if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) 
    {
        
   ?>
                        <div class="card d-flex flex-row mb-3" onclick="loadticketdetails(<?php echo $row['id'] ?>)">
                            <div class="d-flex flex-grow-1 min-width-zero">
                                <div
                                    class="card-body align-self-center d-flex flex-column flex-md-row justify-content-between min-width-zero align-items-md-center">
                                    <a class="list-item-heading mb-0 truncate w-20 w-xs-100 mt-0">
                                        <span class="align-middle d-inline-block"><?php echo $row['ticket_service'] ?></span>
                                    </a>
                                    <p class="mb-0 text-muted text-small w-40 w-xs-100"><?php echo $row['issue'] ?></p>
                                    <p class="mb-0 text-muted text-small w-20 w-xs-100"><?php echo date('M j Y g:i A', strtotime($row['created_at'])) ?></p>
                                    <div class="w-15 w-xs-100">
                                        <span class="badge badge-pill badge-secondary"><?php echo $row['status'] ?></span>
                                    </div>
                                </div>
                                <label class="custom-control custom-checkbox mb-1 align-self-center mr-4">
                                    <input type="checkbox" class="custom-control-input" >
                                    <span class="custom-control-label">&nbsp;</span>
                                </label>
                            </div>
                        </div>

                        <?php 
                        } 
                        }
                        else 
                        { 
                            echo "Not Ticket Found";
                        } ?>

                      
                    </div>
                </div>
            </div>
        </div>


        <div class="app-menu">
            <div class="scroll">
                        <div class="scroll-content" id="main">
                            
                            
        </div>
        
                        </div>
                            <div class="bottom-fixed d-flex justify-content-between align-items-center">
            <input class="form-control flex-grow-1" id="comment" type="text" placeholder="Say something...">
            <input type="hidden" id="current_ticket"/>
            <div>
             
                <button type="button" class="btn btn-primary icon-button large" id="sent_message">
                    <i class="simple-icon-arrow-right"></i>
                </button>

            </div>
                    </div>
            <a class="app-menu-button d-inline-block d-xl-none" href="#">
                <i class="simple-icon-options"></i>
            </a>
        </div>
    </main>

    <?php include "./includes/footer.php"; ?>

    <script src="js/vendor/jquery-3.3.1.min.js"></script>
    <script src="js/vendor/bootstrap.bundle.min.js"></script>
    <script src="js/vendor/perfect-scrollbar.min.js"></script>
    <script src="js/vendor/select2.full.js"></script>
    <script src="js/vendor/mousetrap.min.js"></script>
    <script src="js/vendor/jquery.contextMenu.min.js"></script>
    <script src="js/dore.script.js"></script>
    <script src="js/scripts.js"></script>
    <script>
        
        function loadticketdetails(id)
        {
            $.ajax({
                type: "POST",
                dataType: "html",
                url: "comment.php",
                data: "id=" + id,
                complete: function(data) {
                    $('#main').html(data.responseText);
                    $('#current_ticket').val(id);
                }
            });
        }

        $('#sent_message').on('click',function(){
            let id=$('#current_ticket').val();
            let comment=$('#comment').val();
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "add_comment.php",
                data: "id=" + id+"&comment="+comment,
                complete: function(data) {
                    loadticketdetails(id);
                }
            });
        })
    </script>
</body>

</html>