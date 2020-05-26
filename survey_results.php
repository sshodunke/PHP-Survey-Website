<?php

// includes the header in the page
require_once 'header.php';

//should the landing page be shown?
$show_landing = false;

// counter for chart ids
$counter = 0;

// user isn't logged in, display a message saying they must be:
if (!isset($_SESSION['loggedInSkeleton'])) {
	echo "You must be logged in to view this page.<br>";
}

elseif(isset($_GET['view_survey_stats'])) {
	// connect directly to our database (notice 4th argument):
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	// if the connection fails, we need to know, so allow this exit:
	if (!$connection) {
		die("Connection failed: " . $mysqli_connect_error);
    }

    // gets the survey title using get method
    $survey_name = $_GET['view_survey_stats'];
    $survey_id = $_GET['survey_id'];
   
    /* --------------------------------------- SURVEY RESPONSES ---------------------------------------    */

    echo "<h2> $survey_name </h2>";

    // total number the survey has been took
    $query = "SELECT survey_responses FROM surveys WHERE survey_id = $survey_id";
    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_assoc($result);

    // message to display on the screen - depends on amount of times survey has been took
    if ($row['survey_responses'] > 0) {
        echo "<p> This survey has been taken a total of " . $row['survey_responses'] . " times";
    }
    elseif($row['survey_responses'] == 1) {
        echo "<p> This survey has been taken once </p>";
    }
    else {
        echo "<p> This survey has not yet been taken <p>";
    }

    // Select/Radio Question Results

    $query = "SELECT DISTINCT(question_field), question_type
        FROM questions
        WHERE question_type <> 'Text' AND survey_id = '$survey_id'";

    // display results for radio/select questions
    $result = mysqli_query($connection, $query);
    $n = mysqli_num_rows($result);
    if ($result) {
        // if a question is found which does not have the question type 'text' then....
        if($n > 0) {
            echo "<h3> <u> Multiple Choice Results </u> </h3>";
            // loops the amount of times a radio/select question is found in db
            for($i = 0; $i < $n; $i++) {

                // echoes question heading
                $row = mysqli_fetch_assoc($result);
                $current_question = $row['question_field'];
                echo "<h3> " . $row['question_field'] . "</h3>";

                // this query selects questions, user submit answers, counts the amount times a radio/select option was picked and if the question type is radio/select
                $query2 = "SELECT questions.question_field AS 'Questions', (user_submit.user_answer) AS 'Answer',  COUNT(user_submit.user_answer) AS 'Total', questions.question_type AS 'question_type'
                    FROM user_submit
                    INNER JOIN questions ON user_submit.question_id = questions.question_id
                    WHERE user_submit.survey_id = $survey_id AND question_type <> 'Text' AND question_field = '" . $row['question_field'] . "'
                    GROUP BY user_submit.user_answer, user_submit.question_id
                    HAVING Answer IS NOT NULL
                    ORDER BY Questions, Total DESC";
    
                $result2 = mysqli_query($connection, $query2);
                $n2 = mysqli_num_rows($result2);
    
                //this query is used to count the number of times the question has been took
                $query3 = "SELECT questions.question_field AS 'Questions', (user_submit.user_answer) AS 'Answer'
                    FROM user_submit
                    INNER JOIN questions ON user_submit.question_id = questions.question_id
                    WHERE user_submit.survey_id = $survey_id AND question_type <> 'Text' AND question_field = '" . $row['question_field'] . "'
                    GROUP BY user_submit.submit_id
                    HAVING Answer IS NOT NULL";
    
                $result3 = mysqli_query($connection, $query3);
                $n3 = mysqli_num_rows($result3);
                
                // if the question has been took more than once then...
                if ($n3 > 1) {
                    echo "<p> This question has a total of $n3 responses";
                    if ($result2) {
                        // table
                        echo "<table>";
                        echo "<tr><th>Answer Choices</th><th>Responses</th><th></th></tr>";
                        // loops amount of times a row was returned from query2
                        for($j = 0; $j < $n2; $j++) {
                            $row2 = mysqli_fetch_assoc($result2);
                            // inserts it into db
                            echo "<tr> <td> " . $row2['Answer'] . " </td> <td>" . $row2['Total'] . "</td> <td>" . round(($row2['Total']   /  $n3)*100,1) . "%</td> </tr>";
                        }
                        echo "</table><br>";
                        echo "<form method='post' action='export.php'>";
                        echo "<input type='hidden' name='survey_id' value='$survey_id'>";
                        echo "<input type='hidden' name='question' value='$current_question'>";   
                        // exports the table result into csv                     
                        echo "<input type='submit' name='export_csv' value='Export'>";
                        echo "</form>";
                    

                    // highcharts script
                    echo <<<_END
                    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
                    <script src="https://code.highcharts.com/highcharts.js"></script>
                    <script src="https://code.highcharts.com/modules/exporting.js"></script>
                    <script src="https://code.highcharts.com/modules/export-data.js"></script>
                    <div id="$counter" style="min-width: 20px; height: 400px; max-width: 300px"></div>
                    <script>

                        // Build the chart
                        Highcharts.chart('$counter', {
                            chart: {
                                plotBackgroundColor: null,
                                plotBorderWidth: null,
                                plotShadow: false,
                                type: 'pie'
                            },
                            title: {
                                text: 'Results'
                            },
                            tooltip: {
                                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                            },
                            plotOptions: {
                                pie: {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    dataLabels: {
                                        enabled: false
                                    },
                                    showInLegend: true
                                }
                            },
                            series: [{
                                name: 'Results',
                                colorByPoint: true,
                                data: [
_END;
                            // same query as query 2 from above
                            $query = "SELECT questions.question_field AS 'Questions', (user_submit.user_answer) AS 'Answer',  COUNT(user_submit.user_answer) AS 'Total', questions.question_type AS 'question_type'
                            FROM user_submit
                            INNER JOIN questions ON user_submit.question_id = questions.question_id
                            WHERE user_submit.survey_id = $survey_id AND question_type <> 'Text' AND question_field = '$current_question'
                            GROUP BY user_submit.user_answer, user_submit.question_id
                            HAVING Answer IS NOT NULL
                            ORDER BY Questions, Total DESC";

                            // echos the name of the option and the total
                            $result4 = mysqli_query($connection, $query);
                            for($l = 0; $l <$n2; $l++){
                                $row3 = mysqli_fetch_assoc($result4);
                                echo "{name:' " . $row3['Answer'] . "' ,y:" . $row3['Total'] . " }, ";
                            }

                            echo <<<_END

                        
                                ],
                                }]
                            });
                            </script>
_END;
                    }
                }
                // if the question has only been taken once then...
                else if ($n3 == 1) {
                    echo "<p> This question has been taken once";
                    if ($result2) {
                        echo "<table>";
                        echo "<tr><th>Answer Choices</th><th>Responses</th><th></th></tr>";
                        for($j = 0; $j < $n2; $j++) {
                            $row2 = mysqli_fetch_assoc($result2);
                            echo "<tr> <td> " . $row2['Answer'] . " </td> <td>" . $row2['Total'] . "</td> <td>" . round(($row2['Total']   *  100)/$n2,2) . "%</td> </tr>";
                        }
                        echo "</table><br>";
                    }
                }
                else {
                    echo "This question has not been taken";
                }
                $counter++;
            }
        }
    }

    // Text Question Results

    $query = "SELECT DISTINCT(question_field), question_type
        FROM questions
        WHERE question_type = 'Text' AND survey_id = '$survey_id'";
    
    // display results for text questions
    $result = mysqli_query($connection, $query);
    $n = mysqli_num_rows($result);
    if ($result) {
        // if a question is found which has the question type 'text' then....
        if ($n > 0) {
            // heading
            echo "<br><h3><u> Text Results </u></h3>";
            
            for($i = 0; $i < $n; $i++) {
                
                // echoes question heading
                $row = mysqli_fetch_assoc($result);
                echo "<h3> " . $row['question_field'] . "</h3>";
    
                // this query selects questions, user submit answers, counts the amount times a text option was picked and if the question type is text 
                $query2="SELECT questions.question_field AS 'Questions', (user_submit.user_answer) AS 'Answer',  COUNT(user_submit.user_answer) AS 'Total', questions.question_type AS 'question_type'
                    FROM user_submit
                    INNER JOIN questions ON user_submit.question_id = questions.question_id
                    WHERE user_submit.survey_id = $survey_id AND question_type = 'Text' AND question_field = '" . $row['question_field'] . "'
                    GROUP BY user_submit.user_answer, user_submit.question_id
                    HAVING Answer IS NOT NULL AND Answer <>  ''
                    ORDER BY Total DESC";
                
                $result2 = mysqli_query($connection, $query2);
                $n2 = mysqli_num_rows($result2);

                //this query is used to count the number of times the question has been took

                $query3 = "SELECT questions.question_field AS 'Questions', (user_submit.user_answer) AS 'Answer'
                    FROM user_submit
                    INNER JOIN questions ON user_submit.question_id = questions.question_id
                    WHERE user_submit.survey_id = $survey_id AND question_type = 'Text' AND question_field = '" . $row['question_field'] . "'
                    GROUP BY user_submit.submit_id
                    HAVING Answer IS NOT NULL AND Answer <> ''";

                $result3 = mysqli_query($connection, $query3);
                $n3 = mysqli_num_rows($result3);

                // if the question has been took more than once then....
                if ($n3 > 1) {
                    echo "<p> This question has a total of $n3 responses";
                    if ($result2) {
                        echo "<table>";
                        echo "<tr><th>Responses</th><th>Count</th></tr>";
                        for($j = 0; $j < $n2; $j++) {
                            $row2 = mysqli_fetch_assoc($result2);
                            echo "<tr> <td> " . $row2['Answer'] . " </td> <td> " . $row2['Total'] . " </tr>";
                        }
                        echo "</table><br>";
                    }
                }

                // if the question has only been taken once...
                elseif($n3 == 1) {
                    echo "<p> This question has been taken once";
                    if ($result2) {
                        echo "<table>";
                        echo "<tr><th>Responses</th><th>Count</th></tr>";
                        for($j = 0; $j < $n2; $j++) {
                            $row2 = mysqli_fetch_assoc($result2);
                            echo "<tr> <td> " . $row2['Answer'] . " </td> <td> " . $row2['Total'] . " </tr>";
                        }
                        echo "</table><br>";
                    }

                }
                // if the question has not been taken...
                else {
                    echo "This question has not been taken";
                }



            }
        }
    }
}

