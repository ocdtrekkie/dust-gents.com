<?php

/**
 * Fetch the data from the given URL and return the result
 *
 * @param string $url
 * @return SimpleXMLElement
 */
function makeApiRequest($url) {

	// Initialize a new request for this URL
	$ch = curl_init($url);

	// Set the options for this request
	curl_setopt_array($ch, array(
		CURLOPT_FOLLOWLOCATION => true, // Yes, we want to follow a redirect
		CURLOPT_RETURNTRANSFER => true, // Yes, we want that curl_exec returns the fetched data
		CURLOPT_SSL_VERIFYPEER => false, // Do not verify the SSL certificate
	));

	// Fetch the data from the URL
	$data = curl_exec($ch);

	// Close the connection
	curl_close($ch);

	// Return a new SimpleXMLElement based upon the received data
	try {
		return new SimpleXMLElement($data);
	}
	// In case of failure, simulate an error document to get a
	// SimpleXMLElement object in any case
	catch (Exception $e) {
		$time = gmdate('Y-m-d H:i:s');
		return new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?>
			<eveapi version=\"2\">
			  <currentTime>$time</currentTime>
			  <error code=\"{$e->getCode()}\">{$e->getMessage()}</error>
			  <cachedUntil>$time</cachedUntil>
			</eveapi>");
	}
}

/**
 * Load and configure Pheal
 *
 * @return Pheal
 */
function loadPheal() {
	// Do this only once
	if (!class_exists('Pheal', FALSE)) {

		// Load the stuff
		require_once 'pheal/Pheal.php';

		// register the class loader
		spl_autoload_register("Pheal::classload");

		// Set the cache and tell it were to save its contents
		PhealConfig::getInstance()->cache = new PhealFileCache('/home/dustgent/.pheal-cache/');

		// Do not verify peer on SSL requests
		PhealConfig::getInstance()->http_ssl_verifypeer = false;

		// Enable access detection
		PhealConfig::getInstance()->access = new PhealCheckAccess();
	}
}

function process_db_array($array, $field) {
	$new_array = array();
	foreach ($array as $item) {
	$new_array[$item[$field]] = $item;
	}
	return $new_array;
}

/**
 * Get the db connection
 *
 * @return PDO
 */
function getDb() {
	static $db;
	if (!$db) {
		$db = new PDO('mysql:host=localhost;dbname=DBNAME_REMOVED', 'DBUSER_REMOVED', 'DBPASS_REMOVED', array(
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		));
	}
	return $db;
}

