<?php

// Things to notice:
// This script is called by every other script (via require_once)
// It begins the HTML output, with the customary tags, that will produce each of the pages on the web site
// It starts the session and displays a different set of menu links depending on whether the user is logged in or not...
// ... And, if they are logged in, whether or not they are the admin
// It also reads in the credentials for our database connection from credentials.php

// database connection details:
require_once "credentials.php";

// our helper functions:
require_once "helper.php";

/*
//sets the timeout to 20mins
ini_set('session.gc_maxlifetime',60*20);
session_set_cookie_params(60*20);
*/

// start/restart the session
session_start();

if (isset($_SESSION['loggedInSkeleton']))
{
	// THIS PERSON IS LOGGED IN
	// show the logged in menu options:

echo <<<_END
<!DOCTYPE html>
<html>
	<head>
	<title>A Survey Website</title>
	<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet"> <!--- Open Sans Font from Google Fonts -->
	
	<style>
	body {
	font-family: 'Open Sans', sans-serif;
	line-height:1.6em;	
	font-size: 15px;
	margin-top: 20px;
	margin-left: 2%;
	}

	p {
		width: 50%;
	}
	table {
		border-collapse: collapse;
	}
	td, th {
		border: 1px solid #ddd;
		padding: 8px;
	}
	tr:hover {
		background-color: #ddd;
	}
	th {
		background-color: #4075d5;
		color: white;
	}

	a {
		text-decoration: none;
		color: blue;

	}
	</style>
	</head>
<body>
<a href='about.php'>About</a> ||
<a href='account.php'>My Account</a> ||
<a href='surveys_manage.php'>My Surveys</a> ||
<a href='survey_results.php'>Survey Results</a> ||
<a href='competitors.php'>Design and Analysis</a> ||
<a href='sign_out.php'>Sign Out ({$_SESSION['username']})</a>
_END;
	// add an extra menu option if this was the admin:
	if ($_SESSION['username'] == "admin")
	{
		echo " |||| <a href='admin.php'>Admin Tools</a>";
	}
}
else
{
	// THIS PERSON IS NOT LOGGED IN
	// show the logged out menu options:
	
echo <<<_END
<!DOCTYPE html>
<html>
<head>
<title>A Survey Website</title>
<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet"> <!--- Open Sans Font from Google Fonts -->
<style> 
body {
	font-family: 'Open Sans', sans-serif;
	line-height:1.6em;	
	font-size: 15px;
	margin-top: 20px;
	margin-left: 2%;
}

p {
	width: 50%;
}
table {
	border-collapse: collapse;
}
td, th {
	border: 1px solid #ddd;
	padding: 8px;
}
tr:hover {
	background-color: #ddd;
}
th {
	background-color: #4075d5;
	color: white;
}

a {
	text-decoration: none;
	color: blue;
}

</style>
</head>
<body>
<a href='about.php'>About</a> ||
<a href='sign_up.php'>Sign Up</a> ||
<a href='sign_in.php'>Sign In</a>
_END;
}
echo <<<_END
<br>
<h1>2CWK50: A Survey Website</h1>
_END;
?>