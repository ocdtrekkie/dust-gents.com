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

echo "<div class=\"col\"><p><b>Submit cumulative stats only.</b> Most of these can be found on your Character Sheet in the Neocom. In order to get Win/Loss Ratio, you will need to open the Leaderboard on the Neocom, and tab down to \"Win/Loss Ratio\" and over to \"Contacts\" and find your name. <b>Do not use commas. At all.</b></p><br>";

	echo "<form action=\"calc_stats.php\" method=\"post\"><fieldset>Submit stats for " . $chardata['characterName'] .
	"<table>
	<tr><td>Character ID:</td><td><input name=\"characterID\" size=\"8\" value=\"" . $chardata['characterID'] ."\" readonly></td></tr>
	<tr><td>Kills:</td><td><input name=\"kills\" id=\"kills\" size=\"3\"></td></tr>
	<tr><td>Deaths:</td><td><input name=\"deaths\" id=\"deaths\" size=\"3\"></td></tr>
	<tr><td>War Points:</td><td><input name=\"warpoints\" id=\"warpoints\" size=\"6\"></td></tr>
	<tr><td>Win/Loss Ratio:</td><td><input name=\"wlr\" id=\"wlr\" size=\"3\"></td></tr>
	<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"Submit updated stats\"></td></tr>
	</table>
	</fieldset></form>";

echo "</div>";

require "includes/footer.php"; ?>