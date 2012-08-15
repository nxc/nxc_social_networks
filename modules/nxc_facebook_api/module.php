<?php
/**
 * @package nxcSocialNetworks
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    20 Oct 2010
 **/

$Module = array(
	'name'            => 'NXC Facebook API',
 	'variable_params' => true
);

$ViewList = array();
$ViewList['settings'] = array(
	'functions'               => array( 'settings' ),
	'script'                  => 'settings.php',
	'params'                  => array( 'connected' ),
	'default_navigation_part' => 'ezsetupnavigationpart'
);
$ViewList['authorize_redirect'] = array(
	'functions' => array( 'settings' ),
	'script'    => 'authorize_redirect.php'
);
$ViewList['access_token'] = array(
	'functions' => array( 'settings' ),
	'script'    => 'access_token.php'
);

$FunctionList = array();
$FunctionList['settings'] = array();
?>
