<?php  
    //Include
    include_once('php/config.php');
    include_once('php/utils.php');
    include_once('php/User.php');
    
    //Session
    session_start();

    ini_set('display_errors', DEBUG);

    //If session variable is not set,
    //user is on invalid page
    if (!isset($_SESSION['user'])) {
        header('Location: error.php');
        return;
    }
    
    //Retrieve user from session variable
    $user = $_SESSION['user'];
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
                        <form method="post" action="betaling.php">
                            <span class="description">Voornaam:</span>
                            <span><?php echo htmlspecialchars($user->firstname); ?></span>
                            <br>
                            <span class="description">Achternaam:</span>
                            <span><?php echo htmlspecialchars($user->lastname); ?></span>
                            <br>
                            <span class="description">Geboortedatum:</span>
                            <span><?php echo htmlspecialchars($user->birthdate); ?></span>
                            <br>
                            <span class="description">E-mail:</span>
                            <span><?php echo htmlspecialchars($user->email); ?></span>
                            <br>

                            <div class="form-text">
                                Klopt deze info?
                            </div>
                            
                            <input type="submit" value="Ja" name="btnNext" title="Volgende" />
                            <a href="index.php" title="Vorige">Nee</a>
                        </form>

                        <script>
                            showProgress(2,5);
                        </script>
                    </div>
                </div>
            </div>
            <div id="footer"></div>
        </div>
    </body>
</html>
