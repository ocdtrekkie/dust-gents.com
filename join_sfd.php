<?php $page_title = "Special Forces Divisions";
require "includes/functions.php";
require "includes/global.php";
require "includes/header.php";
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);

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

if(($_GET['do']=='submit') && ($_GET['group'] >= 31) && ($_GET['group'] <= 36)) {
	if($_GET['action']=='join'){
	group_user_add($_GET['group'], $user->data['user_id'], false, false, false, false, true);
	echo "Application Pending.";
	}
}
else { echo "<p><b>Special Forces Divisions</b></p><br>

<p>We want to get everyone more involved, and more focused in making this game fun to play. Beyond the standard top-down leadership structure, we want people to find similar game interests and work together to accomplish them.</p><br>

<p>GoonSwarm uses a system they call SIGs, or Special Interest Groups. These groups range from in-game squads to out-of-game technical divisions. As imitation is the greatest form of flattery, we are going to use this idea. We will call them Special Forces Divisions, or SFDs. And they’ll be as simple to use as being forum groups.</p><br>

<p>Some will be completely open, some will require application, and some will surely be closed and invitation-only. New SFDs can be created as necessary with leadership approval. We want to encourage players to find a niche for themselves in the alliance... and then dominate it.</p><br>

<p><b>[SFD] Death From Above</b><br>
Leader: <a href=\"http://forums.dust-gents.com/memberlist.php?mode=viewprofile&u=129\">xyccoc</a><br>
Applications: Open<br>
Death From Above is our EVE players who will rain death from the sky upon battlegrounds. All players with EVE characters in our alliance should be members of DFA. DFA leadership will collaborate and manage all EVE players in our alliance, and ensure they comply with Gents standards of conduct and procedure.<br>
<a href=\"join_sfd.php?do=submit&action=join&group=32\">Apply to Join [SFD] Death From Above</a><br>
<a href=\"http://forums.dust-gents.com/memberlist.php?g=32&mode=group\">View the Roster</a></p><br>

<p><b>[SFD] Ground Forces Intelligence Directorate</b><br>
Leader: Position Open<br>
Applications: Will Be Reviewed<br>
The Ground Forces Intelligence Directorate shall be responsible for gathering and presenting information on the political status of ground forces across New Eden. Both overt and covert methods are encouraged, but should not encroach on diplomatic territory. Those accepted into the GFID will be trusted members of our intelligence community.<br>
<a href=\"join_sfd.php?do=submit&action=join&group=34\">Apply to Join [SFD] Ground Forces Intelligence Directorate</a><br>
<a href=\"http://forums.dust-gents.com/memberlist.php?g=34&mode=group\">View the Roster</a></p><br>

<p><b>[SFD] Automation Task Force</b><br>
Leader: <a href=\"http://forums.dust-gents.com/memberlist.php?mode=viewprofile&u=2\">Soraya Xel</a><br>
Applications: Will Be Reviewed<br>
The Automation Task Force is the web services team for our alliance. We will be creating, maintaining, and enhancing the dust-gents.com site and any other services our players require outside the game. ATF applicants should be programmers or administrators, with a particular preference of PHP/MySQL and/or EVE API expertise.<br>
<a href=\"join_sfd.php?do=submit&action=join&group=31\">Apply to Join [SFD] Automation Task Force</a><br>
<a href=\"http://forums.dust-gents.com/memberlist.php?g=31&mode=group\">View the Roster</a></p><br>

<p><b>[SFD] Heavy Motor Brigade</b><br>
Leader: <a href=\"http://forums.dust-gents.com/memberlist.php?mode=viewprofile&u=55\">Villanor Aquarius</a><br>
Applications: Open<br>
A group dedicated to HAV use ranging from the close range brawlers to the long range snipers and everything in between.<br>
<a href=\"join_sfd.php?do=submit&action=join&group=33\">Apply to Join [SFD] Heavy Motor Brigade</a><br>
<a href=\"http://forums.dust-gents.com/memberlist.php?g=33&mode=group\">View the Roster</a></p><br>

<p><b>[SFD] Inner Orbit Aeronautics Agency</b><br>
Leader: <a href=\"http://forums.dust-gents.com/memberlist.php?mode=viewprofile&u=81\">B689</a><br>
Applications: Open<br>
A group of aerial players on the DUST side, dedicated to discussing tactics varying from quick drops and pickups to sticking out the fight for aerial cover.<br>
<a href=\"join_sfd.php?do=submit&action=join&group=35\">Apply to Join [SFD] Inner Orbit Aeronautics Agency</a><br>
<a href=\"http://forums.dust-gents.com/memberlist.php?g=35&mode=group\">View the Roster</a></p><br>

<p><b>[SFD] Light Cavalry Tactics Division</b><br>
Leader: <a href=\"http://forums.dust-gents.com/memberlist.php?mode=viewprofile&u=89\">Gralin Cohlmack</a><br>
Applications: Open<br>
The LCTD is responsible for the development of tactics used to optimize the exploitation of LAVs on the battlefield. This includes the creation and implementation of strategies for coordinated squadron formations; fitting configurations; rapid response deployment and evacuation operations; and offensive and defensive support.<br>
<a href=\"join_sfd.php?do=submit&action=join&group=36\">Apply to Join [SFD] Light Cavalry Tactics Division</a><br>
<a href=\"http://forums.dust-gents.com/memberlist.php?g=36&mode=group\">View the Roster</a></p><br>
";}

echo "</div>";

require "includes/footer.php"; ?>