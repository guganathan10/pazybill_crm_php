
<?php 
require_once("./config/db.php");
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

    $sql = "select * from support_queries where id='".$_REQUEST['id']."'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $comments=json_decode($row['comments'],true);
    foreach($comments as $comment)
    {
?>
<div class="card bg-grey d-inline-block mb-3 float-left mr-2">
                                <div class="position-absolute pt-1 pr-2 r-0">
                                    <span class="text-extra-small text-muted"><?php echo $comment['date'] ?></span>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex flex-row pb-2">
                                        <div class=" d-flex flex-grow-1 min-width-zero">
                                            <div
                                                class="m-2 pl-0 align-self-center d-flex flex-column flex-lg-row justify-content-between min-width-zero">
                                                <div class="min-width-zero">
                                                    <p class="mb-0 truncate list-item-heading" style="width: 200px; overflow: hidden;"><?php echo $comment['replaied_by'] ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="chat-text-left">
                                        <p class="mb-0 text-semi-muted">
                                            <?php echo $comment['comment'] ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <?php } ?>

                            