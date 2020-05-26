<?php

// Create suitable test data for each of those tables 
// NOTE: this last one is VERY IMPORTANT - you need to include test data that enables the markers to test all of your site's functionality

// read in the details of our MySQL server:
require_once "credentials.php";

// connect to the host:
$connection = mysqli_connect($dbhost, $dbuser, $dbpass);
// exit the script with a useful message if there was an error:
if (!$connection) {
	die("Connection failed: " . $mysqli_connect_error);
}
  
// build a statement to create a new database:
$sql = "CREATE DATABASE IF NOT EXISTS " . $dbname;

// no data returned, we just test for true(success)/false(failure):
if (mysqli_query($connection, $sql)) {
	echo "Database created successfully, or already exists<br>";
} 
else {
	die("Error creating database: " . mysqli_error($connection));
}

// connect to our database:
mysqli_select_db($connection, $dbname);


/* --------------------------------------- USERS TABLE ---------------------------------------  */


// if there's an old version of our table, then drop it:
$sql = "DROP TABLE IF EXISTS users";

// no data returned, we just test for true(success)/false(failure):
if (mysqli_query($connection, $sql)) {
	echo "Dropped existing table: users<br>";
}
else {
	die("Error checking for existing table: " . mysqli_error($connection));
}

// make USER table:
$sql = 
	"CREATE TABLE users (
		username VARCHAR(16) PRIMARY KEY, 
		password VARCHAR(32), 
		email VARCHAR(64), 
		dob DATE, 
		telephone VARCHAR(16), 
		first_name VARCHAR(16), 
		surname VARCHAR(24)
	)";

// no data returned, we just test for true(success)/false(failure):
if (mysqli_query($connection, $sql)) {
	echo "Table created successfully: users<br>";
}
else {
	die("Error creating table: " . mysqli_error($connection));
}

// put some data in our table:
$usernames[] = 'barrym'; $passwords[] = 'letmein'; $emails[] = 'barry@m-domain.com'; $dob[] = '1992-10-04'; $telephone[] = '07425152689'; $firstname[] = 'barry'; $lastname[] = 'mmm';
$usernames[] = 'mandyb'; $passwords[] = 'abc123'; $emails[] = 'webmaster@mandy-g.co.uk'; $dob[] = '1996-10-8'; $telephone[] = '07425133689'; $firstname[] = 'mandy'; $lastname[] = 'bbb';
$usernames[] = 'timmy'; $passwords[] = 'secret95'; $emails[] = 'timmy@lassie.com'; $dob[] = '1988-10-1'; $telephone[] = '07425452689'; $firstname[] = 'timmy'; $lastname[] = 'ttttt';
$usernames[] = 'briang'; $passwords[] = 'password'; $emails[] = 'brian@quahog.gov'; $dob[] = '2000-10-22'; $telephone[] = '07422122659'; $firstname[] = 'brian'; $lastname[] = 'ggggg';
$usernames[] = 'a'; $passwords[] = 'test'; $emails[] = 'a@alphabet.test.com'; $dob[] = '2001-9-12'; $telephone[] = '07667788689'; $firstname[] = 'al'; $lastname[] = 'lastname';
$usernames[] = 'b'; $passwords[] = 'test'; $emails[] = 'b@alphabet.test.com'; $dob[] = '1998-5-11'; $telephone[] = '07425152777'; $firstname[] = 'bill'; $lastname[] = 'cl';
$usernames[] = 'c'; $passwords[] = 'test'; $emails[] = 'c@alphabet.test.com'; $dob[] = '1998-4-4'; $telephone[] = '07425152222'; $firstname[] = 'cam'; $lastname[] = 'ccamm';
$usernames[] = 'd'; $passwords[] = 'test'; $emails[] = 'd@alphabet.test.com'; $dob[] = '1997-3-4'; $telephone[] = '07425152333'; $firstname[] = 'dave'; $lastname[] = 'davelastname';
$usernames[] = 'admin'; $passwords[] = 'secret'; $emails[] = 'admin@mail.com'; $dob[] = '1995-4-4'; $telephone[] = '07425152444'; $firstname[] = 'admin'; $lastname[] = 'adminlastname';

