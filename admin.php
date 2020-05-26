<?php

// execute the header script:
require_once "header.php";

// admin tools set to not show
$show_users = false;
$show_update = false;
$show_delete = false;
$show_signup_form = false;
// default values to show in the update view
$username = "";
$password = "";
$first_name = "";
$surname = "";
$email = "";
$dob = "";
$telephone = "";
// strings to hold any validation error messages:
$username_val = "";
$password_val = "";
$first_name_val = "";
$surname_val = "";
$email_val = "";
$dob_val = "";
$telephone_val = "";

// message to output to admin:
$message = "";

// admin isn't logged in, display a message saying they must be:
if (!isset($_SESSION['loggedInSkeleton'])) {
	echo "You must be logged in to view this page.<br>";
}

// admin just tried to view a profile
elseif(isset($_GET['view'])) {
	// gets the username from $_GET and stores it in $username
	$username = $_GET['view'];

	// connect directly to our database (notice 4th argument):
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

	// if the connection fails, we need to know, so allow this exit:
	if (!$connection) {
		die("Connection failed: " . $mysqli_connect_error);
	}

	// selects all users from the database
	$query = "SELECT * FROM users WHERE username = '$username'";
	
	// executes the query and stores the result in $result
	$result = mysqli_query($connection, $query);

	// number of rows returned from the query
	$n = mysqli_num_rows($result);

	// displays user information table
	echo "<table>";
	for($i = 0; $i < $n; $i++) {
		$row = mysqli_fetch_assoc($result);
	}
	echo" 
	<tr> <td> Username </td> <td> {$row['username']} </td> <tr>
	<tr> <td> Password </td> <td> {$row['password']} </td> <tr>
	<tr> <td> First Name </td> <td> {$row['first_name']} </td> <tr>
	<tr> <td> Surname </td> <td> {$row['surname']} </td> <tr>
	<tr> <td> Email </td> <td> {$row['email']} </td> <tr>
	<tr> <td> Date Of Birth </td> <td> {$row['dob']} </td> <tr>
	<tr> <td> Telephone </td> <td> {$row['telephone']} </td> <tr>
	";
	echo "</table>";

	// we're finished with the database, close the connection:
	mysqli_close($connection);
}

// admin just clicked on update button
elseif(isset($_GET['update'])) {
	// show the update table
	$show_update = true;

	// gets the username from get method
	$username = $_GET['update'];
	
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
		//$password = $row['password'];
		$password = "";

	}

	// we're finished with the database, close the connection:
	mysqli_close($connection);

}

// user just clicked on delete button
elseif(isset($_GET['delete'])) {
	// show the delete table
	$show_delete = true;

	// gets username from get method
	$username = $_GET['delete'];

	// connect directly to our database (notice 4th argument):
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	
	// if the connection fails, we need to know, so allow this exit:
	if (!$connection) {
		die("Connection failed: " . $mysqli_connect_error);
	}
	
	// check if username is admin
	// if username is not admin, proceed with deletion of profile
	if ($username !== "admin") {
		$query = "DELETE FROM users WHERE username = '$username'";
		
		// this query can return data ($result is an identifier):
		$result = mysqli_query($connection, $query);

		// no data returned, we just test for true(success)/false(failure):

		// DELETION SUCESS!
		if($result) {
			$message = "Sucesfully deleted profile <br>
			Please <a href='admin.php'> click here</a><br>";
		}
		// DELETION FAILURE!
		else {
			$message = "Error deleting profile <br>
			Please <a href='admin.php'> click here</a><br>";

		}		
	}

	// display a message saying that the admin account cannot be deleted
	else {
		$message = "Admin profile may not be deleted! <br>
		Please <a href='admin.php'> click here</a><br>";
	}

	// we're finished with the database, close the connection:
	mysqli_close($connection);
}

// user just clicked on "create a new user profile" button
elseif(isset($_GET['create'])) {
	$show_signup_form = true;
}

