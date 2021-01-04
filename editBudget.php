<?php
require 'functions.php';
require 'session.php';
$msg = "";


if(isset($_GET['id'])) {
    // Select the record that is going to be updated
    $sql = $con->prepare('SELECT budgetName, plannedAmount, appliedAmount, dueDate, notes FROM budgets WHERE budgetID = ?');
    $sql->bind_param('i', $_GET['id']);
    $sql->execute();
    $sql->bind_result($budgetName, $plannedAmount, $appliedAmount, $dueDate, $notes);
    $sql->fetch();
    $sql->close();
}

if(isset($_POST["edit"])) {

    $dueDate = !empty($_POST['dueDate']) ? $_POST['dueDate']: NULL;
    $notes = !empty($_POST['notes']) ? $_POST['notes']: NULL;
    $appliedAmount = !empty($_POST['appliedAmount']) ? $_POST['appliedAmount']: NULL;

    // get info from db
    if ($sql = $con->prepare('SELECT * FROM budgets WHERE budgetID = ?')) {
        $sql->bind_param('i', $_GET['id']);
        $sql->execute();
        $sql->store_result();

        if ($stmt = $con->prepare('UPDATE budgets SET budgetName = ?, plannedAmount = ?, appliedAmount = ?, dueDate = ?, notes = ? WHERE budgetID = ?')) {

            $stmt->bind_param('ssssss', $_POST['budgetName'], $_POST['plannedAmount'], $appliedAmount, $dueDate, $notes, $_GET['id']);
            $stmt->execute();

            $msg = 'You have successfully changed your Budget!';
            echo "<script type='text/javascript'>alert('$msg');</script>";
            header("Refresh:.5; url=budgets.php");
        } else {
            $msg = "Could not prepare ";
            echo "<script type='text/javascript'>alert('$msg');</script>";
        }

        $sql->close();

    }else {
        //something went wrong with sql statement
        $msg = "Could not prepare statement!";
        echo "<script type='text/javascript'>alert('$msg');</script>";
    }
}





?>

<?=template_header('Edit Budget');?>

<?=template_nav();?>

<?=template_menu();?>

<div class="column main">
<section class="section">
    <div class="container">
        <h1 class="title">Edit Budget</h1>

        <form action="editBudget.php?id=<?=$_GET['id']?>" method="post">

            <div class="field">
                <label class="label">Budget Name</label>
                <div class="control">
                    <input class="input" type="text" value="<?=$budgetName?>" name="budgetName" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Planned Amount</label>
                <div class="control">
                    <input type="number" class="input" step="any" value="<?=$plannedAmount?>" name="plannedAmount" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Applied Amount</label>
                <div class="control">
                    <input type="number" class="input" step="any" value="<?=$appliedAmount?>" name="appliedAmount" >
                </div>
            </div>
            <div class="field">
                <label class="label">Due Date</label>
                <div class="control">
                    <input type="date" class="input" value="<?=$dueDate?>" name="dueDate">
                </div>
            </div>
            <div class="field">
                <label class="label">Notes</label>
                <div class="control">
                    <textarea name="notes" id=notes cols="100" value="<?=$notes?>" rows="10"></textarea>
                </div>
            </div>

            <div class="field">
                <div class="control">
                    <button type="submit" name="edit" class="button is-link">Submit</button>
                </div>
            </div>
        </form>
    </div>
</section>
</div>