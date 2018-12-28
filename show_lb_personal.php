<?php $page_title = "View Personal Stats";
require "includes/functions.php";
require "includes/global.php";
require "includes/header.php";

$apiquery = getDb()->prepare('SELECT * FROM `members` WHERE `user_id` = ?');

if ($user->data['user_id'] != ANONYMOUS)
{
	$apiqueryresult = $apiquery->execute(array($user->data['user_id']));
	$chardata = $apiquery->fetch(PDO::FETCH_ASSOC);
	
	if (!$chardata)
	{
	die("Unauthorized");
	}
}
else
die("Unauthorized");

echo "<div class=\"col\"><h1><font color=white>" . $chardata['characterName'] . "</font></h1>
<table><tr><th>Last Updated</th><th>WPPD</th><th>KDR</th><th>SDR</th><th>WLR</th><th>Kills</th><th>Deaths</th><th>War Points</th></tr>";

$showitquery = GetDb()->prepare('SELECT * FROM `leaderboard` WHERE `characterID` = ? ORDER BY date DESC');
$showitqueryresult = $showitquery->execute(array($chardata['characterID']));
$lbdata = $showitquery->fetchAll();

foreach($lbdata as $lbitem):
	$wppd = $lbitem['wppd'];
	$kdr = round($lbitem['kdr'],2);
	$sdr = round($lbitem['ds_supd'],2);
	$wlr = $lbitem['wlr'];
	$kills = $lbitem['kills'];
	$deaths = $lbitem['deaths'];
	$warpoints = $lbitem['warpoints'];
	$lastupdated = $lbitem['date'];
	
	echo "<tr><td>$lastupdated</td><td>$wppd</td><td>$kdr</td><td>$sdr</td><td>$wlr</td><td>$kills</td><td>$deaths</td><td>$warpoints</td></tr>";
endforeach;

echo "</table><br><img src=\"show_lb_personal2.php\">
<table>
<tr><td>WPPD</td><td><b>War Points/Death</b> - The number of war points earning during the course of an average life.</td></tr>
<tr><td>KDR</td><td><b>Kill/Death Ratio</b> - Points earned from kills over points lost from deaths during the course of an average life.</td></tr>
<tr><td>SDR</td><td><b>Support/Death Ratio</b> - Points earned from support over points lost from deaths during the course of an average life.</td></tr>
<tr><td>WLR</td><td><b>Wins/Losses</b> - The number of wins per loss.</td></tr>
</table></div>";

require "includes/footer.php"; ?>