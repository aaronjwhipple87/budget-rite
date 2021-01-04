<?php
require 'functions.php';
require 'session.php';
$msg = "";



// get budget totals
$sql = $con->prepare("
SELECT b.budgetID, SUM(t.transactionAmount) as budgetSum 
FROM transactions t
INNER JOIN
    budgets b 
    on t.budgetID = b.budgetID
WHERE b.userId = ?
    AND t.published = 1
GROUP BY b.budgetID
");

$sql->bind_param('i', $_SESSION['id']);
$sql->execute();
$result = $sql->get_result();
$budgetTotal = $result->fetch_all(MYSQLI_ASSOC);
$sql->close();


//query that selects all the transactions for user
$sql = $con->prepare("SELECT budgetID, budgetName, plannedAmount, appliedAmount, DATE_FORMAT(dueDate, '%m-%d-%y') AS dueDate, DATE_FORMAT(created, '%m-%d-%y') AS created, notes
FROM budgets
WHERE userId = ? AND published = 1
ORDER BY
	dueDate DESC");
$sql->bind_param("i", $_SESSION['id']);
$sql->execute();
$result = $sql->get_result();
$budgets = $result->fetch_all(MYSQLI_ASSOC);


?>

<?=template_header('Current Budgets');?>

<?=template_nav();?>

<?=template_menu();?>

<div class="column main">
    <section class="section">
        <div class="container">
            <h1 class="title">Current Budgets</h1>
            <p class="subtitle">Welcome, you can view, edit or delete budgets below.</p>
            <a href="addBudget.php" class="button is-success is-small">
                <span class="icon"><i class="fas fa-plus"></i></span>
                <span>Add Budget</span>
            </a>
            <a href="allbudgets.php" class="button is-primary is-small">
                <span>See All Budgets</span>
            </a>
        </div>
        <!-- desktop chart -->
        <div class="container pt-3 is-hidden-mobile">
            <table class="table is-bordered is-fullwidth">
                <thead>
                <tr>
                    <td>#</td>
                    <td>Budget Name</td>
                    <td>Planned Amount</td>
                    <td>Sum Transaction Amount</td>
                    <td>Due Date</td>
                    <td>Notes</td>
                    <td>Created</td>
                    <td>Action</td>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($budgets as $row): ?>
                    <tr>
                        <td>
                            <?=$row['budgetID']?>
                        </td>
                        <td>
                            <a href="singleBudget.php?id=<?=$row['budgetID']?>"><?=$row['budgetName']?></a>
                        </td>
                        <td>
                            <?=$row['plannedAmount']?>
                        </td>

                        <td>
                        <?php foreach ($budgetTotal as $total): ?>

                            <?=($total['budgetID'] == $row['budgetID'] ? $total['budgetSum'] :  ' ')?>

                        <?php endforeach;?>
                        </td>

                        <td>
                            <?=$row['dueDate']?>
                        </td>
                        <td>
                            <?=$row['notes']?>
                        </td>
                        <td>
                            <?=$row['created']?>
                        </td>
                        <td>
                            <a href="editBudget.php?id=<?=$row['budgetID']?>" class="button is-link is-small" title="Edit Budget">
                                <span class="icon"><i class="fas fa-edit"></i></span>
                            </a>
                            <a href="deleteBudget.php?id=<?=$row['budgetID']?>" class="button is-danger is-small" title="Delete Budget">
                                <span class="icon"><i class="fas fa-trash"></i></span>
                            </a>
                        </td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
        </div>
        <!-- mobile chart -->
        <div class="container pt-3 is-hidden-desktop">
            <table class="table is-bordered">
                <thead>
                <tr>
                    <td>Budget Name</td>
                    <td>Action</td>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($budgets as $row): ?>
                    <tr>
                        <td>
                            <a href="singleBudget.php?id=<?=$row['budgetID']?>"><?=$row['budgetName']?></a>
                        </td>

                        <td>
                            <a href="editBudget.php?id=<?=$row['budgetID']?>" class="button is-link is-small" title="Edit Budget">
                                <span class="icon"><i class="fas fa-edit"></i></span>
                            </a>
                            <a href="deleteBudget.php?id=<?=$row['budgetID']?>" class="button is-danger is-small" title="Delete Budget">
                                <span class="icon"><i class="fas fa-trash"></i></span>
                            </a>
                        </td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </section>
</div>
