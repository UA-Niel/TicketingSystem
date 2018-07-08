<?php
    //Include
    include_once('php/config.php');
    include_once('php/utils.php');
    include_once('php/User.php');
    include_once("php/Order.php");
    include_once("php/Database.php");
    include_once("php/phpqrcode/qrlib.php");
   

    //Session
    session_start();

    //Debug mode
    ini_set('display_errors', DEBUG);
    
    //Check if user is allowed on ticket.php
    if (!isset($_SESSION['order'])) {
        header('Location: error.php');
    }
    
    //Get order
    $order = $_SESSION['order'];
    
    //User must have paid to continue
    if (!$order->isPaid()) {
        header('Location: error.php');
    }
    
    //Get ID and hash from database with paymentID
    $db = new Database();
    $ID = $db->select($order->paymentID, 'ID');
    $hash = $db->select($order->paymentID, 'hash');
    
    //Data for QRCode
    $str = $ID . "," . $hash;
    $path = "qrcodes/" . $ID . "_" . $order->paymentID . ".png";
   
    //Generate QRCode(ID,hash)
    QRCode::png($str, $path);
    
    //Destroy session
    session_destroy();
?>
    
<!DOCTYPE html>
<html>
    <head>
        <?php readfile(getcwd() . "/partials/head.html") ?>
        <link rel="stylesheet" type="text/css" href="style/ticket.css">
        <link rel="stylesheet" type="text/css" href="style/print.css">
        <script src="scripts/html2canvas.js"></script>
        <script src="scripts/print.js"></script>
        
    </head>
    <body onload="printPage()">
        <div id="wrapper">
            <div id="content">
                <div class="container">
                    <?php
                        //Header
                        readfile(getcwd() . "/partials/header.html");
                    ?>
                    
                    <h1 style="font-family: 'Raleway Bold';">Bal Tropical</h1>
                    <h2>Voorverkoop Inkomkaarten</h2>

                    <div class="form-wrapper">
                        <div class="form-text">
                            Uw ticket<br>
                            <span>Druk uw ticket nu af, en knip het uit!</span>
                            <span class="tip"><b>Tip:</b> Sommige browsers hebben geen ondersteunende afdruk-functie, dan is het handiger een screenshot te nemen, en deze af te drukken.</span>
                            
                            <div class="scissor-line">
                                <div id="capture" class="ticket">
                                    <img class="logo" src="images/BalTropical_logo_small.png" />
                                    <div class="logo-text">Online Voorverkoop</div>
                                    <img class="palmboom" src="images/palmboom.png" />
                                    <div class="ndvibes left">
                                        NDVibes
                                    </div>
                                    <div class="ndvibes right">
                                        Niel Duysters
                                    </div>
                                    <div class="user-info">
                                        <span class="firstname"><?php echo htmlspecialchars($order->user->firstname); ?></span>
                                        <span class="lastname"><?php echo htmlspecialchars($order->user->lastname); ?></span>
                                        <br>
                                        
                                        <span class="birthdate"><?php echo htmlspecialchars($order->user->birthdate); ?></span>
                                        <span class="age">(<?php echo htmlspecialchars($order->user->getAge()); ?>)</span>
                                        <br>
                                        
                                        <span class="code"><?php echo htmlspecialchars(pseudoCode($order)); ?></span>
                                    </div>
                                    
                                    <img class="qrcode" src="<?php echo htmlspecialchars($path); ?>" />
                                </div>
                            </div>
                        </div>
                        <script>
                            showProgress(5,5);
                        </script>
                    </div>
                    
                    <div class="ndvibes-wrapper">
                        <img src="images/ndvibes.png" class="ndvibes-logo" /><br>
                        <span>Made by Niel Duysters</span>
                    </div>
                    
                </div>
            </div>
            <div id="footer"></div>
        </div>
    </body>
</html>
