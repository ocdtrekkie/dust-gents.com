<?php $page_title = "View Corporation";
require "includes/global.php";
require "includes/header.php";
require_once 'includes/functions.php';
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
$msg = '';

if (!empty($_GET['corporationID'])) {
	$corporationID = $_GET['corporationID'];
	$corpquery = getDb()->prepare('SELECT * FROM `corporations` WHERE `corporationID` = ?');
	$corpresult = $corpquery->execute(array($corporationID));
	$corp = $corpquery->fetch(PDO::FETCH_ASSOC);
	
	$ceoquery = getDb()->prepare('SELECT * FROM `members` WHERE `characterID` = ?');
	$ceoresult = $ceoquery->execute(array($corp['ceoID']));
	$ceo = $ceoquery->fetch(PDO::FETCH_ASSOC);
	
	echo "<table><tr><td><img src=\"http://image.eveonline.com/Corporation/" . $corp['corporationID'] . "_128.png\"></td><td><p><font size=\"6\" color=\"white\">" . $corp['corporationName'] . "</font><font size=\"4\"> [" . $corp['ticker'] . "]</font></p>
	<br><p>CEO: " . $ceo['characterName'] . "<br>Member Count: " . $corp['member_count'] . "<br>Tax Rate: " . $corp['taxRate'] . "%</p></td></tr></table>
	
	<p>" . $corp['description'] . "</p>";

	if ($user->data['user_id'] != ANONYMOUS)
	{
		$apiquery = getDb()->prepare('SELECT * FROM `members` WHERE `user_id` = ?');
		$apiresult = $apiquery->execute(array($user->data['user_id']));
		$chardata = $apiquery->fetch(PDO::FETCH_ASSOC);
		
		if ($chardata)
		{
			if (!empty($_GET['inactivereport'])) {
				$rosterquery = getDb()->prepare('SELECT * FROM `members` WHERE `corporationID` = ? ORDER BY `lastlog` ASC');
			}
			else
			{
				$rosterquery = getDb()->prepare('SELECT * FROM `members` WHERE `corporationID` = ? ORDER BY `roles` DESC, `characterName` ASC');
			}
			$rosterresult = $rosterquery->execute(array($corporationID));
			$roster = $rosterquery->fetchAll(PDO::FETCH_ASSOC);
			
			echo "<br><p><font size=\"4\">Corporation Roster:</font><br>Last updated: " . $corp['lastupdated'] . "</p>
			<div style=\"float: left; padding: 20px;\">
			<table class=\"tablebg\" cellspacing=\"1\">";
			
			foreach ($roster as $member):
				if ($member['roles'] == 9223372036854775807)
				{
					if ($member['characterID'] == $corp['ceoID'])
						$roletext = " - CEO";
					else
						$roletext = " - Director";
				}
				elseif ($member['roles'] == 1152921504606847104 || $member['roles'] == 128)
					$roletext = " - Personnel Director";
				else
					$roletext = "";
				if (substr($member['characterID'], 0, 2) == '21' && strlen($member['characterID']) == '10')
					$dustie = true;
				else
					$dustie = false;
				if ($dustie == false)
					$membername = "<font color=\"aqua\"><b>" . $member['characterName'] . "</b></font>";
				else
					$membername = "<b>" . $member['characterName'] . "</b> (" . $member['role_1'] . "/" . $member['role_2'] . ")";
				if ($member['user_id'] != '0')
					$pmlink = "<a href=\"http://forums.dust-gents.com/ucp.php?i=pm&mode=compose&u=" . $member['user_id'] . "\" border=\"0\"><img src=\"forums/styles/CoDFaction/imageset/en/icon_contact_pm.gif\" /></a>";
				else
					$pmlink = "";
			
				echo "<tr><td class=\"row1\" align=\"left\">" . $membername . $roletext . "</td><td class=\"row1\">$pmlink</td><td class=\"row1\" align=\"left\">Last Seen: " . $member['lastlog'] . "</td></tr>";
			endforeach;
			
			echo "</table></div>";
		}
	}

}
else
{
	echo "No corporation specified.";
}

echo "<br>";

require "includes/footer.php"; ?>