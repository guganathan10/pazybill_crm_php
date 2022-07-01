 <?php 
 $link=json_decode(file_get_contents("./menu.json"),true);
 $request= basename($_SERVER[REQUEST_URI]);  
 ?>
 <div class="menu">
        <div class="main-menu">
            <div class="scroll">
                <ul class="list-unstyled">
                    <?php foreach ($link as $key => $value){ ?>
                    <li class="<?php if($value['link']== $request){ echo 'active';} ?>">
                        <a href="<?php echo $value['link'] ?>">
                            <i class="<?php echo $value['icon'] ?>"></i>
                            <span><?php echo $value['name'] ?></span>
                        </a>
                    </li>
                    <?php } ?>
                   
                 
                    
                   
                </ul>
            </div>
        </div>

       
    </div>
