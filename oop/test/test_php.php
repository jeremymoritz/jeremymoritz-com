<!DOCTYPE html>
<html lang='en'>
	<head>
		<meta charset='UTF-8'>
		<title>PHP Test</title>
		<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></script>
	</head>
	<body>
	<?php
		//Write a function that takes a reference to an array of numbers, loops over the array and returns the sum of those numbers.
		
		function countArray($numArray = null) {
			if(is_null($numArray)) {
				return 0;
			}
			
			$count = 0;			
			foreach($numArray as $num) {
				$count += $num;
			}
			
			return $count;
		}
			
		$myArray = array(25, 62, 24, 91, 11);
		echo "<p class='awesome'>The sum of the numbers in the array is: " . countArray($myArray) . "!!!</p>";
	?>
	</body>
</html>
