<?php //	List all domains that are delinquent and hide their content

$domain = str_replace('www.','',filter_input(INPUT_GET, 'd'));

$xlist = array(
//	"tonymoritz.com",
	"erecipecards.com",
	"gameonkc.com"
);

/*
if(time() > strtotime('2013-03-19')) {
	$xlist[] = "erecipecards.com";
}
*/


if(in_array($domain, $xlist)) {
	$siteOff = "
		<div style='height:1200px; width:1300px; padding:20px; margin:20px; position:absolute; top:0; left:0; z-index:9999; font-size:40px; background:#fff; color:#000; font-weight:normal; font-family:sans-serif; text-align:left'>
			<b>ERROR:</b> Site temporarily obstructed<!-- due to billing issues-->
			<!--<p style='font-size:15px'><a href='mailto:contact@erecipecards.com'>contact@erecipecards.com</a></p>-->
		</div>";
	die($siteOff);
}

?>
