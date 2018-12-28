<?php $page_title = "District Log";
require "includes/functions.php";
require "includes/global.php";
require "includes/header.php";

	echo "<p align=\"left\"><b>Explore Districts:</b> <a href=\"districts.php\">By District</a> | <a href=\"districts.php?sort=reinforce\">By Reinforcement Timer</a> | <a href=\"districts.php?sort=corptimer\">By Corporation</a> | <a href=\"districts.php?sort=lowclones\">Low Clones</a><br>
			<b>District Holdings:</b> <a href=\"districts_report.php\">By Corporation</a> | <a href=\"districts_alliances.php\">By Alliance</a><br>
			<b>District Logs:</b> <a href=\"districts_log.php\">Ownership Changes</a></p><br>
			<p align=\"left\"><font color=\"grey\">Updates are run five minutes after the hour. This service is provided free of charge for all users. Donations of ISK to Soraya Xel (DUST) or Crasniya (EVE) are welcome.</font></p>
			<div style=\"float: left; padding: 20px;\">";

echo "<div class=\"col\"><table><tr><th width=\"150\">Date (CST)</th><th>Event</th></tr>";

$showitquery = GetDb()->prepare('SELECT * FROM `districts_log` ORDER BY date DESC LIMIT 50');
$showitqueryresult = $showitquery->execute(array());
$lbdata = $showitquery->fetchAll();

foreach($lbdata as $lbitem):
	$event = $lbitem['event'];
	$date = $lbitem['date'];
	
	echo "<tr><td>$date</td><td>$event</td></tr>";
endforeach;

echo "</table></div>";

require "includes/footer.php"; ?>