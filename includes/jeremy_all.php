<?php
date_default_timezone_set('America/Chicago');	//	Sets the timezone to be Central
ob_start();	//	starts output buffer (allows for setting php headers after declaring output, without errors)
ini_set('display_errors', 1);	//	show errors

//	these variables allow me to set differences for individual pages (note: if further changes are needed, define $header variable on page)
$title = isset($title) ? $title : 'JeremyMoritz.com';	//	title of page
$head_content = isset($head_content) ? $head_content : "";	//	additional head content

$navLinks = array(
	array('href'=>'index.php', 		'title'=>'Jeremy Moritz Home Page', 'faIcon'=>'home', 				'navText'=>'Home'),
	array('href'=>'examples.php', 'title'=>'Example Sites', 					'faIcon'=>'list-alt', 		'navText'=>'Example Sites'),
	array('href'=>'about.php', 		'title'=>'About Jeremy', 						'faIcon'=>'info-circle', 	'navText'=>'About'),
	array('href'=>'contact.php', 	'title'=>'Contact Jeremy Moritz', 	'faIcon'=>'phone', 				'navText'=>'Contact')
);
$fullNav = '';
$collapseNav = '';
foreach($navLinks as $n) {
	$fullNav .= "<a href='" . $n['href'] . "' title='" . $n['title'] . "' class='btn btn-purple'><i class='fa fa-" . $n['faIcon'] . "'></i> " . $n['navText'] . "</a>\n";
	$collapseNav .= "<li><a href='" . $n['href'] . "' title='" . $n['title'] . "'><i class='fa fa-" . $n['faIcon'] . "'></i> " . $n['navText'] . "</a></li>\n";
}

$header = "
	<!DOCTYPE html>
	<html lang='en'>
	<head>
		<meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'><!-- for Bootstrap -->
		<meta name='description' content='Jeremy Moritz Web Designer'>
		<meta name='keywords' content='Jeremy Moritz, Web Design, PHP, HTML, XHTML, CSS, Javascript, Flash'>
		<title>$title</title>
		<link rel='shortcut icon' href='favicon.ico'>
		<script src='//code.jquery.com/jquery-latest.js'></script>
		<!--[if lt IE 9]>
			<script src='http://html5shim.googlecode.com/svn/trunk/html5.js'></script>
		<![endif]-->
		<link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootstrap/latest/css/bootstrap.min.css'>
		<link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootstrap/latest/css/bootstrap-theme.min.css'>
		<link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css'>
		<link rel='stylesheet' href='includes/jeremy.css'>
		$head_content
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
		</script>
	</head>";

$topper = "
	<div id='content' class='container'>
		<nav id='topnav' class='btn-group btn-group-justified hidden-xs' role='navigation'>
			$fullNav
		</nav>

		<nav class='navbar navbar-fixed-top navbar-inverse visible-xs' role='navigation'>
			<div class='navbar-header'>
			<button class='navbar-toggle navbar-brand pull-right' data-toggle='collapse' data-target='#collapse'>
				<span class='sr-only'>Toggle navigation</span>
				<i class='fa fa-bars'></i>
				MENU
			</button>
			</div>
			<div class='collapse navbar-collapse' id='collapse'>
				<ul class='nav navbar-nav'>
					$collapseNav
				</ul>
			</div>
		</nav>
		<div class='inner'>
			<h1 class='hidden'>Jeremy Moritz - Programmer / Web Developer</h1>";

$footer = "
			<div class='footer'></div>
			<script src='//maxcdn.bootstrapcdn.com/bootstrap/latest/js/bootstrap.min.js'></script>
			<script type='text/javascript' src='includes/jeremy.js'></script>
		</div><!-- /.inner -->
	</div>";

$myBirthday = '1981-06-30';	//	for About page
$angelsBirthday = '2004-09-18';	//	for About page
$chasesBirthday = '2012-07-20';	//	for About page
$weddingDay = '2001-12-22';	//	for About page

	/***********************
	*	Functions		*
	***********************/

//	THESE REPLACE THE $_GET, $_POST, etc.
function apiGet($key,$default=false) {return filter_input(INPUT_GET, $key) ? filter_input(INPUT_GET, $key) : $default;}
function apiPost($key,$default=false) {return filter_input(INPUT_POST, $key) ? filter_input(INPUT_POST, $key) : $default;}
function apiCookie($key,$default=false) {return filter_input(INPUT_COOKIE, $key) ? filter_input(INPUT_COOKIE, $key) : $default;}
function apiSession($key,$default=false) {return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;}
	//	Check to see if a parameter has been set and if so, return it
