<?php
require 'functions.php';
require 'session.php';
$msg = "";


if(isset($_POST["submit"])){
    // Post data not empty insert a new record
    $dueDate = !empty($_POST['dueDate']) ? $_POST['dueDate']: NULL;
    $notes = !empty($_POST['notes']) ? $_POST['notes']: NULL;


    if($sql = $con->prepare('SELECT * FROM budgets WHERE budgetName = ? AND userID = ?')) {
        $sql->bind_param('ss', $_POST['budgetName'], $_SESSION['id']);
        $sql->execute();
        $sql->store_result();


        if($sql->num_rows > 0) {
            //email exists
            $msg = "Budget Name already exists";
            echo "<script type='text/javascript'>alert('$msg');</script>";
        }else {

            if ($sql = $con->prepare('INSERT INTO budgets ( budgetName, plannedAmount, appliedAmount, dueDate, notes, userID ) VALUES (?,?, 0, ?, ?, ?)')) {
                $sql->bind_param('sssss', $_POST['budgetName'], $_POST['plannedAmount'], $dueDate, $notes, $_SESSION['id']);
                $sql->execute();
                $msg = 'Budget created successfully!';
                echo "<script type='text/javascript'>alert('$msg');</script>";
                header("Refresh:1; url=budgets.php");

            } else {
                $msg = "Could not prepare statement";
                echo "<script type='text/javascript'>alert('$msg');</script>";
            }


        }
        $sql->close();
    } else {
        //something went wrong with sql statement
        $msg = "Could not prepare statement!";
        echo "<script type='text/javascript'>alert('$msg');</script>";
    }
}


?>

<?=template_header('Add Budget');?>

<?=template_nav();?>

<?=template_menu();?>

<div class="column main">
    <section class="section">
        <div class="container">
            <h1 class="title">Create Budget</h1>

            <form action="addBudget.php" method="post">

                <div class="field">
                    <label class="label">Budget Name</label>
                    <div class="control">
                        <input class="input" type="text" name="budgetName" required>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Planned Amount</label>
                    <div class="control">
                        <input type="number" class="input" step="any" name="plannedAmount" required>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Due Date</label>
                    <div class="control">
                        <input type="date" class="input" name="dueDate">
                    </div>
                </div>
                <div class="field">
                    <label class="label">Notes</label>
                    <div class="control">
                        <textarea name="notes" id=notes cols="40" rows="10"></textarea>
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
</div>