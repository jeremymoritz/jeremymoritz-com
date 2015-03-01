<?php
	//	make list of all files in this directory
	$files = array();
	if($handle = opendir('.')) {
		while(false !== ($entry = readdir($handle))) {
			if($entry != "." && $entry != ".." && !strpos($entry, ".js") && !strpos($entry, "~")) {
				$files[] = $entry;
			}
		}
		closedir($handle);
	}
	
	sort($files);
?>

<!DOCTYPE html>
<html>
<head>
	<title>Project Euler Projects - Jeremy Moritz</title>
</head>
<body>
	<h1>Click on a Project Below</h1>
	<ul>
		<?php
			foreach($files as $file) {
				echo "<li><a href='$file'>$file</a></li>";
			}
		?>
	</ul>
</body>
</html>
