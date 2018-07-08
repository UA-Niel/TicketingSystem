<?php
    //Implementation file for utils

    //Display error
    //:param msg: Error message to output
    function error($msg) {
        echo "<div class=\"error\"><b>Foutmelding: </b>" . htmlspecialchars($msg) . "</div>";
    }

    //Validate if date-string is actually a date
    //:param date: Date-string to validate
    //:param format: Format the date is supposed to be in
    //:return bool: Returns true if valid
    function validateDate($date, $format = 'Y-m-d H:i:s') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    //Function to generate the secure hash
    //:param name: Name of the user to hash for
    //:paran id: ID of the user to hash for
    //:return hash: Return the BCrypt generated hash
    function generateHash($name, $id) {
        $str = "";

        //Generate random number
        do {
            $random = openssl_random_pseudo_bytes(16, $strong);
        } while(!$strong);
        $str .= bin2hex($random);

        //Get timestamp
        $date = new DateTime();
        $str .= $date->getTimestamp();

        //Add id and name
        $str .= $id . $name;

        //Hash the string and return
        return password_hash($str, PASSWORD_DEFAULT);
    }

    //Function to convert a letter to it's order in the alphabet (A = 1, ..., Z = 26)
    //:param letter: Letter to get order in alphabet from 
    //:return int: Order of alphabet (0 on failure)
    function letterToNumber($letter) {
        $alphabet = "abcdefghijklmnopqrstuvwxyz";
        
        for ($i = 0; $i < strlen($alphabet); $i++) {
            if ($alphabet[$i] == strtolower($letter)) {
                return $i+1;
            }
        }
        
        return 0;
    }
    
    //Function to make a cipher two-digit (e,g: 5 -> 05)
    //:param i: Cipher to convert to digit
    //:return str: Two-digit string
    function twoDigit($i) {
        if (strlen($i) == 1 && is_numeric($i)) {
            return "0" . $i;
        }
        
        return $i;
    }
    
    //Function to make a cipher four-digit (e,g: 5 -> 0005)
    //:param i: Cipher to convert to digit
    //:return str: Two-digit string
    function fourDigit($i) {
    
        if (strlen($i) >= 4)
            return $i;
    
        while (strlen($i) < 4) {
            $i = "0" . $i;
        }
        
        return $i;
    }
    
    //Function to generate pseudo-code on tickets
    //:param order: Order to generate pseudo-code for
    //:return str: String containing the pseudo-code
    function pseudoCode($order) {
        $str = "";
    
        //Get date
        $str .= date('Y');
        $str .= "-";
        
        //First letters
        $str .= twoDigit(letterToNumber($order->user->firstname[0]));
        $str .= twoDigit(letterToNumber($order->user->lastname[0]));
        $str .= "-";
        
        //ID
        $str .= fourDigit($order->id);
        
        return $str;
    }
