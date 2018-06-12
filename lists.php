<!DOCTYPE html>
<?php
    session_start();
    $currentpage="Shopping Lists";
    include "pages.php";
?>
<html>
	<head>
		<title>Shopping Lists</title>
		<!-- <link rel="stylesheet" href="index.css"> -->
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

    // TODO If sessions ever start working again, remove this file
    include 'fake_session.php';

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
        echo "<form method=\"post\">";
        echo "<input type=\"hidden\" name=\"action\" value=\"displayList\">";
        while($row = mysqli_fetch_row($result)) {	
            foreach($row as $cell){
                echo "<input type='submit'";
                if($_POST['listname'] == $cell) // highlight current recipe
                    echo "class='active' "; 
                echo "name='listname' value='$cell'>";
            }
        }
        echo "</form>";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if($_POST["action"] == "newList"){
                $lname = $_POST["lname"];
                $newID = rand(1, 999999999);

                $idTestSQL = "SELECT COUNT(*) 
                              FROM `Shopping List` S 
                              WHERE S.ListID = '$newID'";
                $result = mysqli_query($conn, $idTestSQL);
                $idTaken = mysqli_fetch_assoc($result)["num"];
                while($idTaken != 0){
                    $newID = rand(1, 999999999);

                    $idTestSQL = "SELECT COUNT(*) as 'num'
                                  FROM `Shopping List` S 
                                  WHERE S.ListID = '$newID'";
                    $result = mysqli_query($conn, $idTestSQL);
                    $idTaken = mysqli_fetch_assoc($result)["num"];
                }

                $newListSQL = "INSERT INTO `Shopping List`(ListID,
                                                           ListName,
                                                           UserName)
                              VALUES('$newID', '$lname', '$uname')";
                if(mysqli_query($conn, $newListSQL)){
                    $msg = "New list $lname created!";
                }else{
                    $msg = "List $lname could not be created.";
                }
            }else if($_POST["action"] == "displayList"){
                // Display ingredients of active list
                $lname = $_POST["listname"];
                // get listname
                // from listhas get ingredients
                $ingrSQL = "SELECT H.Quantity, H.Unit, I.IngrName as 'Ingredient'
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
        }   
    }else {
        $msg = "You must be logged in to view shopping lists!";
    }

	mysqli_free_result($result);
	mysqli_close($conn);
?>
    <h2> <?php echo "$msg"; ?> </h2>
<form method="post" id="newRecipe">
<fieldset>
	<legend>New List:</legend>
    <p>
        <label for="listName">List Name:</label>
        <input type="text" class="required" name="lname" id="lname">
    </p>
    <input type="hidden" name="action" value="newList">
    <input type ="submit" value=" Submit " />
</fieldset>
</form>
</body>

</html>
