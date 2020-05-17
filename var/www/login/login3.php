<?PHP

$uname = "";
$pword = "";
$errorMessage = "";
$num_rows = 0;

//==========================================
//	ESCAPE DANGEROUS SQL CHARACTERS
//==========================================
function quote_smart($value, $handle) {

   if (get_magic_quotes_gpc()) {
       $value = stripslashes($value);
   }

   if (!is_numeric($value)) {
       $value = "'" . mysql_real_escape_string($value, $handle) . "'";
   }
   return $value;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	$uname = $_POST['username'];
	$pword = $_POST['password'];

	$uname = htmlspecialchars($uname);
	$pword = htmlspecialchars($pword);

	//==========================================
	//	CONNECT TO THE LOCAL DATABASE
	//==========================================
	$user_name = "root";
	$pass_word = "SQLPw0rd";
	$database = "aatw";
	$server = "127.0.0.1";

	$db_handle = mysql_connect($server, $user_name, $pass_word);
	$db_found = mysql_select_db($database, $db_handle);

	if ($db_found) {

		$uname = quote_smart($uname, $db_handle);
		$pword = quote_smart($pword, $db_handle);

		$SQL = "SELECT * FROM login WHERE L1 = $uname AND L2 = md5($pword)";
		$result = mysql_query($SQL);
		$num_rows = mysql_num_rows($result);

	//====================================================
	//	CHECK TO SEE IF THE $result VARIABLE IS TRUE
	//====================================================

		if ($result) {
			if ($num_rows > 0) {
				$guid = mysql_fetch_row($result);
                                session_start();
                                $_SESSION['login'] = "1";
                                $_SESSION['guid'] = $guid[0];
                                $_SESSION['uname'] = $uname;
				header ("Location: ../securemenu.php");
			}
			else {
				$SQL = "SELECT * FROM login WHERE L1 = $uname";
				$result = mysql_query($SQL);
			     $num_rows = mysql_num_rows($result);
				if ($num_rows > 0) {
					$errorMessage = "Incorrect password";
				}
			}	
		}
		else {
			$errorMessage = "Error logging on";
		}

	mysql_close($db_handle);

	}

	else {
		$errorMessage = "Error logging on";
	}

}


?>


<html>
<head>
<title>Login</title>
<link rel="shortcut icon" href="../favicon.ico">
<link rel="stylesheet" type="text/css" href="../stylesheet.css">
</head>
<body>
<img src="../wrr.gif" alt="world rodent racing">
<ul>
<li><a href="../index.html">Home</a></li>
<li><a href="../winners.php">Winners</a></li>
<li><a href="../mapcluster.php">Maps</a></li>
<li><a href="../graph/graphs.php">Graphs</a></li>
<li id=selected>My Pets</li>
<li><a href="../about.html">About</a></li>
</ul>
<?PHP if ($errorMessage != "") echo "<p><h2>".$errorMessage."</h2>";?>
<p>
<FORM NAME ="form1" METHOD ="POST" ACTION ="login3.php">
<p>
Username: <INPUT TYPE = 'TEXT' Name ='username'  value="<?PHP print $uname;?>" maxlength="20">
Password: <INPUT TYPE = 'PASSWORD' Name ='password'  value="<?PHP print $pword;?>" maxlength="16">
<INPUT TYPE = "Submit" Name = "Submit1"  VALUE = "Login">
</p>
</FORM>
<p>
<ul><li><a href="forgotpword.php">Reset Password</a></li><li><a href="signup.php">Signup</a></li></ul>
<P>

</body>
</html>
