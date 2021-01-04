<?php
require 'functions.php';
$msg = "";



if(isset($_POST["submit"])){
    if (!isset($_POST['firstName'], $_POST['lastName'], $_POST['phoneNumber'], $_POST['password'], $_POST['email'])) {
        $msg = 'Please complete the registration form!';
        echo "<script type='text/javascript'>alert('$msg');</script>";
    }

    if(empty($_POST['firstName']) || empty($_POST['lastName']) || empty($_POST['phoneNumber']) || empty($_POST['password']) || empty($_POST['email'])) {
        $msg = 'Please complete the registration form!';
        echo "<script type='text/javascript'>alert('$msg');</script>";
    }


    //field validations
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

        if($sql->num_rows > 0) {
            //email exists
            $msg = "Email already exists";
            echo "<script type='text/javascript'>alert('$msg');</script>";
        } else {
            //insert a new record
            if($stmt = $con->prepare('INSERT INTO users (email, userPassword, firstName, lastName, phoneNumber) VALUES (?,?,?,?,?)')) {
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $stmt->bind_param('ssssi', $_POST['email'], $password, $_POST['firstName'], $_POST['lastName'], $_POST['phoneNumber']);
                $stmt->execute();
                $msg = 'You have successfully registered, you can now login!';
                echo "<script type='text/javascript'>alert('$msg');</script>";
                header( "Refresh: .5 ; url= login.php ");


            } else {
                $msg = "Could not prepare statement";
                echo "<script type='text/javascript'>alert('$msg');</script>";
            }
        }

        $sql->close();

    } else {
        //something went wrong with sql statement
        $msg = "Could not prepare statement!";
        echo "<script type='text/javascript'>alert('$msg');</script>";
    }
}

?>

<?=template_header('Register');?>

<nav class="navbar is-light" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <a class="navbar-item" href="index.php">
            <img src="img/BR-icon.png" alt="">
        </a>

        <a role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </a>
    </div>

    <div id="navbarBasicExample" class="navbar-menu">

        <div class="navbar-start">
            <a class="navbar-item" href="dashboard.php">
                Home
            </a>

        </div>

        <div class="navbar-end">
            <div class="navbar-item">
                <div class="buttons">

                    <a href="login.php" class="button is-success">
                        Login
                    </a>
                </div>
            </div>
        </div>

    </div>
</nav>
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
            Create a new user account
        </h1>
        <form action="register.php" method="post" class="form" onsubmit="return confirm('Are you sure you want to submit?');">
            <div class="field">
                <label class="label">Email</label>
                <div class="control">
                    <input class="input" name="email" type="email" placeholder="e.g. johndoe@gmail.com" required>
                </div>
            </div>
            <div class="field">
                <label class="label">First Name</label>
                <div class="control">
                    <input class="input" type="text" name="firstName" placeholder="John" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Last Name</label>
                <div class="control">
                    <input class="input" type="text" name="lastName" placeholder="Doe" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Phone Number</label>
                <div class="control">
                    <input type="tel" name="phoneNumber" class="input" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Password</label>
                <div class="control">
                    <input type="password" id="password1" oninput="setPasswordConfirmValidity();" name="password" class="input" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Confirm Password</label>
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
<section class="section">
    <div class="container">
        <h2 class="subtitle">
            Have an account already?
        </h2>
        <a class="button is-success" href="login.php">Login</a>
    </div>
</section>

<?=template_footer();?>