<?php
    //Include
    include_once('php/config.php');
    include_once('php/utils.php');
    include_once('php/User.php');
    
    //Session
    session_start();

    ini_set('display_errors', DEBUG);
    
    if (isset($_POST['btnNext'])) {
        if (isset($_SESSION['user']) || isset($_SESSION['order'])) {
            header('Location: betaling.php');
        }
    
        //POST to variables
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $birthdate = $_POST['birthdate'];
        $accepted = $_POST['accepted'];
        
        //Make user object
        $user = new User();

        //Input validation
        if ($firstname == "") {
            error("Geen voornaam ingegeven."); 
        } else {
            $user->firstname = $firstname;
        } 
        if ($lastname == "") {
            error("Geen achternaam ingegeven.");
        } else {
            $user->lastname = $lastname;
        } 
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            error("Geen geldig email-adres ingegeven.");
        } else {
            $user->email = $email;
        } 
        if ($birthdate == "" || validateDate($birthdate)) {
            error("Geen geldige geboortedatum ingegeven.");
        } else {
            $user->birthdate = $birthdate;
        } 
        if ($accepted == false ) {
            error("Je moet de voorwaarden accepteren.");
        } else {
            $user->accepted = $accepted;
        }

        //Add user to session
        $_SESSION['user'] = $user;

        //Go to next stage in ordering-process
        //if user is valid
        if ($user->isValid()) {
            header('Location: check.php');
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <?php readfile(getcwd() . "/partials/head.html") ?>
        <link rel="stylesheet" type="text/css" href="style/index.css">
        <script src="scripts/index.js"></script>
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
                        <form method="post" action="index.php">
                            <input type="text" name="firstname" placeholder="Voornaam" id="focus" value="<?php if(isset($_SESSION['user']->firstname)) echo $_SESSION['user']->firstname; ?>" required />
                            <input type="text" name="lastname" placeholder="Achternaam" value="<?php if(isset($_SESSION['user']->lastname)) echo $_SESSION['user']->lastname; ?>" required />
                            <br>
                            <input type="email" name="email" placeholder="voorbeeld@mail.com" value="<?php if(isset($_SESSION['user']->email)) echo $_SESSION['user']->email; ?>" required />
                            <br>
                            <label>Geboortedatum</label>
                            <input type="date" name="birthdate" value="<?php if (isset($_SESSION['user']->birthdate)) echo $_SESSION['user']->birthdate; ?>" placeholder="mm/dd/yyyy" required />
                            <br>
                            <label>Kortingscode</label>
                            <input type="text" name="coupon" placeholder="00000" maxlength="5" />
                            <br class="mobile">
                            <input type="checkbox" name="accepted" value="1" required />
                            <span id="tos">Algemene Voorwaarde en Privacyverklaring</span>
                            <br class="mobile">
                            <br class="mobile">
                            <input type="submit" value="Volgende" name="btnNext" />
                            <br class="mobile">
                            
                        
                            <script>
                                showProgress(1,5);
                            </script>
                        </form>

                    </div>
                    <div class="price">
                        <span>Prijs: €5</span>
                    </div>
                    <div class="dymanic-btn">
                        <a id="koopNu-btn" class="a-btn">KOOP NU JOUW TICKET</a>
                    </div>
                </div>
            </div>
            <div id="footer"></div>
        </div>
    </body>
</html>
