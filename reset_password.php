<?php
include 'functions.php';
$msg = "";


if(isset($_POST["submit"])){

    //field validation
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $msg = 'Email is not valid';
        echo "<script type='text/javascript'>alert('$msg');</script>";
    }
    if (strlen($_POST['password']) >= 20 || strlen($_POST['password']) <= 8) {
        $msg ='Password must be between 8 and 20 characters long!';
        echo "<script type='text/javascript'>alert('$msg');</script>";
    }

    //check if account exists
    if($sql = $con->prepare('SELECT * FROM users WHERE email = ?')) {
        $sql->bind_param('s', $_POST['email']);
        $sql->execute();
        $sql->store_result();


        //check if email exists
        if($sql->num_rows > 0) {

            if ($stmt = $con->prepare('UPDATE users SET userPassword = ? WHERE email=?')) {
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $stmt->bind_param('ss', $password,  $_POST['email']);
                $stmt->execute();

                $msg = 'You have successfully changed your password, you can now login!';
                echo "<script type='text/javascript'>alert('$msg');</script>";
                header( "Refresh:1; url=login.php");
            }else {
                $msg = "Could not prepare statement";
                echo "<script type='text/javascript'>alert('$msg');</script>";
            }
        }else {
            //email does not exist
            $msg = "Incorrect email, try again";
            echo "<script type='text/javascript'>alert('$msg');</script>";
        }
        $sql->close();
    }else {
        //something went wrong with sql statement
        $msg = "Could not prepare statement!";
        echo "<script type='text/javascript'>alert('$msg');</script>";
    }
}


?>

<?=template_header('Reset Password');?>

<?=template_nav();?>

<script>
    function setPasswordConfirmValidity(str) {
        const password1 = document.getElementById('password1');
        const password2 = document.getElementById('password2');

        if (password1.value === password2.value) {
            password2.setCustomValidity('');
        } else {
            password2.setCustomValidity('Passwords must match');
        }
        console.log('password2 customError ', document.getElementById('password2').validity.customError);
        console.log('password2 validationMessage ', document.getElementById('password2').validationMessage);
    }
</script>
<section class="section">
    <div class="container">
        <h1 class="title">
            Create a new password
        </h1>
        <form action="reset_password.php" method="post" class="form" onsubmit="return confirm('Are you sure you want to submit?');">
            <div class="field">
                <label class="label">Email</label>
                <div class="control">
                    <input class="input" name="email" type="email" placeholder="e.g. johndoe@gmail.com" required>
                </div>
            </div>
            <div class="field">
                <label class="label">New Password</label>
                <div class="control">
                    <input type="password" id="password1" oninput="setPasswordConfirmValidity();" name="password" class="input" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Confirm New Password</label>
                <div class="control">
                    <input type="password" id="password2" oninput="setPasswordConfirmValidity();" class="input" required>
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







<?=template_footer();?>
