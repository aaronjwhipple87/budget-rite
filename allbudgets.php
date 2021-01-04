<?php
require 'functions.php';
require 'session.php';
$msg = "";


//query that selects all the budgets for user
$sql = $con->prepare("SELECT budgetID, budgetName, plannedAmount, appliedAmount, DATE_FORMAT(dueDate, '%m-%d-%y') AS dueDate, DATE_FORMAT(created, '%m-%d-%y') AS created, notes, published, 
CASE 
    WHEN published = 1 THEN '&#10004;'
    ELSE ' '  
END AS published
FROM budgets
WHERE userId = ?
ORDER BY 
	created DESC");
$sql->bind_param("i", $_SESSION['id']);
$sql->execute();
$result = $sql->get_result();
$budgets = $result->fetch_all(MYSQLI_ASSOC);


?>

<?=template_header('All Budgets');?>

<?=template_nav();?>

<?=template_menu();?>

<div class="column main">
    <section class="section">
        <div class="container">
            <h1 class="title">All Budgets</h1>
            <p class="subtitle">Welcome, below are all budgets created, including deleted ones. <br>
            You can view, edit, or add old budgets to your current budgets list.</p>
            <a href="budgets.php" class="button is-primary is-small">
                <span>See Current Budgets List</span>
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

                    <td>Due Date</td>
                    <td>Notes</td>
                    <td>Created</td>
                    <td>Current List</td>
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
                            <?=$row['budgetName']?>
                        </td>
                        <td>
                            <?=$row['plannedAmount']?>
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
                        <td >
                            <?=$row['published']?>
                        </td>
                        <td>
                            <a href="editBudget.php?id=<?=$row['budgetID']?>" class="button is-link is-small" title="Edit Budget">
                                <span class="icon"><i class="fas fa-edit"></i></span>
                            </a>
                            <?php if($row['published'] == ' '){ ?>

                            <a href="publishBudget.php?id=<?=$row['budgetID']?>"  class="button is-primary is-small" title="Add Budget To Current">
                                <span  class="icon"><i  class="fas fa-plus"></i></i></span>
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
            <table class="table is-bordered">
                <thead>
                <tr>
                    <td>Budget Name</td>
                    <td>Created</td>
                    <td>Current List</td>
                    <td>Action</td>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($budgets as $row): ?>
                    <tr>
                        <td>
                            <?=$row['budgetName']?>
                        </td>
                        <td>
                            <?=$row['created']?>
                        </td>
                        <td >
                            <?=$row['published']?>
                        </td>
                        <td>
                            <a href="editBudget.php?id=<?=$row['budgetID']?>" class="button is-link is-small" title="Edit Budget">
                                <span class="icon"><i class="fas fa-edit"></i></span>
                            </a>
                            <?php if($row['published'] == ' '){ ?>

                                <a href="publishBudget.php?id=<?=$row['budgetID']?>"  class="button is-primary is-small" title="Add Budget To Current">
                                    <span  class="icon"><i  class="fas fa-plus"></i></i></span>
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