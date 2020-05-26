<?php

// execute the header script:
require_once "header.php";

echo <<<_END
<p>A survey website created by Smith Shodunke.</p>

<p>Features of this website include survey creation, survey management, account management and survey result analysis. </p>

<p>Creating surveys is quick and simple as the website guides you step by step guide.<br>
After creating a survey and adding questions you may further edit the survey by making a question mandatory or 
adding a new question to the survey.</p>

<p>Survey results can also be viewed via the Survey Results tab. 
Click the survey you wish to view and a quick summary of the responses the survey has obtained will be displayed. 
The pie chart can be filtered by clicking on legend below it.</p>

_END;

// finish of the HTML for this page:
require_once "footer.php";

?>