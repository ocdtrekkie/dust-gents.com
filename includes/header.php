<html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-gb" xml:lang="en-gb">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="content-language" content="en-gb" />
<meta http-equiv="content-style-type" content="text/css" />
<title><?php echo $page_title; ?> | Top Men | Gentlemen and Goons</title>
<link rel="stylesheet" href="http://forums.dust-gents.com/style.php?id=2&amp;lang=en" type="text/css" />
</head>
<body class="ltr">
<div id="container">

<div id="topbarwrapper">
<div id="topbar">
<div class="topbarcenter">
<div class="fl">
<?php 
if ($user->data['user_id'] != ANONYMOUS)
{
	echo "<a href=\"http://forums.dust-gents.com/ucp.php?mode=logout&sid=" . $user->data['session_id'] . "\">Logout [ " . $user->data['username'] . " ]</a> | &nbsp;<a href=\"http://forums.dust-gents.com/ucp.php?i=pm&folder=inbox\"><strong>" . $user->data['user_new_privmsg'] . "</strong> new messages</a> | &nbsp;<a href=\"http://forums.dust-gents.com/ucp.php\">User Control Panel</a>";
}
if ($user->data['user_id'] == ANONYMOUS)
{
	echo "<a href=\"http://forums.dust-gents.com/ucp.php?mode=login\">Login</a> | &nbsp;<strong><a href=\"http://forums.dust-gents.com/ucp.php?mode=register\">Register</a></strong>";
}
?>
 | &nbsp;<a href=\"http://forums.dust-gents.com/faq.php\">FAQ</a>
<?php
if ($user->data['user_id'] != ANONYMOUS)
{
	echo " | &nbsp;<a href=\"http://forums.dust-gents.com/mchat.php#mChat\" title=\"Mini-Chat\">Mini-Chat</a>";
}
if ($user->data['user_id'] == 2)
{
	echo " | &nbsp;<a href=\"manager.php\" title=\"API Manager\">API Manager</a>";
}
?>
</div>
<div class="clear"></div>
</div>
</div>
<div id="breadc"><a href="index.php">Site index</a></div>
</div>

<div id="wrapper">
<div id="navigation"><ul id="nav">
<li><a href="http://www.dust-gents.com/index.php" class="navhover nav_home"><span>Home</span></a></li>
<li><a href="http://forums.dust-gents.com/index.php" class="navhover nav_forums"><span>Forums</span></a></li>
<?php if ($user->data['user_id'] != ANONYMOUS)
{
    echo "
	<li><a href=\"http://forums.dust-gents.com/memberlist.php\" class=\"navhover nav_members\"><span>Members</span></a></li>
	";
} ?>
<li><a href="http://forums.dust-gents.com/search.php" class="navhover nav_search"><span>Search</span></a></li>
<?php if ($user->data['user_id'] == ANONYMOUS)
{
    echo "
	<li><a href=\"http://forums.dust-gents.com/ucp.php?mode=register\" class=\"navhover nav_register\"><span>Register</span></a></li>
	";
} ?>
<li><a href="http://www.eve-gents.com" class="navhover nav_evegents"><span>EVE Gents</span></a></li>
</ul></div>

<div id="contentwrapper">