function apiSnag($key,$default=false) {
	if(apiGet($key,$default)) {return apiGet($key,$default);
	} elseif(apiPost($key,$default)) {return apiPost($key,$default);
	} elseif(apiCookie($key,$default)) {return apiCookie($key,$default);
	} elseif(apiSession($key,$default)) {return apiSession($key,$default);
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
//	Checks if an email is valid
function isValidEmail($email) {
	return preg_match("/^[a-zA-Z]\w+(\.\w+)*\@\w+(\.[0-9a-zA-Z]+)*\.[a-zA-Z]{2,4}$/", $email);
}

	//	return the age in years if given a birthday and (optionally) another date (also may optionally pad it to a certain length like 2)
function getAge($iBirthdayTimestamp, $iCurrentTimestamp = false, $padZeros = 2) {	//	by default, it pads it left to a length of 2 (zerofill)
	$iBirthdayTimestamp = preg_match('/^\d{4}-\d{2}-\d{2}$/', $iBirthdayTimestamp) ? strtotime($iBirthdayTimestamp) : $iBirthdayTimestamp;
	$iCurrentTimestamp = $iCurrentTimestamp ? $iCurrentTimestamp : time();	//	default is today

	$iDiffYear  = date('Y', $iCurrentTimestamp) - date('Y', $iBirthdayTimestamp);
	$iDiffMonth = date('n', $iCurrentTimestamp) - date('n', $iBirthdayTimestamp);
	$iDiffDay   = date('j', $iCurrentTimestamp) - date('j', $iBirthdayTimestamp);

	// If birthday has not happen yet for this year, subtract 1.
	if ($iDiffMonth < 0 || ($iDiffMonth == 0 && $iDiffDay < 0)) {
		$iDiffYear--;
	}

	//	uncomment this next line if you want to pad the age
		//	$iDiffYear = str_pad($iDiffYear, $padZeros, '0', STR_PAD_LEFT);
	return $iDiffYear;
}

	//	converts "<a href='mailto:abc@example.com'>abc@example.com</a>" to "<a href='mailt&#111;:abc&#64;example.c&#111;m'>abc&#64;example.c&#111;m</a>" to hide from spamBots
function disguiseMail($mail) {
	return str_replace('@','&#64;',str_replace('o','&#111;',$mail));
}
	//	converts "abc@example.com" to "<a href='mailt&#111;:abc&#64;example.c&#111;m'>abc&#64;example.c&#111;m</a>" to hide from spamBots
function disguiseMailLink($simpleEmailAddress) {
	return disguiseMail("<a href='mailto:$simpleEmailAddress'>$simpleEmailAddress</a>");
}

	//	determines if the user is on a mobile browser (phone, iPad, etc.)
function isMobile() {
	$user_agent = $_SERVER['HTTP_USER_AGENT'];	// Get the user agent
	// Array of known mobile user agents.  This list is from the 21 October 2010 WURFL File.  Most mobile devices send a pretty standard string that can be covered by one of these.  I believe I have found all the agents (as of the date above).  It's advised to periodically check this list against the WURFL file, available at: http://wurfl.sourceforge.net/ (though i don't know how to read that crazy-large xml file)
	$mobile_agents = Array(
		'240x320', 'acer', 'acoon', 'acs-', 'abacho', 'ahong', 'airness', 'alcatel', 'amoi', 'android', 'anywhereyougo.com', 'applewebkit/525', 'applewebkit/532', 'asus', 'audio', 'au-mic', 'avantogo', 'becker', 'benq', 'bilbo', 'bird', 'blackberry', 'blazer', 'bleu', 'cdm-', 'compal', 'coolpad', 'danger', 'dbtel', 'dopod', 'elaine', 'eric', 'etouch', 'fly ' , 'fly_', 'fly-', 'go.web', 'goodaccess', 'gradiente', 'grundig', 'haier', 'hedy', 'hitachi', 'htc', 'huawei', 'hutchison', 'inno', 'ipad', 'ipaq', 'ipod', 'jbrowser', 'kddi', 'kgt', 'kwc', 'lenovo', 'lg ', 'lg2', 'lg3', 'lg4', 'lg5', 'lg7', 'lg8', 'lg9', 'lg-', 'lge-', 'lge9', 'longcos', 'maemo', 'mercator', 'meridian', 'micromax', 'midp', 'mini', 'mitsu', 'mmm', 'mmp', 'mobi', 'mot-', 'moto', 'nec-', 'netfront', 'newgen', 'nexian', 'nf-browser', 'nintendo', 'nitro', 'nokia', 'nook', 'novarra', 'obigo', 'palm', 'panasonic', 'pantech', 'philips', 'phone', 'pg-', 'playstation', 'pocket', 'pt-', 'qc-', 'qtek', 'rover', 'sagem', 'sama', 'samu', 'sanyo', 'samsung', 'sch-', 'scooter', 'sec-', 'sendo', 'sgh-', 'sharp', 'siemens', 'sie-', 'softbank', 'sony', 'spice', 'sprint', 'spv', 'symbian', 'tablet', 'talkabout', 'tcl-', 'teleca', 'telit', 'tianyu', 'tim-', 'toshiba', 'tsm', 'up.browser', 'utec', 'utstar', 'verykool', 'virgin', 'vk-', 'voda', 'voxtel', 'vx', 'wap', 'wellco', 'wig browser', 'wii', 'windows ce', 'wireless', 'xda', 'xde', 'zte'
	);
	$is_mobile = false;	//	innocent until proven guilty (of being a mobile device)
	foreach ($mobile_agents as $device) {
		if (stristr($user_agent, $device)) {	//	check user-agent to see if it's in the list
			$is_mobile = true;	//	if so, it's a mobile device
			break;	// we don't need to test anymore once we get a "True" value
		}
	}
	return $is_mobile;	//	function returns true if it's mobile or false it's not
}

	//	determines if the IP is on the blacklist
function isBlacklistedIP($ip) {
	//	Check to be sure their IP is not on the blacklist before submitting it
	$blackIPs = array();
	$blacklistJSON = json_decode(file_get_contents('http://jeremymoritz.com/support/ip_blacklist.json'));
	$ipIsBlacklisted = false;	//	innocent until proven guilty
	foreach($blacklistJSON->blacklist as $bIP) {
		if($ip === $bIP) {
			$ipIsBlacklisted = true;
			break;
		}
	}
	// $ipParts = explode('.', $ip);
	// foreach($blackIPs as $blackIP) {
	// 	$blackIPParts = explode('.', $blackIP);
	// 	if($ipParts[0] === $blackIPParts[0] && $ipParts[1] === $blackIPParts[1]) {
	// 		$ipIsBlacklisted = true;	//	if the first 2 parts of IP address are the on the blacklist, then kick this guy out!
	// 	}
	// }

	return $ipIsBlacklisted;	//	function returns true if blacklisted, else false
}
?>
