<?php
ob_start();	//	starts output buffer (allows for setting php headers after declaring output, without errors)
putenv("TZ=US/Central");	//	Sets the timezone to be Central

//	these variables allow me to set differences for individual pages (note: if further changes are needed, define $header variable on page)
$title = isset($title) ? $title : 'Kolbe Fake Test';	//	title of page
$head_content = isset($head_content) ? $head_content : "";	//	additional head content

$header = "
	<!DOCTYPE html>
	<html lang='en'>
	<head>
		<meta charset='UTF-8'>
		<title>$title</title>
		<script src='//cdnjs.cloudflare.com/ajax/libs/jquery/2.0.2/jquery.min.js'></script>
		<link rel='stylesheet/less' type='text/css' href='kolbe.less'>
		<script src='//cdnjs.cloudflare.com/ajax/libs/less.js/1.3.3/less.min.js' type='text/javascript'></script>
		<!--[if lt IE 9]>
			<script src='http://html5shim.googlecode.com/svn/trunk/html5.js'></script>
		<![endif]-->
		$head_content
	</head>
	<script type='text/javascript'>
		//	google analytics
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-23066588-1']);
		_gaq.push(['_setDomainName', '.jeremymoritz.com']);
		_gaq.push(['_trackPageview']);

		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
	</script>";

$topper = "
	<section id='content'>
		<h1 class='hidden'>$title</h1>";

$footer = "
		<footer class='footer'></footer>
		<script type='text/javascript' src='kolbe.js'></script>
	</section>";

	/***********************
	*	Database Stuff	   *
	***********************/

//	For configuring passwords, etc. for MySQL
require('config.php');

####	CONNECT TO THE DATABASE		######
try {
	$dbh = new PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['db'], $config['username'], $config['password'], array(PDO::ATTR_PERSISTENT => true));
} catch (PDOException $e) {
	die($e->getMessage() . "\n Please contact us to tell us about this error... info@jeremymoritz.com");
}

#takes a pdo statement handle and returns an array of row objects
function sthFetchObjects($sth) {
	$out = array();
	while($o = $sth->fetchObject()) {
		$out[] = $o;
	}
	return $out;
}

	/***********************
	*	API Functions	   *
	***********************/

//	THESE REPLACE THE $_GET, $_POST, etc. (can pass in a default value if none is found)
function apiGet($key, $default = false) {return isset($_GET[$key]) ? (is_array($_GET[$key]) ? $_GET[$key] : filter_input(INPUT_GET, $key)) : $default;}
function apiPost($key, $default = false) {return isset($_POST[$key]) ? (is_array($_POST[$key]) ? $_POST[$key] : filter_input(INPUT_POST, $key)) : $default;}
function apiCookie($key, $default = false) {return isset($_COOKIE[$key]) ? (is_array($_COOKIE[$key]) ? $_COOKIE[$key] : filter_input(INPUT_COOKIE, $key)) : $default;}
function apiSession($key, $default = false) {return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;}
	//	Check to see if a parameter has been set and if so, return it
function apiSnag($key, $default = false) {
	if (apiGet($key, $default)) {return apiGet($key, $default);
	} elseif (apiPost($key, $default)) {return apiPost($key, $default);
	} elseif (apiCookie($key, $default)) {return apiCookie($key, $default);
	} elseif (apiSession($key, $default)) {return apiSession($key, $default);
	} else {return $default;
	}
}
	//	try to snag a value; if it's not there, use the default passed-in; also allows for explicit typing of varName to make sure people are passing the correct type (i.e. integer)
function apiSnagType($varName,$default=false,$type=false) {
	$retVal = $default;
	if(apiSnag($varName)) {
		if(!$type || gettype(apiSnag($varName)) == $type) {	//	if type is set, make sure it's the right type
			$retVal = apiSnag($varName);
		}
	}
	return $retVal;
}
?>
