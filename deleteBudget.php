<?php
require "functions.php";
require 'session.php';
$msg = '';

if(isset($_POST["delete"])){

    if ($sql = $con->prepare('SELECT published FROM budgets WHERE budgetID = ?')) {
        $sql->bind_param("i", $_GET['id']);
        $sql->execute();
        $sql->store_result();

        //un-publish budget from current list
        if ($sql->num_rows > 0) {
            if(['published'] == 0) {
                $sql = $con->prepare('UPDATE budgets SET published = 1 WHERE budgetID = ?');
                $sql->bind_param('i', $_GET['id']);
                $sql->execute();
            }else{
                $sql = $con->prepare('UPDATE budgets SET published = 0 WHERE budgetID = ?');
                $sql->bind_param('i', $_GET['id']);
                $sql->execute();
            }

            $msg = 'Budget Removed successfully!';
            echo "<script type='text/javascript'>alert('$msg');</script>";
            header( "Refresh:1; url=budgets.php");
        }else {
            $msg = "Could not prepare statement";
            echo "<script type='text/javascript'>alert('$msg');</script>";
        }
    }else {

        $msg= 'No record with that ID!';
        echo "<script type='text/javascript'>alert('$msg');</script>";
        header("Refresh:1; url=budgets.php");


    }
    $sql->close();
}

?>

<?=template_header('Delete Budget');?>

<?=template_nav();?>

<?=template_menu();?>

<div class="column main">
    <section class="section">
        <div class="container">
            <h1 class="title">Remove Budget</h1>
            <h2 class="subtitle">Are you sure you want to remove budget from current list?</h2>
            <form action="deleteBudget.php?id=<?=$_GET['id']?>" method="post">
                <div class="buttons">
                    <button type="submit" name="delete" class="button is-success">Yes</button>
                    <a href="budgets.php" class="button is-danger">No</a>
                </div>
            </form>
        </div>
    </section>
</div>