<?php
    session_start();
    $currentpage="Login";
    include "pages.php";
?>
<!DOCTYPE html>
<!-- Login Page -->
<html>
	<head>
		<title>Create Recipe</title>
	</head>
<body>

<?php
    //<script type = "text/javascript"  src = "verifyInput.js" > </script> 
    //<link rel="stylesheet" href="index.css">
	include "header.php";
	$msg = "Create a Recipe";

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
        // get all this user's recipes
        // put them into an array for later use
        // if the array is empty, don't show it

        $recipesSQL = "SELECT R.RecipeName FROM Recipe R WHERE R.UserName=\"$uname\"";
        $result = mysqli_query($conn, $recipesSQL);
        if(!$result){
            die("Could not query recipes");
        }
        $recipes = mysqli_fetch_row($result);

        if($_POST["action"] == "newRecipe"){
            // Initializing new recipe
            $rname = mysqli_real_escape_string($conn, $_POST['rname']);
            $cook = mysqli_real_escape_string($conn, $_POST['cookTime']);
            // TODO ensure that a duplicate will never be generated
            $newID = rand(1, 999999999);
            $newRecSQL = "INSERT INTO Recipe(RecipeID, Rating,
                                             RecipeName, DateMade,
                                             CookTime, UserName)
                          VALUES('$newID', '0', '$rname',
                                 '" . date(). "', '$cook', '$uname')";
            if(mysqli_query($conn, $newRecSQL)){
                $msg = "New recipe $rname created!";
            }else{
                $msg = "Recipe $rname could not be created.";
            }
        }else if($_POST["action"] == "newIng"){
            // get recipe id
            $rname = mysqli_real_escape_string($conn, $_POST['recipe']);
            $recIDSQL = "SELECT R.RecipeID 
                         FROM Recipe R 
                         WHERE R.RecipeName = '$rname'";
            $result = mysqli_query($conn, $recIDSQL);
            $recID = mysqli_fetch_assoc($result)["RecipeID"];

            // insert new ingredient
            $iname = mysqli_real_escape_string($conn, $_POST['iname']);
            $qty = mysqli_real_escape_string($conn, $_POST['quant']);
            $unit = mysqli_real_escape_string($conn, $_POST['unit']);

            $newIngSQL = "CALL addIngredient('$iname', '$recID',
                                             '$qty', '$unit')";
            if(mysqli_query($conn, $newIngSQL)){
                echo "Added $iname to $rname!";
            }else{
                echo "Add $iname to $rname failed! " . mysqli_error($conn);
            }

        }else if($_POST["action"] == "newInst"){
            // get recipe id
            $rname = mysqli_real_escape_string($conn, $_POST['recipe']);
            $recIDSQL = "SELECT R.RecipeID 
                         FROM Recipe R 
                         WHERE R.RecipeName = '$rname'";
            $result = mysqli_query($conn, $recIDSQL);
            $recID = mysqli_fetch_assoc($result)["RecipeID"];

            // get number of previous instructions for this recipe
            $instNumSQL = "SELECT COUNT(*) as 'num' 
                           FROM `Recipe Instruction` I 
                           WHERE I.RecipeID = '$recID'";
            $result = mysqli_query($conn, $instNumSQL);
            $instNum = mysqli_fetch_assoc($result)["num"];
            $instNum = $instNum + 1;

            $inst = mysqli_real_escape_string($conn, $_POST['inst']);
            echo "You want to add a new instruction!";
            $newInstSQL = "INSERT INTO `Recipe Instruction`(RecipeID,
                                                           InstructionNumber,
                                                           Instruction)
                          VALUES('$recID', '$instNum', '$inst')";
            if(mysqli_query($conn, $newInstSQL)){
                echo "Added \"$inst\" to $rname!";
            }else{
                echo "Add \"$inst\" to $rname failed! " . mysqli_error($conn);
            }
        }
    }else{
        $msg = "You must be logged in to view recipes!";
    }
// close connection
mysqli_close($conn);
?>
<?php if($_SESSION["username"]): ?>
	<section>
    <h2> <?php echo $msg;?> </h2>

<form method="post" id="newRecipe">
<fieldset>
    <p>
        <label for="recName">Recipe Name:</label>
        <input type="text" class="required" name="rname" id="rname">
    </p>
    <p>
        <label for="cooktime">Cook Time:</label>
        <input type="number" class="required" name="cookTime" id="cook">
    </p>
    <input type="hidden" name="action" value="newRecipe">
    <input type ="submit" name="action" value=" Submit " />
</fieldset>
</form>
<form method="post" id="addIng">
<fieldset>
	<legend>Add Ingredient:</legend>
    <input type="hidden" name="action" value="newIng">
    <p>
    <label for="recipe">Recipe:</label>
        <select name="recipe">
        <?php
            foreach($recipes as $rec){
                echo "<option value=\"$rec\">$rec</option>";
            }
        ?>
        </select>
    </p>
    <p>
        <label for="iname">Ingredient Name:</label>
        <input type="text" class="required" name="iname" id="iname">
    </p>
    <p>
        <label for="quant">Quantity:</label>
        <input type="number" class="required" name="quant" id="quant">
    </p>
    <p>
        <label for="unit">Unit:</label>
        <input type="text" class="required" name="unit" id="unit">
    </p>
    <p>
    <input type = "submit"  value = "Submit" />
    <input type = "reset"  value = "Clear Form" />
    </p>
</fieldset>
</form>
<form method="post" id="addInst">
<fieldset>
    <p>
        <label for="recipe">Recipe:</label>
        <select name="recipe">
        <?php
            foreach($recipes as $rec){
                echo "<option value=\"$rec\">$rec</option>";
            }
        ?>
        </select>
    </p>
	<legend>Instruction:</legend>
    <input type="hidden" name="action" value="newInst">
    <p>
        <label for="inst">Instruction Text:</label>
        <input type="text" class="required" name="inst" id="inst">
    </p>
    <p>
        <input type = "submit"  value = "Submit" />
        <input type = "reset"  value = "Clear Form" />
    </p>
</fieldset>
</form>
<?php else: ?>
    <h2> <?php echo $msg;?> </h2>
<?php endif; ?>

</body>
</html>