// loop through the arrays above and add rows to the table:
for ($i=0; $i<count($usernames); $i++)
{
	$sql = "INSERT INTO users (username, password, email, dob, telephone, first_name, surname) VALUES ('$usernames[$i]', '$passwords[$i]', '$emails[$i]', DATE('$dob[$i]'), '$telephone[$i]', '$firstname[$i]', '$lastname[$i]' )";
	
	// no data returned, we just test for true(success)/false(failure):
	if (mysqli_query($connection, $sql)) 
	{
		echo "row inserted<br>";
	}
	else 
	{
		die("Error inserting row: " . mysqli_error($connection));
	}
}


/* --------------------------------------- SURVEYS TABLE ---------------------------------------    */


// if there's an old version of our table, then drop it:
$sql = "DROP TABLE IF EXISTS surveys";

// no data returned, we just test for true(success)/false(failure):
if (mysqli_query($connection, $sql)) {
	echo "Dropped existing table: surveys<br>";
} 
else {	
	die("Error checking for existing table: " . mysqli_error($connection));
}

// make survey table:
$sql = 
	"CREATE TABLE surveys (
		survey_id INT(10) AUTO_INCREMENT PRIMARY KEY, 
		survey_name VARCHAR(255),
		survey_questions INT(10),
		survey_author VARCHAR(255) NOT NULL,
		survey_responses INT(10),
		share_survey VARCHAR(255)
	)";

// no data returned, we just test for true(success)/false(failure):
if (mysqli_query($connection, $sql)) {
	echo "Table created successfully: survey<br>";
}
else {
	die("Error creating table: " . mysqli_error($connection));
}

// put some data in our table:
$survey_id[] = '1'; $survey_name[] = 'The NBA Survey'; $survey_questions[] = '4'; $survey_author[] = 'barrym'; $survey_responses[] = '9'; $share_survey[] = 'Yes';
$survey_id[] = '2'; $survey_name[] = 'Website Survey'; $survey_questions[] = '3'; $survey_author[] = 'barrym'; $survey_responses[] = '5'; $share_survey[] = 'Yes';

// loop through the arrays above and add rows to the table:
for ($i=0; $i<count($survey_id); $i++) {
	$sql = "INSERT INTO surveys (survey_id, survey_name, survey_questions,survey_author, survey_responses, share_survey) VALUES ('$survey_id[$i]', '$survey_name[$i]', '$survey_questions[$i]', '$survey_author[$i]', '$survey_responses[$i]', '$share_survey[$i]' )";
	
	// no data returned, we just test for true(success)/false(failure):
	if (mysqli_query($connection, $sql)) {
		echo "row inserted<br>";
	}
	else {
		die("Error inserting row: " . mysqli_error($connection));
	}
}

// reset arrays
$survey_id = array();


/* --------------------------------------- QUESTIONS TABLE ---------------------------------------    */


// if there's an old version of our table, then drop it:
$sql = "DROP TABLE IF EXISTS questions";

// no data returned, we just test for true(success)/false(failure):
if (mysqli_query($connection, $sql)) {
	echo "Dropped existing table: questions<br>";
} 
else {	
	die("Error checking for existing table: " . mysqli_error($connection));
}

// make questions table:
$sql = 
	"CREATE TABLE questions (
		question_id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
		survey_id INT(10), 
		question_field VARCHAR(255),
		question_type VARCHAR(255),
		mandatory VARCHAR(255)
		)";

// no data returned, we just test for true(success)/false(failure):
if (mysqli_query($connection, $sql)) {
	echo "Table created successfully: questions<br>";
}
else {
	die("Error creating table: " . mysqli_error($connection));
}

// put some data in our table:
$question_id[] = '1'; $survey_id[] = '1'; $question_field[] = 'Favourite NBA team?'; $question_type[] = 'Text'; $mandatory[] = 'Yes';
$question_id[] = '2'; $survey_id[] = '1'; $question_field[] = 'Who is your favourite NBA player?'; $question_type[] = 'Text'; $mandatory[] = 'No';
$question_id[] = '3'; $survey_id[] = '1'; $question_field[] = 'Favourite NBA team out of this list?'; $question_type[] = 'Select'; $mandatory[] = 'No';
$question_id[] = '4'; $survey_id[] = '1'; $question_field[] = 'Who is your favourite NBA player out of this list?'; $question_type[] = 'Radio'; $mandatory[] = 'No';

