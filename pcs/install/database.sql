CREATE TABLE IF NOT EXISTS `upcoming_events`(
  `eid` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Event id',
  `district_location` VARCHAR(200) NOT NULL COMMENT 'District location',
  `description` TEXT COMMENT 'Optional event description',
  `ts` INT UNSIGNED NOT NULL COMMENT 'Event timestamp',
  `type` INT UNSIGNED NOT NULL COMMENT 'Event type (attack, defense, FW, etc.)',
  `created_by` INT UNSIGNED NOT NULL COMMENT 'User id of the event\'s author',
  PRIMARY KEY (`eid`)
);

ALTER TABLE `upcoming_events`
  ADD COLUMN `enemy_corp` VARCHAR(200) NULL COMMENT 'Enemy corporation' AFTER `district_location`,
  ADD COLUMN `friendly_corp` VARCHAR(200) NULL COMMENT 'Friendly corporation' AFTER `enemy_corp`;

ALTER TABLE `upcoming_events`
  ADD COLUMN `forum_thread` INT UNSIGNED NOT NULL COMMENT 'Id of corresponding forum thread' AFTER `created_by`;

ALTER TABLE `upcoming_events`
  ADD COLUMN `platoon_leader` VARCHAR(200) NOT NULL COMMENT 'Platoon leader for event' AFTER `friendly_corp`;

-- 1.4
ALTER TABLE `upcoming_events`
  ADD COLUMN `backup_platoon_leaders` VARCHAR(600) NULL COMMENT 'Backup Platoon leaders for event' AFTER `platoon_leader`;

-- 1.5
ALTER TABLE `upcoming_events`
  ADD COLUMN `duration` INT UNSIGNED NOT NULL COMMENT 'Duration of the event in seconds' AFTER `ts`;

-- 1.6
CREATE TABLE `community_events` (
`ceid` int(10) unsigned NOT NULL AUTO_INCREMENT,
`title` text NOT NULL,
`link` text NOT NULL,
`startdate` int(10) unsigned NOT NULL,
`enddate` int(10) unsigned NOT NULL,
`created_by` int(10) unsigned NOT NULL,
PRIMARY KEY (`ceid`)
);

