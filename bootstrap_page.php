<?php	//	Bootstrap page
ob_start();	//	starts output buffer (allows for setting php headers after declaring output, without errors)
putenv("TZ=US/Central");	//	Sets the timezone to be Central

$footJS = "
	<script src='//cdnjs.cloudflare.com/ajax/libs/modernizr/2.6.2/modernizr.min.js'></script>
	<script src='//code.jquery.com/jquery-latest.js'></script>
	<script src='//cdnjs.cloudflare.com/ajax/libs/less.js/1.4.1/less.min.js'></script>
	<script src='//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js'></script>";
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='UTF-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1.0'>
	<title>Up and Running with Bootstrap</title>
	<link href='includes/bootstrap/less/bootstrap.less' rel='stylesheet/less' type='text/css'>
	<link href='includes/bootstrap/less/bootstrap.less' rel='stylesheet/less' type='text/css'>
	<!--[if lt IE 9]>
		<script src='http://html5shim.googlecode.com/svn/trunk/html5.js'></script>
	<![endif]-->
	<style>
		.container * {border: 1px solid #ddd;}
	</style>
</head>
<body>
	<div class='navbar'>
		<a class='btn navbar-btn navbar-right' data-toggle='collapse' data-target='.navbar-collapse'>
			<span class='glyphicon glyphicon-th-list'></span>
		</a>
			
		<a href='#' class='navbar-brand'>Roux Academy Conference</a>
		<div class="navbar-collapse">
			<ul class='nav navbar-nav'>
				<li class='active'><a href='#'>Home</a></li>
				<li><a href='#'>The Artists</a></li>
				<li><a href='#'>The Art</a></li>
				<li class='dropdown'><a href='#' class='dropdown-toggle'>Schedule <span class='caret'></span></a>
					<ul class='dropdown-menu'>
						<li><a href='#'>Day One</a></li>
						<li><a href='#'>Day Two</a></li>
						<li><a href='#'>Day Three</a></li>
					</ul>
				</li>
				<li><a href='#'>Venue</a></li>
				<li><a href='#'>Hotels</a></li>
			</ul>
		</div>
	</div>
<!--
	<div class='container'>
		<section class='row'>
			<header class='col-md-12 jumbotron'>
				<img src='//placekitten.com/g/300/200' alt='' class='pull-right'>
				<h1>Roux Academy Art Conference</h1>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
				<p><a href='#' class="btn btn-primary btn-lg">Learn More</a></p>
			</header>
		</section>
	</div>
	
	<div class='row'>
		<ul class='thumbnails list-unstyled'>
			<li class='col-md-4'>
				<article class='img-thumbnail'>
					<img src='//placekitten.com/g/250/250' alt='' class='img-responsive'>
					<h3>Jonathan Ferrar</h3>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
				</article>
			</li>
			<li class='col-md-4'>
				<article class='img-thumbnail'>
					<img src='//placekitten.com/g/251/250' alt='' class='img-responsive'>
					<h3>Jennifer Jerome</h3>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisie eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
				</article>
			</li>
			<li class='col-md-4'>
				<article class='img-thumbnail'>
					<img src='//placekitten.com/g/252/250' alt='' class='img-responsive'>
					<h3>Lavonne LaRue</h3>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt.</p>
				</article>
			</li>
		</ul>
	</div>
	
	<div class='container'>
		<header class='row'>
			<h1 class='col-md-12'>This is my first Bootstrap Page.</h1>
		</header>
		<section class='row'>
			<aside class='col-md-3'>
				<p>Here is a left column on this web page.</p>
			</aside>
			<article class='col-md-9'>
				<h2>Article title</h2>
				<p>Here is the main content area of this web page.</p>
			</article>
		</section>
		<section class='row'>
			<article class='col-md-9 col-md-offset-3'>
				<h2>Another Article</h2>
				<p>Here is another article</p>
			</article>
		</section>
		<section class='row'>
			<div class='col-md-9 col-md-offset-3'>
				<div class='row'>
					<div class='col-md-6'>
						<h3>Info Box 1</h3>
						<img src='http://placekitten.com/g/400/287' alt='' class='img-responsive'>
						<p>Here is some text about the picture</p>
					</div>
					<div class='col-md-6'>
						<h3>Info Box 2</h3>
						<img src='http://placekitten.com/g/400/255' alt='' class='img-responsive'>
						<p>Here is extra text about the picture</p>
					</div>
				</div>
			</div>
		</section>
		<section class='row'>
			<div class='col-md-4 visible-lg'>Visible on a desktop</div>
			<div class='col-md-4 visible-md'>Visible on a tablet</div>
			<div class='col-md-4 visible-sm'>Visible on a phone</div>
			<div class='col-md-4 visible-xs'>Visible on super-tiny</div>
		</section>
		<footer class='col-md-2 col-md-offset-10'>
			&copy; Copyright 2012
		</footer>
	</div>
-->
	<?=$footJS;?>
</body>
</html>
