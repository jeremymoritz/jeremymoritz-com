<?php
require('kolbe.php');

if(apiGet('random') || apiPost('test') && apiPost('test') === 'Kolbe') {
	$firstName = apiPost('firstName');
	$lastName = apiPost('lastName');
	$age = apiPost('age');
	$date = apiPost('date');
	$mosts = apiPost('m');	//	list of most
	$leasts = apiPost('l');	//	list of least
	$answers = '';
	$questionCount = 0;
	$highestPossibleScore = 10;	//	everything is out of 10
	$midwayScore = $highestPossibleScore / 2;
	$approved = 1;	//	approved by default

	$typeScores = array(
		'FF' => 0,
		'FT' => 0,
		'QS' => 0,
		'IM' => 0
	);

	if(apiGet('random') && apiGet('random')) {
		for($i = 1; $i <= 36; $i++) {
			$mosts[$i] = array_rand($typeScores);
			$leasts[$i] = array_rand($typeScores);
		}
	}

	foreach($mosts as $qId => $mType) {
		$questionCount++;
		$answers .= ($answers > '' ? '|' : '') . $qId . "_" . $mType . "-" . $leasts[$qId];	//	answers to each specific question

		$typeScores[$mType] += ($qId === 11 ? -1 : 1);	//	question 11 is in reverse
		$typeScores[$leasts[$qId]] -= ($qId === 11 ? -1 : 1);	//	question 11 is in reverse
	}

	$valueOfEachQuestion = $midwayScore / $questionCount;	//	if all questions are answered the same way, they can only go up or down by a few points

	function roundBiased($num) {
		$precision = 0.2;
		if($num >= 0) {
			return $num - floor($num) > $precision ? ceil($num) : floor($num);
		} else {
			return $num + ceil($num) < ($precision * -1) ? floor($num) : ceil($num);
		}
	}

	$unRoundedTypeScores = array();
	foreach($typeScores as $type => $score) {
		$typeScores[$type] = $midwayScore + roundBiased($score * $valueOfEachQuestion);
		$unRoundedTypeScores[$type] = $midwayScore + ($score * $valueOfEachQuestion);
	}

	$sql = "
		INSERT INTO kb_results (
			firstName,
			lastName,
			age,
			answers,
			FF,
			FT,
			QS,
			IM,
			date,
			approved
		) VALUES (
			:firstName,
			:lastName,
			:age,
			:answers,
			:FF,
			:FT,
			:QS,
			:IM,
			:date,
			:approved
		);";
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':firstName', $firstName, PDO::PARAM_STR);
	$sth->bindParam(':lastName', $lastName, PDO::PARAM_STR);
	$sth->bindParam(':age', $age, PDO::PARAM_INT);
	$sth->bindParam(':answers', $answers, PDO::PARAM_STR);
	$sth->bindParam(':FF', $typeScores['FF'], PDO::PARAM_INT);
	$sth->bindParam(':FT', $typeScores['FT'], PDO::PARAM_INT);
	$sth->bindParam(':QS', $typeScores['QS'], PDO::PARAM_INT);
	$sth->bindParam(':IM', $typeScores['IM'], PDO::PARAM_INT);
	$sth->bindParam(':date', $date, PDO::PARAM_STR);
	$sth->bindParam(':approved', $approved, PDO::PARAM_INT);

	if(apiGet('random') || strtolower($firstName) === 'test') {
		echo "typeScores: <pre>";
		print_r($typeScores);
		echo "</pre>";
		echo "unRoundedTypeScores: <pre>";
		print_r($unRoundedTypeScores);
		echo "</pre>";
		echo "answers: <pre>";
		print_r($answers);
		echo "</pre>";
	} else {
		$sth->execute();

		//	Send an email to myself...
		$to = 'jeremy@jeremymoritz.com';
		$message = "<b>name:</b> $firstName $lastName<br>\n"
			. "<b>age:</b> $age<br>\n"
			. "<b>result:</b><br>\nFact Finder: {$typeScores['FF']}<br>\nFollow Through: {$typeScores['FT']}<br>\nQuick Start: {$typeScores['QS']}<br>\nImplementer: {$typeScores['IM']}<br>\n"
			. "<b>all results:</b> http://jeremymoritz.com/_projects/kolbe/results.php";
		$subject = "New Kolbe Results:  $firstName $lastName - {$typeScores['FF']}{$typeScores['FT']}{$typeScores['QS']}{$typeScores['IM']}";
		$headers = "From: Kolbe Fake Test Results <$to>\n"
			. "Reply-To: No Reply <$to>\n"
			. "MIME-Version: 1.0\n"
			. "Content-type: text/html; charset=iso-8859-1";
		$message = preg_replace("#(?<!\r)\n#si", "\r\n", $message);	// Fix any bare linefeeds in the message to make it RFC821 Compliant
		$headers = preg_replace("#(?<!\r)\n#si", "\r\n", $headers); // Make sure there are no bare linefeeds in the headers
		mail($to,$subject,$message,$headers);

		header("Location: results.php?FF={$typeScores['FF']}&FT={$typeScores['FT']}&QS={$typeScores['QS']}&IM={$typeScores['IM']}&firstName=" . urlencode($firstName) . "&lastName=" . urlencode($lastName) . "&age={$age}");
 	}
} else {
	header("Location: test.php");
}
?>
