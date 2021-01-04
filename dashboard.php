<?php
require 'functions.php';
require 'session.php';
$msg = "";

// get user info from db
$sql = $con->prepare('SELECT firstName, lastName FROM users WHERE userId = ?');

$sql->bind_param('i', $_SESSION['id']);
$sql->execute();
$sql->bind_result($firstName, $lastName);
$sql->fetch();
$sql->close();


// get top 10 transactions from db
$sql = $con->prepare("
SELECT 
	t.transactionName, t.transactionType, b.budgetName, t.transactionAmount, DATE_FORMAT(t.transactionDate, '%m-%d-%y') AS transactionDate
FROM transactions t
INNER JOIN
    budgets b 
    on t.budgetID = b.budgetID
WHERE b.userId = ?
    AND t.published = 1
ORDER BY 
	transactionDate DESC
LIMIT 10
");

$sql->bind_param('i', $_SESSION['id']);
$sql->execute();
$result = $sql->get_result();
$transactions = $result->fetch_all(MYSQLI_ASSOC);
$sql->close();

// get transaction type totals
$sql = $con->prepare("
SELECT t.transactionType, SUM(t.transactionAmount) AS transactionTypeSum
FROM transactions t
INNER JOIN
    budgets b 
    on t.budgetID = b.budgetID
WHERE b.userId = ?
    AND t.published = 1
GROUP BY
	t.transactionType
");

$sql->bind_param('i', $_SESSION['id']);
$sql->execute();
$result = $sql->get_result();
$transactionTypeTotals = $result->fetch_all(MYSQLI_ASSOC);
$sql->close();

// get budget totals
$sql = $con->prepare("
SELECT b.budgetName, SUM(t.transactionAmount) as budgetSum
FROM transactions t
INNER JOIN
    budgets b 
    on t.budgetID = b.budgetID
WHERE b.userId = ?
    AND t.published = 1
GROUP BY
	b.budgetName
");

$sql->bind_param('i', $_SESSION['id']);
$sql->execute();
$result = $sql->get_result();
$budgetTotals = $result->fetch_all(MYSQLI_ASSOC);
$sql->close();

// google chart income amount
$sql = $con->prepare("
SELECT 
	IFNULL(SUM(t.transactionAmount), 0) as incomeAmount
FROM 
	transactions as t
LEFT JOIN
	budgets as b on t.budgetID = b.budgetID
WHERE
	b.userID = ?
    AND t.published = 1
    AND MONTH(t.transactionDate) = MONTH(CURRENT_DATE())
    AND t.transactionType = 'Income'
");

$sql->bind_param('i', $_SESSION['id']);
$sql->execute();
$sql->bind_result($incomeAmount);
$sql->fetch();
$sql->close();

// google chart savings amount
$sql = $con->prepare("
SELECT 
    IFNULL(SUM(t.transactionAmount), 0) as savingsAmount
FROM 
	transactions as t
LEFT JOIN
	budgets as b on t.budgetID = b.budgetID
WHERE
	b.userID = ?
    AND t.published = 1
    AND MONTH(t.transactionDate) = MONTH(CURRENT_DATE())
    AND t.transactionType = 'Savings'
");

$sql->bind_param('i', $_SESSION['id']);
$sql->execute();
$sql->bind_result($savingsAmount);
$sql->fetch();
$sql->close();

// google chart bills amount
$sql = $con->prepare("
SELECT 
    IFNULL(SUM(t.transactionAmount), 0) as billsAmount
FROM 
	transactions as t
LEFT JOIN
	budgets as b on t.budgetID = b.budgetID
WHERE
	b.userID = ?
    AND t.published = 1
    AND MONTH(t.transactionDate) = MONTH(CURRENT_DATE())
    AND t.transactionType = 'Bills'
");

$sql->bind_param('i', $_SESSION['id']);
$sql->execute();
$sql->bind_result($billsAmount);
$sql->fetch();
$sql->close();

// google chart expenses amount
$sql = $con->prepare("
SELECT 
    IFNULL(SUM(t.transactionAmount), 0) as expensesAmount
FROM 
	transactions as t
LEFT JOIN
	budgets as b on t.budgetID = b.budgetID
WHERE
	b.userID = ?
    AND t.published = 1
    AND MONTH(t.transactionDate) = MONTH(CURRENT_DATE())
    AND t.transactionType = 'Expenses'
");

$sql->bind_param('i', $_SESSION['id']);
$sql->execute();
$sql->bind_result($expensesAmount);
$sql->fetch();
$sql->close();
?>



<?=template_header('Dashboard');?>

<?=template_nav();?>

<?=template_menu();?>


<!-- Modal 1 for new users -->
<div id="modal" class="modal">
    <div class="modal-background"></div>
    <div class="modal-content">
        <div class="box">
            <div class="content">
                <h1 class="is-italic has-text-success is-bold">Welcome, New User!</h1>
                <p>To begin, please press the Next button below.</p>
            </div>
            <a href="#modal2" data-toggle="modal" data-dismiss="modal" class="button is-info is-small" id="next">Next</a>
            <button class="button is-danger is-small" id="closebtn">Close</button>
            <p class="has-text-danger"> *If no budget is entered this screen will continue to re-appear on dash</p>
        </div>
    </div>
    <button class="modal-close is-large" aria-label="close"></button>
</div>

<!-- Modal 2 for new users -->
<div id="modal2" class="modal">
    <div class="modal-background"></div>
    <div class="modal-content">
        <div class="box">
            <div class="content">
                <h1 class="is-italic is-bold has-text-success">STEP 1:</h1>

                <p class="image is-4by3">
                    <img src="img/step1.png" alt="step 1- create a budget">
                </p>
                <h4 class="has-text-danger is-italic">Click on the 'Create Budget' button</h4>
            </div>
            <a href="#modal3" data-toggle="modal" data-dismiss="modal" class="button is-info is-small" id="next2">Next</a>
            <a href="#modal" data-toggle="modal" data-dismiss="modal" class="button is-info is-small" id="previous">Previous</a>
            <button class="button is-danger is-small" id="closebtn2">Close</button>
        </div>
    </div>
    <button class="modal-close is-large" aria-label="close"></button>
</div>

<!-- Modal 3 for new users -->
<div id="modal3" class="modal">
    <div class="modal-background"></div>
    <div class="modal-content">
        <div class="box">
            <div class="content">
                <h1 class="is-italic is-bold has-text-success">STEP 2:</h1>

                <p class="image is-4by3">
                    <img src="img/step2.png" alt="step 2- fill out budget form">
                </p>
                <h4 class="has-text-danger is-italic">Fill out Budget Form</h4>
            </div>
            <a href="#modal4" data-toggle="modal" data-dismiss="modal" class="button is-info is-small" id="next3">Next</a>
            <a href="#modal2" data-toggle="modal" data-dismiss="modal" class="button is-info is-small" id="previous2">Previous</a>
            <button class="button is-danger is-small" id="closebtn3">Close</button>
        </div>
    </div>
    <button class="modal-close is-large" aria-label="close"></button>
</div>

<!-- Modal 4 for new users -->
<div id="modal4" class="modal">
    <div class="modal-background"></div>
    <div class="modal-content">
        <div class="box">
            <div class="content">
                <h1 class="is-italic is-bold has-text-success">STEP 3:</h1>

                <p class="image is-4by3">
                    <img src="img/step3.png" alt="step 3- click add transaction button">
                </p>
                <h4 class="has-text-danger is-italic">Click on the 'Add Transaction' button</h4>
            </div>
            <a href="#modal5" data-toggle="modal" data-dismiss="modal" class="button is-info is-small" id="next4">Next</a>
            <a href="#modal3" data-toggle="modal" data-dismiss="modal" class="button is-info is-small" id="previous3">Previous</a>
            <button class="button is-danger is-small" id="closebtn4">Close</button>
        </div>
    </div>
    <button class="modal-close is-large" aria-label="close"></button>
</div>

<!-- Modal 5 for new users -->
<div id="modal5" class="modal">
    <div class="modal-background"></div>
    <div class="modal-content">
        <div class="box">
            <div class="content">
                <h1 class="is-italic is-bold has-text-success">STEP 3:</h1>

                <p class="image is-4by3">
                    <img src="img/step4.png" alt="step 2- fill out transaction form">
                </p>
                <h4 class="has-text-danger is-italic">Fill out Transaction Form</h4>
            </div>
            <a href="#modal3" data-toggle="modal" data-dismiss="modal" class="button is-info is-small" id="previous4">Previous</a>
            <button class="button is-danger is-small" id="closebtn5">Close</button>
        </div>
    </div>
    <button class="modal-close is-large" aria-label="close"></button>
</div>


<div class="column main" id="dashboardMobile">
    <section class="section dashboardMobile">
        <div class="container">
            <div class="columns">
                <div class="column is-three-quarters">
                    <h1 class="title">Welcome, <?=$firstName?> <?=$lastName?>!</h1>
                    <p class="subtitle">Here is your monthly review for : <span class="has-text-weight-bold"> <?= date("F, Y") ?> </span></p>
                </div>
                <div class="column">
                    <a href="addTrans.php" class="button is-primary is-outlined">Add Transaction</a>
                    <a href="addBudget.php" class="button is-primary is-outlined">Create Budget</a>
                </div>
            </div>
        </div>
    </section>
    <div class="section dashboardMobile">
            <div class="container">
                <p class="title">Monthly Income & Expense: </p>
                <div class="card" id="donutchart" style="width: 900px; height: 500px;"></div>
            </div>
    </div>
    </section>
    <section class="section dashboardMobile">
        <div class="container">
            <div class="columns">
                <div class="column">
                    <div class="card is-fullheight">
                        <div class="card-header is-justify-content-center">
                            <p class="title has-text-centered m-2">Category Totals</p>
                        </div>
                        <div class="card-content">
                            <div class="columns is-multiline is-mobile has-text-centered">
                                <?php foreach ($transactionTypeTotals as $row): ?>
                                <div class="column is-half">
                                    <p class="has-text-weight-bold is-size-5"><?= $row['transactionType'] ?></p>
                                </div>
                                <div class="column is-half">
                                    <p class="is-size-5">$<?= $row['transactionTypeSum'] ?></p>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column has-text-centered">
                    <div class="card is-fullheight">
                        <div class="card-header is-justify-content-center">
                            <p class="title m-2">Budget Totals</p>
                        </div>
                        <div class="card-content">
                            <div class="columns is-multiline is-mobile has-text-centered">
                                <?php foreach ($budgetTotals as $row): ?>
                                <div class="column is-half">
                                    <p class="has-text-weight-bold is-size-5"><?= $row['budgetName'] ?></p>
                                </div>
                                <div class="column is-half">
                                    <p class="is-size-5">$<?= $row['budgetSum'] ?></p>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="section">
        <div class="container">
            <p class="title">Most Recent Transactions:</p>
            <table class="table is-fullwidth is-hoverable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th class="is-hidden-mobile">Category</th>
                        <th>Budget</th>
                        <th>Amount</th>
                        <th class="is-hidden-mobile">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $row): ?>
                        <tr class="<?= $row['transactionType'] == 'Income' ? 'is-selected' : '' ?>">
                            <td class="has-text-weight-bold"><?=$row['transactionName']?></td>
                            <td class="is-hidden-mobile"><?=$row['transactionType']?></td>
                            <td><?=$row['budgetName']?></td>
                            <td><?=$row['transactionAmount']?></td>
                            <td class="is-hidden-mobile"><?=$row['transactionDate']?></td>
                        </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </section>
    </div>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load("current", {packages:["corechart"]});
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Income', 'Expenses'],
            ['Bills', <?=abs($billsAmount)?>],
            ['Expenses', <?=abs($expensesAmount)?>],
            ['Savings', <?=$savingsAmount?>],
            ['Income', <?=$incomeAmount?>],
        ]);

        var options = {
            pieHole: 0.4,
            colors: ['red', 'red', '#298046', '#298046'],
        };

        var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
        chart.draw(data, options);
        }
    </script>
</div>
</div>
<?php

$modal = <<<EOT
  <script>
  $(document).ready(function(){
    $("#modal").addClass("is-active");
  });
  //next modal
  $("#next").click(function() {
    $("#modal").removeClass("is-active"); 
    $("#modal2").addClass("is-active");
  });
  $("#next2").click(function() {
    $("#modal2").removeClass("is-active"); 
    $("#modal3").addClass("is-active");
  });
  $("#next3").click(function() {
    $("#modal3").removeClass("is-active"); 
    $("#modal4").addClass("is-active");
  });
  $("#next4").click(function() {
    $("#modal4").removeClass("is-active"); 
    $("#modal5").addClass("is-active");
  });
  
  //previous modal
  $("#previous").click(function() {
    $("#modal2").removeClass("is-active"); 
    $("#modal").addClass("is-active");
  });
  $("#previous2").click(function() {
    $("#modal3").removeClass("is-active"); 
    $("#modal2").addClass("is-active");
  });
  $("#previous3").click(function() {
    $("#modal4").removeClass("is-active"); 
    $("#modal3").addClass("is-active");
  });
  $("#previous4").click(function() {
    $("#modal5").removeClass("is-active"); 
    $("#modal4").addClass("is-active");
  });
  
  //close modal
  $(".modal-close").click(function() {
    $(".modal").removeClass("is-active"); 
  });
  $("#closebtn").click(function() {
    $(".modal").removeClass("is-active"); 
  });
  $("#closebtn2").click(function() {
    $("#modal2").removeClass("is-active"); 
  });
  $("#closebtn3").click(function() {
    $("#modal3").removeClass("is-active"); 
  });
  $("#closebtn4").click(function() {
    $("#modal4").removeClass("is-active"); 
  });
  $("#closebtn5").click(function() {
    $("#modal5").removeClass("is-active"); 
  });
  </script>
EOT;

// get info from db
if ($sql = $con->prepare('SELECT * FROM budgets WHERE userID = ?')) {
    $sql->bind_param('i', $_SESSION['id']);
    $sql->execute();
    $sql->store_result();

    if ($sql->num_rows == 0) {
        echo $modal;
    }

}
$sql->close();

?>