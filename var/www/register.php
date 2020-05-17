<?PHP
session_start();
if (!(isset($_SESSION['login']) && $_SESSION['login'] != '')) {
	header ("Location: login/login3.php");
}

$guid = $_SESSION['guid'];
$uname = $_SESSION['uname'];

?>
<html>
<head>
<script type="text/javascript">
        var citiesByCode = {
            1: ["Select...","Belfast","Cardiff","Edinburgh","London"],
            2: ["Select...","New York"],
		 3: ["Select...","Toronto"],
		 4: ["Select...","Madrid"]
        }
        function makeSubmenu(value) {
            if(value.length==0) document.getElementById("citySelect").innerHTML = "<option></option>";
            else {
                var citiesOptions = "";
                for(cityId in citiesByCode[value]) {
                    citiesOptions+="<option value='"+citiesByCode[value][cityId]+"'>"+citiesByCode[value][cityId]+"</option>";
                }
                document.getElementById("citySelect").innerHTML = citiesOptions;
            }
        }
</script>
<title>Register My Pet</title>
<link rel="shortcut icon" href="favicon.ico">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
</head>

<body>
<img src="wrr.gif" alt="world rodent racing">
<ul>
<li id=selected>Register Pet</li>
<li><a href="securemenu.php">Back</a></li>
</ul>
<p>
<p>
<FORM NAME ="form1" METHOD ="POST" ACTION ="reg_with_db.php">
Username: <INPUT TYPE = 'TEXT' Name ='uname'  readonly='true' value="<?PHP print $uname;?>" maxlength="20">
<p>
Country:
<select id="countrySelect" name="location" size="1" onchange="makeSubmenu(this.value)">
  <option>Select...</option>
  <option value="1">United Kingdom</option>
  <option value="2">USA</option>
  <option value="3">Canada</option>
  <option value="4">Spain</option>
</select>
<p>
Nearest city: <select id="citySelect" name="city" size="1">
        <option></option>
    </select>
<p>
Pet Name: <INPUT TYPE = 'TEXT' Name ='petname'  value="<?PHP print $petname;?>" maxlength="40">
<p>
Species: <select name="pettype">
   <option selected value="">Select...</option>
   <option value="Chipmonk">Chipmonk</option>
   <option value="Chinchilla">Chinchilla</option>
   <option value="Degu">Degu</option>
   <option value="Gerbil">Gerbil</option>
   <option value="Hamster">Hamster</option>
   <option value="Mouse">Mouse</option>
   <option value="Rat">Rat</option>
</select>
<p>
Breed: <INPUT TYPE = 'TEXT' Name ='breed'  value="<?PHP print $breed;?>" maxlength="30">
<p>
Pet Age (months): <INPUT TYPE = 'INT' Name ='petage'  value="<?PHP print $petage;?>" maxlength="2">
<p>
Pet Sex (M or F):<select name="petsex">
  <option value="">Select...</option>
  <option value="M">Male</option>
  <option value="F">Female</option>
</select> 
<p>
<P>
<INPUT TYPE = "Submit" Name = "Submit1"  VALUE = "Register">

</FORM>

<P>

</body>
</html>
