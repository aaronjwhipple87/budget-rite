<?php
require 'functions.php';
require 'session.php';
$msg = "";

//query that selects all the budget names for user
$sql = $con->prepare("SELECT budgetName, budgetID
FROM budgets
WHERE userId = ?
AND published = 1");
$sql->bind_param("i", $_SESSION['id']);
$sql->execute();
$result = $sql->get_result();
$budgets = $result->fetch_all(MYSQLI_ASSOC);

if(isset($_POST["submit"])){

    if(($_POST['transType'] == 'Bills' || $_POST['transType'] == 'Expenses') && $_POST['transAmount'] > 0) {
        $transAmount = $_POST['transAmount'] * -1;
    }else if(($_POST['transType'] == 'Savings' || $_POST['transType'] == 'Income') && $_POST['transAmount'] < 0)
        $transAmount = $_POST['transAmount'] * -1;
    else{
        $transAmount = $_POST['transAmount'];
    }

    if($sql = $con->prepare("INSERT INTO transactions ( transactionType, transactionName, transactionAmount, transactionDate, budgetID ) VALUES (?,?,?, NOW(),?)")) {
        $sql->bind_param('sssi', $_POST['transType'],$_POST['transName'], $transAmount, $_POST['budgetID']);
        $sql->execute();

        $msg = 'Transaction created successfully!';
        echo "<script type='text/javascript'>alert('$msg');</script>";
        header( "Refresh:1; url=transactions.php");

    } else {
        $msg = "Could not prepare statement";
        echo "<script type='text/javascript'>alert('$msg');</script>";
    }

//        if ($sql = $con->prepare('SELECT * FROM budgets WHERE BudgetID = ?')) {
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
}


?>

<?=template_header('Add Transaction');?>

<?=template_nav();?>

<?=template_menu();?>

<div class="column main">
    <section class="section">
        <div class="container">
            <h1 class="title">Create Transaction</h1>

            <form action="addTrans.php" method="post">

                <div class="field">
                    <label class="label">Transaction Type</label>
                    <div class="select" >
                        <select name="transType" required>
                            <div class="select">
                                <option value="">Select</option>
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
                            <div class="select">
                                <option value="">Select</option>
                                <?php foreach ($budgets as $row): ?>
                                    <option value="<?=$row['budgetID']?>"><?=$row['budgetName']?> </option>
                                <?php endforeach;?>
                            </div>
                        </select>
                    </div>
                    <p class="has-text-danger">Please note, you have to have associate with a budget created. If you have not created a budget yet, please <a href="addBudget.php"> create one</a>.</p>
                </div>
                <div class="field">
                    <label class="label">Transaction Name</label>
                    <div class="control">
                        <input class="input" type="text" name="transName" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Amount</label>
                    <div class="control">
                        <input type="number" class="input" step="any" name="transAmount" required>
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