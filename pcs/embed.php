<?php

require('../includes/functions.php');

require_once('../pcs/config.php');

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include_once($phpbb_root_path . 'common.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_user.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_posting.' . $phpEx);

require('lib.php');

function pcs_embedded_events_list() {
  // These are from forums
  global $user;
  global $auth;

  // These are from lib.php
  global $phpbb_root_path;
  global $phpEx;

  global $cfg;

  $auth->acl($user->data);
  $user->setup('ucp');

  // Only registered users can go beyond this point
  if (!$user->data['is_registered'])
  {
    if ($user->data['is_bot'])
    {
      redirect(append_sid("{$phpbb_root_path}index.$phpEx"));
    }

    login_box('', $user->lang['LOGIN_EXPLAIN_UCP']);
  }

  // Now check if this user has verified CCP entry
  $apiquery = getDb()->prepare('SELECT * FROM `members` WHERE `user_id` = ?');
  if ($user->data['user_id'] != ANONYMOUS) {
    $apiqueryresult = $apiquery->execute(array($user->data['user_id']));
    $chardata = $apiquery->fetch(PDO::FETCH_ASSOC);

    if (!$chardata) {
      return '';
    }
  }
  else {
    return '';
  }

  $upcoming_events = load_upcoming_events(false, 3 * 86400);
  $community_events = load_current_community_events();
  if (empty($upcoming_events) && empty($community_events)) {
    return '';
  }

  $view = new View();

  $view->user_name = $user->data['username'];
  $view->user_tz_offset = $user->timezone;
  $view->user_date_format = $user->data['user_dateformat'];
  $view->user_groups = load_user_groups($user->data['user_id']);
  $view->user_id = $user->data['user_id'];
  $view->events = $upcoming_events;
  $view->community_events = $community_events;
  $view->tz_change_link = $cfg['tz_change_link'];
  $view->pcs_url = $cfg['pcs_url'];
  $template = 'embed.tpl.php';

  return $view->render('templates/' . $template);
}