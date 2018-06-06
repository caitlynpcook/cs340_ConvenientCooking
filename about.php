<!DOCTYPE html>
<?php
		$currentpage="Shopping Lists";
		include "pages.php";
?>
<html>
	<head>
		<title>About Us</title>
		<link rel="stylesheet" href="index.css">
	</head>
<body>


<?php
// change the value of $dbuser and $dbpass to your username and password
	include 'connectvars.php'; 
	include 'header.php';	

	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	if (!$conn) {
		die('Could not connect: ' . mysql_error());
	}	

	// mysqli_free_result($result);
	mysqli_close($conn);
?>

<h1> What is this? </h1>
<p> This is a class project for CS 340. We are developing an application to store recipes and build shopping lists from them to gain familiarity with SQL and databases.</p>

<h1> Contact us </h1>
<p>You can reach us (Caitlyn Cook, Nate Pelzl, Vincent Nguyen, and Jonathan Rohr) by email at cookcai@oregonstate.edu</p>

<h1> FAQs </h1>
<p> This section will be filled out if people ever ask us anything. </p>

</body>

</html>
