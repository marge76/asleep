<?PHP
session_start();
if (!(isset($_SESSION['login']) && $_SESSION['login'] != '')) {
	header ("Location: login/login3.php");
}

$guid = $_SESSION['guid'];
$uname = $_SESSION['uname'];
$petname = $_GET['petname'];

require_once 'PJBS.php';
#create an instance with something like
$drv = new PJBS();
#connect to a JDBC data source with
$drv->connect("jdbc:derby://localhost:6414/gaiandb", "gaiandb", "passw0rd");
#execute a query
$sql = "select * from new com.ibm.db2j.GaianQuery('select * from members where GUID = ''".$guid."'' and petname=''".$petname."''','with_provenance') GQ"; 
$res = $drv->exec($sql);
$array = $drv->fetch_array($res);
?>
<html>
<head>
<title>Edit My Pet</title>
<link rel="shortcut icon" href="favicon.ico">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
</head>

<body>
<img src="wrr.gif" alt="world rodent racing">
<ul>
<li id=selected>Edit Pet</li>
<li><a href="securemenu.php">Back</a></li>
</ul>
<p>

<FORM NAME ="form1" METHOD ="POST" ACTION ="reg_with_db.php?update=1">

Username: <INPUT TYPE = 'TEXT' Name ='uname'  readonly='true' value="<?PHP print $uname;?>" maxlength="20">
<p>
Country: <select name="location">
  <option value="">Select...</option>
  <option <?php if ($array[LOC_ID]==1) print " selected "; ?> value="1">United Kingdom</option>
  <option <?php if ($array[LOC_ID]==2) print " selected "; ?> value="2">USA</option>
  <option <?php if ($array[LOC_ID]==3) print " selected "; ?> value="3">Canada</option>
  <option <?php if ($array[LOC_ID]==4) print " selected "; ?> value="4">Spain</option>
</select>
<p>
Nearest city: <INPUT TYPE = 'TEXT' Name ='city'  value="<?PHP print $array[CITY];?>" maxlength="20">
<p>
Pet Name: <INPUT TYPE = 'TEXT' Name ='petname' readonly='true' value="<?PHP print $petname;?>" maxlength="20">
<p>
Pet Age (months): <INPUT TYPE = 'INT' Name ='petage'  value="<?PHP print $array[PETAGE];?>" maxlength="2">
<p>
Pet Sex (M or F):<select name="petsex">
  <option value="">Select...</option>
  <option <?php if ($array[PETSEX]=="M") print " selected "; ?> value="M">Male</option>
  <option <?php if ($array[PETSEX]=="F") print " selected "; ?> value="F">Female</option>
</select> 
<p>
Species: <select name="pettype">
   <option selected value="">Select...</option>
   <option <?php if ($array[PETTYPE]=="Chipmonk") print " selected "; ?> value="Chipmonk">Chipmonk</option>
   <option <?php if ($array[PETTYPE]=="Chinchilla") print " selected "; ?> value="Chinchilla">Chinchilla</option>
   <option <?php if ($array[PETTYPE]=="Degu") print " selected "; ?> value="Degu">Degu</option>
   <option <?php if ($array[PETTYPE]=="Gerbil") print " selected "; ?> value="Gerbil">Gerbil</option>
   <option <?php if ($array[PETTYPE]=="Hamster") print " selected "; ?> value="Hamster">Hamster</option>
   <option <?php if ($array[PETTYPE]=="Mouse") print " selected "; ?> value="Mouse">Mouse</option>
   <option <?php if ($array[PETTYPE]=="Rat") print " selected "; ?> value="Rat">Rat</option>
</select>
<p>
Breed: <INPUT TYPE = 'TEXT' Name ='breed'  value="<?PHP print $array[BREED];?>" maxlength="20">
<p>

<P>
<INPUT TYPE = "Submit" Name = "Submit1"  VALUE = "Update">


</FORM>

<P>

</body>
</html>
