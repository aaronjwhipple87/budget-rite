<?php
include 'functions.php';
$msg = "";
session_start();


if (isset($_POST["login"])) {

    // Check username and password are set
    if (!isset($_POST['email'], $_POST['password'])) {

        // kill if both not filled out
        $msg = 'Please fill both the username and password field!';
        echo "<script type='text/javascript'>alert('$msg');</script>";
    }

    // check if account exists
    if ($sql = $con->prepare('SELECT userID, userPassword FROM users WHERE email = ?')) {

        $sql->bind_param('s', $_POST['email']);
        $sql->execute();
        $sql->store_result();

        if ($sql->num_rows > 0) {
            $sql->bind_result($userID, $userPassword);
            $sql->fetch();

            // if exists, verify the password.
            if (password_verify($_POST['password'], $userPassword)) {

                // Verification success! User has loggedin!

                // Create sessions so we know the user is logged in

                session_regenerate_id();

                $_SESSION['loggedin'] = TRUE;

                $_SESSION['email'] = $_POST['email'];

                $_SESSION['id'] = $userID;


                header('Location: dashboard.php');

            } else {

                $msg = 'Incorrect password!';
                echo "<script type='text/javascript'>alert('$msg');</script>";
//                header('Location: login.php?message=Incorrect Password');



            }

        } else {

            $msg = 'Incorrect email!';
            echo "<script type='text/javascript'>alert('$msg');</script>";
//            header('Location: login.php?message=Incorrect Email');

        }


        $sql->close();

    }
}


?>




<?=template_header('Login');?>

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
                        <a href="register.php"class="button is-primary">
                            Register
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </nav>

<section class="section">
    <div class="container">
        <h1 class="title">
            Login
        </h1>
        <form action="login.php" method="post" class="form">
            <div class="field">
                <label class="label">Email</label>
                <div class="control">
                    <input class="input" type="email" name="email" placeholder="e.g. johndoe@gmail.com" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Password</label>
                <div class="control">
                    <input class="input" type="password" name="password" placeholder="********" required>
                </div>
            </div>
            <div class="field">
                <p class="control">
                    <button type="submit" name="login" class="button is-success">Login</button>
                </p>
            </div>

        </form>
        <a href="forgot.php">Forgot Password?</a>
    </div>
</section>
<section class="section">
    <div class="container">
        <h2 class="subtitle">
            Don't have an account?
        </h2>
        <a class="button is-link" href="register.php">Register</a>
    </div>
</section>

<?=template_footer();?>

