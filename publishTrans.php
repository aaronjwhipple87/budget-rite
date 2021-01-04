<?php
require "functions.php";
require 'session.php';
$msg = '';

if(isset($_POST["publish"])){

    if ($sql = $con->prepare('SELECT * FROM transactions WHERE transactionID = ?')) {
        $sql->bind_param("i", $_GET['id']);
        $sql->execute();
        $sql->store_result();

        //publish budget from current list
        if ($sql->num_rows > 0) {
            $sql = $con->prepare('UPDATE transactions SET published = 1 WHERE transactionID = ?');
            $sql->bind_param('i', $_GET['id']);
            $sql->execute();

            $msg = 'Transaction published successfully!';
            echo "<script type='text/javascript'>alert('$msg');</script>";
            header( "Refresh:1; url=transactions.php");
        }else {
            $msg = "Could not prepare statement";
            echo "<script type='text/javascript'>alert('$msg');</script>";
        }
    }else {

        $msg= 'No record with that ID!';
        echo "<script type='text/javascript'>alert('$msg');</script>";
        header("Refresh:1; url=transactions.php");


    }
    $sql->close();
}

?>

<?=template_header('Publish Transaction');?>

<?=template_nav();?>

<?=template_menu();?>

    <!---document main content goes here -->
<div class="column  main">
    <section class="section">
        <div class="container">
            <h1 class="title">Publish Transaction to Current List</h1>
            <h2 class="subtitle">Are you sure you want to publish transaction to current list?</h2>
            <form action="publishTrans.php?id=<?=$_GET['id']?>" method="post">
                <div class="buttons">
                    <button type="submit" name="publish" class="button is-success">Yes</button>
                    <a href="allTransactions.php" class="button is-danger">No</a>
                </div>
            </form>
        </div>
    </section>
</div>