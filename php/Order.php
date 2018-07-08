<?php
    //Include the Mollie-API
    include_once("mollie-api-php/vendor/autoload.php");
    include_once("User.php");
    include_once('Database.php');
    include_once('config.php');

    ini_set('display_errors', DEBUG);

    //PHP-file for Order-class
    class Order {
        //Constructor
        function __construct() {
            $this->price = "";
            $this->description = "";
            $this->redirect = "";
            $this->date = "";
            $this->time = "";
            $this->user = "";
            
        }
        
        //Function to execute the payment
        //:return bool: Returns false on failure
        public function execute($mollie) {
            //Check if order is valid
            if (!$this->isValid())
                return false;

            //Payment properties
            $payment = $mollie->payments->create([
                "amount" => [
                    "currency" => "EUR",
                    "value" => $this->price
                ],

                "description" => $this->description,
                "redirectUrl" => $this->redirect,
                "webhookUrl" => $this->webhook
            ]);

            //Add paymentID to class
            $this->paymentID = $payment->id;

            //Update paymentID in database
            $db = new Database();
            $db->setTable('orders');
            
            $db->update($db->lastID(), array(
                'PaymentID' => $payment->id
            ));

            //Save order to SESSION
            $_SESSION['order'] = $this;

            //Goto Mollie-checkout page
            header("Location: " . $payment->getCheckoutUrl(), true, 303);

            return true;
        }

        //Function to check if the Order is valid
        //:return bool: Returns true if succesful
        public function isValid() {
            //First user should be valid
            if (!$this->user->isValid())
                return false;
            

            //Check properties
            if ($this->price <= 0)
                return false;
            if ($this->description == "" ||
                $this->redirect == "" ||
                $this->date == "" ||
                $this->time == "" ||
                $this->user == "") {
                    return false;
                }

            return true;
        }
        
        //Function to check if order isPaid
        //:return bool: Returns true if paid
        public function isPaid() {
            $db = new Database();
                   
            if ($db->select($this->paymentID, 'paid') == 1) {
                return true;
            } else
                return false;
        }
    
        //Properties
        public $id = 0;
        public $paymentID = "";
        public $price = "";
        public $description = "";
        public $webhook = "";
        public $redirect = "";
        public $date = "";
        public $time = "";
        public $user = NULL;
        public $mollie = NULL;
        public $paid = 0;
    }
