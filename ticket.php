<?php
    //Include
    include_once('php/config.php');
    include_once('php/utils.php');
    include_once('php/User.php');
    include_once("php/Order.php");
    include_once("php/Database.php");
    include_once("php/phpqrcode/qrlib.php");
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require 'php/phpmailer/Exception.php';
    require 'php/phpmailer/PHPMailer.php';
    require 'php/phpmailer/SMTP.php';
    
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
    
    //Generate pseudo-code
    $pseudocode = pseudoCode($order);
    
    //Send mail
    $message = file_get_contents("mail.html");
    $message = str_replace("%firstname%", $order->user->firstname, $message);
    $message = str_replace("%lastname%", $order->user->lastname, $message);
    $message = str_replace("%birthdate%", $order->user->birthdate, $message);
    $message = str_replace("%age%", $order->user->getAge(), $message);
    $message = str_replace("%code%", $pseudocode, $message);
    $message = str_replace("%path%", HOST.$path, $message);
    
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->SMTPDebug = DEBUG;
    $mail->Host = 'mx1.hostinger.com';
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->Username = MAIL_USER;
    $mail->Password = MAIL_PASS;
    $mail->setFrom(MAIL_FROM, 'Bal Tropical Tickets');
    $mail->addAddress($order->user->email, $order->user->username . " " . $order->user->lastname);
    $mail->Subject = 'Uw Bal Tropical ticket';
    $mail->msgHTML($message, __DIR__);
    $mail->send();
?>

<!DOCTYPE html>
<html>
    <head>
        <?php readfile(getcwd() . "/partials/head.html") ?>
        <link rel="stylesheet" type="text/css" href="style/ticket.css">
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
                            Uw ticket<br>
                            <span>Druk uw ticket nu af!</span>
                            
                            <div class="ticket">
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
                                    
                                    <span class="code"><?php echo htmlspecialchars($pseudocode); ?></span>
                                </div>
                                
                                <img class="qrcode" src="<?php echo htmlspecialchars($path); ?>" />
                            </div>
                            
                            <br><br>
                            <a href="print.php" target="_blank">Afdrukken</a>
                            <a href="bedankt.php">Volgende</a>
                        </div>
                        <script>
                            showProgress(4,5);
                        </script>
                    </div>
                </div>
            </div>
            <div id="footer"></div>
        </div>
    </body>
</html>
