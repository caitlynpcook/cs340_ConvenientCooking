<!DOCTYPE html>
<?php
    session_start();
    $currentpage="Shopping Lists";
    include "pages.php";
?>
<html>
	<head>
		<title>Shopping Lists</title>
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

    if($_SESSION["username"]){
        $uname = $_SESSION["username"];
        // query for all list names
        // screen for no active list
        // screen for active list
        //      list all ingredients
        $query = "SELECT L.ListName FROM `Shopping List` L WHERE L.UserName = '$uname'";
        
    // Get results from query
        $result = mysqli_query($conn, $query);
        if (!$result) {
            die("Query to show list names from table failed");
        }
        echo "<h1>Shopping Lists:</h1>";
        echo '<form method="post">';
        while($row = mysqli_fetch_row($result)) {	
            // $row is array... foreach( .. ) puts every element
            // of $row to $cell variable
            foreach($row as $cell){
                echo "<input type='submit'";
                if($_POST['listname'] == $cell) // highlight current recipe
                    echo "class='active' "; 
                echo "name='listname' value='$cell'>";
            }
        }
        echo "</form>";

    // Display ingredients of active list
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $lname = $_POST['listname'];
            // get listname
            // from listhas get ingredients
            $ingrSQL = "SELECT H.Quantity, H.Unit, I.IngrName
                        FROM Ingredients I, `List Has` H, `Shopping List` L
                        WHERE I.IngrID = H.IngrID
                        AND H.ListID = L.ListID
                        AND L.ListName = '$lname'";
                        
            $result = mysqli_query($conn, $ingrSQL);
            if (!$result) {
                die("Query to show list contents from table failed");
            }

            // output list ingredients
            $fields_num = mysqli_num_fields($result);
            echo "<h1>Ingredients:</h1>";
            echo "<table id='t01' border='1'><tr>";
            
            // printing table headers
            for($i=0; $i<$fields_num; $i++) {	
                $field = mysqli_fetch_field($result);	
                echo "<td><b>$field->name</b></td>";
            }
            echo "</tr>\n";
            // ingredient contents
            while($row = mysqli_fetch_row($result)) {	
                echo "<tr>";	
                for($i=0; $i<$fields_num; $i++) {	
                    echo "<td>" . $row[$i] . "</td>";	
                }
                echo "</tr>\n";
            }
        }
    }else {
        echo "You must be logged in to view recipes!";
    }

	mysqli_free_result($result);
	mysqli_close($conn);
?>
</body>

</html>
