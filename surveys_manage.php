<?php

// includes the header in the page
require_once "header.php";

// message to output to user:
$message = "";

// strings to hold any validation error messages:
$survey_name_val = "";
$survey_questions_val = "";

// should we display these pages:
$show_create_new_survey = false;
$show_take_this_survey = false;
$show_landing = false;

// holds the title of the survey. created by user
$survey_name = "";

// holds the number of questions a survey should have. created by user
$survey_questions = "";

$answer_text ="";

$question_title = "";

$answer_field = "";

// validation for user survey response
$user_answer_val = "";


// user isn't logged in, display a message saying they must be:
if (!isset($_SESSION['loggedInSkeleton'])) {
	echo "You must be logged in to view this page.<br>";
}





// clicked "Create New Survey" button
elseif(isset($_GET['create_new_survey'])) {
	$show_create_new_survey = true;
}

// user clicked "Take This Survey" button
elseif(isset($_GET['take_survey'])) {

	// get the survey title
	$survey_id= $_GET['take_survey'];
	
	// connect to the database
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	// connection fails; exit
	if (!$connection) {
		die("Connection failed: " . $mysqli_connect_error);
	}

	// query used to get survey title from survey table	
	$query = "SELECT survey_name FROM surveys where survey_id = '$survey_id'";
	$result = mysqli_query($connection, $query);
	$row = mysqli_fetch_assoc($result);
	$survey_title = $row['survey_name'];

	// select all data from questions using the survey id
	$query = "SELECT * FROM questions WHERE survey_id = '$survey_id'";
	$result =  mysqli_query($connection, $query);
	$n = mysqli_num_rows($result);

	// stores a number which increments everytime a question is printed
	// this is used to know the number of questions we have
	$question_number = 0;

	// select type of questions to display. If the question should be radio, text etc.
	$secondQuery = "SELECT question_type FROM questions WHERE survey_id = '$survey_id'";
	$secondResult = mysqli_query($connection, $secondQuery);

	if($result) {

		// displayes question if a row is returned
		if($n > 0) {
			
			// title of the survey
			echo "<h2> " . $survey_title . "</h2>";

			// form
			echo "<form action='surveys_manage.php' method='post'>";

			// loops while there are rows to be pulled from 'questions' table'
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				// stores the question in $question
				$question = $row['question_field'];
				// fetches the type of question a question is; obtained from the database
				$secondRow = mysqli_fetch_array($secondResult, MYSQLI_ASSOC);
				$question_type = $secondRow['question_type'];
				// question title
				echo'<label>' . $question .  '</label> <br>';

				// display question as text
				if($question_type == 'Text') {

					// this query is used to obtain the question id from the database
					$query2 = "SELECT * FROM questions WHERE question_field = '$question' AND survey_id = '$survey_id'";
					$result2 = mysqli_query($connection, $query2);
					$row2 = mysqli_fetch_assoc($result2);
					$mandatory = $row2['mandatory'];

					if ($mandatory == 'Yes') {
						echo "<input type='text' maxlength='64' name='answers[]' value='".$answer_text."' required> <br>";
					}
					else {
						echo "<input type='text' maxlength='64' name='answers[]' value='".$answer_text."'> <br>";
					}

					$question_number++;
				}

				// displays question as radio
				// radio questions are mandatory
				if($question_type == 'Radio') {

					// used for checking the first radio button
					$counter = 0;

					// this query is used to obtain the question id from the database
					$query2 = "SELECT * FROM questions WHERE question_field = '$question' AND survey_id = '$survey_id'";
					$result2 = mysqli_query($connection, $query2);
					$row2 = mysqli_fetch_assoc($result2);
					$question_id = $row2['question_id'];

					// obtain answers from the database
					$query2 = "SELECT * FROM answers WHERE question_id = $question_id";
					$result2 = mysqli_query($connection, $query2);

					// lists the radio list
					while($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {
						// first radio butten is checked
						if ($counter == 0) {
							echo "<input type='radio' name='answers[$question_number]' checked='checked' value='".$row2['answer_field']."'>" . $row2['answer_field'] . "<br>";
							$counter++;
						}
						// other radio buttens are not checked
						else {
							echo "<input type='radio' name='answers[$question_number]' value='".$row2['answer_field']."'>" . $row2['answer_field'] . "<br>";
						}
					}
					$question_number++;
				}

				// displays question as select
				if($question_type == 'Select') {

					// this query is used to obtain the question id from the database
					$query2 = "SELECT * FROM questions WHERE question_field = '$question' AND survey_id = '$survey_id'";
					$result2 = mysqli_query($connection, $query2);
					$row2 = mysqli_fetch_assoc($result2);
					$question_id = $row2['question_id'];

					// obtain answers from the database
					$query2 = "SELECT * FROM answers WHERE question_id = $question_id";
					$result2 = mysqli_query($connection, $query2);
					
					echo "<select name='answers[]'>";
					// lists the select list
					while($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {
						echo "<option value='".$row2['answer_field']."'>" . $row2['answer_field'] . "</option>";
					}

					echo "</select> <br>";
					$question_number++;
				}
			}
			// sets a cookie when the survey is displayed
			// used so survey is only sent once
			setcookie("send_survey", "untitled", time()+60*20);
			// submit survey
			echo '<input type="hidden" name="number_of_questions" value="'.$question_number.'">';
			echo '<input type="hidden" name="survey_id" value="'.$survey_id.'">';
			echo '<input type="submit" name="post_survey" value="Submit">';

			echo '</form>';
		}

		// no questions found in survey
		else {
			// title of the survey
			echo "<h2> " . $survey_title . "</h2>";
			// tell the user there are no questions 
			echo "<p>there are currently no questions avaiable in this survey.</p>";
		}
	}

	// query error
	else {
		echo 'Error, data could not be obtained from the query';
	}

	// we're finished with the database, close the connection:
	mysqli_close($connection);
}

// user clicked the active survey button
elseif(isset($_GET['activate_survey'])) {
	
	// connect to the database
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	// connection fails; exit
	if (!$connection) {
		die("Connection failed: " . $mysqli_connect_error);
	}

	// getters to get data from previous form
	$survey_id = $_GET['activate_survey'];

	// query activates survey using survey id	
	$query = "UPDATE surveys SET share_survey='Yes' WHERE survey_id = '$survey_id'";
	$result = mysqli_query($connection, $query);
	if ($result) {
		echo "<p>Survey has been activated</p>";
		echo "<a href='surveys_manage.php'>Return to landing page</a>";
	}

	// we're finished with the database, close the connection:
	mysqli_close($connection);	
}

// user clicked the deactivate survey button
elseif(isset($_GET['deactivate_survey'])) {
	
	// connect to the database
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	// connection fails; exit
	if (!$connection) {
		die("Connection failed: " . $mysqli_connect_error);
	}

	// getters to get data from previous form	
	$survey_id = $_GET['deactivate_survey'];

	// query deactivates survey using survey id
	$query = "UPDATE surveys SET share_survey='No' WHERE survey_id = '$survey_id'";
	$result = mysqli_query($connection, $query);
	if ($result) {
		echo "<p>Survey has been deactivated</p>";
		echo "<a href='surveys_manage.php'>Return to landing page</a>";
	}

	// we're finished with the database, close the connection:
	mysqli_close($connection);	
}

// user clicked the share survey button
elseif(isset($_GET['share_survey'])) {
	
	// connect to the database
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	// connection fails; exit
	if (!$connection) {
		die("Connection failed: " . $mysqli_connect_error);
	}

	// getters to get data from previous form	
	$survey_id = $_GET['share_survey'];

	echo 'Survey Link:<br>' . $_SERVER['HTTP_HOST'] . '/17082528_2CWK50/surveys_manage.php?take_survey=' . $survey_id . '<br>';
	echo "<a href='surveys_manage.php'>Return to landing page</a>";

	// we're finished with the database, close the connection:
	mysqli_close($connection);	
}

// Survey Edit 1/3
// user just clicked 'edit survey' button
// either edits already made new questions or creates new questions
elseif(isset($_GET['edit_survey'])) {
	// connect directly to our database (notice 4th argument):
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	// if the connection fails, we need to know, so allow this exit:
	if (!$connection) {
		die("Connection failed: " . $mysqli_connect_error);
	}

	// gets survey id from get method
	$survey_id = $_GET['edit_survey'];

	// query to get the survey title
	$query = "SELECT survey_name FROM surveys WHERE survey_id = '$survey_id'";
	$result = mysqli_query($connection, $query);
	$row = mysqli_fetch_assoc($result);
	$survey_name = $row['survey_name'];

	// query to check if survey_id is in questions table
	$query = "SELECT survey_id FROM questions WHERE survey_id = '$survey_id'";
	$result = mysqli_query($connection, $query);
	
	// no rows returned means there are no questions
	if(mysqli_num_rows($result) == 0) {
		// select the number of questions from the survey
		$query = "SELECT survey_questions FROM surveys WHERE survey_id = '$survey_id'";
		$result = mysqli_query($connection, $query);
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

		// echoes Survey Edit Process - 1/3
		echo "<h2>Survey Edit 1/3</h2>";

		// echoes title of survey
		echo "<h3>Survey: " . $survey_name . "</h3>";

		// form
		echo "<form action='surveys_manage.php' method='get'>";
			// loops questions input
			for($i = 0; $i < $row['survey_questions']; $i++ ) {
				// used to know the question number
				// if looped once then first loop is question 1

				echo "<h4>Question " . ($i+1) . "</h4>";

				echo "Question Name: <input type='text' minlength='3' name='question_title[]' value='$question_title' maxlength='64' required placeholder='Minimum length: 3'> <br>
				Question Type:  
				<select name='question_type[]'> 
				<option value='Text'> Text </option> 
				<option value='Select'> Select </option> 
				<option value='Radio'> Radio </option> 
				</select>";

				// passes on survey id and survey name by using hidden inputs
				echo "<input type='hidden' name='survey_id' value='$survey_id'>";
				echo "<input type='hidden' name='survey_title' value='$survey_name'>";
			}
			echo "<br><button type='submit' name='send_edit_survey'>Next>>></button>";
		echo "</form>";
	}

	// rows returned means there are questions in db
	// display question edit screen
	else {
		echo "<h2> List of Questions </h2>";
		$query = "SELECT * FROM questions WHERE survey_id = '$survey_id'";
		$result = mysqli_query($connection, $query);
		
		// diplays questions along with an edit or delete button
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			echo "<form action='surveys_manage.php' method='get'>";
			echo $row['question_field'];
			echo "<input type='hidden' name='survey_id' value='$survey_id'>";
			echo "<button type='submit' name='edit_question' value='" . $row['question_id'] . "'>Edit</button>";
			echo "<button type='submit' name='delete_question' value='" . $row['question_id'] . "'>Delete</button><br><br>";
			echo "</form>";
		}

		echo <<<_END
		<form action='surveys_manage.php' method='get'>
		<input type='hidden' name='survey_title' value='$survey_name'>
		<button type='submit' name='add_new_question' value='$survey_id'>Add New Question</button>
		</form>
_END;

	}

	// we're finished with the database, close the connection:
	mysqli_close($connection);
}

