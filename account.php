<!DOCTYPE html>
<!-- Login Page -->
<?php
		$currentpage="Account";
		include "pages.php";
?>
<html>
	<head>
		<title>Account information</title>
	</head>
<body>

<?php
    //<script type = "text/javascript"  src = "verifyInput.js" > </script> 
    //<link rel="stylesheet" href="index.css">
	include "header.php";
	$msg = "View Account";

// change the value of $dbuser and $dbpass to your username and password
	include 'connectvars.php'; 
    // TODO If sessions ever start working again, remove this file
    include 'fake_session.php';
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	if (!$conn) {
		die('Could not connect: ' . mysql_error());
	}
    if($_SESSION["username"]){
        $uname = $_SESSION["username"];
        
        $accInfoSQL = "SELECT FirstName as 'First Name',
                              LastName as 'Last Name',
                              UserName as 'Username',
                              JoinDate as 'Join Date',
                              Email
            FROM User WHERE UserName = '$uname'";
        $result = mysqli_query($conn, $accInfoSQL);
        if(!$result){
            die("Could not query recipes");
        }
        // get number of columns in table	
        $fields_num = mysqli_num_fields($result);
        echo "<table id='t01' border='1'><tr>";
        
        // printing table headers
        for($i=0; $i<$fields_num; $i++) {	
            $field = mysqli_fetch_field($result);	
            echo "<td><b>$field->name</b></td>";
        }
        echo "</tr>\n";
        while($row = mysqli_fetch_row($result)) {	
            echo "<tr>";	
            // $row is array... foreach( .. ) puts every element
            // of $row to $cell variable	
            foreach($row as $cell)		
                echo "<td>$cell</td>";	
            echo "</tr>\n";
        }
    }

// close connection
mysqli_close($conn);
?>
	<section>
    <h2> <?php echo $msg; ?> </h2>
</body>
</html>
