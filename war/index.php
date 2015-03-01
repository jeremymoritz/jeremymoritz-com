<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='UTF-8'>
	<meta name='description' content='War!!'>
	<meta name='keywords' content='War'>
	<title>OOP War! - by Jeremy Moritz</title>
	<script src='includes/jquery-1.7.1.min.js'></script>
	<link rel='stylesheet' type='text/css' href='inc/war.css'>
	<?php
		require_once('inc/class_lib.php');
	?>
</head>
<body>
<?php
	$form = "
		<form action='" . $_SERVER['PHP_SELF'] . "' method='get'>
			<p>Player 1: <input type='text' name='p1' value='" . (filter_input(INPUT_GET, 'p1') ? filter_input(INPUT_GET, 'p1') : "Jeremy") . "' onfocus='javascript:select()'></p>
			<p>Player 2: <input type='text' name='p2' value='" . (filter_input(INPUT_GET, 'p2') ? filter_input(INPUT_GET, 'p2') : "Davey") . "' onfocus='javascript:select()'></p>
			<input type='submit' value=\"Let's Play!\" />
		</form>";
	if(!filter_input(INPUT_GET, 'p1') || !filter_input(INPUT_GET, 'p2')) {
		echo "
			<h2>Let's Play War!</h2>
			<h3>Who will play?</h3>
			$form";
	} else {
		set_time_limit(5);	//	max time (in seconds) to run
		define("LOSING_SCORE", 0);	//	player loses when their pile gets below this number

		$p1 = new player(filter_input(INPUT_GET, 'p1') ? filter_input(INPUT_GET, 'p1') : "Jeremy");
		$p2 = new player(filter_input(INPUT_GET, 'p2') ? filter_input(INPUT_GET, 'p2') : "Davey");

		$deck = new deck;
		echo '<h2>The game begins!</h2>';

		$deck->dealCards($p1, $p2);

		// game
		$game = new game;

		echo "
			<table>
				<tr>
					<th>" . substr($p1->get_name(),0,1) . "<br>Card</th>
					<th>" . substr($p2->get_name(),0,1) . "<br>Card</th>
					<th>" . substr($p1->get_name(),0,1) . "<br>Pile</th>
					<th>" . substr($p2->get_name(),0,1) . "<br>Pile</th>
					<th>Spoils of War</th>
				</tr>
				<tr>
					<td colspan='2'>START:</td>
					<td>{$p1->get_pileCount()}</td>
					<td>{$p2->get_pileCount()}</td>
					<td></td>
				</tr>\n";
		$countBattles = 0;
		while($p1->get_pileCount() > LOSING_SCORE && $p2->get_pileCount() > LOSING_SCORE && $countBattles < 2000) {
			if($p1->get_topCard()->get_value() > $p2->get_topCard()->get_value()) {
				$winner = $p1;
			} elseif($p1->get_topCard()->get_value() < $p2->get_topCard()->get_value()) {
				$winner = $p2;
			} else {
				$winner = false;	//	no winner
			}

			echo "
				<tr" . (!$winner ? " class='tie'" : "") . ">"	//	css display if it's a tie
				. "<td>" . ($winner == $p1 ? "<strong>" : "") . $p1->get_topCard()->showCard() . ($winner == $p1 ? "</strong>" : "") . "</td>\n"	//	strong css for winning card
				. "<td>" . ($winner == $p2 ? "<strong>" : "") . $p2->get_topCard()->showCard() . ($winner == $p2 ? "</strong>" : "") . "</td>\n";

			$game->battle($p1, $p2);

			$eCardDisplay = "";
			if($game->get_escrowCount()) {
				$escrowCards = $game->get_escrow();
				foreach($escrowCards as $eCard) {
					$eCardDisplay .= $eCard->cardImg();
				}
			}


			echo "
					<td>{$p1->get_pileCount()}</td><td>{$p2->get_pileCount()}</td><td class='spoils'>$eCardDisplay" . ($game->get_escrowCount() ? " ({$game->get_escrowCount()})" : "") . "</td>
				</tr>";	//	strong css for winning card

			$countBattles++;
		}
		echo "</table>";

		if($countBattles >= 2000) {
			$h2 = "Too many Battles! ($countBattles)";
		}

		if($p1->get_pileCount() > LOSING_SCORE) {
			$h2 = $p1->get_name() . " wins! ({$p2->get_name()} ran out of cards)";
		} else {
			$h2 = $p2->get_name() . " wins! ({$p1->get_name()} ran out of cards)";
		}

		echo "<h2>$h2</h2>
			<br>Battle Count: <b>$countBattles</b>
			<h3>Want to play again?</h3>
			$form";
	}
?>
</body>
</html>
