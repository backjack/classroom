<?php require_once('./lib/config.php'); ?><?php
validateDb();
if(isset($_POST['submit'])){
    //try to connect with credentials
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);
    if($password!==$confirmPassword){
        $message="Your two password values do not match! Please check and try again";
    }
    else{
try{

        $firstname = trim($_POST['first_name']);
        $lastname = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        $password= md5(trim(($_POST['password'])));
        $plainPassword = $_POST['password'];

        $sql = "INSERT INTO accounts (first_name, last_name, email, password, role_id, notify) VALUES ('$firstname', '$lastname', '$email','$password',1,1)";
        $conn->exec($sql);

                $subject = 'You new account details';
                $message= "Here are your new TrainEasy account details:<br/>
Email: $email <br/>
Password: $plainPassword
";
                $headers  = 'MIME-Version: 1.0' . "\r\n";

                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Create email headers

                $headers .= 'X-Mailer: PHP/' . phpversion();
    try{
        mail($email,$subject,$message,$headers);
    }
    catch(Exception $ex){

    }


            header('Location: site.php');

}catch (Exception $ex){
    $message = 'Error: Account creation failed! Please try again: '.$ex->getMessage();
}
    }


}

?>
<?php require('./lib/header.php'); ?>
    <h2>Admin Account</h2>

<?php if(isset($message)):?>
    <div class="alert alert-warning alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <?php echo $message; ?>
    </div>
<?php endif; ?>

    <form action="account.php" method="post" class="form">

        <div class="form-group">
            <label for="first_name">First Name</label>
            <input required="required" class="form-control" type="text" name="first_name"  value="<?php echo (isset($_POST['first_name'])? $_POST['first_name']:@$_SESSION['account']['first_name'])?>"/>
        </div>
        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input required="required"  class="form-control" type="text" name="last_name" value="<?php echo (isset($_POST['last_name'])? $_POST['last_name']:@$_SESSION['account']['last_name'])?>"/>
        </div>
        <div class="form-group">
            <label for="username">Email</label>
            <input required="required"  class="form-control" type="email" name="email" value="<?php echo (isset($_POST['email'])? $_POST['email']:@$_SESSION['account']['email'])?>"/>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input min="6"  required="required"    class="form-control" type="password" name="password" value=""/>
        </div>
        <div class="form-group">
            <label for="password">Confirm Password</label>
            <input  min="6"   required="required"    class="form-control" type="password" name="confirm_password" value=""/>
        </div>
        <div class="form-footer">
            <button class="btn btn-primary pull-right btn-lg" type="submit" name="submit">Submit</button>
        </div>
    </form>

<?php require('./lib/footer.php'); ?>