$question_id[] = '5'; $survey_id[] = '2'; $question_field[] = 'Would you recommened this survey website to a friend?'; $question_type[] = 'Radio'; $mandatory[] = 'No';
$question_id[] = '6'; $survey_id[] = '2'; $question_field[] = 'What do you like about the website?'; $question_type[] = 'Text'; $mandatory[] = 'No';
$question_id[] = '7'; $survey_id[] = '2'; $question_field[] = 'What do you dislike about the website?'; $question_type[] = 'Text'; $mandatory[] = 'No';

// loop through the arrays above and add rows to the table:
for ($i=0; $i<count($question_id); $i++) {
	$sql = "INSERT INTO questions (question_id, survey_id, question_field, question_type, mandatory) VALUES ('$question_id[$i]', '$survey_id[$i]', '$question_field[$i]', '$question_type[$i]', '$mandatory[$i]' )";
	
	// no data returned, we just test for true(success)/false(failure):
	if (mysqli_query($connection, $sql)) {
		echo "row inserted<br>";
	}
	else {
		die("Error inserting row: " . mysqli_error($connection));
	}
}

// reset arrays
$question_id = array();
$survey_id = array();

/* --------------------------------------- ANSWERS TABLE ---------------------------------------    */


// if there's an old version of our table, then drop it:
$sql = "DROP TABLE IF EXISTS answers";

// no data returned, we just test for true(success)/false(failure):
if (mysqli_query($connection, $sql)) {
	echo "Dropped existing table: answers<br>";
} 
else {	
	die("Error checking for existing table: " . mysqli_error($connection));
}

// make answers table:
$sql = 
	"CREATE TABLE answers (
		answer_id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
		question_id INT(10), 
		answer_field VARCHAR(255)
	)";

// no data returned, we just test for true(success)/false(failure):
if (mysqli_query($connection, $sql)) {
	echo "Table created successfully: questions<br>";
}
else {
	die("Error creating table: " . mysqli_error($connection));
}

// put some data in our table:
$answer_id[] = '1'; $id_question[] = '3'; $answer_field[] = 'Boston Celtics';
$answer_id[] = '2'; $id_question[] = '3'; $answer_field[] = 'Golden State Warriors';
$answer_id[] = '3'; $id_question[] = '4'; $answer_field[] = 'Kyrie Irving';
$answer_id[] = '4'; $id_question[] = '4'; $answer_field[] = 'Steph Curry';
$answer_id[] = '5'; $id_question[] = '4'; $answer_field[] = 'James Harden';

$answer_id[] = '6'; $id_question[] = '5'; $answer_field[] = 'Yes';
$answer_id[] = '7'; $id_question[] = '5'; $answer_field[] = 'No';



// loop through the arrays above and add rows to the table:
for ($i=0; $i<count($answer_id); $i++) {
	$sql = "INSERT INTO answers (answer_id, question_id, answer_field) VALUES ('$answer_id[$i]', '$id_question[$i]', '$answer_field[$i]')";
	
	// no data returned, we just test for true(success)/false(failure):
	if (mysqli_query($connection, $sql)) {
		echo "row inserted<br>";
	}
	else {
		die("Error inserting row: " . mysqli_error($connection));
	}
}

/* --------------------------------------- user_submit TABLE ---------------------------------------    */

// if there's an old version of our table, then drop it:
	$sql = "DROP TABLE IF EXISTS user_submit";

	// no data returned, we just test for true(success)/false(failure):
	if (mysqli_query($connection, $sql)) {
		echo "Dropped existing table: user_submit<br>";
	} 
	else {	
		die("Error checking for existing table: " . mysqli_error($connection));
	}
	
	// make user_submit table:
	$sql = 
		"CREATE TABLE user_submit (
			submit_id INT(10) AUTO_INCREMENT PRIMARY KEY,
			question_id INT(10),
			user_answer VARCHAR(255),
			survey_id INT(10),
			username VARCHAR(255)
		)";
		
