<?php
require 'functions.php';
require 'session.php';
$msg = "";


//query that selects all the transactions for chosen Budget
$sql = $con->prepare("SELECT  t.published, t.transactionID, b.budgetName, t.transactionType, t.transactionName, t.transactionAmount, DATE_FORMAT(t.transactionDate, '%m-%d-%y') AS transactionDate 
FROM transactions t
INNER JOIN
    budgets b 
    on t.budgetID = b.budgetID
WHERE b.userId = ?
AND t.published = 1
AND b.budgetId = ?
ORDER BY 
	transactionDate DESC");
$sql->bind_param("ii", $_SESSION['id'],$_GET['id']);
$sql->execute();
$result = $sql->get_result();
$trans = $result->fetch_all(MYSQLI_ASSOC);
$sql->close();

//budget name, planned amount
$sql = $con->prepare('SELECT budgetName, plannedAmount
FROM budgets
WHERE userId = ?
AND budgetId = ?');
$sql->bind_param("ii", $_SESSION['id'],$_GET['id']);
$sql->execute();
$sql->bind_result($budgetName, $plannedAmt);
$sql->fetch();
$sql->close();

// get budget totals
$sql = $con->prepare("
SELECT SUM(t.transactionAmount) 
FROM transactions t
INNER JOIN
    budgets b 
    on t.budgetID = b.budgetID
WHERE b.userId = ?
    AND b.budgetID = ?
    AND t.published = 1
");

$sql->bind_param('ii', $_SESSION['id'],$_GET['id']);
$sql->execute();
$sql->bind_result($budgetSum);
$sql->fetch();
$sql->close();

$remainingAmt = ($budgetSum < 0 ? ($plannedAmt + $budgetSum): ($plannedAmt - $budgetSum));
?>

<?=template_header('Single Budget');?>

<?=template_nav();?>

<?=template_menu();?>

<div class="column main">
    <section class="section is-flex is-flex-direction-column is-align-content-center">
        <div class="container ">
            <div class="columns">
                <div class="column">
                    <h1 class="title">Budget: <span class="is-italic has-text-success"><?=$budgetName?></span></h1>
                    <p class="subtitle">Welcome, below are all transactions assigned to the budget. </p>
                    <a href="addTrans.php" class="button is-success is-small">
                        <span class="icon"><i class="fas fa-plus"></i></span>
                        <span>Add Transaction</span>
                    </a>
                </div>
                <div class="column">
                    <h1 class="title">Planned Amt: <?=$plannedAmt?></h1>
                    <h1 class="has-text-weight-bold <?= ($remainingAmt < 0  ? 'has-text-danger' : 'has-text-black') ?>">Remaining Amt: <?=$remainingAmt?></h1>
                </div>
            </div>
        </div>
        <!-- desktop chart -->
        <div class="container pt-3 is-hidden-mobile">
            <table class="table is-bordered">
                <thead>
                <tr>
                    <td>Transaction ID</td>
                    <td>Transaction Type</td>
                    <td>Transaction Name</td>
                    <td>Amount</td>
                    <td>Created</td>
                    <td>Action</td>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($trans as $row): ?>
                    <tr>
                        <td>
                            <?=$row['transactionID']?>
                        </td>
                        <td>
                            <?=$row['transactionType']?>
                        </td>
                        <td>
                            <?=$row['transactionName']?>
                        </td>
                        <td class="<?= ($row['transactionType'] == 'Bills' || $row['transactionType'] == 'Expenses') ? 'has-text-danger' : 'has-text-black' ?>">

                            <?=$row['transactionAmount']?>

                        </td>
                        <td>
                            <?=$row['transactionDate']?>
                        </td>
                        <td>
                            <a href="editTrans.php?id=<?=$row['transactionID']?>" class="button is-link is-small" title="Edit Trans">
                                <span class="icon"><i class="fas fa-edit"></i></span>
                            </a>
                            <a href="deleteTrans.php?id=<?=$row['transactionID']?>" class="button is-danger is-small" title="Delete Trans">
                                <span class="icon"><i class="fas fa-trash"></i></span>
                            </a>
                        </td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
            <h1 class="title <?= ($budgetSum < 0  ? 'has-text-danger' : 'has-text-black') ?>">Total: <?=$budgetSum?></h1>
        </div>
        <!-- mobile chart -->
        <div class="container pt-3 is-hidden-desktop">
            <table class="table is-bordered">
                <thead>
                <tr>
                    <td>Transaction Type</td>
                    <td>Transaction Name</td>
                    <td>Amount</td>
                    <td>Action</td>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($trans as $row): ?>
                    <tr>
                        <td>
                            <?=$row['transactionType']?>
                        </td>
                        <td>
                            <?=$row['transactionName']?>
                        </td>
                        <td class="<?= ($row['transactionType'] == 'Bills' || $row['transactionType'] == 'Expenses') ? 'has-text-danger' : 'has-text-black' ?>">

                            <?=$row['transactionAmount']?>

                        </td>
                        <td>
                            <a href="editTrans.php?id=<?=$row['transactionID']?>" class="button is-link is-small" title="Edit Trans">
                                <span class="icon"><i class="fas fa-edit"></i></span>
                            </a>
                            <a href="deleteTrans.php?id=<?=$row['transactionID']?>" class="button is-danger is-small" title="Delete Trans">
                                <span class="icon"><i class="fas fa-trash"></i></span>
                            </a>
                        </td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
            <h1 class="title <?= ($budgetSum < 0  ? 'has-text-danger' : 'has-text-black') ?>">Total: <?=$budgetSum?></h1>
        </div>
    </section>
</div>