<?php
require 'functions.php';
require 'session.php';
$msg = "";

//if(isset($_POST["submit"])){
//
//        if($sql = $con->prepare('INSERT INTO bills ( billType, billName, billAmount, billdueDate, userID ) VALUES (?,?,?,?,?)')) {
//            $sql->bind_param('ssisi', $_POST['billType'],$_POST['billName'], $_POST['billAmount'], $_POST['billdueDate'], $_SESSION['id']);
//            $sql->execute();
//            $msg = 'Bill created successfully!';
//            echo "<script type='text/javascript'>alert('$msg');</script>";
//            header( "Refresh:1; url=http://icarus.cs.weber.edu/~aw54652/web_4350/budget/bills.php");
//
//        } else {
//            $msg = "Could not prepare statement";
//            echo "<script type='text/javascript'>alert('$msg');</script>";
//        }
//
//        $sql->close();
//}



?>

<?=template_header('Create Bill');?>

<?=template_nav();?>


<section class="section">
    <div class="container">
        <h1 class="title">Create Bill</h1>
        <form action="createBill.php" method="post">

            <div class="field">
                <label class="label">Bill Type</label>
                <div class="select" >
                    <select name="billType">
                        <option>Select</option>
                        <div class="select">
                            <option>Monthly</option>
                            <option>Yearly</option>
                            <option>Weekly</option>
                            <option>Bi-weekly</option>
                            <option>One-time</option>
                        </div>
                    </select>
                </div>
            </div>
            <div class="field">
                <label class="label">Bill Name</label>
                <div class="control">
                    <input class="input" type="text" name="billName" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Amount</label>
                <div class="control">
                    <input type="number" class="input" step="any" name="billAmount" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Due Date</label>
                <div class="control">
                    <input type="date" class="input" name="billdueDate" required>
                </div>
            </div>
            <div class="field">
                <div class="control">
                    <button type="submit" name="submit" class="button is-link">Submit</button>
                </div>
            </div>
        </form>
    </div>
</section>



<?=template_footer();?>