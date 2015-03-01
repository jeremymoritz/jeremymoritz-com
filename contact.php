<?php	//	Jeremy Moritz
require_once('includes/jeremy_all.php');

if(apiSnag('submit')) {
	//	They've filled out the form and submitted it.  Now let's send me an email!
	$name = apiSnag('name');
	$email = filter_var(apiPost('email'), FILTER_SANITIZE_EMAIL);
	$email = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : 'INVALID_EMAIL (' . addslashes($email) . ')';
	$phone = apiSnag('phone');
	$comments = apiSnag('comments');
	$date = date('Y-m-d H:i:s');	//	this accounts for our Central timezone;
	$ip = $_SERVER['REMOTE_ADDR'];

	//	Send a mailer to Jeremy
	$to = 'jeremy@jeremymoritz.com';
	$message = "<b>Website Contact:</b><br><br>\n"
		. "Name: $name<br>\n"
		. "Email: $email<br>\n"
		. "Phone: $phone<br>\n"
		. "Comments: $comments<br>\n"
		. "Date: $date<br>\n"
		. "I.P.: $ip";
	$subject = "Web Contact: $name";
	$headers = "From: $name <$email>\n"
		. "Reply-To: $name <$email>\n"
		. "MIME-Version: 1.0\n"
		. "Content-type: text/html; charset=iso-8859-1\n";
	$message = preg_replace("#(?<!\r)\n#si", "\r\n", $message);	// Fix any bare linefeeds in the message to make it RFC821 Compliant
	$headers = preg_replace('#(?<!\r)\n#si', "\r\n", $headers); 	// Make sure there are no bare linefeeds in the headers


	$likelySpam = false;
	function setAndEmpty($postValue) {
		return isset($_POST[$postValue]) && empty($_POST[$postValue]);
	}
	function setAndEmptyArray($postValuesArr) {
		foreach($postValuesArr as $val) {
			if(!setAndEmpty($val)) {return false;}
		}
		return true;
	}
	if(!setAndEmptyArray(array('fname', 'questions', 'referral')) || apiPost('website') !== 'http://') {	//	if any of these bot-trap (hidden) fields are filled, this is almost certainly spam
		$likelySpam = true;
	}

	if(!$likelySpam) {
		mail($to,$subject,$message,$headers);
	}

	$completedform = true;
}
?>
<?=$header;?>
<body>
<?=$topper;?>
	<div class='main' id='contact'>
		<div class='row'>
			<div class='col-xs-12'>
				<h2>Contact me for a free estimate...</h2>
			</div>
			<div class='col-md-6'>
				<p>Please fill out the form below or feel free to call me directly to discuss your web design needs.</p>
			</div>
		</div>
		<div class='row'>
			<div class='col-sm-6 col-md-5 col-lg-6' id='form'>
<?php
if(isset($completedform)) {
?>
				<h3>Thank you for your interest in web design services from <em>JeremyMoritz.com</em>!</h3>
				<h3>You may expect a reply soon.</h3>
<?php
} else {
?>
				<form action='<?=$_SERVER['PHP_SELF'];?>' method='post' role='form'>
					<div class='row top-buffer'>
						<div class='col-sm-5 col-md-4 col-lg-3 text-right'><label>Name</label></div>
						<div class='col-sm-7 col-md-8 col-lg-9'><input type='text' name='name' class='form-control'></div>
					</div>
					<div class='row top-buffer'>
						<div class='col-sm-5 col-md-4 col-lg-3 text-right'><label>Email</label></div>
						<div class='col-sm-7 col-md-8 col-lg-9'><input type='text' name='email' class='form-control'></div>
					</div>
					<div class='row top-buffer text-right'>
						<div class='col-sm-5 col-md-4 col-lg-3'><label>Phone</label></div>
						<div class='col-sm-7 col-md-8 col-lg-9'><input type='text' name='phone' class='form-control'></div>
					</div>
					<div class='row top-buffer text-right'>
						<div class='col-sm-5 col-md-4 col-lg-3'><label>Comments or Questions</label></div>
						<div class='col-sm-7 col-md-8 col-lg-9'><textarea name='comments' rows='6' cols='23' class='form-control'></textarea></div>
					</div>

					<!-- BOT-TRAP: THIS PART IS MEANT TO STOP SPAM SUBMISSIONS FROM ROBOTS (if you see this section, don't change these fields) -->
						<p class='bot-trap'><label>Robot Questions</label><textarea name='questions' rows='6' cols='40'></textarea></p>
						<p class='bot-trap'><label>Robot Referral</label><input type='text' name='referral' value=''></p>
						<p class='bot-trap'><label>Robot Name</label><input type='text' name='fname' value=''></p>
						<p class='bot-trap'><label>Robot Website</label><input type='text' name='website' value='http://'></p>
					<!-- /BOT-TRAP -->

					<div class='row top-buffer'>
						<div class='col-sm-7 col-md-8 col-lg-9 col-sm-offset-5 col-md-offset-4 col-lg-offset-3'><input type='submit' value='Send' name='submit' class='btn btn-purple form-control'></div>
					</div>
				</form>
<?php
}
?>
			</div>
		</div>
		<div class='row top-buffer-3'>
			<div class='col-sm-6 col-md-5 col-lg-6'>
				<p>5502 Oakview<br>
					Shawnee, KS 66216<br>

<?php
if(isMobile()) {
	echo '<script>document.write("<a href=\'"+"te"+"l:+1"+"816"+"86674"+"89\'>(816"+") 8-MO"+"RITZ</a> <span class=\'tiny\'>(86"+"6-7489)</span>");</script>';
} else {
	echo '<script>document.write("<a href=\'"+"cal"+"lto"+"://+1"+"81686"+"67489\'>(8"+"16) 8-MOR"+"ITZ</a> <span class=\'tiny\'>(866-74"+"89)</span>");</script>';
}
?>
				</p>
				<div class='clearfix'></div>
			</div>
		</div>
	</div>
<?=$footer;?>
</body>
</html>
