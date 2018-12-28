<?php $page_title = "CREST District Info";
	// Initialize stuff
	require "includes/global.php";
	require "includes/header.php";
	require_once 'includes/functions.php';
	include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
	$msg = '';
	
	echo "<p align=\"left\"><b>Explore Districts:</b> <a href=\"districts.php\">By District</a> | <a href=\"districts.php?sort=reinforce\">By Reinforcement Timer</a> | <a href=\"districts.php?sort=corptimer\">By Corporation</a> | <a href=\"districts.php?sort=lowclones\">Low Clones</a><br>
			<b>District Holdings:</b> <a href=\"districts_report.php\">By Corporation</a> | <a href=\"districts_alliances.php\">By Alliance</a><br>
			<b>District Logs:</b> <a href=\"districts_log.php\">Ownership Changes</a></p><br>
			<p align=\"left\"><font color=\"grey\">Updates are run five minutes after the hour. This service is provided free of charge for all users. Donations of ISK to Soraya Xel (DUST) or Crasniya (EVE) are welcome.</font></p>
			<div style=\"float: left; padding: 20px;\">";
	
	$gettally = getDb()->prepare('SELECT * FROM `districts_tally` ORDER BY count DESC, owner_name ASC');

	$tally = $gettally->execute();
	$tally = $gettally->fetchAll(PDO::FETCH_ASSOC);
	
	echo "<table class=\"tablebg\" cellspacing=\"1\">";
	
	foreach ($tally as $corp):
		if ($corp['alliance_id'] != 0)
			$alliance_name = " [" . $corp['alliance_name'] . "]";
		else
			$alliance_name = "";
		echo "<tr><td class=\"row1\" align=\"left\" width=\"300\"><b>" . $corp['owner_name'] . "</b>" . $alliance_name . "</td><td class=\"row1\" align=\"right\" width=\"25\">" . $corp['count'] . "</td></tr>";
	endforeach;
	
	echo "</table></div>";

	require "includes/footer.php";
?>