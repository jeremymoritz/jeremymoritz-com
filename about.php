<?php	//	Jeremy Moritz
require_once('includes/jeremy_all.php');
?>
<?=$header;?>
<body>
	<?=$topper;?>
	<article class='main' id='about'>
		<div class='row'>
			<div class='col-xs-12'>
				<h2>About me...</h2>
			</div>
		</div>
		<section class='row' id='personal'>
			<div class='col-xs-12'>
				<h3>Personal</h3>
			</div>
			<div class='col-xs-12 col-sm-12 col-md-12 col-lg-8'>
				<a href='http://www.MoritzFamily.com'><img src='images/moritzfamily-2013.png' alt='' class='enlarge' title='Hover to Enlarge'></a>
				<p>I am <?=getAge(strtotime($myBirthday))?> years old.  I'm a committed Christian and a loving husband and father.  My wife Christine and I have been happily married for over <?=getAge($weddingDay)?> years, and we have 5 wonderful children ranging in age from <?=getAge($angelsBirthday)?> years old to <?=getAge($chasesBirthday)?>. <a href='http://www.MoritzFamily.com'>View my family website</a>.</p>
			</div>
		</section>
		<section class='row' id='professional'>
			<div class='col-xs-12'>
				<h3>Professional</h3>
			</div>
			<div class='col-md-7 col-lg-8'>
				<p>I have been working for a successful internet company called Cities Unlimited for 9 years.  I started on the tech team and later became CEO of the company, a position I've held for over 5 years now.  My job affords me ample flexibility to do many things I enjoy including designing and building websites.</p>
				<p id='specialties_para'>I do all of my website design with bare code (as opposed to programs like FrontPage or WordPress) which allows for maximum control and cleanliness in design.   My specialty is programming using
				JavaScript <img src='images/logo_javascript_sm.png' alt='Javascript Logo'> and
				PHP <img src='images/logo_php_sm.png' alt='PHP Logo'> with
				MySQL <img src='images/logo_mysql_sm.png' alt='MySQL Logo'> database handling (providing a broad range of functionality to your website).  In addition to this, I am fluent in
				HTML5 <img src='images/logo_html5_sm.png' alt='HTML5 Logo'>,
				CSS3 <img src='images/logo_css3_sm.png' alt='CSS3 Logo'>, and
				JQuery <img src='images/logo_jquery_sm.png' alt='JQuery Logo'>. I am also very skilled with
				Photoshop <img src='images/logo_photoshop_sm.png' alt='Photoshop Logo'>.  I prefer
				Git <img src='images/logo_git_sm.png' alt='Git logo'> for version control, and my current sites are built with the Twitter
				Bootstrap <img src='images/logo_bootstrap_sm.png' alt='Bootstrap Logo'> framework, so they look great on mobile devices.  For a quick demonstration, try resizing your browser window to it's narrowest setting to see how this site looks on an iPhone.</p>
				<p>I am always easy to get ahold of.  I answer my phone, respond to all emails and text messages, and return any missed calls.  I also work quickly and within a budget.  <a href='contact.php'>Contact me</a> for a free estimate!</p>
			</div>
		</section>
	</article>
	<?=$footer;?>
</body>
</html>
