<?php $page_title = "Update Stats";
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

echo "<div class=\"col\">";

$characterID = (int)$_POST['characterID'];
	$kills = str_replace( ',', '', $_POST['kills']);
	$kills = (int)$kills;
	$deaths = str_replace( ',', '', $_POST['deaths']);
	$deaths = (int)$deaths;
	$warpoints = str_replace( ',', '', $_POST['warpoints']);
	$warpoints = (int)$warpoints;
	$wlr = (float)$_POST['wlr'];
	
	if ($characterID != $chardata['characterID'])
		die("Do not tamper with the form!");
		
	$kdr = round($kills / $deaths, 4);
	$wppd = round($warpoints / $deaths, 2);
	$ds_kp = $kills * 50;
	$ds_dp = $deaths * 50;
	$ds_sp = $warpoints - $ds_kp;
	$ds_sup = $ds_sp / $ds_kp; //dust514stats support ratio
	$ds_supd = round($ds_sp / $ds_dp, 4); //dust514stats support death ratio
	
	$submitit = getDb()->prepare('INSERT INTO `leaderboard` (`characterID`, `kills`, `deaths`, `warpoints`, `wlr`, `date`, `kdr`, `wppd`, `ds_sup`, `ds_supd`) VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?)');
	$submitit2 = getDb()->prepare('INSERT INTO `leaderboard_col` (`characterID`, `kills`, `deaths`, `warpoints`, `wlr`, `date`, `kdr`, `wppd`, `ds_sup`, `ds_supd`) VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `kills` = ?, `deaths` = ?, `warpoints` = ?, `wlr` = ?, `date` = NOW(), `kdr` = ?, `wppd` = ?, `ds_sup` = ?, `ds_supd` = ?');
	$submititresult = $submitit->execute(array($characterID, $kills, $deaths, $warpoints, $wlr, $kdr, $wppd, $ds_sup, $ds_supd));
	$submitit2result = $submitit2->execute(array($characterID, $kills, $deaths, $warpoints, $wlr, $kdr, $wppd, $ds_sup, $ds_supd, $kills, $deaths, $warpoints, $wlr, $kdr, $wppd, $ds_sup, $ds_supd));
	
	echo "<div>Kill/Death Ratio: $kdr<br>Win/Loss Ratio: $wlr<br>War Points Per Death: $wppd<br>Support Ratio: $ds_sup<br>Support/Death Ratio: $ds_supd</div>";
	
echo "</div>";

require "includes/footer.php"; ?>