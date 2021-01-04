<?php
require 'functions.php';
require 'session.php';
$msg = "";




?>

<?=template_header('Create Transaction');?>

<?=template_nav();?>


<section class="section">
    <div class="container">
        <h1 class="title">Create Transaction</h1>
        <form action="createTrans.php" method="post">

            <div class="field">
                <label class="label">Transaction Type</label>
                <div class="control">
                    <input class="input" type="text" name="transType" required>
                </div>
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
                    <input type="number" class="input" name="transAmount" required>
                </div>
            </div>
            <div class="field">
                <div class="control">
                    <button class="button is-link">Submit</button>
                </div>
            </div>
        </form>
        <?php if($msg): ?>
            <p><?=$msg?></p>
        <?php endif; ?>
    </div>
</section>



<?=template_footer();?>