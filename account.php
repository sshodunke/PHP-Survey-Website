<?php

// execute the header script:
require_once "header.php";

// default values we show in the form:
$first_name = "";
$surname = "";
$email = "";
$dob = "";
$telephone = "";
// strings to hold any validation error messages:
$first_name_val = "";
$surname_val = "";
$email_val = "";
$dob_val = "";
$telephone_val = "";
// should we show the set profile form?:
$show_account_form = false;
// message to output to user:
$message = "";

// user isn't logged in, display a message saying they must be:
if (!isset($_SESSION['loggedInSkeleton'])) {
	echo "You must be logged in to view this page.<br>";
}

// user just tried to update their profile
elseif (isset($_POST['email'])) {
	// connect directly to our database (notice 4th argument) we need the connection for sanitisation:
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	// if the connection fails, we need to know, so allow this exit:
	if (!$connection) {
		die("Connection failed: " . $mysqli_connect_error);
	}
	
	// SANITISATION (see helper.php for the function definition)
	$first_name = sanitise($_POST['first_name'], $connection);
	$surname = sanitise($_POST['surname'], $connection);
	$email = sanitise($_POST['email'], $connection);
	$dob = sanitise($_POST['dob'], $connection);
	$telephone = sanitise($_POST['telephone'], $connection);

	// VALIDATION (see helper.php for the function definitions)
	// allows user to not be required to enter a first name
	if ($first_name == "") {
		$first_name_val = "";
	}
	else {
		$first_name_val = validateString($first_name, 2, 16);
	}
	
	// allows user to not be required to enter a surnames
	if ($surname == "") {
		$surname_val = "";
	}
	else {
		$surname_val = validateString($surname, 2, 16);
	}
	
	// allows user to not be required to enter an email address
	if ($email == "") {
		$email_val = "";
	}
	else {
		$email_val = validateEmail($email, 6, 64);
	}
	
	// allows user to not be required to enter a telephone number
	if($telephone == "") {
		$telephone_val = "";
	}
	else {
		$telephone_val = validatePhone($telephone, 6, 12);
	}
	
	// allows user to not be required to enter a date of birth
	if($dob == "") {
		$dob_val = "";
	}
	else {
		$dob_val = validateDate($dob);
	}
	
	// concatenate all the validation results together ($errors will only be empty if ALL the data is valid):
	$errors = $first_name_val . $surname_val . $email_val . $dob_val . $telephone_val;
	
	// check that all the validation tests passed before going to the database:
	if ($errors == "") {		
		// read their username from the session:
		$username = $_SESSION["username"];
				
		// check for a row in our profiles table with a matching username:
		$query = "SELECT * FROM users WHERE username='$username'";
		
		// this query can return data ($result is an identifier):
		$result = mysqli_query($connection, $query);
		
		// how many rows came back? (can only be 1 or 0 because username is the primary key in our table):
		$n = mysqli_num_rows($result);
			
		// if there was a match then UPDATE their profile data
		if ($n > 0) {
			// dob update included in the query if dob data is present
			if($dob !== "") {
				$query = "UPDATE users SET first_name = '$first_name', surname = '$surname', email='$email', dob = '$dob', telephone = '$telephone' WHERE username='$username'";
			}
			// query does not include dob as there is not dob data to insert/update
			else {
				$query = "UPDATE users SET first_name = '$first_name', surname = '$surname', email='$email', telephone = '$telephone' WHERE username='$username'";
			}
			$result = mysqli_query($connection, $query);
		}
	
		// no data returned, we just test for true(success)/false(failure):
		if ($result) {
			// show a successful update message:
			$message = "Profile successfully updated<br>";
		} 
		else {
			// show the set profile form:
			$show_account_form = true;
			// show an unsuccessful update message:
			$message = "Update failed<br>";
		}
	}

	// validation failed, show the form again with guidance:
	else {
		$show_account_form = true;
		// show an unsuccessful update message:
		$message = "Update failed, please check the errors above and try again<br>";
	}
	
	// we're finished with the database, close the connection:
	mysqli_close($connection);
}

// user has arrived at the page for the first time, show any data already in the table:
else {
	// read the username from the session:
	$username = $_SESSION["username"];
		
	// connect directly to our database (notice 4th argument):
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	
	// if the connection fails, we need to know, so allow this exit:
	if (!$connection) {
		die("Connection failed: " . $mysqli_connect_error);
	}
	
	// check for a row in our profiles table with a matching username:
	$query = "SELECT * FROM users WHERE username='$username'";
	
	// this query can return data ($result is an identifier):
	$result = mysqli_query($connection, $query);
	
	// how many rows came back? (can only be 1 or 0 because username is the primary key in our table):
	$n = mysqli_num_rows($result);
		
	// if there was a match then extract their profile data:
	if ($n > 0) {
		// use the identifier to fetch one row as an associative array (elements named after columns):
		$row = mysqli_fetch_assoc($result);
		// extract their profile data for use in the HTML:
		$first_name = $row['first_name'];
		$surname = $row['surname'];
		$email = $row['email'];
		$dob = $row['dob'];
		$telephone = $row['telephone'];
	}
	
	// show the set profile form:
	$show_account_form = true;
	
	// we're finished with the database, close the connection:
	mysqli_close($connection);
	
}

// show the form that allows the user to update the logged in profile
if ($show_account_form) {
echo <<<_END

	<form action="account.php" method="post">
	Update your profile info:<br> <br>
	Username: {$_SESSION['username']} <br>
	<br>
	
	First Name: <br>
	<input type="text" name="first_name" maxlength="16" value="$first_name"> $first_name_val
	<br>
	
	Surname: <br>
	<input type="text" name="surname" maxlength="16" value="$surname"> $surname_val
	<br>
	
	Email Address: <br>
	<input type="email" name="email" maxlength="64" value="$email"> $email_val
	<br>
	
	Date of Birth: <br>
	<input type="date" name="dob" value="$dob"> $dob_val
	<br>
	
	Telephone: <br>
	<input type="tel" name="telephone" maxlength="12" value="$telephone"> $telephone_val
	<br> <br>

	<input type="submit" value="Submit">
	</form>
	<br>
_END;
}

// display our message to the user:
echo $message;

// finish of the HTML for this page:
require_once "footer.php";
?>