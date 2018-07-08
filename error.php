<?php
    //Include
    include_once('php/config.php');
    include_once('php/utils.php');
    include_once('php/User.php');
    include_once("php/Order.php");
   

    //Session
    session_start();

    ini_set('display_errors', DEBUG);
?>

<!DOCTYPE html>
<html>
    <head>
        <?php readfile(getcwd() . "/partials/head.html") ?>
        <link rel="stylesheet" type="text/css" href="style/check.css">
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
                            Foutmelding<br>
                            <span>Er heeft zich een fout voorgedaan!</span>
                            <br><br>
                            <a href="index.php">Probeer opnieuw</a>
                        </div>
                        <script>
                            showProgress(0,5);
                        </script>
                    </div>
                </div>
            </div>
            <div id="footer"></div>
        </div>
    </body>
</html>
