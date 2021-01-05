<?php
require 'functions.php';
require 'session.php';
$msg = "";
$curMonth = idate("m");
$curYear = idate("Y");

$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $curMonth, $curYear);
//finds all budget names, amounts, and arithmetic to calculate remaining total.
$sql = $con->prepare("SELECT budgetID, budgetName, plannedAmount, appliedAmount, (plannedAmount - appliedAmount) as resultAmount
FROM budgets
WHERE userId = ? and published = 1");
$sql->bind_param("i", $_SESSION['id']);
$sql->execute();
$result = $sql->get_result();
$budgets = $result->fetch_all(MYSQLI_ASSOC);

//pulls all applied transactions
$sql = $con->prepare("SELECT b.budgetName, t.transactionType,  t.transactionAmount
FROM transactions t
INNER JOIN
    budgets b
    ON t.budgetID = b.budgetID
WHERE b.userId = ?
AND t.published = 1
");

$sql->bind_param("i", $_SESSION['id']);
$sql->execute();
$results = $sql->get_result();
$appliedAmounts = $results->fetch_all(MYSQLI_ASSOC);

//calculates remaining amount in each category
$sql = $con->prepare("SELECT b.budgetName, t.transactionType, (b.plannedAmount - ABS(t.transactionAmount)) AS resultAmount
FROM budgets b
INNER JOIN 
    transactions t
    on b.budgetID = t.budgetID
    WHERE b.userId = ?
    and t.published = 1");

$sql->bind_param("i", $_SESSION["id"]);
$sql->execute();
$results = $sql->get_result();
$resultAmounts = $results->fetch_all(MYSQLI_ASSOC);
$sql->close();


//calculates sum of all budgets
$sql2 = $con->prepare("
SELECT SUM(plannedAmount) as plannedSum
FROM budgets 
WHERE userId = ? and published = 1");

$sql2->bind_param("i", $_SESSION['id']);
$sql2->execute();
$result2 = $sql2->get_result();
$budgets2 = $result2->fetch_all(MYSQLI_ASSOC);

//calculates sum of remaining amounts

$sql = $con->prepare("SELECT SUM(b.plannedAmount - ABS(t.transactionAmount)) as resultSum, SUM(b.plannedAmount) as plannedSum
FROM budgets b 
INNER JOIN
    transactions t
    on b.budgetID = t.budgetID
WHERE b.userId = ?
    AND t.published = 1");

$sql->bind_param('i', $_SESSION['id']);
$sql->execute();
$result = $sql->get_result();
$resultTotal = $result->fetch_all(MYSQLI_ASSOC);
$sql->close();


// get budget totals
$sql = $con->prepare("
SELECT SUM(ABS(t.transactionAmount)) as appliedSum, SUM(b.plannedAmount) as plannedSum
FROM transactions t
INNER JOIN
    budgets b 
    on t.budgetID = b.budgetID
WHERE b.userId = ?
    AND t.published = 1
");



$sql->bind_param('i', $_SESSION['id']);
$sql->execute();
$result = $sql->get_result();
$appliedTotal = $result->fetch_all(MYSQLI_ASSOC);
$sql->close();

?>

<?=template_header('Reports');?>

<?=template_nav();?>

<?=template_menu();?>

<!-- document main content goes here -->
<div class="column  main">
    <section class="section">
        <div class="container">
            <h1 class="title">Reports</h1>
            <?php foreach($resultTotal as $row):?>
            <h2 class="subtitle">Daily Spending Limit: <?=round($row['resultSum'] / $daysInMonth, 2);?></h2>
            <?php endforeach;?>
            <div class="columns">
                <div class="column">
                    <h3 class="reportCategory">Planned</h3>
                    <div class="single-chart">
                        <?php foreach($budgets2 as $row):
                if ($row['plannedSum'] == 0) {
                    $percentage1 = 0;
                } else {
                    $percentage1 = $row['plannedSum'] / $row['plannedSum'] * 100;
                }
            endforeach;?>
                        <svg viewBox="0 0 36 36" class="circular-chart brown">
                            <path class="circle-bg" d="M18 2.0845
          a 15.9155 15.9155 0 0 1 0 31.831
          a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="circle" stroke-dasharray="<?=$percentage1;?>, 100" d="M18 2.0845
          a 15.9155 15.9155 0 0 1 0 31.831
          a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <?php foreach($budgets2 as $row):?>
                            <text x="18" y="20.35" class="percentage"><?=$row['plannedSum']?></text>
                            <?php endforeach;?>
                        </svg>
                    </div>
                    <table class="reportTable">

                        <?php foreach ($budgets as $row): ?>
                        <tr>
                            <td><?=$row['budgetName'];?></td>

                            <td><?=$row['plannedAmount'];?></td>
                        </tr>
                        <?php endforeach;?>

                        <?php foreach ($budgets2 as $row):?>
                        <tr>
                            <td>Total</td>
                            <td><?=$row["plannedSum"];?></td>
                        </tr>
                        <?php endforeach; ?>

                    </table>
                </div>

                <div class="column">
                    <h3 class="reportCategory">Spent</h3>
                    <div class="single-chart">
                        <!--
                    <?php foreach($appliedTotal as $row):
                if ($row['plannedSum'] == 0) {
                    $percentage2 = 0;
                } else {
                    $percentage2 = abs($row['appliedSum']) / $row['plannedSum'] * 100;
                }
            endforeach;
            
                ?>
                -->
                        <svg viewBox="0 0 36 36" class="circular-chart green">
                            <path class="circle-bg" d="M18 2.0845
          a 15.9155 15.9155 0 0 1 0 31.831
          a 15.9155 15.9155 0 0 1 0 -31.831" />

                            <path class="circle" stroke-dasharray="<?=$percentage2;?>, 100" d="M18 2.0845
          a 15.9155 15.9155 0 0 1 0 31.831
          a 15.9155 15.9155 0 0 1 0 -31.831" />

                            <?php foreach ($appliedTotal as $row):?>
                            <text x="18" y="20.35" class="percentage"><?=$row['appliedSum'];?></text>
                            <?php endforeach;?>
                        </svg>
                    </div>
                    <table class="reportTable">

                        <?php foreach ($appliedAmounts as $row):?>
                        <tr>
                            <td><?=$row['budgetName'];?></td>

                            <td><?=$row['transactionAmount'];?></td>
                        </tr>
                        <?php endforeach;?>


                        <?php foreach ($appliedTotal as $row):?>
                        <tr>
                            <td>Total</td>

                            <td><?=$row['appliedSum']?></td>

                        </tr>
                        <?php endforeach;?>
                    </table>
                </div>

                <div class="column">
                    <h3 class="reportCategory">Remaining</h3>
                    <div class="single-chart">
                        <?php foreach($resultTotal as $row):
                if ($row['plannedSum'] == 0) {
                    $percentage3 = 0;
                } else {
                    $percentage3 = $row['resultSum'] / $row['plannedSum'] * 100;
                }
            endforeach;
                ?>
                        <svg viewBox="0 0 36 36" class="circular-chart green">
                            <path class="circle-bg" d="M18 2.0845
          a 15.9155 15.9155 0 0 1 0 31.831
          a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <?php foreach($resultTotal as $row):?>
                            <path class="circle" stroke-dasharray="<?=$percentage3;?>, 100" d="M18 2.0845
          a 15.9155 15.9155 0 0 1 0 31.831
          a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <?php endforeach; ?>
                            <?php foreach($resultTotal as $row):?>
                            <text x="18" y="20.35" class="percentage"><?=$row['resultSum']?></text>
                            <?php endforeach;?>
                        </svg>
                    </div>
                    <table class="reportTable">
                        <?php foreach($resultAmounts as $row):?>
                        <tr>
                            <td><?=$row['budgetName'];?></td>

                            <td><?=$row['resultAmount'];?></td>
                        </tr>
                        <?php endforeach;?>
                        <?php foreach ($resultTotal as $row):?>
                        <tr>
                            <td>Total</td>
                            <td><?=$row["resultSum"];?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>