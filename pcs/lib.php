<?php

/**
 * Simple view renderer.
 */
class View {
  /**
   * Escape string for browser output.
   *
   * @param string $text
   * @return string
   */
  protected function escape($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
  }

  public function render($tpl, $is_mobile = FALSE) {
    ob_start();
    // Add global styles
    print '<link rel="stylesheet" href="style.css" type="text/css" />';

    if ($is_mobile) {
      print '<link rel="stylesheet" href="style_mobile.css" type="text/css" />';
    }
    include $tpl;
    $content = ob_get_clean();

    return $content;
  }
}

/**
 * Simple widget renderer.
 */
class Widget extends View {

  public function render($tpl) {
    ob_start();
    include $tpl;
    $content = ob_get_clean();

    return $content;
  }
}

class Topic {
  protected $user = null;
  protected $event = null;
  protected $is_posted = false;

  public function __construct(Event $ev, $user) {
    $this->event = $ev;
    $this->user = $user;
  }

  protected function populateTemplate($template) {

    $friendly_corp = $this->event->getFriendlyCorp();
    $friendly_corp = empty($friendly_corp) ? '?' : $friendly_corp;
    $enemy_corp = $this->event->getEnemyCorp();
    $enemy_corp = empty($enemy_corp) ? '?' : $enemy_corp;
    $backup_platoon_leaders = $this->event->getBackupPlatoonLeaders();
    $backup_platoon_leaders = empty($backup_platoon_leaders) ? '-' : $backup_platoon_leaders;

    $text = str_replace(
      array('[event_type]', '[friendly_corp]', '[enemy_corp]', '[platoon_leader]', '[backup_platoon_leaders]', '[eve_time]', '[district_location]'),
      array(
        $this->event->getReadableType(),
        $friendly_corp,
        $enemy_corp,
        $this->event->getPlatoonLeader(),
        $backup_platoon_leaders,
        $this->event->getEveDate()->getDate(),
        $this->event->getDistrictLocation(),
      ),
      $template
    );

    return $text;
  }

  public function post($forum_id) {
    if ($this->is_posted) {
      return false;
    }

    switch ($this->event->getType()) {
      case Event::TYPE_PC_ATTACK:
      case Event::TYPE_PC_DEFENSE:
        $subject = '[event_type] @ [eve_time] GMT - [enemy_corp]';
        $text = '
          [event_type]
          [friendly_corp] vs [enemy_corp] @ [eve_time] GMT
          Alliance Corp: [friendly_corp]
          Enemy Corp: [enemy_corp]
          District: [district_location]
          Platoon Leader: [platoon_leader]
          Backup Platoon Leaders: [backup_platoon_leaders]
        ';
      break;
      case Event::TYPE_OTHER:
      case Event::TYPE_FW_BATTLE:
        $subject = '[event_type] @ [eve_time] GMT - [district_location]';
        $text = '
          [event_type] @ [eve_time] GMT
          Description: [district_location]
          Platoon Leader: [platoon_leader]
        ';
      break;
      default:
        $subject = '[event_type] @ [eve_time] GMT - [friendly_corp] vs [enemy_corp]';
        $text = '
          [event_type]
          [friendly_corp] vs [enemy_corp] @ [eve_time] GMT
          Alliance Corp: [friendly_corp]
          Enemy Corp: [enemy_corp]
          District: [district_location]
          Platoon Leader: [platoon_leader]
        ';
        break;
    }

    $subject = utf8_normalize_nfc($this->populateTemplate($subject));
    $text = utf8_normalize_nfc($this->populateTemplate($text));

    $uid = $this->user->data['user_id'];

    $poll = $bitfield = $options = '';

    generate_text_for_storage($subject, $uid, $bitfield, $options, false, false, false);
    generate_text_for_storage($text, $uid, $bitfield, $options, true, true, true);

    $data = array(
      'forum_id' => $forum_id,
      'icon_id' => false,

      'enable_bbcode' => true,
      'enable_smilies' => true,
      'enable_urls' => true,
      'enable_sig' => true,

      'message' => $text,
      'message_md5' => md5($text),

      'bbcode_bitfield' => $bitfield,
      'bbcode_uid' => $uid,

      'post_edit_locked' => 0,
      'topic_title' => $subject,
      'notify_set' => false,
      'notify' => false,
      'post_time' => 0,
      'forum_name' => '',
      'enable_indexing' => true,
    );

    submit_post('post', $subject, '', POST_NORMAL, $poll, $data);

    $topic_id = $data['topic_id'];
    $this->event->setForumThread($topic_id);

    $this->is_posted = TRUE;
    return TRUE;
  }
}