// no data returned, we just test for true(success)/false(failure):
	if (mysqli_query($connection, $sql)) {
		echo "Table created successfully: questions<br>";
	}
	else {
		die("Error creating table: " . mysqli_error($connection));
	}

// put some data in our table:
$submit_id[] = '1'; $question_id[] = '5'; $user_answer[] = 'Yes'; $survey_id[] = '2'; $username[] = 'b';
$submit_id[] = '2'; $question_id[] = '5'; $user_answer[] = 'Yes'; $survey_id[] = '2'; $username[] = 'b';
$submit_id[] = '3'; $question_id[] = '5'; $user_answer[] = 'No'; $survey_id[] = '2'; $username[] = 'a';
$submit_id[] = '4'; $question_id[] = '5'; $user_answer[] = 'Yes'; $survey_id[] = '2'; $username[] = 'd';
$submit_id[] = '5'; $question_id[] = '5'; $user_answer[] = 'Yes'; $survey_id[] = '2'; $username[] = 'c';

$submit_id[] = '6'; $question_id[] = '6'; $user_answer[] = 'I like everything about it'; $survey_id[] = '2'; $username[] = 'b';
$submit_id[] = '7'; $question_id[] = '6'; $user_answer[] = 'Survey Creation is easy to use'; $survey_id[] = '2'; $username[] = 'b';
$submit_id[] = '8'; $question_id[] = '6'; $user_answer[] = 'Everything'; $survey_id[] = '2'; $username[] = 'd';
$submit_id[] = '9'; $question_id[] = '6'; $user_answer[] = 'I like the survey results page'; $survey_id[] = '2'; $username[] = 'c';

$submit_id[] = '10'; $question_id[] = '7'; $user_answer[] = 'I find it hard to navigate'; $survey_id[] = '2'; $username[] = 'a';

$submit_id[] = '11'; $question_id[] = '1'; $user_answer[] = 'Boston Celtics'; $survey_id[] = '1'; $username[] = 'b';
$submit_id[] = '12'; $question_id[] = '1'; $user_answer[] = 'Lakers'; $survey_id[] = '1'; $username[] = 'b';
$submit_id[] = '13'; $question_id[] = '1'; $user_answer[] = 'GSW'; $survey_id[] = '1'; $username[] = 'a';
$submit_id[] = '14'; $question_id[] = '1'; $user_answer[] = 'Golden State Warriors'; $survey_id[] = '1'; $username[] = 'd';
$submit_id[] = '15'; $question_id[] = '1'; $user_answer[] = 'Spurs'; $survey_id[] = '1'; $username[] = 'c';
$submit_id[] = '16'; $question_id[] = '1'; $user_answer[] = 'Boston'; $survey_id[] = '1'; $username[] = 'briang';
$submit_id[] = '17'; $question_id[] = '1'; $user_answer[] = 'Golden State Warriors'; $survey_id[] = '1'; $username[] = 'd';
$submit_id[] = '18'; $question_id[] = '1'; $user_answer[] = 'Boston Celtics'; $survey_id[] = '1'; $username[] = 'barrym';
$submit_id[] = '19'; $question_id[] = '1'; $user_answer[] = 'Boston Celtics'; $survey_id[] = '1'; $username[] = 'mandyb';

$submit_id[] = '20'; $question_id[] = '2'; $user_answer[] = 'Kyrie Irving'; $survey_id[] = '1'; $username[] = 'b';
$submit_id[] = '21'; $question_id[] = '2'; $user_answer[] = 'Steph Curry'; $survey_id[] = '1'; $username[] = 'b';
$submit_id[] = '22'; $question_id[] = '2'; $user_answer[] = 'Magic J'; $survey_id[] = '1'; $username[] = 'a';
$submit_id[] = '23'; $question_id[] = '2'; $user_answer[] = 'Lebron James'; $survey_id[] = '1'; $username[] = 'd';
$submit_id[] = '24'; $question_id[] = '2'; $user_answer[] = 'AI'; $survey_id[] = '1'; $username[] = 'c';
$submit_id[] = '25'; $question_id[] = '2'; $user_answer[] = 'Allen Iverson'; $survey_id[] = '1'; $username[] = 'briang';
$submit_id[] = '26'; $question_id[] = '2'; $user_answer[] = 'KD'; $survey_id[] = '1'; $username[] = 'd';
$submit_id[] = '27'; $question_id[] = '2'; $user_answer[] = 'Micheal Jordan'; $survey_id[] = '1'; $username[] = 'barrym';
$submit_id[] = '28'; $question_id[] = '2'; $user_answer[] = 'Micheal Jordan'; $survey_id[] = '1'; $username[] = 'mandyb';

