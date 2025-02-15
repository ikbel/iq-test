<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
  
global $wpdb;
$charset_collate = $wpdb->get_charset_collate();
  
$createQuery = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."gp_iq_test` (
  `id` bigint(255) NOT NULL AUTO_INCREMENT,
  `test_id` varchar(50) COLLATE utf8_general_ci NOT NULL,
  `age` varchar(10) COLLATE utf8_general_ci NOT NULL,
  `server_start` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `start` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `result` int(11) NOT NULL,
  `time` time NOT NULL DEFAULT '00:00:00',
  `finish` varchar(11) COLLATE utf8_general_ci NOT NULL DEFAULT 'no',
  `payment` varchar(11) COLLATE utf8_general_ci NOT NULL DEFAULT 'no',
  `payment_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) $charset_collate;";
  $wpdb->query($createQuery);
  
$createQuery = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."gp_iq_test_data` (
  `id` bigint(255) NOT NULL AUTO_INCREMENT,
  `test_id` varchar(50) COLLATE utf8_general_ci NOT NULL,
  `question` int(11) NOT NULL,
  `answer` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `test_id` (`test_id`)
) $charset_collate;";
  $wpdb->query($createQuery); 
  
  $result = $wpdb->query("SHOW COLUMNS FROM `".$wpdb->prefix."gp_iq_test` LIKE 'server_start'");
if(empty ($result)) {
   $wpdb->query("ALTER TABLE `".$wpdb->prefix."qp_iq_test` ADD `server_start` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `age`");
}