class EveDate {
  protected $date;
  protected $ts;

  public function __construct($ts) {
    $this->ts = (int) $ts;
    $this->date = $this->convertTimestamp2Eve($this->ts);
  }

  /**
   * Convert timestamp to Date object in Eve TZ.
   *
   * @return DateTime
   */
  protected function convertTimestamp2Eve($ts) {
    $eve_date = new DateTime();
    $eve_date->setTimestamp($ts);
    $eve_date->setTimezone(new DateTimeZone('Etc/GMT'));

    return $eve_date;
  }

  /**
   * @return int
   */
  public function getTs() {
    return $this->ts;
  }

  public function getDate() {
    return $this->date->format('Y M d, Hi');
  }

  public function getMonth() {
    return (int) $this->date->format('m');
  }

  public function getDay() {
    return (int) $this->date->format('d');
  }

  public function getYear() {
    return (int) $this->date->format('Y');
  }

  public function getHours() {
    return (int) $this->date->format('H');
  }

  public function getMinutes() {
    return (int) $this->date->format('i');
  }

  public function getLocalDate($timezone, $format) {
    $local_date = new DateTime();
    $local_date->setTimestamp($this->ts);
    $local_date->setTimezone(new DateTimeZone($timezone));

    return $local_date->format($format);
  }

  /**
   * Represent a date as string interval from now, e.g. 3 days ago.
   *
   * @return string
   */
  public function getIntervalFromNow() {
    $interval = $this->date->diff(new \DateTime('now'));
    if (!$interval->invert) {
      $format_string = '%h hours ago';
    }
    else {
      $format_string = '%h hours left';
    }
    if ($interval->d) {
      $format_string = '%d days, ' . $format_string;
    }
    if ($interval->m) {
      $format_string = '%m months, ' . $format_string;
    }
    if ($interval->y) {
      $format_string = '%y years, ' . $format_string;
    }
    return $interval->format($format_string);
  }
}

abstract class BasicEvent {

  protected function processSubmittedDate($data, $day_key, $month_key, $year_key, $hours_key, $minutes_key, $date_msg_prefix) {
    if (!isset($data[$day_key]) || empty($data[$day_key])) {
      throw new InvalidArgumentException('Missing ' . $date_msg_prefix . ' day');
    }
    if (!isset($data[$month_key]) || empty($data[$month_key])) {
      throw new InvalidArgumentException('Missing ' . $date_msg_prefix . ' month');
    }
    if (!isset($data[$year_key]) || empty($data[$year_key])) {
      throw new InvalidArgumentException('Missing ' . $date_msg_prefix . ' year');
    }
    if (!checkdate($data[$month_key], $data[$day_key], $data[$year_key])) {
      throw new InvalidArgumentException('Invalid ' . $date_msg_prefix . ' date');
    }
    if (!isset($data[$hours_key]) || ($data[$hours_key] == '')) {
      throw new InvalidArgumentException('Missing ' . $date_msg_prefix . ' hours');
    }
    if (!isset($data[$minutes_key]) || ($data[$minutes_key] == '')) {
      throw new InvalidArgumentException('Missing ' . $date_msg_prefix . ' minutes');
    }
    $str_date = $data[$day_key] . '/' . $data[$month_key] . '/' . $data[$year_key];
    $str_date .= ' ' . sprintf('%02d', $data[$hours_key]) . ':' . sprintf('%02d', $data[$minutes_key]) . ' GMT';
    $date = DateTime::createFromFormat('d/m/Y H:i e', $str_date);
    if (!($date instanceof DateTime)) {
      throw new InvalidArgumentException('Invalid date');
    }

    $timestamp = $date->getTimestamp();
    if (!$timestamp) {
      throw new InvalidArgumentException('Invalid date');
    }

    return $timestamp;
  }
}

