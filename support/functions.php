<?php

//	Array of all 50 states
$states_s2l = array(
	'--' => '',
	'AL' => 'Alabama',			'AK' => 'Alaska',			'AZ' => 'Arizona',			'AR' => 'Arkansas',
	'CA' => 'California',		'CO' => 'Colorado',			'CT' => 'Connecticut',		'DC' => 'District Of Columbia',
	'DE' => 'Delaware',			'FL' => 'Florida',			'GA' => 'Georgia',			'HI' => 'Hawaii',
	'ID' => 'Idaho',			'IL' => 'Illinois',			'IN' => 'Indiana',			'IA' => 'Iowa',
	'KS' => 'Kansas',			'KY' => 'Kentucky',			'LA' => 'Louisiana',		'ME' => 'Maine',
	'MD' => 'Maryland',			'MA' => 'Massachusetts',	'MI' => 'Michigan',			'MN' => 'Minnesota',
	'MS' => 'Mississippi',		'MO' => 'Missouri',			'MT' => 'Montana',			'NE' => 'Nebraska',
	'NV' => 'Nevada',			'NH' => 'New Hampshire',	'NJ' => 'New Jersey',		'NM' => 'New Mexico',
	'NY' => 'New York',			'NC' => 'North Carolina',	'ND' => 'North Dakota',		'OH' => 'Ohio',
	'OK' => 'Oklahoma',			'OR' => 'Oregon',			'PA' => 'Pennsylvania',		'RI' => 'Rhode Island',
	'SC' => 'South Carolina',	'SD' => 'South Dakota',		'TN' => 'Tennessee',		'TX' => 'Texas',
	'UT' => 'Utah',				'VT' => 'Vermont',			'VA' => 'Virginia',			'WA' => 'Washington',
	'WV' => 'West Virginia',	'WI' => 'Wisconsin',		'WY' => 'Wyoming'
);
$states_l2s = array_flip($states_s2l);
asort($states_l2s);
//	State options for forms
$stateOptions = '';
foreach($states_l2s as $st) {
	$display = $st ? $st : '--';
	$stateOptions .= "<option value='$st'>$display</option>";
}

	/*********************
	*	PDO Functions	*
	*********************/

#takes a pdo statement handle and returns an array of row objects
function sthFetchObjects($sth) {
	$out = array();
	while($o = $sth->fetchObject()) {
		$out[] = $o;
	}
	return $out;
}

	/********************
	*	Objects			*
	********************/

	/********************
	*	Functions		*
	********************/

//	THESE REPLACE THE $_GET, $_POST, etc. (can pass in a default value if none is found)
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

	//	Checks if an email is valid
function isValidEmail($email) {
	return preg_match("/^[^@]+@[^@]+$/", $email);
}

	//	converts "<a href='mailto:abc@example.com'>abc@example.com</a>" to "<a href='mailt&#111;:abc&#64;example.c&#111;m'>abc&#64;example.c&#111;m</a>" to hide from spamBots
function disguiseMail($mail) {
	return str_replace('@','&#64;',str_replace('o','&#111;',$mail));
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

	$iDiffYear = str_pad($iDiffYear, $padZeros, '0', STR_PAD_LEFT);	//	pad the age
	return $iDiffYear;
}

	//	format inches into feet and inches
function formatHeight($inches) {
	$inches = intval($inches);
	return floor($inches / 12) . "' " . ($inches % 12) . '"';
}

	//	format date and time like "2/14/14 at 7:05pm"
function formatDateTime($dateTime) {
	return date('n/j/y \a\t g:ia', strtotime($dateTime));
}

	//	puts a circle around any number from 1 to 20 (using hex code... for greater numbers, use css border-radius)
function circleNumber($num) {
	$num = intval($num);
	if($num <= 20) {
		return '&#' . strval(9311 + $num) . ';';	//	9311 is the start of the decimal characters
		//	return '&#x' . strval(2459 + $num) . ';';	//	this works identically but uses hexcode instead of decimal code
	}
	return strval($num);
}

	//	add a parameter to a page with or without a query string
function addToQueryString($page, $queryString) {
	return $page . (strpos($page, '?') ? '&' : '?') . $queryString;
}
?>
