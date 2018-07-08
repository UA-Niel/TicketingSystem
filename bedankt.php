<?php
    //Include
    include_once('php/config.php');
    include_once('php/utils.php');
    include_once('php/User.php');
    include_once("php/Order.php");
   

    //Session
    session_start();

    //Debug
    ini_set('display_errors', DEBUG);
    
    //Check if is valid on page
    
    if (isset($_SESSION['order'])) {
        if (!$_SESSION['order']->isPaid())
            header('Location: index.php');
    }
        
    //Destroy session    
    session_destroy();
?>

<!DOCTYPE html>
<html>
    <head>
        <?php readfile(getcwd() . "/partials/head.html") ?>
        <link rel="stylesheet" type="text/css" href="style/bedankt.css">
    </head>
    <body>
        <div id="wrapper">
            <div id="content">
                <div class="container">
                    <?php
                        //Header
                        readfile(getcwd() . "/partials/header.html");
                    ?>

                    <div class="form-wrapper">
                        <div class="form-text">
                            Bedankt<br>
                            <span>Bedankt voor de aankoop van uw ticket! See you there!</span>
                            
                            <a class="ndvibes" href="https://ndvibes.com" target="_blank">
                                <img src="images/ndvibes.png" /><br>
                                <span>Made by Niel Duysters</span>
                            </a>
                            
                            <br><br>
                            <a href="index.php">Home</a>
                        </div>
                        <script>
                            showProgress(5,5);
                        </script>
                    </div>
                </div>
            </div>
            <div id="footer"></div>
        </div>
    </body>
</html>
