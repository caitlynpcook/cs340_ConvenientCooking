<!DOCTYPE html>
<!-- Login Page -->
<?php
    session_start();
    $currentpage="Login";
    include "pages.php";
?>
<html>
	<head>
		<title>Login/New User</title>
	</head>
<body>

<?php
    //<script type = "text/javascript"  src = "verifyInput.js" > </script> 
    //<link rel="stylesheet" href="index.css">
	include "header.php";
	$msg = "Login or Create Account";

// change the value of $dbuser and $dbpass to your username and password
	include 'connectvars.php'; 
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	if (!$conn) {
		die('Could not connect: ' . mysql_error());
	}
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if($_POST['action'] == "login"){
            // Login process
            $uname = mysqli_real_escape_string($conn, $_POST['uname']);
            $pswd = mysqli_real_escape_string($conn, $_POST['pswd']);

            $queryIn = "SELECT Salt, Password FROM User where UserName='$uname' ";
            $resultIn = mysqli_query($conn, $queryIn);

            if($row = mysqli_fetch_assoc($resultIn)){
                $salt = $row['Salt'];
                $salted_pwd = MD5("$pswd$salt");

                $saltSQL = "SELECT UserName FROM User WHERE UserName ='$uname' AND Password ='$salted_pwd'";

                $loginres = mysqli_query($conn, $saltSQL);
                if(!$loginres){
                    die('Login query failed');
                }
                if($loginrow = mysqli_fetch_assoc($loginres)){
                    $msg = "Login successful!";
                    if(!$_SESSION["username"])
                        $_SESSION["username"] = $uname;
                } else {
                    $msg = "Incorrect username or password!";
                }
            } else {
                $msg = "Incorrect username or password!";
            }
        }
        else if($_POST['action'] == "register"){
            $uname = mysqli_real_escape_string($conn, $_POST['uname']);
            $fname = mysqli_real_escape_string($conn, $_POST['fname']);
            $lname = mysqli_real_escape_string($conn, $_POST['lname']);
            $pswd = mysqli_real_escape_string($conn, $_POST['pswd']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $today = mysqli_real_escape_string($conn, $_POST['date']);
            $salt = base64_encode(mcrypt_create_iv(12, MCRYPT_DEV_URANDOM));

            $queryIn = "SELECT * FROM User where UserName='$uname' ";
            $resultIn = mysqli_query($conn, $queryIn);
            if(mysqli_num_rows($resultIn) > 0) {
                $msg = "Username $uname is taken, choose another<p>";
            } else {
                $query = "INSERT INTO User(UserName, FirstName, LastName,
                                            Email, Password, JoinDate, salt)
                          VALUES ('$uname', '$fname', '$lname', '$email',
                                   MD5('$pswd$salt'), '$today', '$salt')";
                if(mysqli_query($conn, $query)){
                    $msg = "Record added successfully.";
                } else {
                    echo "ERROR: Could not execute $query. " . mysqli_error($conn);
                }
            }
        }
}
// close connection
mysqli_close($conn);

?>
	<section>
    <h2> <?php echo $msg; ?> </h2>

<form method="post" id="login">
<fieldset>
	<legend>Login:</legend>
    <p>
        <label for="uname">Username:</label>
        <input type="text" class="required" name="uname" id="uname">
    </p>
    <p>
        <label for="pswd">Password:</label>
        <input type="text" class="required" name="pswd" id="pswd">
    </p>
    <input type="hidden" name="action" value="login">
</fieldset>
      <p>
        <input type = "submit"  value = "Submit" />
        <input type = "reset"  value = "Clear Form" />
      </p>
</form>
<form method="post" id="register">
<fieldset>
	<legend>Register:</legend>
    <p>
        <label for="fname">First Name:</label>
        <input type="text" class="required" name="fname" id="fname">
    </p>
    <p>
        <label for="lname">Last Name:</label>
        <input type="text" class="required" name="lname" id="lname">
    </p>
    <p>
        <label for="uname">Username:</label>
        <input type="text" class="required" name="uname" id="uname">
    </p>
    <p>
        <label for="pswd">Password:</label>
        <input type="text" class="required" name="pswd" id="pswd">
    </p>
    <p>
        <label for="email">Email:</label>
        <input type="text" class="required" name="email" id="email">
    </p>
    <input type="hidden" name="action" value="register">
    <input type="hidden" name="date" value="<?php echo date();?>">
</fieldset>
      <p>
        <input type = "submit"  value = "Submit" />
        <input type = "reset"  value = "Clear Form" />
      </p>
</form>
</body>
</html>