$submit_id[] = '29'; $question_id[] = '3'; $user_answer[] = 'Boston Celtics'; $survey_id[] = '1'; $username[] = 'b';
$submit_id[] = '30'; $question_id[] = '3'; $user_answer[] = 'Boston Celtics'; $survey_id[] = '1'; $username[] = 'b';
$submit_id[] = '31'; $question_id[] = '3'; $user_answer[] = 'Boston Celtics'; $survey_id[] = '1'; $username[] = 'a';
$submit_id[] = '32'; $question_id[] = '3'; $user_answer[] = 'Golden State Warriors'; $survey_id[] = '1'; $username[] = 'd';
$submit_id[] = '33'; $question_id[] = '3'; $user_answer[] = 'Boston Celtics'; $survey_id[] = '1'; $username[] = 'c';
$submit_id[] = '34'; $question_id[] = '3'; $user_answer[] = 'Boston Celtics'; $survey_id[] = '1'; $username[] = 'briang';
$submit_id[] = '35'; $question_id[] = '3'; $user_answer[] = 'Golden State Warriors'; $survey_id[] = '1'; $username[] = 'd';
$submit_id[] = '36'; $question_id[] = '3'; $user_answer[] = 'Golden State Warriors'; $survey_id[] = '1'; $username[] = 'barrym';
$submit_id[] = '37'; $question_id[] = '3'; $user_answer[] = 'Golden State Warriors'; $survey_id[] = '1'; $username[] = 'mandyb';

$submit_id[] = '38'; $question_id[] = '4'; $user_answer[] = 'James Harden'; $survey_id[] = '1'; $username[] = 'b';
$submit_id[] = '39'; $question_id[] = '4'; $user_answer[] = 'Kyrie Irving'; $survey_id[] = '1'; $username[] = 'b';
$submit_id[] = '40'; $question_id[] = '4'; $user_answer[] = 'Kyrie Irving'; $survey_id[] = '1'; $username[] = 'a';
$submit_id[] = '41'; $question_id[] = '4'; $user_answer[] = 'Kyrie Irving'; $survey_id[] = '1'; $username[] = 'd';
$submit_id[] = '42'; $question_id[] = '4'; $user_answer[] = 'Kyrie Irving'; $survey_id[] = '1'; $username[] = 'c';
$submit_id[] = '43'; $question_id[] = '4'; $user_answer[] = 'Kyrie Irving'; $survey_id[] = '1'; $username[] = 'briang';
$submit_id[] = '44'; $question_id[] = '4'; $user_answer[] = 'Kyrie Irving'; $survey_id[] = '1'; $username[] = 'd';
$submit_id[] = '45'; $question_id[] = '4'; $user_answer[] = 'James Harden'; $survey_id[] = '1'; $username[] = 'barrym';
$submit_id[] = '46'; $question_id[] = '4'; $user_answer[] = 'Steph Curry'; $survey_id[] = '1'; $username[] = 'mandyb';


// loop through the arrays above and add rows to the table:
for ($i=0; $i<count($submit_id); $i++) {
	$sql = "INSERT INTO user_submit (submit_id, question_id, user_answer, survey_id, username) VALUES ('$submit_id[$i]', '$question_id[$i]', '$user_answer[$i]', '$survey_id[$i]', '$username[$i]' )";
	
	// no data returned, we just test for true(success)/false(failure):
	if (mysqli_query($connection, $sql)) {
		echo "row inserted<br>";
	}
	else {
		die("Error inserting row: " . mysqli_error($connection));
	}
}




// we're finished, close the connection:
mysqli_close($connection);
?>