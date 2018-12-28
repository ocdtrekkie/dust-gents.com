<?php $page_title = "Update Roles";
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

echo "<div class=\"col\"><p>Your roles define your playstyle on the battlefield. Your primary role is your \"60% time\" playstyle. Or the playstyle you use the most. Generally, most of your skills should be focused on it, and when possible, it's the playstyle you should use for important matches. The secondary playstyle is perhaps more aptly, your \"30% time\" playstyle, which is one you use some other times. While your skills in it may not be maxxed, they generally will be more versed than your skills in other roles.</p><br>";

	echo "<form action=\"update_roles.php\" method=\"post\"><fieldset>Update roles for " . $chardata['characterName'] .
	"<table>
	<tr><td>Character ID:</td><td><input name=\"characterID\" size=\"8\" value=\"" . $chardata['characterID'] ."\" readonly></td></tr>
	<tr><td>Primary Role:</td><td><select name=\"primary\">
	<option value=\"UB\" selected>UB - Unspecified Blueberry</option>
	<option value=\"FL\">FL - Frontline</option>
	<option value=\"HW\">HW - Heavy Weapons</option>
	<option value=\"SN\">SN - Sniper</option>
	<option value=\"LG\">LG - Logistics</option>
	<option value=\"SC\">SC - Scout</option>
	<option value=\"TK\">TK - Tank Driver</option>
	<option value=\"DS\">DS - Dropship Pilot</option>
	</select></td></tr>
	<tr><td>Secondary Role:</td><td><select name=\"secondary\">
	<option value=\"UB\" selected>UB - Unspecified Blueberry</option>
	<option value=\"FL\">FL - Frontline</option>
	<option value=\"HW\">HW - Heavy Weapons</option>
	<option value=\"SN\">SN - Sniper</option>
	<option value=\"LG\">LG - Logistics</option>
	<option value=\"SC\">SC - Scout</option>
	<option value=\"TK\">TK - Tank Driver</option>
	<option value=\"DS\">DS - Dropship Pilot</option>
	</select></td></tr>
	<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"Submit updated roles\"></td></tr>
	</table>
	</fieldset></form>";

echo "</div>";

require "includes/footer.php"; ?>