// Survey Edit 2/3
// Set the number of options for a question
elseif(isset($_GET['send_edit_survey'])) {
	// getters to get data from previous form
	$option = $_GET['question_type'];
	$question_type = $_GET['question_type'];
	$question_title = $_GET['question_title'];
	$total_questions = $_GET['question_title'];
	$survey_id = $_GET['survey_id'];

	$only_text = true;

	echo "<h2>Survey Edit 2/3</h2>";
	echo "<p>Set the number of options a radio/select question should have. <br>
	If radio/select options were not set then you may skip the options process and create the survey </p>";
	
	echo "<form action='surveys_manage.php' method='GET'>";
	for($i = 0; $i < count($option); $i++){
		if($option[$i] == 'Select' || $option[$i] == 'Radio') {
			// set to false because a question is select/radio
			$only_text = false;
			echo "<h3>" . $question_title[$i] . "</h3>";
			echo "Number of options: <br>";
			echo "<input type='number' name='options_number[]' min='2' max='10' required>";

			// pass along survey id and title of question
			echo "<input type='hidden' name='survey_id' value='$survey_id'>";
			echo "<input type='hidden' name='question_title[]' value='" . $question_title[$i] . "'> <br>";
		}
		echo "<input type='hidden' name='total_questions[]' value='" . $total_questions[$i] . "'>";
		echo "<input type='hidden' name='question_type[]' value='" . $question_type[$i] . "'>";
	}

	// displays this submit button if only text questions present
	if($only_text) {
		// close previous form
		echo "</form>";
		// start new post form to skip Survey Edit 3/3
		echo "<form action='surveys_manage.php' method='POST'>";
		for($i = 0; $i < count($option); $i++) {
			echo "<input type='hidden' name='survey_id' value='$survey_id'>";
			echo "<input type='hidden' name='question_title[]' value='" . $question_title[$i] . "'> <br>";
			echo "<input type='hidden' name='text_only'>";
		}
		echo "<br><button type='submit' name='submit_edit_survey'>Submit</button>";
		echo "</form>";
	}
	// displays this submit button if radio/select questions present
	else {
		echo "<br><button type='submit' name='send_options'>Next>>></button>";
	}

	echo "</form>";
}

