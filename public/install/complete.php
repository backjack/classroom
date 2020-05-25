<?php require_once('./lib/config.php'); ?><?php
validateDb();
//get base url
$url = "http".(!empty($_SERVER['HTTPS'])?"s":"").
    "://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
$baseUrl = str_replace('/install/complete.php','',$url);

?>
<?php require('./lib/header.php'); ?>
    <h2>Setup Complete!</h2>

<p>You can now login to the backend or visit the frontend. Login to the backend with the credentials you created in the second step</p>
 <div class="row">
     <div class="col-md-6">
         <h3>Login to the Backend</h3>
         <p>You can login to the backend at any time by using this url: <?php echo $baseUrl ?>/admin </p>
         <a target="_blank" class="btn btn-lg btn-primary" href="<?php echo $baseUrl ?>/admin">Login</a>
     </div>
     <div class="col-md-6">
         <h3>Visit the Frontend</h3>
         <p>The url for the frontend (i.e. Student site) is: <?php echo $baseUrl ?> </p>
         <a target="_blank" class="btn btn-lg btn-primary" href="<?php echo $baseUrl ?>/">Visit</a>
     </div>

 </div>

<?php
session_destroy();
?>
<?php require('./lib/footer.php'); ?>