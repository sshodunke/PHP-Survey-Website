<?php

// execute the header script:
require_once "header.php";

// default values we show in the form:
$username = "";
$password = "";
// strings to hold any validation error messages:
$username_val = "";
$password_val = "";
// should we show the signin form:
$show_signin_form = false;
// message to output to user:
$message = "";

// user is already logged in, just display a message:
if (isset($_SESSION['loggedInSkeleton'])) {
	echo "You are already logged in, please log out first.<br>";
}

// user has just tried to log in:
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
	$username_val = validateString($username, 1, 16);
	$password_val = validateString($password, 1, 16);
	
	// concatenate all the validation results together ($errors will only be empty if ALL the data is valid):
	$errors = $username_val . $password_val;
	
	// check that all the validation tests passed before going to the database:
	if ($errors == "") {
		// query checks if username and password are in database
		$testLogin = mysqli_query($connection, "SELECT username FROM users WHERE username = '$username'  AND password = '$password'");
		
		// if query returns a row then a username and password match was found
		if (mysqli_num_rows($testLogin) > 0) {
			$n = 1;	
		}
		else {
			$n = 0;
		}
			
		// if there was a match then set the session variables and display a success message:
		if ($n > 0) {
			// set a session variable to record that this user has successfully logged in:
			$_SESSION['loggedInSkeleton'] = true;
			// and copy their username into the session data for use by our other scripts:
			$_SESSION['username'] = $username;
			
			// show a successful signin message:
			$message = "Hi, $username, you have successfully logged in, please <a href='account.php'>click here</a><br>";
		}

		// no match found, decides which error message to dispalay
		else {
			// query checks if username inputted by user is in the database
			$testUsername = mysqli_query($connection, "SELECT username FROM users WHERE username = '$username'");

			// if query returns a row then the username is in database
			if (mysqli_num_rows($testUsername) > 0) {
				// fake a match with the database table:
				$n = 1;	
			}
			else {
				$n = 0;
			}

			// if a row is returned then the username is in the database
			// decide where message to display to the user
			if ($n > 0) {
				$message = "Sign in failed; Password is incorrect. <br>";
			}
			else {
				$message = "Sign in failed; Username not found. <br>";
			}
			
			// no matching credentials found so redisplay the signin form with a failure message:
			$show_signin_form = true;
		}
		
	}
	
	// validation failed, show the form again with guidance:
	else {
		$show_signin_form = true;
		// show an unsuccessful signin message:
		$message = "Sign in failed, please check the errors shown above and try again<br>";
	}
	
	// we're finished with the database, close the connection:
	mysqli_close($connection);

}

// user has arrived at the page for the first time, just show them the form:
else {
	$show_signin_form = true;
}

// show the form that allows users to log in
if ($show_signin_form) {
// Note we use an HTTP POST request to avoid their password appearing in the URL:
echo <<<_END
	<form action="sign_in.php" method="post">
	Please enter your username and password: <br> <br>
	Username: <br> 
	<input type="text" name="username" maxlength="16" value="$username" required> $username_val <br>
	Password: <br>
	<input type="password" name="password" maxlength="16" value="$password" required> $password_val <br> <br>
	<input type="submit" value="Submit"> 
	</form>	
	<br>
_END;
}

// display our message to the user:
echo $message;

// finish off the HTML for this page:
require_once "footer.php";
?>