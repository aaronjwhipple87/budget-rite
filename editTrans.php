<?php
require 'functions.php';
require 'session.php';
$msg = "";


if(isset($_GET['id'])) {
    //query that selects all the budget names for user
    $sql = $con->prepare("SELECT budgetName, budgetID
        FROM budgets
        WHERE userId = ?");
    $sql->bind_param("i", $_SESSION['id']);
    $sql->execute();
    $result = $sql->get_result();
    $budgets = $result->fetch_all(MYSQLI_ASSOC);



    // Select the record that is going to be updated
    $sql = $con->prepare("SELECT  b.budgetName, t.transactionType, t.transactionName, t.transactionAmount, b.budgetID
    FROM transactions t
    INNER JOIN
        budgets b 
        on t.budgetID = b.budgetID
    WHERE transactionID = ?");

    $sql->bind_param('i', $_GET['id']);
    $sql->execute();
    $sql->bind_result($budgetName, $transactionType, $transactionName, $transactionAmount, $budgetID);
    $sql->fetch();
    $sql->close();
}

if(isset($_POST["edit"])) {

    if(($_POST['transType'] == 'Bills' || $_POST['transType'] == 'Expenses') && $_POST['transAmount'] > 0) {
        $transAmount = $_POST['transAmount'] * -1;
    }else if(($_POST['transType'] == 'Savings' || $_POST['transType'] == 'Income') && $_POST['transAmount'] < 0)
        $transAmount = $_POST['transAmount'] * -1;
    else{
        $transAmount = $_POST['transAmount'];
    }


    // get info from db
    if ($sql = $con->prepare('SELECT * FROM transactions WHERE transactionID = ?')) {
        $sql->bind_param('i', $_GET['id']);
        $sql->execute();
        $sql->store_result();

        if ($stmt = $con->prepare('UPDATE transactions SET transactionType = ?, transactionName = ?, transactionAmount = ?, budgetID = ? WHERE transactionID = ?')) {

            $stmt->bind_param('sssii', $_POST['transType'], $_POST['transName'], $transAmount, $_POST['budgetID'], $_GET['id']);
            $stmt->execute();

            $msg = 'You have successfully updated your Transaction!';
            echo "<script type='text/javascript'>alert('$msg');</script>";
            header("Refresh:.5; url=transactions.php");

        } else {
            $msg = "Could not prepare ";
            echo "<script type='text/javascript'>alert('$msg');</script>";
        }

//        if ($sql = $con->prepare('SELECT appliedAmount FROM budgets WHERE BudgetID = ?')) {
//            $sql->bind_param('i', $_POST['budgetID']);
//            $sql->execute();
//            $sql->store_result();
//
//            if ($sql->num_rows > 0) {
//                $sql = $con->prepare('UPDATE budgets
//                SET appliedAmount = appliedAmount + ?
//                WHERE BudgetID = ?');
//                $sql->bind_param('si', $transAmount,$_POST['budgetID']);
//                $sql->execute();
//
//
//
//            } else {
//                $msg = "Could not prepare statement";
//                echo "<script type='text/javascript'>alert('$msg');</script>";
//            }
//
//        }





        $sql->close();

    }else {
        //something went wrong with sql statement
        $msg = "Could not prepare statement!";
        echo "<script type='text/javascript'>alert('$msg');</script>";
    }
}





?>

<?=template_header('Edit Transaction');?>

<?=template_nav();?>

<?=template_menu();?>

<div class="column main">
    <section class="section">
        <div class="container">
            <h1 class="title">Edit Transaction</h1>

            <form action="editTrans.php?id=<?=$_GET['id']?>" method="post">

                <div class="field">
                    <label class="label">Transaction Type</label>
                    <div class="select" >
                        <select name="transType" required>
                            <option ><?=$transactionType?></option>
                            <div class="select">
                                <option value="Bills">Bills</option>
                                <option value="Income">Income</option>
                                <option value="Expenses">Expenses</option>
                                <option value="Savings">Savings</option>

                            </div>
                        </select>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Budget Name</label>
                    <div class="select" >
                        <select name="budgetID" required>
                                <option value="<?=$budgetID?>"><?=$budgetName?> </option>
                            <div class="select">
                                <?php foreach ($budgets as $row): ?>
                                    <option value="<?=$row['budgetID']?>"><?=$row['budgetName']?> </option>
                                <?php endforeach;?>
                            </div>
                        </select>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Transaction Name</label>
                    <div class="control">
                        <input class="input" type="text" value="<?=$transactionName?>" name="transName" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Amount</label>
                    <div class="control">
                        <input type="number" class="input" step="any" value="<?=$transactionAmount?>" name="transAmount" required>
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