// Survey Edit 3/3
// User inputs the answers for the options
elseif(isset($_GET['send_options'])) {
	
	// getters to get data from previous form
	if(isset($_GET['options_number'])) {
		$options_number = $_GET['options_number'];
	}
	if(isset($_GET['question_title'])) {
		$question_title = $_GET['question_title'];
	}
	if(isset($_GET['survey_id'])) {
		$survey_id = $_GET['survey_id'];
	}

	$total_questions = $_GET['total_questions'];
	$question_type = $_GET['question_type'];

	// header
	echo "<h2>Survey Edit 3/3</h2>";

	// start form
	echo "<form action='surveys_manage.php' method='post'>";
	// loops as many times as there is questions
	for($i = 0; $i < count($question_title); $i++) {
		echo "<h3>" . $question_title[$i] . "</h3>";

		// loops as many times as the number of options for the question
		for($j = 0; $j < $options_number[$i]; $j++) {
			echo "Option: " . ($j+1 . "<br>");
			echo "<input type='text' name='answer_field[]' value='$answer_field'> <br>";
		}
		echo "<input type='hidden' name='question_title[]' value='$question_title[$i]'>";
		echo "<input type='hidden' name='answer_options[]' value='$options_number[$i]'>";

	}

	echo "<input type='hidden' name='survey_id' value='$survey_id'>";
	
	// for loop here is to loop over the array and store it in a hidden input array
	foreach($total_questions as $question) {
		echo "<input type='hidden' name='total_questions[]' value='$question'>";
	}
	foreach($question_type as $option) {
		echo "<input type='hidden' name='question_type[]' value='$option'>";
	}

	echo "<button type='submit' name='submit_edit_survey'>Submit Edit </button>";
	echo "</form>";

	
}

