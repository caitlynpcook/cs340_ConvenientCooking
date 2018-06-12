<!DOCTYPE html>
<?php
    session_start();
    $currentpage="Reccipes";
    include "pages.php";
?>
<html>
	<head>
		<title>Recipes</title>
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
    //
    // TODO If sessions ever start working again, remove this file
    include 'fake_session.php';

    // query for all list names
    if($_SESSION["username"]){
        $uname = $_SESSION["username"];
        $query = "SELECT R.RecipeName 
            FROM Recipe R 
            WHERE R.UserName = '$uname'";
        
        // Get results from query
        $result = mysqli_query($conn, $query);
        if (!$result) {
            die("Query to show list names from table failed");
        }
        // Show all the user's lists
        echo "<h1>Recipes:</h1>";
        echo '<form method="post">';
        echo "<input type=\"hidden\" name=\"action\" value=\"viewRecipe\">";
        while($row = mysqli_fetch_row($result)) {	
            foreach($row as $cell){
                echo "<input type='submit'";
                if($_POST['listname'] == $cell) // highlight current recipe
                    echo "class='active' "; 
                echo "name='recname' value='$cell'>";
            }
        }
        echo "</form>";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $rname = $_POST['recname'];
            if($_POST["action"] == "viewRecipe"){
                // Display ingredients and instructions of active recipe
                $ingrSQL = "SELECT H.Quantity, H.Unit, I.IngrName as 'Ingredient'
                            FROM Ingredients I, `Recipe Has` H,
                                 Recipe R
                            WHERE I.IngrID = H.IngrID
                            AND R.RecipeID = H.RecipeID
                            AND R.RecipeName = '$rname'";
                $result = mysqli_query($conn, $ingrSQL);
                if (!$result) {die("Query to show list contents failed");}

                // output list ingredients
                $fields_num = mysqli_num_fields($result);
                echo "<h1>Ingredients:</h1>";
                echo "<table id='t01' border='1'><tr>";
                for($i=0; $i<$fields_num; $i++){	
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
                echo "</table>";
                // get instructions 
                $ingrSQL = "SELECT I.InstructionNumber, I.Instruction
                            FROM `Recipe Instruction` I, Recipe R
                            WHERE R.RecipeID = I.RecipeID
                            AND R.RecipeName = '$rname'";
                $result = mysqli_query($conn, $ingrSQL);
                if(!$result){die("Query to show instructions failed");}

                // button to send list to shopping cart
                $listsSQL = "SELECT S.ListName 
                             FROM `Shopping List` S
                             WHERE S.UserName = '$uname'";
                $result = mysqli_query($conn, $listsSQL);
                if(!$result){die("Could not query lists");}

                echo "<form method=\"post\">";
                echo "<input type=\"hidden\" name=\"action\" value=\"addToList\">";
                echo "<input type=\"hidden\" name=\"recname\" value=\"$rname\">";
                echo "<label for=\"shoppinglist\">Add to list:</label>";
                echo "<select name=\"shoppinglist\">";
                while($shoppinglists = mysqli_fetch_assoc($result)){
                    foreach($shoppinglists as $list){
                        echo "<option value=\"$list\">$list</option>";
                    }
                }
                echo "</select>\n</p>";
                echo "<input type = \"submit\"  value = \"Submit\" />";
                echo "</form>";
            }else if($_POST["action"] == "addToList"){
                $lname = $_POST["shoppinglist"];
                echo "You are adding a recipe to a list!";
                // call proc to add a recipe to a shopping list
                $listIDsql = "SELECT L.ListID FROM `Shopping List` L
                              WHERE L.ListName='$lname'";
                $result = mysqli_query($conn, $listIDsql);
                if(!$result){die("Could not query list names");}
                $listID = mysqli_fetch_assoc($result)["ListID"];

                $recIDsql = "SELECT R.RecipeID FROM Recipe R
                              WHERE R.RecipeName='$rname'";
                $result = mysqli_query($conn, $recIDsql);
                if(!$result){die("Could not query recipe id");}
                $recID = mysqli_fetch_assoc($result)["RecipeID"];

                $addRecSQL = "CALL addToListHas('$listID', '$recID')";
                if(mysqli_query($conn, $addRecSQL)){
                    echo "Added $rname to $lname!";
                }else{
                    echo "Add $rname to $lname failed! " . mysqli_error($conn);
                }
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
