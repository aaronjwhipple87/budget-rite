<?php
require 'functions.php';
require 'session.php';
$msg = "";

// get info from db
$sql = $con->prepare('SELECT userPassword, email, firstName, lastName FROM users WHERE userId = ?');

// In this case we can use the account ID to get the account info.
$sql->bind_param('i', $_SESSION['id']);
$sql->execute();
$sql->bind_result($userPassword, $email, $firstName, $lastName);
$sql->fetch();
$sql->close();


//name and email edit
if(isset($_POST["edit"])) {

    //field validations
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $msg = 'Email is not valid';
        echo "<script type='text/javascript'>alert('$msg');</script>";
    }

    // get info from db
    if ($sql = $con->prepare('SELECT * FROM users WHERE userId = ?')) {
        $sql->bind_param('i', $_SESSION['id']);
        $sql->execute();
        $sql->store_result();

        if ($stmt = $con->prepare('UPDATE users SET firstName = ?, lastName = ?, email = ? WHERE userId = ?')) {

            $stmt->bind_param('ssss', $_POST['firstName'], $_POST['lastName'], $_POST['email'], $_SESSION['id']);
            $stmt->execute();

            $msg = 'You have successfully changed your First Name, Last Name, and Email!';
            echo "<script type='text/javascript'>alert('$msg');</script>";
            header("Refresh:.5; url=dashboard.php");
        } else {
            $msg = "Could not prepare ";
            echo "<script type='text/javascript'>alert('$msg');</script>";
        }

        $sql->close();
    } else {
        //something went wrong with sql statement
        $msg = "Could not prepare statement!";
        echo "<script type='text/javascript'>alert('$msg');</script>";
    }
}

//password edit
if(isset($_POST["changePassword"])){

    //field validation
    if (strlen($_POST['password']) >= 20 || strlen($_POST['password']) <= 8) {
        $msg ='Password must be between 8 and 20 characters long!';
        echo "<script type='text/javascript'>alert('$msg');</script>";
    }

    //verify password is correct
    if($sql = $con->prepare('SELECT userPassword FROM users WHERE userId = ?')) {
        $sql->bind_param('s', $_SESSION['id']);
        $sql->execute();
        $sql->store_result();

        //check account exists
        if($sql->num_rows > 0) {
            $sql->bind_result($userPassword);
            $sql->fetch();


            // if exists, verify old password
            if (password_verify($_POST['oldPassword'], $userPassword)) {

                if ($stmt = $con->prepare('UPDATE users SET userPassword = ? WHERE userId = ?')) {
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $stmt->bind_param('ss', $password, $_SESSION['id']);
                    $stmt->execute();

                    $msg = 'You have successfully changed your password!';
                    echo "<script type='text/javascript'>alert('$msg');</script>";
                    header("Refresh:1; url=dashboard.php");
                } else {
                    $msg = "Could not prepare statement";
                    echo "<script type='text/javascript'>alert('$msg');</script>";
                }
            } else {
                //incorrect old email
                $msg = "Incorrect Old Password, try again!";
                echo "<script type='text/javascript'>alert('$msg');</script>";
            }
        }
    }
}




?>

<?=template_header('Settings');?>
<?=template_nav();?>
<?=template_menu();?>

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

<div class="column  main">
<div class="section">
<div class="container">
    <h2 class="title">Settings</h2>
    <div class="columns">
        <div class="column">
<!--            <p class="title">Profile Info</p>-->
            <div class="columns is-multiline">
<!--                <div class="column is-one-third has-text-centered">-->
<!--                    <img src="https://via.placeholder.com/150" alt="">-->
<!--                </div>-->
                <div class="column">
                    <form action="settings.php" method="post" onsubmit="return confirm('Are you sure you want to edit name and email?')";>
                        <div class="field is-horizontal">
                            <div class="field-label is-normal">
                                <label for="firstname" class="label">First Name</label>
                            </div>
                            <div class="field-body">
                                <div class="field">
                                    <input type="text" name="firstName" class="input" value="<?=$firstName?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="field is-horizontal">
                            <div class="field-label is-normal">
                                <label for="lastname" class="label">Last Name</label>
                            </div>
                            <div class="field-body">
                                <div class="field">
                                    <input type="text" name="lastName" class="input" value="<?=$lastName?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="field is-horizontal">
                            <div class="field-label is-normal">
                                <label for="email" class="label">Email</label>
                            </div>
                            <div class="field-body">
                                <div class="field">
                                    <input type="text" name="email" class="input" value="<?=$email?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="field is-horizontal">
                            <div class="field-label is-normal"></div>
                            <div class="field-body">
                                <div class="field">
                                    <div class="control">
                                        <button type="submit" name="edit" class="button is-link">
                                        Edit
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="column is-full mt-4 mb-4">
                    <div class="field is-horizontal">
                        <div class="field-label is-normal">
                            <label class="label">Change Password</label>
                        </div>
                        <form action="settings.php" method="post" onsubmit="return confirm('Are you sure you want to edit password?')";>
                            <div class="field-body">
                                <div class="field">
                                    <input type="password" class="input" name="oldPassword" placeholder="Old Password" required>
                                </div>
                                <div class="field">
                                    <input type="password" id="password1" oninput="setPasswordConfirmValidity();" name="password" placeholder="New Password" class="input" required>
                                </div>
                                <div class="field">
                                    <input type="password" id="password2" oninput="setPasswordConfirmValidity();" class="input" placeholder="Confirm Password" required>
                                </div>
                                <div class="field">
                                    <button type="submit" name="changePassword" class="button is-link">Change Password</button>
                                </div>
                            </div>
                        </form>
                        <div class="field pl-1">
                            <a href="forgot.php"><button type="submit" name="forgotPassword" class="button is-danger">Reset Password?</button></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>