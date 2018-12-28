<?php $page_title = "View Leaderboard";
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

$sort = request_var('sort', 'wppd');
if ($sort == "wppd") $sortval = "wppd";
if ($sort == "kdr") $sortval = "kdr";
if ($sort == "sdr") $sortval = "sdr";
if ($sort == "wlr") $sortval = "wlr";

echo "<div class=\"col\"><table><tr><th>Character</th><th>Roles</th><th><a href=\"show_lb.php?sort=wppd\">WPPD</a></th><th><a href=\"show_lb.php?sort=kdr\">KDR</a></th><th><a href=\"show_lb.php?sort=sdr\">SDR</a></th><th><a href=\"show_lb.php?sort=wlr\">WLR</a></th><th>Last Updated</th></tr>";

$showitquery = GetDb()->prepare('SELECT * FROM `leaderboard_col` ORDER BY wppd DESC');
// Nonfunctonal: $showitquery->bindParam(':sortval', $sortval, PDO::PARAM_STR, 4);
$showitqueryresult = $showitquery->execute();
$lbdata = $showitquery->fetchAll();

$lookupquery = getDb()->prepare('SELECT * FROM `members` WHERE `characterID` = ?');
$tickerquery = getDb()->prepare('SELECT `ticker` FROM `corporations` WHERE `corporationID` = ?');

foreach($lbdata as $lbitem):
	$charid = $lbitem['characterID'];
	$wppd = $lbitem['wppd'];
	$kdr = round($lbitem['kdr'],2);
	$sdr = round($lbitem['ds_supd'],2);
	$wlr = $lbitem['wlr'];
	$lastupdated = $lbitem['date'];
	
	$lookupqueryresult = $lookupquery->execute(array($charid));
	$itemchardata = $lookupquery->fetch(PDO::FETCH_ASSOC);
	
	$tickerqueryresult = $tickerquery->execute(array($itemchardata['corporationID']));
	$ticker = $tickerquery->fetch(PDO::FETCH_ASSOC);
	
	if ($itemchardata['characterName'] != "")
		echo "<tr><td>" . $itemchardata['characterName'] . " [" . $ticker['ticker'] . "]</td><td>" . $itemchardata['role_1'] . "/" . $itemchardata['role_2'] . "</td><td>$wppd</td><td>$kdr</td><td>$sdr</td><td>$wlr</td><td>$lastupdated</td></tr>";
endforeach;

echo "</table><br>
<table>
<tr><td>WPPD</td><td><b>War Points/Death</b> - The number of war points earning during the course of an average life.</td></tr>
<tr><td>KDR</td><td><b>Kill/Death Ratio</b> - Points earned from kills over points lost from deaths during the course of an average life.</td></tr>
<tr><td>SDR</td><td><b>Support/Death Ratio</b> - Points earned from support over points lost from deaths during the course of an average life.</td></tr>
<tr><td>WLR</td><td><b>Wins/Losses</b> - The number of wins per loss.</td></tr>
<tr><td> </td><td> </td></tr>
<tr><td>FL</td><td><b>Frontline</b></td></tr>
<tr><td>HW</td><td><b>Heavy Weapons</b></td></tr>
<tr><td>SN</td><td><b>Sniper</b></td></tr>
<tr><td>LG</td><td><b>Logistics</b></td></tr>
<tr><td>SC</td><td><b>Scout</b></td></tr>
<tr><td>TK</td><td><b>Tank Driver</b></td></tr>
<tr><td>DS</td><td><b>Dropship Pilot</b></td></tr>
<tr><td>UB</td><td><b>Unspecified Blueberry</b></td></tr>
</table></div>";

require "includes/footer.php"; ?>