// edit selected survey question
elseif(isset($_GET['edit_question'])) {
	
	// connect directly to our database (notice 4th argument):
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	// if the connection fails, we need to know, so allow this exit:
	if (!$connection) {
		die("Connection failed: " . $mysqli_connect_error);
	}	
	
	// getters to get data from previous form
	$question_id = $_GET['edit_question'];
	$survey_id = $_GET['survey_id'];

	// input values
	$title_input = "";
	$option_input = "";

	// display a notice message if no options available($options == false)
	$options = false;

	// query selects question title from current question using question id
	$query = "SELECT question_field FROM questions WHERE question_id = '$question_id'";
	$result = mysqli_query($connection, $query);
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	
	// displays form/inputs
	echo "<h3> Edit Question Title </h3>";
	echo "<p>" . $row['question_field'] . "</p>" ;
	echo "<form action='surveys_manage.php' method='post'>";
	echo "Question Title: <input type='text' minlength='3' maxlength='64' name='question_title' value='$title_input'>";
	echo "<button type='submit' name='edit_question_title' value='$question_id'>Update</button>";
	echo "</form>";

	// turn mandatory true/false
	$query = "SELECT * FROM questions WHERE question_id = '$question_id'";
	$result = mysqli_query($connection, $query);
	$row = mysqli_fetch_assoc($result);
	$mandatory = $row['mandatory'];
	// display 
	echo <<<_END
	<p>Mandatory Question? </p>
	<form action='surveys_manage.php' method='post'>
	<select name = 'mandatory'>
		<option value='Yes'>Yes</option>
		<option value='No'>No</option>
	</select>
	<button type='submit' name='mandatory_update' value='$question_id'>Submit</button>
	<p> <strong> Is this question currently mandatory?: </strong> $mandatory </p> 
_END;

	// query selects all options from answers table
	$query = "SELECT answer_field FROM answers WHERE question_id = '$question_id'";
	$result = mysqli_query($connection, $query);

	// selects all answer ids from answers table
	$query2 = "SELECT answer_id FROM answers WHERE question_id = '$question_id'";
	$result2 = mysqli_query($connection, $query2);
	
	// displays option edit form/inputs
	if($result) {
		echo "<h4> Edit Question Options </h4>";
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);
			echo "<form action='surveys_manage.php' method='post'>";
			echo $row['answer_field'] . "<br>";
			echo "Update Option: <input type='text' minlength='3' maxlength='64' name='option_title' value='$option_input' >";
			echo "<input type = 'hidden' name='answer_id' value='" . $row2['answer_id'] ."'>";
			echo "<button type='submit' name='edit_option_title' value='$question_id'>Update</button> <br><br>";
			$options = true;
			echo "</form>";
		}
		if($options == false) {
			echo "<p>No options avaialble to edit</p>";
		}
	}
	else {
		echo "error";
	}

	// we're finished with the database, close the connection:
	mysqli_close($connection);
}

// deletes selected survey question
elseif(isset($_GET['delete_question'])) {
	// connect directly to our database (notice 4th argument):
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	// if the connection fails, we need to know, so allow this exit:
	if (!$connection) {
		die("Connection failed: " . $mysqli_connect_error);
	}	

	$question_id = $_GET['delete_question'];

	// first deletes the question
	$query = "DELETE FROM questions WHERE question_id = $question_id";
	$result = mysqli_query($connection, $query);
	if ($result) {
		// if delete was successfull then delete the answers related to that question
		$query = "DELETE FROM answers WHERE question_id = $question_id";
		$result = mysqli_query($connection, $query);
		if ($result) {
			// output success message
			echo "Successfully deleted question";
		}
	}

	// unable to delete questions, display error message
	else {
		echo "Error deleteing questionn";
	}

	// we're finished with the database, close the connection:
	mysqli_close($connection);	
}

