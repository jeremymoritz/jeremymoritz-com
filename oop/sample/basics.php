<!DOCTYPE HTML>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>OOP in PHP</title>
	<?php include("class_lib.php"); ?>
</head>
<body>
	<?php
		$stefan = new person();	//	handle for a new instance of "person"
		$jimmy = new person;	//	parens are optional
		
		//	set names
		$stefan->set_name("Stefan Mischook");
		$jimmy->set_name("Nick Waddles");

		//	get names
		echo "<br>Stefan's name is " . $stefan->get_name();
		echo "<br>Nick's name is {$jimmy->get_name()}";
		
		//	new instances with constructor set
		$davey = new person("Davey Moritz");
		echo "<br>Davey's name is " . $davey->get_name();
		
		// Since $pinn_number was declared private, this line of code will generate an error. Try it out!
		//echo "<br>Tell me private stuff: ".$stefan->pinn_number;
		
		$james = new employee("Johnny Fingers");
		echo "<br>---> " . $james->get_name();

	?>
</body>
</html>