// user just tried to update profile
elseif(isset($_POST['update'])) {

	// connect directly to our database (notice 4th argument):
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	
	// extracts username from session and stores it in $username 
	$username = $_POST['update_username'];

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
	$password = sanitise($_POST['password'], $connection);

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
	
	// allows user to not be required to enter a password
	if($password == "") {
		$password_val = "";
	}
	else {
		$password_val = validatePassword($password, 4, 62);
	}
	
	// allows user to not be required to enter a date of birth
	if($dob == "") {
		$dob_val = "";
	}
	else {
		$dob_val = validateDate($dob);
	}
	
	// stores all server side validation returns in $errors; error if something is returned
	$errors = $first_name_val . $surname_val . $email_val . $dob_val . $telephone_val . $password_val;
	
	// check that all the validation tests passed before going to the database:
	if($errors == "") {

		// selects all data from current user
		$query = "SELECT * FROM users WHERE username='$username'";
		
		// executes the query and stores the result in $result
		$result = mysqli_query($connection, $query);

		// number of rows returned from the query
		$n = mysqli_num_rows($result);
		if ($n > 0) {
			// dob update included in the query if dob data is present
			// this is to prevent the database to inserting "0000-00-00" in the database after the dob field being null
			if($dob !== "") {
				// password update included in the query if password data is present
				// this is to prevent the database from inserting null into the database after the pw field left empty and replacing current pw
				if($password !== "") {
					$query = "UPDATE users SET first_name = '$first_name', surname = '$surname', email='$email', dob = '$dob', telephone = '$telephone', password = '$password' WHERE username= '$username' ";
				}
				else {
					$query = "UPDATE users SET first_name = '$first_name', surname = '$surname', email='$email', dob = '$dob', telephone = '$telephone' WHERE username= '$username' ";
				}
			}
			// query does not include dob as there is not dob data to insert/update
			else {
				// password update included in the query if password data is present
				// this is to prevent the database from inserting null into the database after the pw field left empty and replacing current pw
				if($password !== "") {
					$query = "UPDATE users SET first_name = '$first_name', surname = '$surname', email='$email', telephone = '$telephone', password = '$password' WHERE username='$username'";
				}
				else {
					$query = "UPDATE users SET first_name = '$first_name', surname = '$surname', email='$email', telephone = '$telephone' WHERE username='$username'";
				}
			}
			$result = mysqli_query($connection, $query);
		}

		// no data returned, we just test for true(success)/false(failure):
		if ($result) {
			// show a successful update message:
			$message = "Profile successfully updated<br>
			You have successfully updated the profile, please <a href='admin.php'> click here</a><br>";
			
		} 
		else {
			// show the set profile form:
			$show_update = true;
			// show an unsuccessful update message:
			$message = "Update failed<br>";
		}
	}
	
	// validation failed, show the form again with guidance:
	else {
		$show_update = true;
		// show an unsuccessful update message:
		$message = "Update failed, please check the errors above and try again<br>";
	}

	// we're finished with the database, close the connection:
	mysqli_close($connection);
}


elseif(isset($_POST['username'])) {
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
	$password_val = validatePassword($password, 4, 16);

	// converts username to lowercase
	//$username = strtolower($username);

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
				$message = "<br>Succesfully created a new user profile<br>
				Please <a href='admin.php'> click here</a><br>";
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
	
	// we're finished with the database, close the connection:
	mysqli_close($connection);
}

// user just arrived at page for the first time
else {
	// only display the page content if this is the admin account 
	if ($_SESSION['username'] == "admin") {
		// display users table
		$show_users = true;

		// connect directly to our database (notice 4th argument):
		$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
		
		// if the connection fails, we need to know, so allow this exit:
		if (!$connection) {
			die("Connection failed: " . $mysqli_connect_error);
		}
		
		// selects all users from the database
		$query = "SELECT username FROM users";

		// executes the query and stores the result in $result
		$result = mysqli_query($connection, $query);
		
		// number of rows returned from the query
		$n = mysqli_num_rows($result);
	}
	// user is not admin, displays permission message
	else {
		echo "You don't have permission to view this page...<br>";
	}
}


// displays list of users stored in the database and admin tools
if ($show_users) {
	echo "<table>";
	echo "<tr><th> Username </th>  <th>View Profile</th> <th> Update Profile </th> <th> Delete Profile </th>  </tr>";
	for ($i = 0; $i < $n; $i++) {
		$row = mysqli_fetch_assoc($result);
		echo "<tr>";
		echo" 
		<form action=\"admin.php\" method=\"get\">
		<td>{$row['username']}</td>
		<td> <button type=\"submit\" name=\"view\" value=\"{$row['username']}\">View Profile</button> </td>
		<td> <button type=\"submit\" name=\"update\" value=\"{$row['username']}\">Update Profile</button> </td>
		<td> <button type=\"submit\" name=\"delete\" value=\"{$row['username']}\">Delete Profile</button> </td>";
	}
	echo "</table>";
	echo "<p> Create new user profile </p>
	<button type =\"submit\" name=\"create\">Create New Profile </button> </form> <br>";
}

// show the update table
if ($show_update) {
	echo <<<_END

	<form action="admin.php" method="post">
	<table>
	
	<tr> 
	<td> Username </td> 
	<td> $username </td> 
	</tr>

	<tr> 
	<td> Password </td> 
	<td> <input type="password" name="password" value="$password"> $password_val </td> 
	</tr>
	
	<tr> 
	<td> First Name </td> 
	<td> <input type="text" name="first_name" value="$first_name" maxlength="16"> $first_name_val </td>
	</tr>
	
	<tr> 
	<td> Surname </td> 
	<td> <input type="text" name="surname" value="$surname" maxlength="16"> $surname_val </td>
	</tr>
	
	<tr> 
	<td> Date of Birth </td> 
	<td> <input type="date" name="dob" value="$dob"> $dob_val </td> 
	</tr>
	
	<tr> 
	<td> Email </td> 
	<td> <input type="email" name="email" value="$email" maxlength="64"> $email_val </td> 
	</tr>

	<tr> <td> Telephone </td> <td> 
	<input type="tel" name="telephone" value="$telephone" maxlength="12"> $telephone_val </td> 
	</tr>	

	</table>
	<br>

	<input type='hidden' name='update_username' value='$username'>
	<input type="submit" name="update" value="Submit">

_END;
}
// shows the form to register a new user profile
if ($show_signup_form) {
	echo <<<_END
	<form action="admin.php" method="post">
	Please choose a username and password: <br><br>
	Username: <br>
	<input type="text" name="username" maxlength="16" value="$username" required> $username_val <br>
	Password: <br>
	<input type="password" name="password" maxlength="16" value="$password" required> $password_val <br> <br>
	<input type="submit" value="Submit">
	</form>
	<br>
_END;
}

echo $message;

// finish off the HTML for this page:
require_once "footer.php";
?>