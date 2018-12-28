<?php $page_title = "Show Roles";
require "includes/functions.php";
require "includes/global.php";
require "includes/header.php";
// JEFF REASOR.

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

echo "<div class=\"col\"><table><tr><th>Character</th><th>Roles</th></tr>";

$showitquery = GetDb()->prepare('SELECT * FROM `members` ORDER BY characterName DESC');
// Nonfunctonal: $showitquery->bindParam(':sortval', $sortval, PDO::PARAM_STR, 4);
$showitqueryresult = $showitquery->execute();
$lbdata = $showitquery->fetchAll();

$tickerquery = getDb()->prepare('SELECT `ticker` FROM `corporations` WHERE `corporationID` = ?');

foreach($lbdata as $lbitem):
	$charid = $lbitem['characterID'];
	
	$tickerqueryresult = $tickerquery->execute(array($lbitem['corporationID']));
	$ticker = $tickerquery->fetch(PDO::FETCH_ASSOC);
	if (( $lbitem['role_1'] == 'LG' ) || ( $lbitem['role_2'] == 'LG' )):
		echo "<tr><td>" . $lbitem['characterName'] . " [" . $ticker['ticker'] . "]</td><td>" . $lbitem['role_1'] . "/" . $lbitem['role_2'] . "<td>$lastupdated</td></tr>";
	endif;	
endforeach;

echo "</table></div>";

require "includes/footer.php"; ?>