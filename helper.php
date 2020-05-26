<?php

// function to sanitise (clean) user data:
function sanitise($str, $connection) {
	if (get_magic_quotes_gpc()) {
		// just in case server is running an old version of PHP with "magic quotes" running:
		$str = stripslashes($str);
	}
	// escape any dangerous characters, e.g. quotes:
	$str = mysqli_real_escape_string($connection, $str);
	// ensure any html code is safe by converting reserved characters to entities:
	$str = htmlentities($str);
	// return the cleaned string:
	return $str;
}

function validateString($field, $minlength, $maxlength) {
    if (strlen($field)<$minlength) {
		// wasn't a valid length, return a help message:		
        return "Minimum length: " . $minlength; 
    }
	elseif (strlen($field)>$maxlength) { 
		// wasn't a valid length, return a help message:
        return "Maximum length: " . $maxlength; 
    }
	// data was valid, return an empty string:
    return ""; 
}

function validatePassword($field, $minlength, $maxlength) {
    if (strlen($field)<$minlength) {
		// wasn't a valid length, return a help message:		
        return "Password too short. Minimum length: " . $minlength; 
	}

	// preg match to validate password

	// checks if password contains a number
	// '[0-9]' must include a number between 0 and 9
	// '+' must include one or more of [0-9] in $field
	if (!preg_match("/[0-9]+/", $field)) {
		return "Password must include a number";
	}

	// checks if password contains a letter
	// '[a-zA-Z]' must include a single character between a-z or A-Z
	// '+' must include one or more of [a-zA-Z] in $field
	if (!preg_match("/[a-zA-Z]+/", $field)) {
		return "Password must include a letter";
	}
	
	// checks if password contains an uppercase letter
	// '[A-Z]' must include a single character between A-Z(lowecase not included)
	// '+' must include one or more of '[A-Z]' in $field
	if (!preg_match("/[A-Z]+/", $field)) {
		return "Password must include an uppercase letter";
	}
	
	elseif (strlen($field)>$maxlength) { 
		// wasn't a valid length, return a help message:
        return "Password too long. Maximum length: " . $maxlength; 
    }
	// data was valid, return an empty string:
    return ""; 
}

function validateUsername($field, $minlength, $maxlength) {
	// gets length of string and checks against variables
    if (strlen($field)<$minlength) {
		// wasn't a valid length, return a help message:		
        return "Minimum length: " . $minlength; 
    }
	elseif (strlen($field)>$maxlength) { 
		// wasn't a valid length, return a help message:
        return "Maximum length: " . $maxlength; 
	}
	
	if (strlen(trim($field)) <= 0) {
		// checks if whitespace is in the username field
		return "Error, whitespace forbidden, please enter a valid input";
	}
	// data was valid, return an empty string:
    return ""; 
}

function validateInt($field, $min, $max) { 
	// see PHP manual for more info on the options: http://php.net/manual/en/function.filter-var.php
	$options = array("options" => array("min_range"=>$min,"max_range"=>$max));
    
	if (!empty($field) && !filter_var($field, FILTER_VALIDATE_INT, $options)) { 
		// wasn't a valid integer, return a help message:
		return "Not a valid number (must be whole and in the range: " . $min . " to " . $max . ")";
    }
	// data was valid, return an empty string:
    return ""; 
}

function validateEmail($field, $min, $max){
	
	// filter, sets min_range to min, max_range to max
	$options = array("options" => array("min_range"=>$min,"max_range"=>$max));

	// puts $field through a email filter and the $options filter
	// returns error if $field could not pass filters
	if(!filter_var($field, FILTER_VALIDATE_EMAIL, $options)) {
		return "Not a valid email";
	}
	return "";
}

function validateDate($field) {
	// preg match to validate date
	// '^' start of line, '&' end of line
	// [0-9] Any number between 0-9 - {4} exactly of 4 eg. 2012 followed by a '-'
	
	// (...) capture everything inside here
	// '0[1-9] number 0 followed by a number between 1 and 9 eg. 08,03,02
	// '|' stands for OR
	// '1[0-2] number 1 followed by a number between 0 and 2 eg, 10,11,12
	// (0[1-9] OR 1[0-2])

	// continues...

	if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$field)){
		// if $field contains the string '-' then the $field is split by it and an array is returned
		$date_test = explode('-', $field);
		// checks if the returned array is a valid date by using checkdate function
		if (checkdate($date_test[1], $date_test[2], $date_test[0])) {
			return "";
		}
		else {
			return "Invalid date, enter date in YYYY-MM-DD format";
		}
	}
	else {
		return "Invalid date, enter date in YYYY-MM-DD format";
	}
}

function validatePhone($field, $min, $max) {
	// preg match checks to validate phoe number
	// '\d' stands for 'Any Digit'
	// { min,max} between min and max
	// checks for any digit between min and max
	if (preg_match("/\d{" . $min . "," . $max . "}/", $field)) {
		return "";
	}

	// gets length of string and checks against variables
    if (strlen($field)<$min) {
		// wasn't a valid length, return a help message:		
        return "Minimum length: " . $min; 
    }
	elseif (strlen($field)>$max) { 
		// wasn't a valid length, return a help message:
        return "Maximum length: " . $max; 
	}
	else {
		return "Invalid telephone number";
	}
}

?>