// user clicked 'delete survey' - deletes a surevy
elseif(isset($_GET['delete_survey'])) {
	// connect directly to our database (notice 4th argument):
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	// if the connection fails, we need to know, so allow this exit:
	if (!$connection) {
		die("Connection failed: " . $mysqli_connect_error);
	}

	// getters to get data from previous form
	$survey_id = $_GET['delete_survey'];

	// query to select all question ids from the selected survey
	$query = "SELECT * FROM questions WHERE survey_id = '$survey_id'";
	$result = mysqli_query($connection, $query);
	// loops while there are rows(questions) to be fetched
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$question_id = $row['question_id'];
		// deletes all questions from current question
		$query2 = "DELETE FROM questions WHERE question_id = $question_id";
		$result2 = mysqli_query($connection, $query2);
		if ($result2) {
			// deletes all answers from current question
			$query3 = "DELETE FROM answers WHERE question_id = $question_id";
			$result3 = mysqli_query($connection, $query3);
		}
	}

	$query = "DELETE FROM surveys WHERE survey_id = $survey_id";
	$result = mysqli_query($connection, $query);
	if ($result) {
		echo "Successfully deleted survey";
	}
	else {
		echo "Error deleting survey";
	}

	// we're finished with the database, close the connection:
	mysqli_close($connection);	
}

// clicked on 'add new question'
// adds new question to survey already with questions
elseif(isset($_GET['add_new_question'])) {

	// connect directly to our database (notice 4th argument):
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	// if the connection fails, we need to know, so allow this exit:
	if (!$connection) {
		die("Connection failed: " . $mysqli_connect_error);
	}

	// getters to get data from previous form
	$survey_id = $_GET['add_new_question'];
	$survey_name = $_GET['survey_title'];

	// echoes title of survey
	echo "<h3>Survey: " . $survey_name . "</h3>";

	// form
	echo "<form action='surveys_manage.php' method='get'>";	
	echo "Question Name: <input type='text' minlength='3' name='question_title[]' value='$question_title' maxlength='64' required placeholder='Minimum length: 3'> <br>
	Question Type:  
	<select name='question_type[]'> 
	<option value='Text'> Text </option> 
	<option value='Select'> Select </option> 
	<option value='Radio'> Radio </option> 
	</select>";	

	// passes on survey id and survey name by using hidden inputs
	echo "<input type='hidden' name='survey_id' value='$survey_id'>";
	echo "<input type='hidden' name='survey_title' value='$survey_name'>";
	echo "<br><button type='submit' name='send_edit_survey'>Next>>></button>";
	echo "</form>";					

	// we're finished with the database, close the connection:
	mysqli_close($connection);	

}




// user just tried to create a new survey
elseif(isset($_POST['create_survey'])) {

	// connect directly to our database (notice 4th argument):
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	// if the connection fails, we need to know, so allow this exit:
	if (!$connection) {
		die("Connection failed: " . $mysqli_connect_error);
	}	

	// getters to get data from previous form
	// SANITISATION (see helper.php for the function definition)	
	$survey_name = sanitise($_POST['survey_name'], $connection);
	$survey_questions = sanitise($_POST['survey_questions'], $connection);

	// VALIDATION (see helper.php for the function definitions)	
	$survey_name_val = validateString($survey_name, 4, 64);
	$survey_questions_val = validateInt($survey_questions, 1, 10);

	// concatenate all the validation results together ($errors will only be empty if ALL the data is valid):
	$errors = $survey_name_val . $survey_questions_val;

	// holds username obtained from session varaible
	$username = $_SESSION['username'];

	// inserts into database if there are no errors present
	if ($errors == "") {
		$query = "INSERT INTO surveys(survey_name, survey_questions, survey_author, survey_responses) VALUES ('$survey_name', '$survey_questions', '$username',0) ";
		$result = mysqli_query($connection, $query);
		
		// succes with creating survey - display message
		if($result) {
			echo '<p>Sucessfully Created Survey: ' . $survey_name . '</p>';
			echo "<a href='surveys_manage.php'>Click here to return to the dashboard</a>";
		}
		// error creating survey - display error message
		else {
			echo 'Error, could not create survey';
		}
	}

	else {
		$show_create_new_survey = true;
	}

	// we're finished with the database, close the connection:
	mysqli_close($connection);	
}

