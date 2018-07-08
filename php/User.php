<?php
    //Class-file for User
    class User {
        //Constructor
        function __construct() {
            $this->firstname = "";
            $this->lastname = "";
            $this->email = "";
            $this->birthdate = "";
            $this->accepted = false;
        }

        //Functions
        //Check if user is valid
        //:return bool: Returns true if valid
        function isValid() {
            if ($this->firstname == "") {
                return false;
            } else if ($this->lastname == "") {
                return false;
            } else if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                return false;
            } else if ($this->birthdate == "" || validateDate($this->birthdate)) {
                return false;
            } else if ($this->accepted == false ) {
                return false;
            } else {
                return true;
            }
        }

        //Function to calculate age of user (-16, +16, or +18)
        //:return age: Age-categorie of user (empty on error)
        function getAge() {
            $age = date_diff(date_create($this->birthdate), date_create('now'))->y;
            
            if ($age < 16) {
                return "-16";
            } else if ($age >= 16 && $age < 18) {
                return "+16";
            } else if ($age >= 18) {
                return "+18";
            } else {
                return "";
            }
        }
        
        //Properties
        public $firstname = "";
        public $lastname = "";
        public $email = "";
        public $birthdate = "";
        public $accepted = false;
    }
