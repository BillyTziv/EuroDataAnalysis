<html>
<head>
<?php
	if(isset($_POST['select1'])){
		echo "hello wthere";
		foreach ($_POST['select1'] as $selectedOption) {
    			echo $selectedOption."\n";
		}
	}
?>
</head>
<body>
<h1> Welcome to the test example </h1>
<form action="index.php" method="post">
    <select name="select1[]" multiple>
        <option value="value1">Value 1</option>
        <option value="value2">Value 2</option>
    </select>
    <input type="submit" name="submit" value="Go"/>
</form>
</body>
</html>
