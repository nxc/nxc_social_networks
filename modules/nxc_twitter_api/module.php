<?php
/**
 * @package nxcSocialNetworks
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    17 Sep 2010
 **/

$Module = array(
	'name'            => 'NXC Twitter API',
 	'variable_params' => true
);

$ViewList = array();
$ViewList['settings'] = array(
	'functions'               => array( 'settings' ),
	'script'                  => 'settings.php',
	'params'                  => array( 'connected' ),
	'default_navigation_part' => 'ezsetupnavigationpart'
);
$ViewList['redirect'] = array(
	'functions' => array( 'settings' ),
	'script'    => 'redirect.php'
);
$ViewList['callback'] = array(
	'functions' => array( 'settings' ),
	'script'    => 'callback.php'
);

$FunctionList = array();
$FunctionList['settings'] = array();
?>