class Event extends BasicEvent {

  const TYPE_PC_ATTACK = 1;
  const TYPE_PC_DEFENSE = 2;
  const TYPE_FW_BATTLE = 3;
  const TYPE_OTHER = 4;

  protected $event_id = null;
  protected $district_location;
  protected $enemy_corp;
  protected $friendly_corp;
  protected $platoon_leader;
  protected $backup_platoon_leaders;
  protected $type;
  protected $created_by;
  protected $ts;
  protected $duration;
  protected $forum_thread;

  public function __construct() {}

  public function populateRaw($raw_data, $created_by) {

    // Validate district location
    if (!isset($raw_data['district_location']) || empty($raw_data['district_location'])) {
      throw new InvalidArgumentException('Missing district location');
    }
    if (strlen($raw_data['district_location']) > 200) {
      throw new InvalidArgumentException('District location is longer than 200 chars');
    }
    $this->district_location = $raw_data['district_location'];

    // Validate platoon leader
    if (!isset($raw_data['platoon_leader']) || empty($raw_data['platoon_leader'])) {
      throw new InvalidArgumentException('Missing platoon leader');
    }
    if (strlen($raw_data['platoon_leader']) > 200) {
      throw new InvalidArgumentException('Platoon leader name is longer than 200 chars');
    }
    $this->platoon_leader = $raw_data['platoon_leader'];

    // Validate backup platoon leaders
    if (isset($raw_data['backup_platoon_leaders'])) {
      if (strlen($raw_data['backup_platoon_leaders']) > 600) {
        throw new InvalidArgumentException('Backup platoon leaders list is longer than 600 chars');
      }
      $this->backup_platoon_leaders = $raw_data['backup_platoon_leaders'];
    }

    // Validate corp
    if (!empty($raw_data['enemy_corp']) && (strlen($raw_data['enemy_corp']) > 200)) {
      throw new InvalidArgumentException('Enemy corporation name is longer than 200 chars');
    }
    $this->enemy_corp = $raw_data['enemy_corp'];
    if (!empty($raw_data['friendly_corp']) && (strlen($raw_data['friendly_corp']) > 200)) {
      throw new InvalidArgumentException('Enemy corporation name is longer than 200 chars');
    }
    $this->friendly_corp = $raw_data['friendly_corp'];

    // Validate event type
    if (!isset($raw_data['event_type']) || empty($raw_data['event_type'])) {
      throw new InvalidArgumentException('Missing event_type');
    }
    if (!in_array((int)$raw_data['event_type'], array(1, 2, 3, 4))) {
      throw new InvalidArgumentException('Unknown event type: ' . $raw_data['event_type']);
    }
    $this->type = (int)$raw_data['event_type'];

    // Validate event date
    $this->ts = $this->processSubmittedDate($raw_data, 'date_day', 'date_month', 'date_year', 'time_hours', 'time_minutes', '');

    // Validate event duration
    if (isset($raw_data['duration'])) {
      $duration = (int) $raw_data['duration'];
      if (0 == $duration) {
        throw new InvalidArgumentException('Invalid duration');
      }
      $this->duration = $raw_data['duration'];
    }
    else {
      // Default event duration is 30 mins
      $this->duration = 1800;
    }

    $this->created_by = (int) $created_by;
  }

