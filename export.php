<?php

require_once "credentials.php";

if(isset($_POST['export_csv'])) {

    $survey_id = $_POST['survey_id'];
    $current_question = $_POST['question'];

    // connect directly to our database (notice 4th argument):
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	// if the connection fails, we need to know, so allow this exit:
	if (!$connection) {
		die("Connection failed: " . $mysqli_connect_error);
    }

    // header so that the file is downloaded, rather than displayed
    header('Content-type: text/csv');
    header('Content-Disposition: attachment; filename="' . $current_question . '.csv"');

    // prevents file from being cached
    header('Pragma: no-cache');
    header('Expires: 0');

    // file pointer connects to the output stream
    $file = fopen('php://output', 'w');

    // send the column headers
    fputcsv($file, array('Answer Choices', 'Responses'));



    //query the database
    $query = "SELECT (user_submit.user_answer) AS 'Answer',  COUNT(user_submit.user_answer) AS 'Total'
        FROM user_submit
        INNER JOIN questions ON user_submit.question_id = questions.question_id
        WHERE user_submit.survey_id = $survey_id AND question_type <> 'Text' AND question_field = '$current_question'
        GROUP BY user_submit.user_answer, user_submit.question_id
        HAVING Answer IS NOT NULL
        ORDER BY Total DESC";
        
    $result = mysqli_query($connection, $query);

    // loop over the rows, outputting them
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($file, $row);
    }
    
    // close the connection
    mysqli_close($connection);
 


}

?>