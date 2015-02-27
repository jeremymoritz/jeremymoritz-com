<?php
$title = 'Kolbe Fake Test Results';
require('kolbe.php');
$featuredPerson = false;
$forWhom = "";

if(apiGet('FF') && apiGet('FT') && apiGet('QS') && apiGet('IM') && apiGet('firstName') && apiGet('lastName') && apiGet('age')) {
	$featuredPerson = true;
	$forWhom = " for " . apiGet('firstName') . " " . apiGet('lastName');
}

$sql = "
	SELECT
		id,
		firstName,
		lastName,
		age,
		FF,
		FT,
		QS,
		IM,
		date
	FROM kb_results
	WHERE approved = 1
	ORDER BY date DESC";
$sth = $dbh->prepare($sql);
$sth->execute();
$results = sthFetchObjects($sth);	//	fetch all of the quotes and put them in $quotes array of objects

$rows = "";
foreach($results as $r) {
	$rows .= "
		<tr>
			<th>{$r->firstName} {$r->lastName} ({$r->age})</th>
			<td class='FF'><div style='height: {$r->FF}0%'></div><span>{$r->FF}</span></td>
			<td class='FT'><div style='height: {$r->FT}0%'></div><span>{$r->FT}</span></td>
			<td class='QS'><div style='height: {$r->QS}0%'></div><span>{$r->QS}</span></td>
			<td class='IM'><div style='height: {$r->IM}0%'></div><span>{$r->IM}</span></td>
		</tr>";
}

?>
<?=$header;?>
<body id="results">
	<?=$topper;?>
		<h2>Conative Style Test Results<?=$forWhom;?></h2>
		<?php
			if($featuredPerson) {
		?>
				<section class="personal">
					<table>
						<thead>
							<tr>
								<th>Fact Finder</th>
								<th>Follow Through</th>
								<th>Quick Start</th>
								<th>Implementer</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td><?=apiGet('FF');?></td>
								<td><?=apiGet('FT');?></td>
								<td><?=apiGet('QS');?></td>
								<td><?=apiGet('IM');?></td>
							</tr>
						</tfoot>
						<tbody>
							<tr>
								<td class='FF'><div style="height: <?=apiGet('FF');?>0%" /></td>
								<td class='FT'><div style="height: <?=apiGet('FT');?>0%" /></td>
								<td class='QS'><div style="height: <?=apiGet('QS');?>0%" /></td>
								<td class='IM'><div style="height: <?=apiGet('IM');?>0%" /></td>
							</tr>
						</tbody>
					</table>
				</section>
		<?php
			}
		?>
		<section class="all_results">
			<table>
				<caption>All Results</caption>
				<thead>
					<tr>
						<th>Name (age)</th>
						<th>Fact Finder</th>
						<th>Follow Through</th>
						<th>Quick Start</th>
						<th>Implementer</th>
					</tr>
				</thead>
				<tbody>
					<?=$rows;?>
				</tbody>
			</table>
		</section>
		<section class="descriptions">
			<p>There are no right/wrong or good/bad results in any area.  A high score in a given mode may be an asset in some cases and a liability in others (it depends on the job/task at hand).  Additionally, a high score in one mode will always be counter-balanced by a low score in another.</p>
			<dl>
				<dt class='FF'>Fact Finder</dt>
					<dd>the instinctive way we gather and share information.
						<ul>
							<li><strong>HIGH SCORE (&ge;6):</strong> You like to examine all of the facts to do something right the first time.</li>
							<li><strong>LOW SCORE (&le;4):</strong> You are not afraid to experiment or start things without adequate information.</li>
						</ul>
					</dd>
				<dt class='FT'>Follow Thru</dt>
					<dd>the instinctive way we arrange and design.
						<ul>
							<li><strong>HIGH SCORE (&ge;6):</strong> You have a strong desire to finish what you start.</li>
							<li><strong>LOW SCORE (&le;4):</strong> You are comfortable with abandoning a project if you feel that it is not worthwhile.</li>
						</ul>
					</dd>
				<dt class='QS'>Quick Start</dt>
					<dd>the instinctive way we deal with risk and uncertainty.
						<ul>
							<li><strong>HIGH SCORE (&ge;6):</strong> You are quick to accept new challenges and seize opportunities.</li>
							<li><strong>LOW SCORE (&le;4):</strong> You hesitate or proceed with caution when new opportunities present themselves.</li>
						</ul>
					</dd>
				<dt class='IM'>Implementor</dt>
					<dd>the instinctive way we handle space and tangibles.
						<ul>
							<li><strong>HIGH SCORE (&ge;6):</strong> You enjoy doing things on your own and/or creating things physically.</li>
							<li><strong>HIGH SCORE (&le;4):</strong> You prefer to delegate tasks and lead others over doing the manual work yourself.</li>
						</ul>
					</dd>
			</dl>
			<p>It is generally considered impossible to change a Kolbe score through practice or learning. Instincts are constant and each individual has a similar proportion of mental energy associated with those instincts. Working against instinctive strengths can deplete mental energy in unproductive ways.</p>
			<p>If an individual employee is in a job which represents a poor conative fit, they may experience strain due to unrealistic conative self-expectations or tension due to the unrealistic conative expectations of others.</p>
			<p>Each of the modes is discussed in greater detail in the <a href='/_projects/kolbe/Kolbe-Statistical-Handbook.pdf' target='_blank'>Kolbe Statistical Handbook</a>.</p>
		</section>
	<?=$footer;?>
</body>
</html>