// posted survey respone
elseif(isset($_POST['post_survey'])) {
	// connect directly to our database (notice 4th argument):
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	// connection fails; exit
	if (!$connection) {
		die("Connection failed: " . $mysqli_connect_error);
	}

	// holds username obtained from session varaible
	$username = $_SESSION['username'];

	// gets the survey id of posted survey via $_POST
	$survey_id = $_POST['survey_id'];

	// validate boolean
	$error = true;

	// boolean to determine if inserts into database was sucesfull
	$post_success = false;

	// holds the number of questions
	$number_of_questions = $_POST['number_of_questions'];

	// holds the user answers from the survey
	$user_answer = $_POST['answers'];

	// validates user inputs
	for($i = 0; $i < count($user_answer); $i++) {

		// validates the input
		$user_answer_val = validateString($user_answer[$i], 0, 64);
		// if the validation catches an invalid input then the error will be stored in the $errors array
		if ($user_answer_val != "") {
			$errors[] = $user_answer_val;
		}
		// no error to store in $errors array
		else {
			$errors[] = "";
		}
	}

	// checks if $errors array does not contain an error
	for ($i = 0; $i < count($errors); $i++) {
		// errors boolean set to true if an error is present
		if ($errors[$i] != "") {
			echo $errors[$i] . "<br>";
			$error = true;
		}
		// else is false
		else {
			$error = false;
		}
	}

	// if no errors are present then proceed 
	if($error == false) {

		// gets the survey responses from db so it can be incremented when a response is successfull
		$query = "SELECT survey_responses FROM surveys WHERE survey_id = $survey_id";
		$result = mysqli_query($connection, $query);
		$row = mysqli_fetch_assoc($result);
		$survey_responses = ($row['survey_responses']+1);

		// used to find the start question_id from the survey
		// declare an array which will then store the question_ids of all looped questions
		$question_id_query = "SELECT question_id FROM questions WHERE survey_id = '$survey_id'";
		$question_id_result = mysqli_query($connection, $question_id_query);
		$question_id_array = [];
		while ($question_id_row = mysqli_fetch_array($question_id_result, MYSQLI_ASSOC)) {
			$question_id_array[] = $question_id_row['question_id'];
		}

		// if a cookie is set(set when submitting a survey) then insert into db
		// this is so that the person taking the survey cannot submit a survey twice
		// works by deleting the cookie after data is inserted into db
		if(isset($_COOKIE['send_survey'])) {
			for($i = 0; $i < $number_of_questions; $i++) {
				$query = "INSERT INTO user_submit(question_id,user_answer,survey_id, username) VALUES ('$question_id_array[$i]','$user_answer[$i]','$survey_id', '$username')";
				$result = mysqli_query($connection, $query);
				if($result) {
					$post_success = true;
					setcookie("send_survey", "", time()-60*20);
				}
			}
		}


		// successs in submiting survey
		if ($post_success) {
			echo 'Thank You for taking the survey<br>
			<a href="surveys_manage.php">Return</a>';
			$query = "UPDATE surveys SET survey_responses = $survey_responses WHERE survey_id = $survey_id";
			$result = mysqli_query($connection, $query);
		}
		// failure
		else {
			echo 'Failure; error in submiting data<br>
			<a href="surveys_manage.php">Return</a>';
		}
	}

	else {
		echo "Could not submit survey; errors reported.<br>
		<a href='surveys_manage.php'>Return</a>";
	}

	// we're finished with the database, close the connection:
	mysqli_close($connection);

}

// Survey Edit Submit
elseif(isset($_POST['submit_edit_survey'])) {
	
	// connect directly to our database (notice 4th argument):
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	// if the connection fails, we need to know, so allow this exit:
	if (!$connection) {
		die("Connection failed: " . $mysqli_connect_error);
	}
	
	$survey_id = $_POST['survey_id'];

	// if this isn't set it means that there are no radio/text questions
	// Text Only Submit
	if (!isset($_POST['total_questions'])) {
		$question_title = $_POST['question_title'];
		for($i = 0; $i < count($question_title); $i++) {
			// insert the new question title and type into the database
			$query = "INSERT INTO questions(survey_id,question_field,question_type) VALUES ('$survey_id', '$question_title[$i]', 'Text')";
			$result = mysqli_query($connection, $query);
			if ($result) {
				echo "Successfully created Question: " . $question_title[$i] . "<br>";
			}
		}

	}

	// Multiple Submit
	else {
		$question_title = $_POST['total_questions'];
		$question_type = $_POST['question_type'];
		$answer_field = $_POST['answer_field'];
		$answer_options = $_POST['answer_options'];
		
		// counters are used to iterate over the answer_field
		// every time query4 is ran it gets iterated so the next value in the answer_field array can be inserted
		// counter2 is used incase the question is not a radio/select
		// we increment this so we can keep track of how many times a text question was made
		// we need to subtract counter2 from $i so that the for loop does not go off bounds
		$counter = 0;
		$counter2 = 0;
		
		// loops as many times as there is question
		for($i = 0; $i < count($question_title); $i++) {

			// check if a question with the same name already exists in the database
			$query = "SELECT * FROM questions WHERE question_field = '$question_title[$i]' AND survey_id = '$survey_id' ";
			$result = mysqli_query($connection, $query);
			$n = mysqli_num_rows($result);
			// if a row is returned it means a question with the same name already increments
			// adds an underscore followed by a number to remedy this
			if ($n > 0) {
				$question_title[$i] = $question_title[$i] . "_" . $i;
			}

			// first need to insert the new question title and type into the database
			$query = "INSERT INTO questions(survey_id,question_field,question_type) VALUES ('$survey_id', '$question_title[$i]', '$question_type[$i]')";
			$result = mysqli_query($connection, $query);
			echo "Sucessfully created Question: " . $question_title[$i] . "<br>";
	
			// find out if the question is a radio or select question
			$query2 = "SELECT question_type FROM questions WHERE question_field = '$question_title[$i]'";
			$result2 = mysqli_query($connection, $query2);
			$row = mysqli_fetch_array($result2, MYSQLI_NUM);
			
			// if question is a select/radio then answer options are needed to be inserted into database
			if ($result && ($row[0] == 'Select' || $row[0] == 'Radio')) {
				// obtain question ids from the selected survey
				$query3 = "SELECT question_id FROM questions WHERE (survey_id = $survey_id) AND (question_type = 'Select' OR question_type='Radio') AND (question_field = '$question_title[$i]')";
				$result3 = mysqli_query($connection, $query3);
				$question_id = mysqli_fetch_array($result3, MYSQLI_NUM);
	
				// insert answer options into database
				if ($result3) {
					for($j = 0; $j < $answer_options[$i-$counter2]; $j++) {
						$query4 = "INSERT INTO answers(question_id,answer_field) VALUES ('$question_id[0]','$answer_field[$counter]')";
						$result = mysqli_query($connection, $query4);
						$counter++;
					}
				}
				mysqli_free_result($result3);
			}
	
			// if question is text then just create question
			elseif($result && ($row[0] == 'Text')) {
				$counter2++;
			}
	
			// error message
			else {
				echo "Error, could not create Question: " . $question_title[$i] . "<br>";
			}
		}
	}



	// we're finished with the database, close the connection:
	mysqli_close($connection);

}

