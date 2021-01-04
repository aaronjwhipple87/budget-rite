<?php
require 'functions.php';
require 'session.php';
$msg = "";

//query that selects all the transactions for user
$sql = $con->prepare("SELECT  t.transactionID, b.budgetName, t.transactionType, t.transactionName, t.transactionAmount, DATE_FORMAT(t.transactionDate, '%m-%d-%y') AS transactionDate, t.published, 
CASE 
    WHEN t.published = 1 THEN '&#10004;'
    ELSE ' '  
END AS published 
FROM transactions t
INNER JOIN
    budgets b 
    on t.budgetID = b.budgetID
WHERE b.userId = ?
AND t.transactionType = 'Bills'
ORDER BY 
	transactionDate DESC");
$sql->bind_param("i", $_SESSION['id']);
$sql->execute();
$result = $sql->get_result();
$trans = $result->fetch_all(MYSQLI_ASSOC);


?>

<?=template_header('All Bills');?>

<?=template_nav();?>

<?=template_menu();?>

<div class="column main">
    <section class="section">
        <div class="container">
            <h1 class="title">All Bills</h1>
            <p class="subtitle">Welcome, below are all bills created, including deleted ones. <br> <br>
                You can view, edit, or add old bills to your current bills list.</p>
            <a href="bills.php" class="button is-primary is-small">
                <span>See Current Bills List</span>
            </a>
        </div>
        <!-- desktop chart -->
        <div class="container pt-3 is-hidden-mobile">
            <table class="table is-bordered is-fullwidth">
                <thead>
                <tr>
                    <td>#</td>
                    <td>Budget Name</td>
                    <td>Transaction Name</td>
                    <td>Amount</td>
                    <td>Created</td>
                    <td>Current List</td>
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
                            <?=$row['budgetName']?>
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
                        <td >
                            <?=$row['published']?>
                        </td>
                        <td>
                            <a href="editTrans.php?id=<?=$row['transactionID']?>" class="button is-link is-small" title="Edit Trans">
                                <span class="icon"><i class="fas fa-edit"></i></span>
                            </a>
                            <?php if($row['published'] == ' '){ ?>

                                <a href="publishTrans.php?id=<?=$row['transactionID']?>" class="button is-primary is-small" title="Publish Trans">
                                    <span class="icon"><i  class="fas fa-plus"></i></span>
                                </a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
        </div>
        <!-- mobile chart -->
        <div class="container pt-3 is-hidden-desktop">
            <table class="table is-bordered is-fullwidth">
                <thead>
                <tr>
                    <td>Budget Name</td>
                    <td>Transaction Name</td>
                    <td>Amount</td>
                    <td>Created</td>
                    <td>Current List</td>
                    <td>Action</td>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($trans as $row): ?>
                    <tr>
                        <td>
                            <?=$row['budgetName']?>
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
                        <td >
                            <?=$row['published']?>
                        </td>
                        <td>
                            <a href="editTrans.php?id=<?=$row['transactionID']?>" class="button is-link is-small" title="Edit Trans">
                                <span class="icon"><i class="fas fa-edit"></i></span>
                            </a>
                            <?php if($row['published'] == ' '){ ?>

                                <a href="publishTrans.php?id=<?=$row['transactionID']?>" class="button is-primary is-small" title="Publish Trans">
                                    <span class="icon"><i  class="fas fa-plus"></i></span>
                                </a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </section>
</div>