<?php
/**
 * @package nxcSocialNetworks
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    16 Sep 2012
 **/

$Module = array(
	'name'            => 'NXC Social Network Tokens',
 	'variable_params' => true
);

$ViewList = array();
$ViewList['settings'] = array(
	'functions'               => array( 'settings' ),
	'script'                  => 'settings.php',
	'params'                  => array(),
	'default_navigation_part' => 'ezsetupnavigationpart'
);
$ViewList['authorize'] = array(
	'functions'               => array( 'settings' ),
	'script'                  => 'authorize.php',
	'params'                  => array( 'type' ),
	'default_navigation_part' => 'ezsetupnavigationpart'
);
$ViewList['get_access_token'] = array(
	'functions'               => array( 'settings' ),
	'script'                  => 'get_access_token.php',
	'params'                  => array( 'type' ),
	'default_navigation_part' => 'ezsetupnavigationpart'
);

$FunctionList = array();
$FunctionList['settings'] = array();
?>