// edit the question title
elseif(isset($_POST['edit_question_title'])) {
	// connect directly to our database (notice 4th argument):
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	// if the connection fails, we need to know, so allow this exit:
	if (!$connection) {
		die("Connection failed: " . $mysqli_connect_error);
	}

	// getters to get data from previous form
	$question_id = $_POST['edit_question_title'];

	// SANITISATION (see helper.php for the function definition)
	$new_title = sanitise($_POST['question_title'], $connection);
	// VALIDATION (see helper.php for the function definition)
	$new_title_val = validateString($new_title, 3, 64);

	// concatenate all the validation results together ($errors will only be empty if ALL the data is valid):
	$errors = $new_title_val;

	// error check passed
	if ($errors == "") {
		$query = "UPDATE questions SET question_field = '$new_title' WHERE question_id = '$question_id'";
		$result = mysqli_query($connection, $query);
		if ($result) {
			echo "Sucessfully updated question title";
		}
		else {
			echo "Error could not update question title";
		}		
	}

	// error check failed
	else {
		echo $errors . '<br><a href="surveys_manage.php">Return</a>';

	}
}

// edits an option for a question
elseif(isset($_POST['edit_option_title'])) {
	// connect directly to our database (notice 4th argument):
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	// if the connection fails, we need to know, so allow this exit:
	if (!$connection) {
		die("Connection failed: " . $mysqli_connect_error);
	}

	// getters to get data from previous form
	$question_id = $_POST['edit_option_title'];
	$answer_id = $_POST['answer_id'];	

	// SANITISATION (see helper.php for the function definition)	
	$answer_field = sanitise($_POST['option_title'], $connection);
	// VALIDATION (see helper.php for the function definition)
	$answer_field_val = validateString($answer_field, 3, 64);

	// concatenate all the validation results together ($errors will only be empty if ALL the data is valid):
	$errors = $answer_field_val;

	// error check passed
	if ($errors == "") {
		$query = "UPDATE answers SET answer_field = '$answer_field' WHERE answer_id ='$answer_id' ";
		$result = mysqli_query($connection, $query);
	
		if($result) {
			echo "Successfully updated the option";
		}
		else {
			echo "Error updating the option";
		}
	}

	// error check failed
	else {
		echo $errors . '<br><a href="surveys_manage.php">Return</a>';
	}
}

// change mandatory option
elseif(isset($_POST['mandatory_update'])) {

	// connect directly to our database (notice 4th argument):
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	// if the connection fails, we need to know, so allow this exit:
	if (!$connection) {
		die("Connection failed: " . $mysqli_connect_error);
	}

	$question_id = $_POST['mandatory_update'];
	$mandatory = $_POST['mandatory'];


	$query = "UPDATE questions SET mandatory = '$mandatory' WHERE question_id = '$question_id'";
	$result = mysqli_query($connection, $query);
	if ($result) {
		echo "Question mandatory settings updated.";
	}
	else {
		echo "Error; question could not be updated.";
	}
}





