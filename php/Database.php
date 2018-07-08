<?php
    //Include
    include_once('config.php');
    include_once('Order.php');

    //PHP-file for Database class
    class Database {
        //Constructor
        public function __construct() {
            $this->conn = new mysqli(DBHOST, USER, PASS, DTBS);
            if (!$this->conn) {
                return false;
            }
        }

        //Function to set table
        //:param table: Name of table to work in
        //:return bool: Returns true if succesful
        public function setTable($table) { 
            //Check if table is allowed
            if (!in_array($this->table, $this->allowed_tables)) {
                return false;
            }

            $this->table = $table;
            return true;
        }

        //Function to insert objects in database
        //:param object: Object to insert into database
        //:return bool: Returns true if inserted succesful
        public function insert($object) {
            //If object is an Order
            if (is_a($object, 'Order')) {
                $this->table = 'orders';
                //Check if table is allowed
                if (!in_array($this->table, $this->allowed_tables)) {
                    return false;
                }
                
                //Insert to database
                $stmt = $this->conn->prepare("INSERT INTO ". htmlspecialchars($this->table) ." (PaymentID, firstname, lastname, email, birthdate, accepted_agreements, paid, scanned) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

                $stmt->bind_param("sssssiii", $paymentID_, $firstname_, $lastname_, $email_, $birthdate_, $accepted_agreements_, $paid_, $scanned_);
                $table_ = $this->table;
                $paymentID_ = $object->paymentID;
                $firstname_ = $object->user->firstname;
                $lastname_ = $object->user->lastname;
                $email_ = $object->user->email;
                $birthdate_ = $object->user->birthdate;
                $accepted_agreements_ = 1;
                $paid_ = $object->paid;
                $scanned_ = 0;
                
                $stmt->execute();
                $stmt->close();
                
                return true;
            }
            
            return false;
        }

        //Function to retrieve Order from database
        //by ID (int) or PaymentID (string)
        //:param id: ID (int) or PaymentID (string) of object to retrieve
        //:return Order: Returns the order matching id
        public function get($id) {
            $stmt = $this->conn->prepare("SELECT ID, PaymentID, firstname, lastname, email, birthdate, accepted_agreements, paid, scanned FROM orders WHERE ID=?");
            if (is_int($id)) {
                //If param is int -> retrieve by ID
                $stmt = $this->conn->prepare("SELECT ID, PaymentID, firstname, lastname, email, birthdate, accepted_agreements, paid, scanned FROM orders WHERE ID=?");
                $stmt->bind_param("i", $ID_);
            } else if (is_string($id)) {
                //Else if param is string -> retrieve by PaymentID
                $stmt = $this->conn->prepare("SELECT ID, PaymentID, firstname, lastname, email, birthdate, accepted_agreements, paid, scanned FROM orders WHERE PaymentID=?");
                $stmt->bind_param("s", $ID_);
            } else {
                //Else return false
                return false;
            }
           
            //Get from database
            $stmt->execute();
            $stmt->bind_result($ID, $paymentID, $firstname, $lastname, $email, $birthdate, $accepted_agreements, $paid, $scanned);
            $stmt->fetch();
            
            //Create Order-object with data from database
            $order = new Order();
            $order->ID = $ID;
            $order->paymentID = $paymentID;
            $order->firstname = $firstname;
            $order->lastname = $lastname;
            $order->email = $email;
            $order->birthdate = $birthdate;
            $order->accepted_agreements = $accepted_agreements;
            $order->paid = $paid;
            $order->scanned = $scanned;
            
            $stmt->close();
            return $order;
        }

        //Function to delete from database by ID (int) or PaymentID (string)
        //:param id: ID (int) or PaymentID (string) of object to delete
        //:return bool: Returns true if succesful
        public function delete($id) {
            $stmt = $this->conn->prepare("DELETE FROM ".htmlspecialchars($this->table)." WHERE ID=?");
            if (is_int($id)) {
                //If param is int -> delete by ID
                $stmt = $this->conn->prepare("DELETE FROM ".htmlspecialchars($this->table)." WHERE ID=?");
                $stmt->bind_param("i", $ID_);
                $ID_ = $id;
            } else if (is_string($id)) {
                //If param is string -> delete by PaymentID
                $stmt = $this->conn->prepare("DELETE FROM orders WHERE PaymentID=?");
                $stmt->bind_param("s", $ID_);
                $ID_ = $id;
            } else {
                //Else return false
                return false;
            }


            //Delete from database
            $stmt->execute();
            $stmt->close();

            return true;
        }

        //Function to update/change data in database
        public function update($id, $data) {
            //Query
            $query = "UPDATE " . $this->table . " SET ";

            //Loop over array (data requiring update and new value)
            foreach ($data as $key => $value) {
                $query .= $key . " = '" . $value . "',";
            }

            //Delete last comma in query-string
            $query = substr($query, 0, -1);

            //Query condition
            $bind = "i";
            if (is_int($id)) {
                $query .= " WHERE ID=?";
                $bind = "i";
            } else if (is_string($id)) {
                $query .= " WHERE PaymentID=?";
                $bind = "s";
            } else {
                return false;
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param($bind, $ID_);
            $ID_ = $id;

            //Update database
            $stmt->execute();
            $stmt->close();

            return true;
        }

        //Function to get the lastID
        //:return int: Latest ID in table
        public function lastID() {
            $result = $this->conn->query("SELECT MAX(ID) FROM orders");
            $row = $result->fetch_assoc();
            return (int)$row['MAX(ID)'];
        }

        //Function to close database connection
        //:return bool: Returns true on success
        public function close() {
            $this->conn->close();
            return true;
        }
        
        //Function to select specific column value matching the PaymentID
        //:param id: PaymentID to check for
        //:param column: Column to check value
        //:return value: Value of column matching PaymentID, also false on failure
        public function select($id, $column) {
            //Check if columm is valid
            if (!in_array($column, $this->allowed_columns))
                return false;
        
            $stmt = $this->conn->prepare("SELECT " . $column . " FROM " . $this->table . " WHERE PaymentID=?");
            $stmt->bind_param("s", $ID_);
            $ID_ = $id;
            $stmt->execute();
            
            $stmt->bind_result($value);
            $stmt->fetch();
            $rvalue = $value;
            $stmt->close();
            
            return $rvalue;
        }

        //Properties
        public $conn = "";
        public $table = "orders";
        public $allowed_tables = array(
            'orders'
        );
        public $allowed_columns = array(
            'ID',
            'PaymentID',
            'paid',
            'scanned',
            'hash'
        );
    }


