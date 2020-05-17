
<html>
<head>
</head>
<body>
<INPUT TYPE = 'TEXT' id='fname' name ='long' value='' maxlength=12 size=10>

<script>
document.getElementById("fname").onchange = function() {myFunction()};

function myFunction() {
    var x = document.getElementById("fname");
    x.value = x.value.toUpperCase();
}
</script>
</body>
</html>
