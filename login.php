<?php 
session_start();
if (isset($_SESSION['store_id']) && $_SESSION['store_id'] != '')
{
    header('Location: index.php');
}
require_once("./config/db.php");
require_once("./config/data.php");
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>PazyBill | Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    
    <link rel="stylesheet" href="font/iconsmind-s/css/iconsminds.css" />
    <link rel="stylesheet" href="font/simple-line-icons/css/simple-line-icons.css" />
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" href="css/vendor/bootstrap.min.css" />
    <link rel="stylesheet" href="css/vendor/bootstrap.rtl.only.min.css" />
    <link rel="stylesheet" href="css/vendor/bootstrap-float-label.min.css" />
    <link rel="stylesheet" href="css/main.css" />
</head>

<body class="background show-spinner no-footer">
    <div class="fixed-background"></div>
    <main>
        <div class="container">
            <div class="row h-100">
                <div class="col-12 col-md-10 mx-auto my-auto">
                    <div class="card auth-card">
                        <div class="position-relative image-side ">

                        </div>
                        <div class="form-side">
                            <a href="#">
                                <span class="logo-single"></span>
                            </a>
                            <?php if(isset($_POST['username']))
                            {
                            $username= $_POST['username'];
                            $password=$_POST['password'];
                            $sql="select * from store_login where username='".$username."'";
                            $result = $conn->query($sql);
                            if($result->num_rows===1)
                            {
                                $row = $result -> fetch_assoc();
                                $hashed_password=$row['password'];
                                if($row["username"]==$username && hash_equals($hashed_password, crypt($_POST['password'], $hashed_password))){
                                    $_SESSION['store_id']=$row['store_id'];
                                    header('Location: index.php');
                                }
                               
                            }
                            } ?>
                            <h6 class="mb-4">Login</h6>
                            <form action="login.php" method="post">
                                <label class="form-group has-float-label mb-4">
                                    <input class="form-control" name="username"/>
                                    <span>Username</span>
                                </label>

                                <label class="form-group has-float-label mb-4">
                                    <input class="form-control" type="password" name="password" placeholder="" />
                                    <span>Password</span>
                                </label>
                                <div class="d-flex justify-content-between align-items-center">
                                    <button class="btn btn-primary btn-lg btn-shadow" type="submit">LOGIN</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="js/vendor/jquery-3.3.1.min.js"></script>
    <script src="js/vendor/bootstrap.bundle.min.js"></script>
    <script src="js/dore.script.js"></script>
    <script src="js/scripts.js"></script>
</body>

</html>