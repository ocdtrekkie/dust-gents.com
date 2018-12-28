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

echo "<div class=\"col\">";

$characterID = (int)$_POST['characterID'];
	$primary = $_POST['primary'];
	$secondary = $_POST['secondary'];
	
	if ($characterID != $chardata['characterID'])
		die("Do not tamper with the form!");
	
	$submitit = getDb()->prepare('UPDATE `members` SET `role_1` = :primary, `role_2` = :secondary WHERE `characterID` = :characterID');
	$submitit->bindParam(':primary', $primary, PDO::PARAM_STR, 2);
	$submitit->bindParam(':secondary', $secondary, PDO::PARAM_STR, 2);
	$submitit->bindParam(':characterID', $characterID, PDO::PARAM_INT);
	$submititresult = $submitit->execute();
	
	echo "<div>Roles updated.</div>";
	
echo "</div>";

require "includes/footer.php"; ?>