<?php

require('../includes/functions.php');

require_once('config.php');

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../forums/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include_once($phpbb_root_path . 'common.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_user.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_posting.' . $phpEx);

require "lib.php";

session_start();

// Start phpBB session
$user->session_begin();

$auth->acl($user->data);
$user->setup('ucp');

// Only registered users can go beyond this point
if (!$user->data['is_registered'])
{
  if ($user->data['is_bot'])
  {
    redirect(append_sid("{$phpbb_root_path}index.$phpEx"));
  }

  if ($id == 'pm' && $mode == 'view' && isset($_GET['p']))
  {
    $redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx?i=pm&p=" . request_var('p', 0));
    login_box($redirect_url, $user->lang['LOGIN_EXPLAIN_UCP']);
  }

  login_box('', $user->lang['LOGIN_EXPLAIN_UCP']);
}

// Now check if this user has verified CCP entry
$apiquery = getDb()->prepare('SELECT * FROM `members` WHERE `user_id` = ?');
if ($user->data['user_id'] != ANONYMOUS) {
  $apiqueryresult = $apiquery->execute(array($user->data['user_id']));
  $chardata = $apiquery->fetch(PDO::FETCH_ASSOC);

  if (!$chardata) {
    header('HTTP/1.0 403 Forbidden');
    die("Unauthorized");
  }
}
else {
  header('HTTP/1.0 403 Forbidden');
  die("Unauthorized");
}

if (!has_pcs_access($user->data['user_id'])) {
  header('HTTP/1.0 403 Forbidden');
  die("Unauthorized");
}

$can_submit_events = has_permission($user->data['user_id'], 'submit_events');
$can_delete_events = has_permission($user->data['user_id'], 'delete_events');
$can_edit_events = has_permission($user->data['user_id'], 'edit_events');
$can_submit_community_events = has_permission($user->data['user_id'], 'submit_community_events');
$can_edit_community_events = has_permission($user->data['user_id'], 'edit_community_events');
$can_delete_community_events = has_permission($user->data['user_id'], 'delete_community_events');

$view = new View();

if ($_SESSION['message']) {
  $view->message = $_SESSION['message'];
  unset($_SESSION['message']);
}

header('Content-Type: text/html; charset=utf-8');