// default view for page visit
else {
    $show_landing = true;
}

// landing page
if ($show_landing) {
    
    // connect directly to our database (notice 4th argument):
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	// if the connection fails, we need to know, so allow this exit:
	if (!$connection) {
		die("Connection failed: " . $mysqli_connect_error);
    }

    // holds username obtained from session variable
    $username = $_SESSION['username'];

    // if admin is logged in, admin can view all survey results
    if ($_SESSION['username'] == "admin") {
        // list surveys created by the user
        $query = "SELECT * FROM surveys";
        $result = mysqli_query($connection, $query);
    }
    else {
        // list surveys created by the user
        $query = "SELECT * FROM surveys WHERE survey_author ='$username'";
        $result = mysqli_query($connection, $query);       
    }
    
    $n = mysqli_num_rows($result);
    
    // displays surveys with button to view survey results
    if($result) {
        if ($n > 0) {
            echo "<h2> Surveys </h2>";
            while($row = mysqli_fetch_assoc($result)) {
                echo "<form action='survey_results.php' method='get'>";
                $survey_name = $row['survey_name'];
                $survey_id = $row['survey_id'];
                echo "<input type='hidden' name='survey_id' value='$survey_id'>";
                echo $survey_name  . "<button type='submit' name='view_survey_stats' value='$survey_name'> View Stats </button><br>";
                echo "</form>";
            }
        }
        else {
            echo "<p>You do not have any surveys.</p>";
        }
    }

    else {
        echo "Error, could not obtain surveys";
    }

	// we're finished with the database, close the connection:
	mysqli_close($connection);    
}

require_once "footer.php";
?>