<?php
include 'functions.php';
$msg = "";

if(isset($_POST["submit"])){

    //field validations
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $msg = 'Email is not valid';
        echo "<script type='text/javascript'>alert('$msg');</script>";
    }
    //validate account exists, if so send activation code
    if($sql = $con->prepare('SELECT * FROM users WHERE email = ?')) {
        $sql->bind_param('s', $_POST['email']);
        $sql->execute();
        $sql->store_result();

        if ($sql->num_rows > 0) {

            //email content
            $from = 'support@budgetrite.com';
            $subject = "Reset your Budget Rite Password";
            $headers = 'From: ' . $from . "\r\n" . 'Reply-To: ' . $from . "\r\n" . 'X-Mailer: PHP/' . phpversion() . "\r\n" . 'MIME-Version: 1.0' . "\r\n" . 'Content-Type: text/html; charset=UTF-8' . "\r\n";
            $reset_link = 'http://icarus.cs.weber.edu/~aw54652/web_4350/budget/reset_password.php?email=' . $_POST['email'];
            $message = '<p>Please click the following link to reset your password: <a href="' . $reset_link. '">Reset Password</a></p>';


            mail($_POST['email'],$subject,$message,$headers);





            $msg = 'Please check your email for reset link';
            echo "<script type='text/javascript'>alert('$msg');</script>";
            header( "Refresh:1; url=index.php");

        } else {
            //email  does not exists
            $msg = "No such user exists in our system. Please re-enter correct email or register.";
            echo "<script type='text/javascript'>alert('$msg');</script>";
        }
    }
}
?>

<?=template_header('Forgot Password');?>

<?=template_nav();?>

    <section class="section">
        <div class="container">
            <h1 class="title">
                Forgot/ Reset Password?
            </h1>
            <form action="forgot.php" method="post">
                <div class="field">
                    <label class="label">Enter your email to send password code:</label>
                    <div class="control">
                        <input class="input" name="email" type="email" placeholder="e.g. johndoe@gmail.com" required>
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