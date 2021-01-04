<?php
require "functions.php";
require 'session.php';
$msg = '';

if(isset($_POST["delete"])){
    if ($sql = $con->prepare('SELECT published FROM transactions WHERE transactionID = ?')) {
        $sql->bind_param('i', $_GET['id']);
        $sql->execute();
        $sql->store_result();

        if ($sql->num_rows > 0) {
            $sql = $con->prepare('UPDATE transactions SET published = 0 WHERE transactionID = ?');
            $sql->bind_param('i', $_GET['id']);
            $sql->execute();

            $msg = 'Transaction deleted successfully!';
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

<?=template_header('Delete Transaction');?>

<?=template_nav();?>

<?=template_menu();?>

<div class="column main">
    <section class="section">
        <div class="container">
            <h1 class="title">Delete Transaction</h1>
            <h2 class="subtitle">Are you sure you want to delete transaction?</h2>
            <form action="deleteTrans.php?id=<?=$_GET['id']?>" method="post">
                <div class="buttons">
                    <button type="submit" name="delete" class="button is-success">Yes</button>
                    <a href="transactions.php" class="button is-danger">No</a>
                </div>
            </form>
        </div>
    </section>
</div>