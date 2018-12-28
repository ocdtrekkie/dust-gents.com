<?php $page_title = "Home";
require "includes/functions.php";
require "includes/global.php";
require "includes/header.php";

$listquery = getDb()->prepare('SELECT * FROM `corporations` WHERE `standings` = 15 ORDER BY `active_count` DESC');
$ceoquery = getDb()->prepare('SELECT * FROM `members` WHERE `characterID` = ?');
// $memquery = getDb()->prepare('SELECT * FROM `corporations` WHERE `corporationID` = ?');
$apiquery = getDb()->prepare('SELECT * FROM `members` WHERE `user_id` = ?');

$listresult = $listquery->execute();
$list = $listquery->fetchAll(PDO::FETCH_ASSOC);

echo "<div class=\"clear\"></div>

<div style=\"float: left; padding: 20px;\">
<table class=\"tablebg\" cellspacing=\"1\">";

foreach ($list as $corp):
	$ceoresult = $ceoquery->execute(array($corp['ceoID']));
	$ceo = $ceoquery->fetch(PDO::FETCH_ASSOC);

	echo "<tr><td class=\"row1\" align=\"center\"><img src=\"http://image.eveonline.com/Corporation/" . $corp['corporationID'] . "_128.png\" width=\"50\"></td><td class=\"row1\" align=\"left\"><a href=\"http://www.dust-gents.com/view_corp.php?corporationID=" . $corp['corporationID'] . "\">" . $corp['corporationName'] . "</a><br><span class=\"gensmall\"><strong>CEO:</strong> " . $ceo['characterName'] . "<br><strong>Members:</strong> " . $corp['member_count'] . "</span></td></tr>";
endforeach;

echo "</table></div>

<div class=\"postbody\"><br /><p align=\"center\">";

// API Verification Check

if ($user->data['user_id'] != ANONYMOUS)
{
	$apiqueryresult = $apiquery->execute(array($user->data['user_id']));
	$chardata = $apiquery->fetch(PDO::FETCH_ASSOC);
	
	if ($chardata)
	{
	echo "<table class=\"tablebg\" with=\"100%\"><tr><td class=\"catdiv\" width=\"575\"><span class=\"gensmall\"><b>API Verified:</b> " . $chardata['characterName'] . " (" . $chardata['characterID'] . ") - " . $chardata['role_1'] . "/" . $chardata['role_2'] . "</span></td></tr>
	<tr><td class=\"row1\"><span class=\"gensmall\"><a href=\"pcs/index.php\">Planetary Conquest Tracker</a><br><a href=\"districts.php\">District Info</a><br><a href=\"charter/index.php\">Alliance Charter</a><br><a href=\"join_sfd.php\">Join a Special Forces Division</a><br> <br><a href=\"submit_stats.php\">Update Stats</a><br><a href=\"submit_roles.php\">Update Roles</a><br> <br><a href=\"show_lb.php\">View Leaderboard</a><br><a href=\"show_lb_personal.php\">View Personal Stats</a><br> <br><a href=\"upload.php\">File Upload Tool</a></span></td></tr>
	</table><br />";
	}
	else
	{
	echo "<table class=\"tablebg\" with=\"100%\"><tr><td class=\"catdiv\" width=\"575\"><span class=\"gensmall\"><b>Unverified User</b></span></td></tr>
	<tr><td class=\"row1\"><span class=\"gensmall\"><a href=\"districts.php\">District Info</a><br><a href=\"charter/index.php\">Alliance Charter</a><br> <br><font color=\"red\">You must mail <b>Automation Task Force</b> in-game with the subject line <b>Refresh</b> to verify that you registered on the DUST Gents forums to gain access to non-public resources.</font></span></td></tr>
	</table><br />";
	}
}
else {
	echo "<table class=\"tablebg\" with=\"100%\"><tr><td class=\"catdiv\" width=\"575\"><span class=\"gensmall\"><b>Guest User</b></span></td></tr>
	<tr><td class=\"row1\"><span class=\"gensmall\"><a href=\"districts.php\">District Info</a><br><a href=\"charter/index.php\">Alliance Charter</a><br> <br><font color=\"red\">You must register on the forums and then verify your registration in order to gain access to non-public resources.</font></span></td></tr>
	</table><br />";
}
?>

<strong>The DUST arm of Gentlemen's Agreement</strong></p>
<br /><p>It is often said that a gentleman has only the best of intentions. He wants to deal with his fellows honorably and fairly; he wants to ensure their opinions are heard, that they feel their input has value and that their needs are respected.</p>
<br /><p>It is also often said that gentlemen just want to have fun. And to do that, there's a whole lot of very honorable institutions that just get in the way. They're only fun when all the participants would be gettling along fine without them.</p>
<br /><p>We don't want to get bogged down in votes. We don't want to sit in endless council meetings. Filibustering is for people who get paid real money for this crap.</p>
<br /><p>All we really want to do is give all manner of Huns, barbarians, idiots, sycophants, whiners, and people who went to the wrong school a proper good thrashing, then nip back round to the club for a strong drink and a big dinner.</p>
<br /><p>And we're going to start by cheerfully bludgeoning those ghastly rubber-nosed clowns until the pie custard leaks out of their pants, then put our feet up on all that fancy Baroque furniture and tap cigar ash on the armrests.</p>
<br /><p>So certain gentlemen have come to an Agreement: We shall strike off in search of glory, honor, and a truly dapper tophat. We shall seek fun and entertainment. We are, in short, doing it for - <i>Jeeves, what was it the young people are calling it these days? Lols? Ah, yes.</i> The lulz. But we shall do it with a certain sense of style, a certain <i>je ne sais quois</i>, that makes us gentlemen as well as jesters.</p>
<br /><p>Just remember that all is fair in love and war.</p>
<br /><p>"Don't be terrible, be awesome. Don't be serious, have fun. Don't be afraid, we can do this."</p><br /></div>

<?php require "includes/footer.php"; ?>