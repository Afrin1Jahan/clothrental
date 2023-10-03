<?php require_once('header.php'); ?>

<?php
// Check if the customer is logged in or not
if(!isset($_SESSION['customer'])) {
    header('location: '.BASE_URL.'logout.php');
    exit;
} else {
    // If customer is logged in, but admin make him inactive, then force logout this user.
    $statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_id=? AND cust_status=?");
    $statement->execute(array($_SESSION['customer']['cust_id'],0));
    $total = $statement->rowCount();
    if($total) {
        header('location: '.BASE_URL.'logout.php');
        exit;
    }
}
?>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php require_once('customer-sidebar.php'); ?>
            </div>
            <div class="col-md-12">
                <div class="user-content">
                    <h3><?php echo LANG_VALUE_25; ?></h3>
                    <h4 style='color:red'>[Note: Please call    +8801644367561 for returning the product]</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th><?php echo '#' ?></th>
                                    <th><?php echo LANG_VALUE_48; ?></th>
                                    <th><?php echo LANG_VALUE_27; ?></th>
                                    <!-- <th><?php echo LANG_VALUE_28; ?></th> -->
                                    <th><?php echo LANG_VALUE_29; ?></th>
                                    <th>Due Amount</th>
                                    <th><?php echo LANG_VALUE_30; ?></th>
                                    <th><?php echo LANG_VALUE_31; ?></th>
                                    <th><?php echo LANG_VALUE_32; ?></th>
                                    <th> Return Status</th>
                                </tr>
                            </thead>
                            <tbody>


            <?php
            /* ===================== Pagination Code Starts ================== */
            $adjacents = 5;

            $statement = $pdo->prepare("SELECT * FROM tbl_payment WHERE customer_email=? ORDER BY id DESC");
            $statement->execute(array($_SESSION['customer']['cust_email']));
            $total_pages = $statement->rowCount();

            $targetpage = BASE_URL.'customer-order.php';
            $limit = 10;
            $page = @$_GET['page'];
            if($page) 
                $start = ($page - 1) * $limit;
            else
                $start = 0;
            
            
            $statement = $pdo->prepare("SELECT * FROM tbl_payment WHERE customer_email=? ORDER BY id DESC LIMIT $start, $limit");
            $statement->execute(array($_SESSION['customer']['cust_email']));
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
           
            
            if ($page == 0) $page = 1;
            $prev = $page - 1;
            $next = $page + 1;
            $lastpage = ceil($total_pages/$limit);
            $lpm1 = $lastpage - 1;   
            $pagination = "";
            if($lastpage > 1)
            {   
                $pagination .= "<div class=\"pagination\">";
                if ($page > 1) 
                    $pagination.= "<a href=\"$targetpage?page=$prev\">&#171; previous</a>";
                else
                    $pagination.= "<span class=\"disabled\">&#171; previous</span>";    
                if ($lastpage < 7 + ($adjacents * 2))
                {   
                    for ($counter = 1; $counter <= $lastpage; $counter++)
                    {
                        if ($counter == $page)
                            $pagination.= "<span class=\"current\">$counter</span>";
                        else
                            $pagination.= "<a href=\"$targetpage?page=$counter\">$counter</a>";                 
                    }
                }
                elseif($lastpage > 5 + ($adjacents * 2))
                {
                    if($page < 1 + ($adjacents * 2))        
                    {
                        for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
                        {
                            if ($counter == $page)
                                $pagination.= "<span class=\"current\">$counter</span>";
                            else
                                $pagination.= "<a href=\"$targetpage?page=$counter\">$counter</a>";                 
                        }
                        $pagination.= "...";
                        $pagination.= "<a href=\"$targetpage?page=$lpm1\">$lpm1</a>";
                        $pagination.= "<a href=\"$targetpage?page=$lastpage\">$lastpage</a>";       
                    }
                    elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
                    {
                        $pagination.= "<a href=\"$targetpage?page=1\">1</a>";
                        $pagination.= "<a href=\"$targetpage?page=2\">2</a>";
                        $pagination.= "...";
                        for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
                        {
                            if ($counter == $page)
                                $pagination.= "<span class=\"current\">$counter</span>";
                            else
                                $pagination.= "<a href=\"$targetpage?page=$counter\">$counter</a>";                 
                        }
                        $pagination.= "...";
                        $pagination.= "<a href=\"$targetpage?page=$lpm1\">$lpm1</a>";
                        $pagination.= "<a href=\"$targetpage?page=$lastpage\">$lastpage</a>";       
                    }
                    else
                    {
                        $pagination.= "<a href=\"$targetpage?page=1\">1</a>";
                        $pagination.= "<a href=\"$targetpage?page=2\">2</a>";
                        $pagination.= "...";
                        for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
                        {
                            if ($counter == $page)
                                $pagination.= "<span class=\"current\">$counter</span>";
                            else
                                $pagination.= "<a href=\"$targetpage?page=$counter\">$counter</a>";                 
                        }
                    }
                }
                if ($page < $counter - 1) 
                    $pagination.= "<a href=\"$targetpage?page=$next\">next &#187;</a>";
                else
                    $pagination.= "<span class=\"disabled\">next &#187;</span>";
                $pagination.= "</div>\n";       
            } 
            /* ===================== Pagination Code Ends ================== */
            ?>


                                <?php
                                $tip = $page*10-10;
                                foreach ($result as $row) {
                                    $tip++;
                                    ?>
                                    <tr>
                                        <td><?php echo $tip; ?></td>
                                        <td>
                                            <?php
                                            $statement1 = $pdo->prepare("SELECT * FROM tbl_order WHERE payment_id=?");
                                            $statement1->execute(array($row['payment_id']));
                                            $result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($result1 as $row1) {
                                                echo 'Product Name: '.$row1['product_name'];
                                                echo '<br>Size: '.$row1['size'];
                                                echo '<br>Color: '.$row1['color'];
                                                echo '<br>Quantity: '.$row1['quantity'];
                                                echo '<br>Unit Price: '.$row1['unit_price'];
                                                echo '<br><br>';
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo  $row['payment_date'];?></td>
                                        <!-- <td><?php echo $row['txnid']; ?></td> -->
                                        <td><?php echo $row['paid_amount']; ?></td>
                                        <td><?php 

                                            // Given date in 'Y-m-d H:i:s' format
                                            $givenDateTime = $row['payment_date'];

                                            // Get the current date/time in the same format
                                            $currentDateTime = date('Y-m-d H:i:s');

                                            // Convert the given and current date/times to DateTime objects
                                            $givenDate = new DateTime($givenDateTime);
                                            $currentDate = new DateTime($currentDateTime);

                                            // Calculate the difference in days
                                            $interval = $currentDate->diff($givenDate);
                                            $daysDifference = $interval->format('%a') -7; //// need to minus 7
                                            if($daysDifference<0) $daysDifference = 0;
                                            if($row['return_status']=='On rent'){
                                                
                                                echo "Extra : $daysDifference Days ";
                                                echo "<br>Dues : ". $daysDifference*100 ;
                                            } else {
                                                echo "Dues : ". $daysDifference*100 ;
                                                echo '<span style="color:green"> <b> PAID </b></span>';
                                            }
                                            ?></td>
                                        <td><?php echo $row['payment_status']; ?></td>
                                        <td><?php echo $row['payment_method']; ?></td>
                                        <td><?php echo $row['payment_id']; ?></td>
                                        <td><?php 
                                        if($row['return_status']==''){
                                           
                                            try {
                                                // $pdo = new PDO("your_database_connection_details_here");
                                            
                                                $statement = $pdo->prepare("UPDATE tbl_payment SET return_status='On rent' WHERE customer_email=? AND payment_id = ?");
                                                
                                                $success = $statement->execute(array($_SESSION['customer']['cust_email'], $row['payment_id']));
                                                
                                            } catch (PDOException $e) {
                                                // Handle any database-related errors
                                                echo "Database Error: " . $e->getMessage();
                                            }
                                        } ?>

                                        <?php

                                        if($row['return_status']=='' || $row['return_status']=='On rent'){
                                            echo '<span style="color:red"> <b> '.$row['return_status'].'</b></span>';
                                        }else{
                                            echo '<span style="color:red"> <b>'.$row['return_status'].' </b> </span>';

                                        }

                                        ?></td>
                                    </tr>
                                    <?php
                                } 
                                ?>                               
                                
                            </tbody>
                        </table>
                        <div class="pagination" style="overflow: hidden;">
                        <?php 
                            echo $pagination; 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>