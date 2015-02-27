<?php
require('kolbe.php');

$sql = "
	SELECT
		q.id,
		q.question
	FROM kb_questions AS q
	ORDER BY q.id";
$sth = $dbh->prepare($sql);
$sth->execute();
$questions = sthFetchObjects($sth);	//	fetch all of the quotes and put them in $quotes array of objects
shuffle($questions);

$questionsHTML = "";
foreach($questions as $q) {
	$sql = "
		SELECT
			a.answer,
			a.type
		FROM kb_answers AS a
		WHERE a.questionId = " . $q->id . "
		ORDER BY a.id
		LIMIT 4";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	$answers = sthFetchObjects($sth);	//	fetch all of the quotes and put them in $quotes array of objects
	shuffle($answers);

	$answersHTML = "";
	foreach($answers as $a) {
		//	NOTE: question #11 is REVERSED!;
		$answersHTML .= "
			<tr>
				<td>" . $a->answer . "</td>
				<td>
					<label for='m_{$q->id}-{$a->type}'>
						<input type='radio' name='" . ($q->id === 11 ? "l" : "m") . "[{$q->id}]' value='" . $a->type . "' id='m_{$q->id}-{$a->type}'>
					</label>
				</td>
				<td>
					<label for='l_{$q->id}-{$a->type}'>
						<input type='radio' name='" . ($q->id === 11 ? "m" : "l") . "[{$q->id}]' value='" . $a->type . "' id='l_{$q->id}-{$a->type}'>
					</label>
				</td>
			</tr>\n";
	}
	$questionsHTML .= "
		<li>
			<table>
				<thead>
					<tr><th>" . $q->question . "...</th><th>Most</th><th>Least</th></tr>
				</thead>
				<tbody>
					$answersHTML
				</tbody>
			</table>
		</li>";
}
$questionsHTML = "<ol>" . $questionsHTML . "</ol>";

?>
<?=$header;?>
<body>
	<?=$topper;?>
		<section>
			<h2>Conative Style Test</h2>
			<form action="process_form.php" method="post">
				<header>
					<ul>
						<li><label>First Name:</label><input type="text" name="firstName" required></li>
						<li><label>Last Name:</label><input type="text" name="lastName" required></li>
						<li><label>Age:</label><input type="number" name="age" maxlength="2" pattern="^\d{1,2}$" required></li>
					</ul>
				</header>
				<p>Answer by asking yourself,<br><em>If free to be myself I would ...</em></p>
				<?=$questionsHTML;?>
				<input type="hidden" name="date" value="<?=date('Y-m-d H:i');?>">
				<input type="hidden" name="test" value="Kolbe">
				<button class="button icon approve">Submit Test</button>
			</form>
		</section>
	<?=$footer;?>
</body>
</html>
