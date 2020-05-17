<?PHP

session_start();
if (!(isset($_SESSION['login']) && $_SESSION['login'] != '')) {
	header ("Location: login/login3.php");
}

$guid = $_SESSION['guid'];
$uname = $_SESSION['uname'];;

$petname = "";
$petage = "";
$petsex = "";
$pettype = "";
$breed = "";
$location = "";
$city = "";
$update = "";
$remove = "";

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

	$petname = $_POST['petname'];
	$pettype = $_POST['pettype'];
	$petsex = $_POST['petsex'];
	$petage = $_POST['petage'];
	$breed = $_POST['breed'];
	$location = $_POST['location'];
	$city = $_POST['city'];

	$petname = htmlspecialchars($petname);
	$pettype = htmlspecialchars($pettype);
	$breed = htmlspecialchars($breed);
	$city = htmlspecialchars($city);

	$update = $_GET['update'];
	$remove = $_GET['remove'];

	require_once 'PJBS.php';
	#create an instance with something like 
	$drv = new PJBS();
	#connect to a JDBC data source with
	$drv->connect("jdbc:derby://localhost:6414/gaiandb", "gaiandb", "passw0rd");
	#execute a query
	$sql = "select * from new com.ibm.db2j.GaianQuery('insert into members values(''".$guid."'', '".$uname."', ''".$petname."'', ".$location.", ''".$city."'', ".$petage.", ''".$petsex."'', ''".$pettype."'', ''".$breed."'')') GQ";
	if ($update == 1) {
		$sql = "select * from new com.ibm.db2j.GaianQuery('update members set LOC_ID=".$location.",CITY=''".$city."'',petage=".$petage.",petsex=''".$petsex."'',pettype =''".$pettype."'',breed=''".$breed."'' where guid=''".$guid."'' and username='".$uname."' and petname =''".$petname."''') GQ";
	}
	if ($remove == 1) {
		$sql = "select * from new com.ibm.db2j.GaianQuery('delete from members where guid=''".$guid."'' and username='".$uname."' and petname =''".$petname."'' and LOC_ID=".$location." and CITY=''".$city."'' and petage=".$petage." and petsex=''".$petsex."'' and pettype =''".$pettype."'' and breed=''".$breed."''') GQ";
		$res = $drv->exec($sql);
		$sql = "select * from new com.ibm.db2j.GaianQuery('delete from winners where guid=''".$guid."'' and petname =''".$petname."''') GQ";	
		$res = $drv->exec($sql);
		$sql = "select * from new com.ibm.db2j.GaianQuery('delete from runlog where guid=''".$guid."'' and petname =''".$petname."''') GQ";	
	}
	$res = $drv->exec($sql);
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" 
"http://www.w3.org/TR/html4/strict.dtd"> 

<html lang="en"> 
    <head> 
        <meta http-equiv="content-type" content="text/html; charset=iso-8859-1"> 
         
        <link rel="stylesheet" type="text/css" href="stylesheet.css"> 
         
        <title>Successful</title> 
     
    </head> 
     <img src="wrr.gif" alt="world rodent racing">
<ul>
<li id=selected>Pet Details</li>
</ul>
    <body> 
<p>
     <?php if ($update==1) {
        echo "<div id='Success'> 
            <h1>Update</h1> 
            <p>You have successfully updated your pets details</p> 
        </div>";
	} elseif ($remove==1) {
 echo "<div id='Success'> 
            <h1>Removal</h1> 
            <p>You have successfully removed your pet</p> 
        </div>";
     } else {
	echo "<div id='Success'> 
            <h1>Registration</h1> 
            <p>Congratulations! You have successfully registered</p> 
        </div>";
	}  ?>
<p>
     <A HREF = securemenu.php>OK</A>
    </body> 

</html> 
