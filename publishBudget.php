<?php
require "functions.php";
require 'session.php';
$msg = '';

if(isset($_POST["publish"])){

    if ($sql = $con->prepare('SELECT * FROM budgets WHERE budgetID = ?')) {
        $sql->bind_param("i", $_GET['id']);
        $sql->execute();
        $sql->store_result();

        //publish budget from current list
        if ($sql->num_rows > 0) {
//            if(['published'] == 0) {
                $sql = $con->prepare('UPDATE budgets SET published = 1 WHERE budgetID = ?');
                $sql->bind_param('i', $_GET['id']);
                $sql->execute();
//            }else{
//                $sql = $con->prepare('UPDATE budgets SET published = 0 WHERE budgetID = ?');
//                $sql->bind_param('i', $_GET['id']);
//                $sql->execute();
//            }

            $msg = 'Budget published successfully!';
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

<?=template_header('Publish Budget');?>

<?=template_nav();?>

<?=template_menu();?>

<div class="column main">
    <section class="section">
        <div class="container">
            <h1 class="title">Publish Budget to Current List</h1>
            <h2 class="subtitle">Are you sure you want to publish budget to current list?</h2>
            <form action="publishBudget.php?id=<?=$_GET['id']?>" method="post">
                <div class="buttons">
                    <button type="submit" name="publish" class="button is-success">Yes</button>
                    <a href="allBudgets.php" class="button is-danger">No</a>
                </div>
            </form>
        </div>
    </section>
</div>

