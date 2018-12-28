<?php
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

if (($user->data['user_id'] != 2) && ($user->data['user_id'] != 55) && ($user->data['user_id'] != 70) && ($user->data['user_id'] != 108) && ($user->data['user_id'] != 688))
die("Unauthorized");

echo "<div class=\"col\">
<table><tr><th>Date</th><th>Event</th></tr>";

$showitquery = GetDb()->prepare('SELECT * FROM `log` ORDER BY date DESC LIMIT 100');
$showitqueryresult = $showitquery->execute(array());
$lbdata = $showitquery->fetchAll();

foreach($lbdata as $lbitem):
	$event = $lbitem['event'];
	$date = $lbitem['date'];
	
	echo "<tr><td>$date</td><td>$event</td></tr>";
endforeach;

echo "</table></div>";

require "includes/footer.php"; ?>