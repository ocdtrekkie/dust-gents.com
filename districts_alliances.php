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
	
	$getalliance = getDb()->prepare('SELECT * FROM `districts_alliances` ORDER BY count DESC, alliance_name ASC');
	$gettally = getDb()->prepare('SELECT * FROM `districts_tally` WHERE `alliance_id` = ? ORDER BY count DESC, owner_name ASC');

	$allianceget = $getalliance->execute();
	$allianceget = $getalliance->fetchAll(PDO::FETCH_ASSOC);
	
	foreach ($allianceget as $alliance):
		echo "<h1><font color=white>" . $alliance['alliance_name'] . " - " . $alliance['count'] . "</font></h1><br>";
		
			$tally = $gettally->execute(array($alliance['alliance_id']));
			$tally = $gettally->fetchAll(PDO::FETCH_ASSOC);
			
			echo "<table class=\"tablebg\" cellspacing=\"1\">";
			
			foreach ($tally as $corp):
				echo "<tr><td class=\"row1\" align=\"left\" width=\"300\"><b>" . $corp['owner_name'] . "</b></td><td class=\"row1\" align=\"right\" width=\"25\">" . $corp['count'] . "</td></tr>";
			endforeach;
	
			echo "</table><p><br></p>";
	endforeach;
	
	echo "</div>";

	require "includes/footer.php";
?>