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
	
	if (!empty($_GET['corporationID'])) {
		$corporationID = $_GET['corporationID'];
		$getdistricts = getDb()->prepare('SELECT * FROM `districts` WHERE `owner_id` = ? ORDER BY name ASC');
		$sort = "name";
		$getresult = $getdistricts->execute(array($corporationID));
		$cntdistricts = getDb()->prepare('SELECT COUNT(*) FROM `districts` WHERE `owner_id` = ?');
		$cntresult = $cntdistricts->execute(array($corporationID));
		$cntresult = $cntdistricts->fetchColumn();
		echo "<p>Districts Owned: " . $cntresult . "</p><br>";
	}
	if (!empty($_GET['reinforce'])) {
		$reinforce = $_GET['reinforce'];
		$getdistricts = getDb()->prepare('SELECT * FROM `districts` WHERE `reinforce` = ? ORDER BY name ASC');
		$getresult = $getdistricts->execute(array($reinforce));
	}
	if ($_GET['sort'] == "corptimer") {
		$getdistricts = getDb()->prepare('SELECT * FROM `districts` ORDER BY owner_name, reinforce ASC');
		$getresult = $getdistricts->execute();
	}
	if ($_GET['sort'] == "lowclones") {
		$getdistricts = getDb()->prepare('SELECT * FROM `districts` ORDER BY clones ASC');
		$getresult = $getdistricts->execute();
	}
	if ($_GET['sort'] == "reinforce") {
		$getdistricts = getDb()->prepare('SELECT * FROM `districts` ORDER BY reinforce ASC');
		$getresult = $getdistricts->execute();
	}
	if (empty($_GET['corporationID']) && empty($_GET['reinforce']) && empty($_GET['sort'])) {
		$getdistricts = getDb()->prepare('SELECT * FROM `districts` ORDER BY name ASC');
		$getresult = $getdistricts->execute();
	}

	$districts = $getdistricts->fetchAll(PDO::FETCH_ASSOC);
	
	echo "<table class=\"tablebg\" cellspacing=\"1\">";
	
	foreach ($districts as $district):
		if ($district['locked'] == 1)
			$status = "Locked";
		if ($district['attacked'] == 1)
			$status = "Under Attack";
		if ($district['attacked'] == 0 && $district['locked'] == 0)
			$status = "Online";
		if ($district['generating'] == 1)
			$clones = $district['clones'] . " + " . $district['clone_rate'];
		else
			$clones = $district['clones'];
		if ($district['infrastructure'] == 364205)
			$infrastructure = "Cargo Hub";
		if ($district['infrastructure'] == 364206)
			$infrastructure = "Research Lab";
		if ($district['infrastructure'] == 364207)
			$infrastructure = "Production";
		$reinforce = $district['reinforce'] . ":00 UTC";
		echo "<tr><td class=\"row1\" align=\"left\"><b>" . $district['name'] . "</b></td><td class=\"row1\">" . $district['owner_name'] . "</td><td class=\"row1\">" . $reinforce . "</td><td class=\"row1\">" . $clones . "</td><td class=\"row1\">" . $status . "</td><td class=\"row1\">" . $infrastructure . "</td></tr>";
	endforeach;
	
	echo "</table></div>";

	require "includes/footer.php";
?>