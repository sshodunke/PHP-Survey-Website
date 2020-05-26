<?php

// execute the header script:
require_once "header.php";

// default values we show in the form:
$username = "";
$password = "";
// strings to hold any validation error messages:
$username_val = "";
$password_val = "";
// should we show the signup form?:
$show_signup_form = false;
// message to output to user:
$message = "";

// user is already logged in, just display a message:
if (isset($_SESSION['loggedInSkeleton'])) {
	echo "You are already logged in, please log out if you wish to create a new account<br>";
}

// user just tried to sign up:
elseif (isset($_POST['username'])) {
	// connect directly to our database (notice 4th argument) we need the connection for sanitisation:
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	// if the connection fails, we need to know, so allow this exit:
	if (!$connection) {
		die("Connection failed: " . $mysqli_connect_error);
	}
	
	// SANITISATION (see helper.php for the function definition)
	$username = sanitise($_POST['username'], $connection);
	$password = sanitise($_POST['password'], $connection);

	// VALIDATION (see helper.php for the function definitions)
	$username_val = validateUsername($username, 3, 16);
	$password_val = validatePassword($password, 4, 32);

	// concatenate all the validation results together ($errors will only be empty if ALL the data is valid):
	$errors = $username_val . $password_val;
	
	// check that all the validation tests passed before going to the database:
	if ($errors == "") {
		// check if username already exists in the database, if row is returned then a user exists
		$checkUsername = mysqli_query($connection, "SELECT username FROM users WHERE username = '$username' ");
		if (mysqli_num_rows($checkUsername) >= 1) {
			$show_signup_form = true;
			// shows username taken message
			$message = "<br>Error, username already taken<br>";
		}

		// tries to register new user and insert into database
		else {
			// try to insert the new details into database
			$query = "INSERT INTO users (username, password) VALUES ('$username', '$password');";
			$result = mysqli_query($connection, $query);
			
			// no data returned, we just test for true(success)/false(failure):

			// SIGN UP SUCESS!!
			if ($result) {
				$message = "<br>Signup was successful, please sign in<br>";
			}
			// SIGN UP UNSUCESSFUL
			else {
				$show_signup_form = true;
				$message = "<br>Sign up failed, please try again<br>";
			}
		}	
	}

	// validation test has failed
	else {
		// validation failed, show the form again with guidance:
		$show_signup_form = true;
		// show an unsuccessful signin message:
		$message = "<br>Sign up failed, please check the errors shown above and try again<br>";
	}
	
	// we're finished with the database, close the connection
	mysqli_close($connection);
}

// just a normal visit to the page, show the signup form:
else {
	$show_signup_form = true;
}

// show the form that allows users to sign up
if ($show_signup_form) {
echo <<<_END
	<form action="sign_up.php" method="post">
	Please choose a username and password: <br><br>
	Username: <br>
	<input type="text" name="username" maxlength="16" value="$username" required> $username_val <br>
	Password: <br>
	<input type="password" name="password" maxlength="32" value="$password" required> $password_val <br> <br>
	<input type="submit" value="Submit">
	</form>
	<br>
_END;
}

// display our message to the user:
echo $message;
//$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

// finish off the HTML for this page:
require_once "footer.php";

?>