// Simple router
$action = isset($_GET['a']) ? $_GET['a'] : 'index';
$template = 'main.tpl.php';
switch ($action) {
  case 'index':
    $view->user_name = $user->data['username'];
    $view->user_tz_offset = $user->timezone;
    $view->user_date_format = $user->data['user_dateformat'];
    $view->user_groups = load_user_groups($user->data['user_id']);
    $view->user_id = $user->data['user_id'];
    $view->can_submit_events = $can_submit_events;
    $view->can_submit_community_events = $can_submit_community_events;
    $view->can_edit_community_events = $can_edit_community_events;
    $view->can_delete_community_events = $can_delete_community_events;
    $view->can_delete_events = $can_delete_events;
    $view->can_edit_events = $can_edit_events;
    $view->events = load_upcoming_events();
    $view->community_events = load_upcoming_community_events();
    $view->tz_change_link = $cfg['tz_change_link'];
    $template = 'main.tpl.php';
    break;

  case 'add_event':
    if (!$can_submit_events) {
      header('HTTP/1.0 403 Forbidden');
      die("Unauthorized");
    }
    $view->district_names = get_all_districts_names();
    $template = 'add_event.tpl.php';
    break;

  case 'add_event_submit':
    if (!$can_submit_events) {
      header('HTTP/1.0 403 Forbidden');
      die("Unauthorized");
    }
    try {
      $event = new Event();
      $event->populateRaw($_POST, $user->data['user_id']);
    } catch (InvalidArgumentException $e) {
      $_SESSION['message'] = $e->getMessage();
      $_SESSION['add_event_form'] = $_POST;
      header('Location: index.php?a=add_event', true, 303);
      exit();
    }
    $event->save();

    // Automatically post topic
    $topic = new Topic($event, $user);
    $topic->post($cfg['forum_id']);

    // Event posted, saved data is no longer needed
    unset($_SESSION['add_event_form']);

    $_SESSION['message'] = 'Event added';
    header('Location: index.php', true, 303);
    exit();

  case 'edit_event':
    if (!isset($_GET) || !isset($_GET['event_id'])) {
      header('HTTP/1.0 404 Not Found');
      die('Page does not exits');
    }
    $event = new Event();
    $event->load((int) $_GET['event_id']);
    if (false === $event) {
      header('HTTP/1.0 404 Not Found');
      die('Page does not exist');
    }
    if ((!$can_edit_events) && ($user->data['user_id'] != $event->getCreatedBy())) {
      header('HTTP/1.0 403 Forbidden');
      die('Unauthorized');
    }
    $view->event = $event;
    $view->district_names = get_all_districts_names();
    $template = 'edit_event.tpl.php';
    break;

  case 'edit_event_submit':
    if (!isset($_POST) || !isset($_POST['event_id'])) {
      header('HTTP/1.0 404 Not Found');
      die('Page does not exist');
    }
    try {
      $event = new Event();
      $event->load($_POST['event_id']);
      if (false === $event) {
        header('HTTP/1.0 404 Not Found');
        die('Page does not exist');
      }
      if ((!$can_edit_events) && ($user->data['user_id'] != $event->getCreatedBy())) {
        header('HTTP/1.0 403 Forbidden');
        die('Unauthorized');
      }
      $event->populateRaw($_POST, $user->data['user_id']);
    } catch (InvalidArgumentException $e) {
      $_SESSION['message'] = $e->getMessage();
      $_SESSION['edit_event_form'] = $_POST;
      header('Location: index.php?a=edit_event&event_id=' . (int) $_POST['event_id'], true, 303);
      exit();
    }
    $event->save();

    // Event edited, form data is no longer needed
    unset($_SESSION['edit_event_form']);

    $_SESSION['message'] = 'Event updated';
    header('Location: index.php', true, 303);
    exit();

  case 'delete_event':
    if (isset($_POST) && isset($_POST['event_id']) && !empty($_POST['event_id'])) {
      $event = new Event();
      $event->load((int) $_POST['event_id']);

      if (!$can_delete_events && ($event->getCreatedBy() != $user->data['user_id'])) {
        header('Location: index.php', true, 403);
        exit();
      }
      $event->drop();
      $_SESSION['message'] = 'Event deleted';
    }
    header('Location: index.php', true, 303);
    exit();

  case 'add_community_event':
    if (!$can_submit_community_events) {
      header('HTTP/1.0 403 Forbidden');
      die("Unauthorized");
    }
    $template = 'add_community_event.tpl.php';
    break;

  case 'add_community_event_submit':
    if (!$can_submit_community_events) {
      header('HTTP/1.0 403 Forbidden');
      die("Unauthorized");
    }
    try {
      $event = new CommunityEvent();
      $event->populateRaw($_POST, $user->data['user_id']);
    } catch (InvalidArgumentException $e) {
      $_SESSION['message'] = $e->getMessage();
      $_SESSION['add_community_event_form'] = $_POST;
      header('Location: index.php?a=add_community_event', true, 303);
      exit();
    }
    $event->save();

    // Event posted, saved data is no longer needed
    unset($_SESSION['add_community_event_form']);

    $_SESSION['message'] = 'Community event added';
    header('Location: index.php', true, 303);
    exit();

  case 'edit_community_event':
    if (!isset($_GET) || !isset($_GET['event_id'])) {
      header('HTTP/1.0 404 Not Found');
      die('Page does not exits');
    }
    $event = new CommunityEvent();
    $event->load((int) $_GET['event_id']);
    if (false === $event) {
      header('HTTP/1.0 404 Not Found');
      die('Page does not exist');
    }
    if ((!$can_edit_community_events) && ($user->data['user_id'] != $event->getCreatedBy())) {
      header('HTTP/1.0 403 Forbidden');
      die('Unauthorized');
    }

    $view->event = $event;
    $template = 'edit_community_event.tpl.php';
    break;

  case 'edit_community_event_submit':
    if (!isset($_POST) || !isset($_POST['event_id'])) {
      header('HTTP/1.0 404 Not Found');
      die('Page does not exist');
    }
    try {
      $event = new CommunityEvent();
      $event->load($_POST['event_id']);
      if (false === $event) {
        header('HTTP/1.0 404 Not Found');
        die('Page does not exist');
      }
      if ((!$can_edit_community_events) && ($user->data['user_id'] != $event->getCreatedBy())) {
        header('HTTP/1.0 403 Forbidden');
        die('Unauthorized');
      }
      $event->populateRaw($_POST, $user->data['user_id']);
    } catch (InvalidArgumentException $e) {
      $_SESSION['message'] = $e->getMessage();
      $_SESSION['edit_community_event_form'] = $_POST;
      header('Location: index.php?a=edit_community_event&event_id=' . (int) $_POST['event_id'], true, 303);
      exit();
    }
    $event->save();

    // Event edited, form data is no longer needed
    unset($_SESSION['edit_community_event_form']);

    $_SESSION['message'] = 'Event updated';
    header('Location: index.php', true, 303);
    exit();
  case 'delete_community_event':
    if (isset($_POST) && isset($_POST['event_id']) && !empty($_POST['event_id'])) {
      $event = new CommunityEvent();
      $event->load((int) $_POST['event_id']);

      if (!$can_delete_community_events && ($event->getCreatedBy() != $user->data['user_id'])) {
        header('Location: index.php', true, 403);
        exit();
      }
      $event->drop();
      $_SESSION['message'] = 'Community event deleted';
    }
    header('Location: index.php', true, 303);
    exit();
  default:
    header('HTTP/1.0 404 Not Found');
    die('Page does not exist');
    break;
}

$page_title = "Planetary Conquest Tracker";
if (!is_mobile()) {
  include '../includes/header.php';
  print $view->render('templates/' . $template);
  include '../includes/footer.php';
}
else {
  include 'templates/mobile_header.tpl.php';
  print $view->render('templates/' . $template, TRUE);
  include 'templates/mobile_footer.tpl.php';
}