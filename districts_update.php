<?php $page_title = "CREST District Info";
	// Initialize stuff
	require "includes/global.php";
	require "includes/header.php";
	require_once 'includes/functions.php';
	include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
	$msg = '';
	
	$rest_districts = file_get_contents('http://public-crest.eveonline.com/districts/');
	$rest_districts = json_decode($rest_districts, true);

	$newdistrict = getDB()->prepare('INSERT INTO `districts` (`id`, `name`, `owner_id`, `owner_name`, `reinforce`, `attacked`, `locked`, `generating`, `clones`, `clone_rate`, `infrastructure`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
	$upddistrict = getDB()->prepare('UPDATE `districts` SET `name` = ?, `owner_id` = ?, `owner_name` = ?, `reinforce` = ?, `attacked` = ?, `locked` = ?, `generating` = ?, `clones` = ?, `clone_rate` = ?, `infrastructure` = ? WHERE `id` = ?');
	$exidistrict = getDB()->prepare('SELECT COUNT(*) FROM `districts` WHERE `id` = ?');
	$chkdistrict = getDB()->prepare('SELECT `owner_name` FROM `districts` WHERE `id` = ?');
	
	$newtally = getDB()->prepare('INSERT INTO `districts_tally` (`owner_id`, `owner_name`, `alliance_id`, `alliance_name`, `count`) VALUES (?, ?, ?, ?, ?)');
	$addtally = getDB()->prepare('UPDATE `districts_tally` SET `count` = `count` + 1 WHERE `owner_id` = ?');
	$exitally = getDB()->prepare('SELECT COUNT(*) FROM `districts_tally` WHERE `owner_id` = ?');
	$alytally = getDB()->prepare('SELECT `alliance_id`, `alliance_name` FROM `alliance_ref` WHERE `corporation_id` = ?');
	$remtally = getDB()->prepare('TRUNCATE TABLE `districts_tally`');
	
	$newalliance = getDB()->prepare('INSERT INTO `districts_alliances` (`alliance_id`, `alliance_name`, `count`) VALUES (?, ?, ?)');
	$addalliance = getDB()->prepare('UPDATE `districts_alliances` SET `count` = `count` + 1 WHERE `alliance_id` = ?');
	$exialliance = getDB()->prepare('SELECT COUNT(*) FROM `districts_alliances` WHERE `alliance_id` = ?');
	$getalliance = getDB()->prepare('SELECT alliance_id FROM `districts_tally` WHERE `owner_id` = ?');
	$remalliance = getDB()->prepare('TRUNCATE TABLE `districts_alliances`');
	
	$logit = getDB()->prepare('INSERT INTO `districts_log` (`event`, `date`) VALUES (?, NOW())');
	
	if ($rest_districts != NULL)
	{
		$tallyreset = $remtally->execute();
		$alliancereset = $remalliance->execute();

		echo "District Count Total: " . $rest_districts['totalCount_str'] . "<p><br>";

		foreach ($rest_districts['items'] as $district):
			$owner = $district['owner'];
			$infrastructure = $district['infrastructure'];
			echo "<b>" . $district['name'] . "</b> - " . $owner['name'] . "<br>";
			// ADD OR UPDATE DISTRICT
			$exiresult = $exidistrict->execute(array($district['id']));
			$exiresult = $exidistrict->fetchColumn();
			if ($exiresult == 0)
			{
				$newresult = $newdistrict->execute(array($district['id'], $district['name'], $owner['id'], $owner['name'], $district['reinforce'], $district['attacked'], $district['locked'], $district['generating'], $district['clones'], $district['cloneRate'], $infrastructure['id']));
			}
			else
			{
				$chkresult = $chkdistrict->execute(array($district['id']));
				$chkresult = $chkdistrict->fetchColumn();
				if ($chkresult != $owner['name'])
					$logitresult = $logit->execute(array($district['name'] . " has been taken over by " . $owner['name'] . ", from previous owner " . $chkresult));
				$updresult = $upddistrict->execute(array($district['name'], $owner['id'], $owner['name'], $district['reinforce'], $district['attacked'], $district['locked'], $district['generating'], $district['clones'], $district['cloneRate'], $infrastructure['id'], $district['id']));
			}
			
			//ADD OR UPDATE CORP
			$tallycheck = $exitally->execute(array($owner['id']));
			$tallycheck = $exitally->fetchColumn();
			if ($tallycheck == 0)
			{
				$tallyally = $alytally->execute(array($owner['id']));
				$tallyally = $alytally->fetch(PDO::FETCH_ASSOC);
				
				$alliance_id = 0;
				$alliance_name = "";
				
				if ($tallyally['alliance_id'] != NULL)
				{
					$alliance_id = $tallyally['alliance_id'];
					//ADD OR UPDATE ALLIANCE
					$alliancecheck = $exialliance->execute(array($alliance_id));
					$alliancecheck = $exialliance->fetchColumn();
					if ($alliancecheck == 0)
					{
						$alliancenew = $newalliance->execute(array($tallyally['alliance_id'], $tallyally['alliance_name'], 1));
					}
					else
					{
						$allianceadd = $addalliance->execute(array($tallyally['alliance_id']));
					}
				}
				if ($tallyally['alliance_name'] != NULL)
					$alliance_name = $tallyally['alliance_name'];
			
				$tallynew = $newtally->execute(array($owner['id'], $owner['name'], $alliance_id, $alliance_name, 1));
			}
			else
			{
				$allianceget = $getalliance->execute(array($owner['id']));
				$allianceget = $getalliance->fetch(PDO::FETCH_ASSOC);
				
				if ($allianceget['alliance_id'] != 0)
					$allianceadd = $addalliance->execute(array($allianceget['alliance_id']));
			
				$tallyupdate = $addtally->execute(array($owner['id']));
			}
		endforeach;
	}
	else
	{
		echo "JSON is returning null.";
	}

	require "includes/footer.php";
?>