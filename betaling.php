<?php
    //Include
    include_once('php/config.php');
    include_once('php/utils.php');
    include_once('php/User.php');
    include_once("php/Order.php");
    include_once("php/Database.php");

    //Session
    session_start();

    //Debug
    ini_set('display_errors', DEBUG);
    
    //Set date to our timezone
    date_default_timezone_set(TIMEZONE);
    
    
    
    //Create Mollie-object
    $mollie = new \Mollie\Api\MollieApiClient();
    $mollie->setApiKey(MOLLIE_KEY);

    //If there is response from Mollie-webhook
    if (isset($_POST['id'])) {
        //Get payment
        $payment = $mollie->payments->get($_POST['id']);
    
        //Make database object
        $db = new Database();
        $db->setTable('orders');
    
        //Check if payment is paid
        if ($payment->isPaid()) {
            //Alter database:
            // - Add secret hash
            // - Set paid to true (paid = true)
            // - Set date of order
            // - Set time of order
            $db->update($_POST['id'], array(
                'hash' => generateHash(),
                'paid' => 1,
                'date' => date('Y-m-d'),
                'time' => date('H-i-s')
            ));
        } else {
           //Delete from database
           $db->delete($_POST['id']);
        }
    }
    
    //If pay-button is clicked
    else if (isset($_POST['btnPay'])) {

        //Creat order-object
        $order = new Order();

        //Set order properies
        $order->price = "5.35";
        $order->description = "Bal Tropical Ticket (incl. Transactiekosten)";
        $order->redirect = HOST . "/betaling.php";
        $order->webhook = HOST . "/betaling.php";
        $order->date = date('d-M-Y');
        $order->time = date('H-i-s');
        $order->user = $_SESSION['user'];
        $order->paid = 0;

        //Add order to database (paid = false)
        $db = new Database();
        $db->insert($order);
        $db->close();

        //Execute order
        $order->execute($mollie);
    } 
    
    //If nothing is clicked
    else {
        //Get order
        if (isset($_SESSION['order'])) {
            //Add ID to order class
            $db = new Database();
            $order = $_SESSION['order'];
            $order->id = $db->select($order->paymentID, 'ID');
            
            //Check if order is paid
            if ($order->isPaid()) {
                //Redirect
                header('Location: ticket.php');
            }
        } else {
            if (!isset($_SESSION['user']))
                //User should not be on this page
                header('Location: error.php');
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <?php readfile(getcwd() . "/partials/head.html") ?>
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
                            <div class="form-text">
                                Je hebt bijna jouw ticket! Nu enkel nog betalen
                                <div class="price">
                                    <span>Prijs: €5</span>
                                </div>
                            </div>

                            <input type="submit" value="Betalen" name="btnPay" title="Betalen" />
                        </form>

                        <script>
                            showProgress(3,5);
                        </script>
                    </div>
                </div>
            </div>
            <div id="footer"></div>
        </div>
    </body>
</html>
