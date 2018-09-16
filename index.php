<?php

require_once dirname(__FILE__) . '/vendor/autoload.php';
require_once dirname(__FILE__) . '/core/Core.php';

$Core = new \Panorama\Core();

$req = !empty($_REQUEST['q'])
	? trim($_REQUEST['q'])
	: '';

if (!defined('PROJECT_API_MODE') || !PROJECT_API_MODE) {
	$Core->handleRequest($req);
}
