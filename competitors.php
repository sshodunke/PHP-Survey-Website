<?php

// Things to notice:
// You need to add your Analysis and Design element of the coursework to this script
// There are lots of web-based survey tools out there already.
// It’s a great idea to create trial accounts so that you can research these systems. 
// This will help you to shape your own designs and functionality. 
// Your analysis of competitor sites should follow an approach that you can decide for yourself. 
// Examining each site and evaluating it against a common set of criteria will make it easier for you to draw comparisons between them. 
// You should use client-side code (i.e., HTML5/JavaScript/jQuery) to help you organise and present your information and analysis 
// For example, using tables, bullet point lists, images, hyperlinking to relevant materials, etc.

// execute the header script:
require_once "header.php";

if (!isset($_SESSION['loggedInSkeleton']))
{
	// user isn't logged in, display a message saying they must be:
	echo "You must be logged in to view this page.<br>";
}
else
{
	echo <<<_END
	
	<h2><u> Google Form </u></h2>

	<h3> Layout/Presentation of Surveys </h3>

	<p> Google Forms uses a layout that is consistent with the rest of the other google products such as Google Drive. 
	When first visiting google forms you will be asked to sign in using your google account. 
	After signing in you will be brought to your dashboard where you may start a new survey form or view an existing one. 
	When creating a new form, you may choose from a template if you desire or start from a blank form. </p>

	<p> When creating a new form from the blank template 
	on the first visit you may be greeted with a tutorial to “Learn about the new Google Forms” which gives you a quick run-down of how to set up a form. </p>

	<p> Google forms easily gives you the option to select what type of question you would like to add to the form via a select button 
	and gives you access to all the options you could need to create a form in a single page. </p>

	<p> In the create form page you may name the title and also give the form a description. 
	In the same page you are given the option to create multiple questions, add an image, video or a section via icons on the sidebar. </p>

	<p> When creating a question you may duplicate the question, delete the question and decide if the question is mandatory. </p>

	<h3> Ease of use </h3>

	<p> Google forms is easy to use as it keeps all useful features in a single page and highlights and annotates these features for the user. 
	Google forms having the same layout as other google apps such as Google drive also makes it so that users that are
	experienced with other google apps can navigate through google forms with ease if they are new to the application. </p>

	<p> When selecting or creating a survey you are brought to a page which has two tabs, 'Questions' and 'Responses'. 
	This tab layout makes it easy for the user to amend questions in response to the answers obtained from the survey. </p>

	<h3> User Account set-up/login process </h3>

	<p> Google forms requires that you have a google account to use the app. 
	Creating a google account is easy and many people may already have a google account or are familiar with the account. 
	If the user already has a google account then there is no need to create another account as google forms is linked to your google account. </p>

	<h3> Question Types </h3>

	<p> Google forms has a wide range of question types from short answers, paragraphs, checkboxes to checkbox grids and linear scale questions. 
	All of these questions can easily be selected through a select button. </p>

	<h3> Analysis Tool </h3>

	<p> Google forms uses the google charts API to display survey results, short text/paragraph question results are display through a list. 
	The survey results may be exported to a csv file. Google forms also gives the user the option to view individual survey responses. </p>

	<h3> Inspiration </h3>

	<p> Google forms inspired my website as I have used their survey results page to give me an idea of how I want the survey results page laid out; 
	although google forms results page is very bare minimum and only offers survey responses in a pie chart I liked how clean it looked. </p>

	<br>

	<h2><u> Survey Monkey </u></h2>

	<h3> Layout presentation of surveys </h3>

	<p> Survey monkeys displays surveys via the landing page. 
	The landing page gives users a quick overview of surveys the users have open, drafts, total number of survey responses, average completion rate of each survey 
	and even the average amount of time a user spent completing the survey. </p>

	<p> When first logging in to survey monkeys you will arrive at the landing page which will give you a walkthrough of making your first survey. </p>

	<p> Survey Monkey will then request for you to name your survey and pick a category which matches your survey. 
	After completing this step you are taken to the question creation page where you will be prompted to name your question and pick the 
	question type you want the question to be.<br>
	Completing these two field will open up another form which will show the choices available for the answer; 
	Survey monkey also has template options such as 'Agree - Disagree' which will automatically make some answer choices for you. </p>

	<p> After designing your questions you will be taken to the preview page where you may preview the survey you have just made in multiple platforms 
	such as mobile and pc. </p>

	<p> After you are done with the preview of the survey Survey Monkey will recommend the user a few methods of sharing the survey such as getting a direct 
	link or sending the survey by email. </p>

	<h3> Ease of use </h3>

	<p> Survey monkey is a friendly and easy to use website as when first arriving to pages that has never been visited by the user 
	the website offers to give the user a walkthrough of how to use features available on the page, shortcuts and how to navigate it. </p>

	<p> Survey monkey also gives users many templates to choose from (over a hundred) when creating a survey if the user needs some inspiration. 
	Survey monkey also shows the popularity of the template and the amount of time the survey may take to complete. </p>

	<h3> User account set-up/login process </h3>

	<p> Creating an account with SurveyMonkey is fairly easy and most users will be familiar with it 
	just involves creating a username, password and inputting your email. Survey monkey also gives users the option to sign up with the 
	following services - Google, Facebook, Office 365 and LinkedIn. </p>

	<p> Survey Monkey accounts that have just been created are limited. Certain features are locked behind a subscription service. 
	The basic account that is limited cannot create unlimited amount of surveys, have unlimited questions per survey, over 1000 responses per month and more. </p>

	<h3> Question Types </h3>

	<p> Survey monkey has a 14 different question types from the user to pick from. 
	Basic question types such as dropdown, multiple choice, checkbox and advanced ones such as star rating and 
	contact information which displays a contact form(name, address, country, etc..) </p>

	<h3> Analysis Tools </h3>

	<p> Survey monkey gives users a wide range of tools to analyse the responses from survey. 
	There are 8 different charts to pick from when deciding how to display results in a chart ranging from horizontal 
	bar charts to line and area graphs. Upgrading to a paid account also gives the user the ability to export data 
	to various file formats such as pdf, xls and csv. </p>

	<h3> Inspiration </h3> 

	<p> Survey monkey may be my favourite survey website out of the three that I analysed. 
	Survey monkey has a very clean landing page and gives new users a guide on how to use most of its features. 
	This website inspired the design layout of my own survey website and made me want to keep it clean. 
	If I have had more time I would have liked to also implement a table that shows the total number of responses a survey has on the survey page. </p>

	<br>

	<h2><u> Survey Planet </u></h2>

	<h3> Layout/presentation of surveys </h3>

	<p> When first visiting the landing page you will be prompted to create a survey if none have been created. 
	If surveys have already been created then the landing page will display created surveys in a box view. 
	A survey box show the amount of responses the survey has, last time a survey response was created and a 
	slider to activate or deactivate the survey. Icons are also displayed in these boxes to access the survey settings, questions, preview and more. </p>

	<p> When creating a new survey a pop up box will appear upon clicking a button which will prompt 
	the user for the survey title, a welcome message and if an email is required or optional for the people taking the survey. </p>

	<p> You will then have the option to create a brand new question or pick a template and work from there. </p>

	<p> When creating a new question you will be brought to a page which displays a sidebar to the left of the screen where you may edit the question 
	such as: name the question, question type, choices etc. To the right is the preview of what the question will look like. 
	After the user saves the question, the user may go back and add another question or activate the survey and then share it. </p>

	<p> Survey planet also gives you the option to pick a theme for your surveys. However many of these are locked behind a subscription service. </p>

	<h3> Ease of use </h3>

	<p> Survey Planet may be a bit confusing to new users as it does not have a guide/walkthrough such as the other 
	survey websites mentioned previously. However the landing page is not as cluttered as Survey monkey making it very easy to get used to. </p>

	<h3> User account set-up/login process </h3>

	<p> Setting up an account in survey planet is simple as it only requires you to insert your name, 
	email address and password. After registering you will be sent an email address for confirmation. 
	Unlike the other survey websites mentioned above survey planet does not give you the option to register through other services such as google and facebook. </p>

	<h3> Question types </h3>

	<p> Survey planet has 9 different question types to choose from including basic question types such as multiple choice and more 
	less used question types such as scale. </p>

	<h3> Analysis Tools </h3>

	<p> Survey planet displays survey results through a donut chart. 
	Other charts are also available such as column and bar but these are locked behind a subscription service. 
	The website shows how many participants took the survey and also details like if the person that took the survey is anonymous, 
	location of the person that took the survey, device, time spent, browser and even the date the survey was took. You also have the option to 
	delete a survey response. </p>

	<h3> Inspiration </h3>

	<p> This survey website did not appeal to me as much as the other websites mentioned previously because I was not a fan of the 
	layout/presentation of the survey. I also did not like how the website makes the user focus on the preview rather than the editing of questions. 
	Therefore I did not draw any inspiration from this website. 
	However a positive I can take from this website was that I did like the idea of using themes to customise the survey. </p>

_END;
}

// finish off the HTML for this page:
require_once "footer.php";
?>