// arrived at page for first time, checks if user is admin
else {
    // display admin tools if admin logged in(checks using session)
	if ($_SESSION['username'] == "admin") {
		
		// echoes admin tools headers
		echo "<h2> Admin Tools </h2>";
		
		// connect directly to our database (notice 4th argument):
		$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
		// if the connection fails, we need to know, so allow this exit:
		if (!$connection) {
			die("Connection failed: " . $mysqli_connect_error);
		}
	
		// query fetches everything from the surveys table
		$query = "SELECT * FROM surveys";
		$result = mysqli_query($connection, $query);

		// fetches survey name and id whilst there are rows to be fetched
		// echoes the survey name along with a button to take the survey and a button to delete the survey
		if($result) {
			echo "<form action='surveys_manage.php' method='get'>";
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$survey_name = $row['survey_name'];
				$survey_id = $row['survey_id'];
				
				echo $survey_name . "<button type='submit' name='edit_survey' value='$survey_id'> Edit Survey </button>";
				
				$query2 = "SELECT * FROM surveys WHERE survey_id = '$survey_id'";
				$result2 = mysqli_query($connection, $query2);
				$row2 = mysqli_fetch_assoc($result2);
				$shared = $row2['share_survey'];
				if ($shared == 'No') {
					echo "<button type='submit' name='activate_survey' value='$survey_id'> Activate Survey</button>";
				}
				else {
					echo "<button type='submit' name='deactivate_survey' value='$survey_id'> Deactivate Survey</button>";
				}

				echo "<button type='submit' name='delete_survey' value='$survey_id'> Delete Survey </button>";
				echo "<button type='submit' name='share_survey' value='$survey_id'>Share Survey</button><br>";

			}
			if (mysqli_num_rows($result) == 0) {
				echo "No surveys available <br>";
			}
		}
		echo "</form>";
		
	}
	$show_landing = true;
}

// show the landing page
if($show_landing) {
	
	// connect directly to our database (notice 4th argument):
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	// if the connection fails, we need to know, so allow this exit:
	if (!$connection) {
		die("Connection failed: " . $mysqli_connect_error);
	}

	// holds username obtained from session varaible
	$username = $_SESSION['username'];

	// query fetches everything from surveys that are being shared
	$query = "SELECT * FROM surveys WHERE share_survey = 'Yes'";
	$result = mysqli_query($connection, $query);
	$n = mysqli_num_rows($result);
	if($result) {
		
		// start form
		echo "<form action='surveys_manage.php' method='get'>";
		
		// lists every survey that has been created by any user
		// fetches survey name and id whilst there are rows to be fetched
		// echoes the survey name along with a button to take the survey
		echo "<h3>List Of Shared Surveys</h3>";
		if ($n > 0) {
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$survey_name = $row['survey_name'];
				$survey_id = $row['survey_id'];
				echo $survey_name . "<button type='submit' name='take_survey' value='$survey_id'> Take This Survey </button> <br>";
			}
		}
		else {
			echo "<p> No surveys are currently being shared </p>";
		}


		// list user created surveys
		// query to fetch everything from the surveys table where the survey creator is the user
		$query = "SELECT * FROM surveys WHERE survey_author = '$username' ";
		$result = mysqli_query($connection, $query);
		if($result) {
			echo "<h3> List Of Surveys Created By Me </h3>";
			// fetches survey name and id whilst there are rows to be fetched
			// echoes the survey name along with a button to edit/delete the survey
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$survey_name = $row['survey_name'];
				$survey_id = $row['survey_id'];
				echo $survey_name . "<button type='submit' name='edit_survey' value='$survey_id'> Edit Survey </button>";
				echo "<button type='submit' name='delete_survey' value='$survey_id'> Delete Survey </button>";

				$query2 = "SELECT * FROM surveys WHERE survey_id = '$survey_id'";
				$result2 = mysqli_query($connection, $query2);
				$row2 = mysqli_fetch_assoc($result2);
				$shared = $row2['share_survey'];
				if ($shared == 'No') {
					echo "<button type='submit' name='activate_survey' value='$survey_id'> Activate Survey</button>";
				}
				else {
					echo "<button type='submit' name='deactivate_survey' value='$survey_id'> Deactivate Survey</button>";
				}
				echo "<button type='submit' name='share_survey' value='$survey_id'>Share Survey</button><br>";
			}
			// no surveys created by user found
			if (mysqli_num_rows($result) == 0) {
				echo "You currently have no surveys. <br>";
			}
		}

		// submit button/end of form
		echo "<br> <button type='submit' name='create_new_survey'> Create New Survey </button><br>";
		echo "</form>";
	}

	else {
		echo "<p>Error selecting surveys from the database</p>";
	}

}

// shows the create survey page
if($show_create_new_survey) {
echo <<<_END
	<h1> Create Survey </h1>

	<form action="surveys_manage.php" method="post">

	Survey Name:
	<input type="text" name="survey_name" maxlength="64" value="$survey_name" required > $survey_name_val <br>

	Number of Questions:
	<input type="number" min='1' max='10' name="survey_questions" value="$survey_questions" required > $survey_questions_val <br>

	<input type="submit" name="create_survey" value="Submit">
	</form>
_END;
}



echo $message;

// finish off the HTML for this page:
require_once "footer.php";

?>