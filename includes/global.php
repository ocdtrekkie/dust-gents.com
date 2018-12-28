<?php 
/* 
* home.php 
* Description: example file for displaying latest posts and topics 
* by battye (for phpBB.com MOD Team) 
* September 29, 2009 
*/ 

define('IN_PHPBB', true); 
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './forums/'; 
$phpEx = substr(strrchr(__FILE__, '.'), 1); 
include($phpbb_root_path . 'common.' . $phpEx); 
include($phpbb_root_path . 'includes/bbcode.' . $phpEx); 
include($phpbb_root_path . 'includes/functions_display.' . $phpEx); 

// Start session management 
$user->session_begin(); 
$auth->acl($user->data); 
$user->setup('viewforum'); 

?>