  public function save() {
    if (!isset($this->event_id)) {
      $q = getDb()->prepare('
        INSERT INTO `upcoming_events` (`district_location`, `enemy_corp`, `friendly_corp`, `platoon_leader`, `backup_platoon_leaders`, `ts`, `duration`, `type`, `created_by`)
        VALUES (:district_location, :enemy_corp, :friendly_corp, :platoon_leader, :backup_platoon_leaders, :ts, :duration, :type, :created_by)
      ');
    }
    else {
      $q = getDb()->prepare('
        UPDATE `upcoming_events` SET
          `district_location` = :district_location,
          `enemy_corp` = :enemy_corp,
          `friendly_corp` = :friendly_corp,
          `platoon_leader` = :platoon_leader,
          `backup_platoon_leaders` = :backup_platoon_leaders,
          `ts` = :ts,
          `duration` = :duration,
          `type` = :type,
          `created_by` = :created_by
          WHERE eid = :event_id LIMIT 1
      ');
      $q->bindParam(':event_id', $this->event_id, PDO::PARAM_INT);
    }

    $q->bindParam(':district_location', $this->district_location, PDO::PARAM_STR);
    $q->bindParam(':enemy_corp', $this->enemy_corp, PDO::PARAM_STR);
    $q->bindParam(':friendly_corp', $this->friendly_corp, PDO::PARAM_STR);
    $q->bindParam(':platoon_leader', $this->platoon_leader, PDO::PARAM_STR);
    $q->bindParam(':backup_platoon_leaders', $this->backup_platoon_leaders, PDO::PARAM_STR);
    $q->bindParam(':ts', $this->ts, PDO::PARAM_INT);
    $q->bindParam(':duration', $this->duration, PDO::PARAM_INT);
    $q->bindParam(':type', $this->type, PDO::PARAM_INT);
    $q->bindParam(':created_by', $this->created_by, PDO::PARAM_INT);

    $res = $q->execute();

    $this->event_id = getDb()->lastInsertId();

    return $res;
  }

  public function drop() {
    $q = getDb()->prepare('
      DELETE FROM `upcoming_events` WHERE eid = :event_id LIMIT 1
    ');
    $q->bindParam(':event_id', $this->event_id, PDO::PARAM_INT);
    return $q->execute();
  }

  public function setForumThread($thread_id) {
    $q = getDb()->prepare('
      UPDATE `upcoming_events` SET `forum_thread` = :thread_id WHERE `eid` = :eid
    ');
    $q->bindParam(':thread_id', $thread_id, PDO::PARAM_INT);
    $q->bindParam(':eid', $this->event_id, PDO::PARAM_INT);
    return $q->execute();
  }

  /**
   * Loads event.
   *
   * @param int $event_id
   */
  public function load($event_id) {
    $q = getDb()->prepare('
      SELECT * FROM `upcoming_events` WHERE eid = :event_id LIMIT 1
    ');
    $q->bindParam(':event_id', $event_id, PDO::PARAM_INT);
    $q->execute();

    $event_row = $q->fetch(PDO::FETCH_ASSOC);

    if (!$event_row) {
      return false;
    }

    $this->populateRow($event_row);
  }

  public function populateRow($event_row) {
    $this->event_id = (int) $event_row['eid'];
    $this->district_location = $event_row['district_location'];
    $this->enemy_corp = $event_row['enemy_corp'];
    $this->friendly_corp = $event_row['friendly_corp'];
    $this->platoon_leader = $event_row['platoon_leader'];
    $this->backup_platoon_leaders = $event_row['backup_platoon_leaders'];
    $this->description = $event_row['description'];
    $this->ts = (int) $event_row['ts'];
    $this->duration = (int) $event_row['duration'];
    $this->type = (int) $event_row['type'];
    $this->created_by = (int) $event_row['created_by'];
    $this->forum_thread = (int) $event_row['forum_thread'];
  }

  public function getReadableType() {
    switch ($this->type) {
      case self::TYPE_PC_ATTACK:
        return 'PC Attack';
      case self::TYPE_PC_DEFENSE:
        return 'PC Defense';
      case self::TYPE_FW_BATTLE:
        return 'FW Battle';
      case self::TYPE_OTHER:
        return 'Other';
    }
  }

  public function getEveDate() {
    $eve_date = new EveDate($this->getTs());
    return $eve_date;
  }

  public function getEveMonth() {
    $eve_date = new EveDate($this->getTs());
    return $eve_date->getMonth();
  }

  public function getEveDay() {
    $eve_date = new EveDate($this->getTs());
    return $eve_date->getDay();
  }

  public function getEveYear() {
    $eve_date = new EveDate($this->getTs());
    return $eve_date->getYear();
  }

  public function getEveHours() {
    $eve_date = new EveDate($this->getTs());
    return $eve_date->getHours();
  }

  public function getEveMinutes() {
    $eve_date = new EveDate($this->getTs());
    return $eve_date->getMinutes();
  }

  public function getLocalDate($timezone, $format) {
    $eve_date = new EveDate($this->getTs());
    return $eve_date->getLocalDate($timezone, $format);
  }

  /**
   * @return int
   */
  public function getEventId()
  {
    return $this->event_id;
  }

  public function getCreatedBy() {
    return $this->created_by;
  }

  /**
   * @return int
   */
  public function getType() {
    return $this->type;
  }

  /**
   * @return string
   */
  public function getDistrictLocation()
  {
    return $this->district_location;
  }

  /**
   * @return string
   */
  public function getEnemyCorp()
  {
    return $this->enemy_corp;
  }

  /**
   * @return string
   */
  public function getFriendlyCorp()
  {
    return $this->friendly_corp;
  }

  /**
   * @return int
   */
  public function getTs()
  {
    return $this->ts;
  }

  /**
   * @return int
   */
  public function getDuration() {
    return $this->duration;
  }

  /**
   * @return int
   */
  public function getTsEnd() {
    $duration = isset($this->duration) ? (int) $this->duration : 0;

    return ($this->ts + $duration);
  }

  /**
   * @return string
   */
  public function getPlatoonLeader()
  {
    return $this->platoon_leader;
  }

  public function getBackupPlatoonLeaders() {
    return isset($this->backup_platoon_leaders) && !empty($this->backup_platoon_leaders) ? $this->backup_platoon_leaders : '';
  }

  /**
   * @return int
   */
  public function getForumThread()
  {
    return $this->forum_thread;
  }
}

class CommunityEvent extends BasicEvent {

  protected $community_event_id = null;
  protected $title;
  protected $link;
  protected $startdate;
  protected $enddate;
  protected $created_by;

  public function __construct() {}

  public function populateRaw($raw_data, $created_by) {

    // Validate title
    if (!isset($raw_data['title']) || empty($raw_data['title'])) {
      throw new InvalidArgumentException('Missing event title');
    }
    if (strlen($raw_data['title']) > 1000) {
      throw new InvalidArgumentException('Event title is longer than 1000 chars');
    }
    $this->title = $raw_data['title'];

    // Validate link
    if (!isset($raw_data['link']) || empty($raw_data['link'])) {
      throw new InvalidArgumentException('Missing event link');
    }
    if (strlen($raw_data['link']) > 1000) {
      throw new InvalidArgumentException('Link is longer than 1000 chars');
    }
    $this->link = $raw_data['link'];

    // Validate event startdate
    $this->startdate = $this->processSubmittedDate($raw_data, 'start_date_day', 'start_date_month', 'start_date_year', 'start_time_hours', 'start_time_minutes', 'start');

    // Validate event enddate
    $this->enddate = $this->processSubmittedDate($raw_data, 'end_date_day', 'end_date_month', 'end_date_year', 'end_time_hours', 'end_time_minutes', 'end');

    if ($this->enddate < $this->startdate) {
      throw new InvalidArgumentException('End date is earlier than start date');
    }

    $this->created_by = (int) $created_by;
  }

  public function save() {
    if (!isset($this->community_event_id)) {
      $q = getDb()->prepare('
        INSERT INTO `community_events` (`title`, `link`, `startdate`, `enddate`, `created_by`)
        VALUES (:title, :link, :startdate, :enddate, :created_by)
      ');
    }
    else {
      $q = getDb()->prepare('
        UPDATE `community_events` SET
          `title` = :title,
          `link` = :link,
          `startdate` = :startdate,
          `enddate` = :enddate,
          `created_by` = :created_by
          WHERE ceid = :community_event_id LIMIT 1
      ');
      $q->bindParam(':community_event_id', $this->community_event_id, PDO::PARAM_INT);
    }

    $q->bindParam(':title', $this->title, PDO::PARAM_STR);
    $q->bindParam(':link', $this->link, PDO::PARAM_STR);
    $q->bindParam(':startdate', $this->startdate, PDO::PARAM_INT);
    $q->bindParam(':enddate', $this->enddate, PDO::PARAM_INT);
    $q->bindParam(':created_by', $this->created_by, PDO::PARAM_INT);

    $res = $q->execute();

    $this->community_event_id = getDb()->lastInsertId();

    return $res;
  }

  public function drop() {
    $q = getDb()->prepare('
      DELETE FROM `community_events` WHERE ceid = :community_event_id LIMIT 1
    ');
    $q->bindParam(':community_event_id', $this->community_event_id, PDO::PARAM_INT);
    return $q->execute();
  }

  /**
   * Loads event.
   *
   * @param int $community_event_id
   */
  public function load($community_event_id) {
    $q = getDb()->prepare('
      SELECT * FROM `community_events` WHERE ceid = :community_event_id LIMIT 1
    ');
    $q->bindParam(':community_event_id', $community_event_id, PDO::PARAM_INT);
    $q->execute();

    $event_row = $q->fetch(PDO::FETCH_ASSOC);

    if (!$event_row) {
      return false;
    }

    $this->populateRow($event_row);
  }

  public function populateRow($event_row) {
    $this->community_event_id = (int) $event_row['ceid'];
    $this->title = $event_row['title'];
    $this->link = $event_row['link'];
    $this->startdate = (int) $event_row['startdate'];
    $this->enddate = (int) $event_row['enddate'];
    $this->created_by = (int) $event_row['created_by'];
  }

  /**
   * @return int
   */
  public function getEventId()
  {
    return $this->community_event_id;
  }

  public function getCreatedBy() {
    return $this->created_by;
  }

  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }

  /**
   * @return string
   */
  public function getLink()
  {
    return $this->link;
  }


  /**
   * @return int
   */
  public function getStartDate()
  {
    return $this->startdate;
  }

  /**
   * @return int
   */
  public function getEndDate() {
    return $this->enddate;
  }

  /**
   * @return EveDate
   */
  public function getStartEveDate() {
    return new EveDate($this->startdate);
  }

  /**
   * @return EveDate
   */
  public function getEndEveDate() {
    return new EveDate($this->enddate);
  }
}

class DistrictsFactory {

  /**
   * @return array
   */
  static public function getAllDistricts() {
    $q = getDb()->prepare('
      SELECT * FROM `districts`
    ');
    $q->execute();

    $districts = array();

    while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
      $district = new District();
      $district->populateWithDbRow($row);
      $districts[] = $district;
    }

    return $districts;
  }
}

class District {
  protected $id;
  protected $name;
  protected $owner_id;
  protected $owner_name;
  protected $reinforce;
  protected $attacked;
  protected $locked;
  protected $generating;
  protected $clones;
  protected $clone_rate;
  protected $infrastructure;

  /**
   * @return string
   */
  public function getOwnerName()
  {
    return $this->owner_name;
  }

  /**
   * @return int
   */
  public function getOwnerId()
  {
    return (int) $this->owner_id;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @return int
   */
  public function getInfrastructure()
  {
    return (int) $this->infrastructure;
  }

  /**
   * @return int
   */
  public function getId()
  {
    return (int) $this->id;
  }

  public function populateWithDbRow($row) {
    $props = array('id', 'name', 'owner_id', 'reinforce', 'attacked', 'locked', 'generating', 'clones', 'clone_rate', 'infrastructure');
    foreach ($props as $prop) {
      $this->{$prop} = $row[$prop];
    }
  }
}

class DateWidget {

  protected $date_label = 'Date';
  protected $time_label = 'Time';
  protected $prefix = '';

  public function setDateLabel($label) {
    $this->date_label = $label;
  }

  public function setTimeLabel($label) {
    $this->time_label = $label;
  }

  public function setPrefix($prefix) {
    $this->prefix = $prefix . '_';
  }

  /**
   * @param DefaultFormValues $defaults Submitted form values.
   * @param EveDate $date Initial date, optional. Will default to current date if omitted.
   *
   * @return string
   */
  public function render(DefaultFormValues $defaults, EveDate $date = null) {
    $widget = new Widget();

    $widget->defaults = $defaults;

    $widget->prefix = $this->prefix;

    $current_eve_date = new DateTime();
    $current_eve_date->setTimestamp(time());
    $current_eve_date->setTimezone(new DateTimeZone('Etc/GMT'));
    $widget->current_eve_date = $current_eve_date;

    $current_month = date('n');
    $selected_month = $defaults->getProp($this->prefix . 'date_month', ($date ? $date->getMonth() : $current_month));
    $widget->selected_month = $selected_month;

    $current_day = date('j');
    $selected_day = $defaults->getProp($this->prefix . 'date_day', ($date ? $date->getDay() : $current_day));
    $widget->selected_day = $selected_day;

    $current_year = date('Y');
    $widget->current_year = $current_year;
    $selected_year = $defaults->getProp($this->prefix . 'date_year', ($date ? $date->getYear() : $current_year));
    $widget->selected_year = $selected_year;

    $selected_hour = $defaults->getProp($this->prefix . 'time_hours', ($date ? $date->getHours() : false));
    $widget->selected_hour = $selected_hour;

    $selected_minutes = $defaults->getProp($this->prefix . 'time_minutes', ($date ? $date->getMinutes() : false));
    $widget->selected_minutes = $selected_minutes;

    $widget->date_label = $this->date_label;
    $widget->time_label = $this->time_label;

    $months = array(
      1 => 'Jan',
      2 => 'Feb',
      3 => 'Mar',
      4 => 'Apr',
      5 => 'May',
      6 => 'Jun',
      7 => 'Jul',
      8 => 'Aug',
      9 => 'Sep',
      10 => 'Oct',
      11 => 'Nov',
      12 => 'Dec',
    );
    $widget->months = $months;

    return $widget->render('widgets/date_picker.tpl.php');
  }
}

/**
 * Loads all upcoming events.
 *
 * @return array
 */
function load_upcoming_events($limit = false, $time_span = false) {

  $sql = 'SELECT * FROM `upcoming_events` WHERE (`ts` + `duration`) >= ?';
  if (false !== $time_span) {
    $sql .= ' AND (`ts` + `duration`) <= ?';
  }
  $sql .= ' ORDER BY ts';
  if (false !== $limit) {
    $sql .= ' LIMIT ' . $limit;
  }

  $params = array(time());
  if (false !== $time_span) {
    $params[] = time() + (int) $time_span;
  }
  $events_query = getDb()->prepare($sql);
  $events_query->execute($params);

  $events = array();
  while ($row = $events_query->fetch(PDO::FETCH_ASSOC)) {
    $event = new Event();
    $event->populateRow($row);
    $events[] = $event;
  }

  return $events;
}

/**
 * Loads all upcoming community events.
 *
 * @return array
 */
function load_upcoming_community_events() {
  $sql = 'SELECT * FROM community_events WHERE (`startdate` + (`enddate` - `startdate`)) > ? ORDER BY `startdate` ASC';
  $params = array(time());

  $community_events_query = getDb()->prepare($sql);
  $community_events_query->execute($params);

  $community_events = array();
  while ($row = $community_events_query->fetch(PDO::FETCH_ASSOC)) {
    $event = new CommunityEvent();
    $event->populateRow($row);
    $community_events[] = $event;
  }

  return $community_events;
}

/**
 * Load all currently running community events.
 *
 * @return array
 */
function load_current_community_events() {
  $sql = 'SELECT * FROM community_events WHERE `startdate` < ? AND `enddate` > ? ORDER BY `startdate` ASC';
  $params = array(time(), time());

  $community_events_query = getDb()->prepare($sql);
  $community_events_query->execute($params);

  $community_events = array();
  while ($row = $community_events_query->fetch(PDO::FETCH_ASSOC)) {
    $event = new CommunityEvent();
    $event->populateRow($row);
    $community_events[] = $event;
  }

  return $community_events;
}

/**
 * Load list of user groups.
 *
 * @param $user
 * @return array
 */
function load_user_groups($user_id) {
  static $cache = array();

  if (isset($cache[$user_id])) {
    return $cache[$user_id];
  }

  $group_memberships = group_memberships(false, array($user_id));
  $user_groups = array();
  foreach ($group_memberships as $group) {
    $group_name = get_group_name($group['group_id']);
    $user_groups[] = array(
      'name' => $group_name,
      'id' => $group['group_id'],
    );
  }

  $cache[$user_id] = $user_groups;

  return $user_groups;
}

/**
 * Check if user has access to PCS based on user's corporation standing.
 *
 * @param int $user_id
 *
 * @return bool
 */
function has_pcs_access($user_id) {
  $user_groups = load_user_groups($user_id);
  $user_groups_map = array();
  foreach ($user_groups as $user_group) {
    $user_groups_map[$user_group['id']] = 1;
  }
  $corps_query = getDb()->prepare('SELECT corporationID, corporationName, standings, phpbb_id FROM `corporations`');
  $corps_query->execute(array());
  $access_granted = FALSE;

  while ($corp = $corps_query->fetch(PDO::FETCH_ASSOC)) {
    if (isset($user_groups_map[$corp['phpbb_id']]) && ($corp['standings'] > 10)) {
      $access_granted = TRUE;
    }
  }

  return $access_granted;
}

/**
 * Check if user has some specific permission.
 *
 * @param int $user_id
 * @param string $permission
 */
function has_permission($user_id, $permission) {
  global $cfg;

  if (!isset($cfg['permissions'][$permission]) || empty($cfg['permissions'][$permission])) {
    return false;
  }

  if (
    isset($cfg['permissions'][$permission]['groups']) &&
    !empty($cfg['permissions'][$permission]['groups']) &&
    count($cfg['permissions'][$permission]['groups'])
  ) {
    $user_groups = load_user_groups($user_id);
    foreach ($user_groups as $user_group) {
      if (in_array($user_group['id'], $cfg['permissions'][$permission]['groups'])) {
        return true;
      }
    }
  }

  return false;
}

function get_tz_name_by_offset($offset) {
  $timezone_name = timezone_name_from_abbr(null, $offset, false);
  if (!$timezone_name) {
    $timezone_name = timezone_name_from_abbr(null, $offset, true);

    // Custom check for several unsupported tz - see https://bugs.php.net/bug.php?id=44780
    if (!$timezone_name) {
      $unsupported_tz = array(
        -34200 => 'ckhst',
        -16200 => 'ant',
        -12600 => 'negt',
        -7200 => 'addt',
        12600 => 'irst',
        16200 => 'aft',
        19800 => 'ist',
        20700 => 'npt',
        21600 => 'aktst',
        23400 => 'burt',
        28800 => 'bnt',
        34200 => 'cast',
        35100 => 'cwst',
        37800 => 'cst',
        39600 => 'anat',
        41400 => 'lhst',
        46800 => 'anast',
        49500 => 'chadt',
        50400 => 'anast',
      );

      if (isset($unsupported_tz[$offset])) {
        $timezone_name = timezone_name_from_abbr($unsupported_tz[$offset], $offset, false);
        if (!$timezone_name) {
          $timezone_name = timezone_name_from_abbr($unsupported_tz[$offset], $offset, true);
        }
      }

      if (!$timezone_name) {
        return false;
      }
    }
  }

  return $timezone_name;
}

function get_all_districts_names() {
  $districts = DistrictsFactory::getAllDistricts();

  $district_names = array();

  foreach ($districts as $district) {
    $district_names[] = $district->getName();
  }

  return $district_names;
}

function is_mobile() {
  static $is_mobile = NULL;

  if (!isset($is_mobile)) {
    require_once('./lib/Mobile_Detect.php');
    $detect = new Mobile_Detect();
    if ($detect->isMobile() && !$detect->isTablet()) {
      $is_mobile = TRUE;
    }
    else {
      $is_mobile = FALSE;
    }
  }

  return $is_mobile;
}

class DefaultFormValues {
  protected $form_data;

  public function __construct($form_data = NULL) {
    if (isset($form_data)) {
      $this->form_data = $form_data;
    }
    else {
      $this->form_data = array();
    }
  }

  public function getProp($propName, $defaultValue = '') {
    if (isset($this->form_data[$propName]) && !empty($this->form_data[$propName])) {
      return $this->form_data[$propName];
    }

    return $defaultValue;
  }
}