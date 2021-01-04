<?php
require 'functions.php';
require 'session.php';
$msg = "";



?>

<?=template_header('Categories');?>

<?=template_nav();?>

<!-- document main content goes here -->
<section class="section">
    <div class="container">
        <h1 class="title">Categories</h1>

        <div class="columns">
            <div class="column">
                Reports
                <a href="reports.php"><img src="img/chart-line-solid.svg" alt="Chart Line"></a>
            </div>
            <div class="column">
                Bills
                <a href="bills.php"><img src="img/file-invoice-dollar-solid.svg" alt="File Invoice Dollar"></a>
            </div>
            <div class="column">
                Income
                <a href="income.php"><img src="img/money-bill-alt-solid.svg" alt="Money Bill"></a>
            </div>
            <div class="column">
                Savings
                <a href="savings.php"><img src="img/piggy-bank-solid.svg" alt="Piggy Bank"></a>
            </div>
        </div>
        <div class="columns">
            <div class="column">
                Expenses
                <a href="expenses.php"><img src="img/hand-holding-usd-solid.svg" alt="Hand Holding USD"></a>
            </div>
            <div class="column">
                Transactions
                <a href="transactions.php"><img src="img/exchange-alt-solid.svg" alt="Exchange"></a>
            </div>
            <div class="column">
                Settings
                <a href="settings.php"><img src="img/cog-solid.svg" alt="Cog"></a>
            </div>
            <div class="column">
                Budgets
                <a href="budgets.php"><img src="img/user-solid.svg" alt="User image"></a>
            </div>
        </div>
    </div>
</section>

<?=template_footer();?>