<?php
/**
 * @package nxcSocialNetworks
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    30 Nov 2010
 **/

$Module = array(
	'name'            => 'NXC LinkedIn API',
 	'variable_params' => true
);

$ViewList = array(
	'settings' => array(
		'functions'               => array( 'settings' ),
		'script'                  => 'settings.php',
		'params'                  => array( 'connected' ),
		'default_navigation_part' => 'ezsetupnavigationpart'
	),
	'redirect' => array(
		'functions' => array( 'settings' ),
		'script'    => 'redirect.php'
	),
	'callback' => array(
		'functions' => array( 'settings' ),
		'script'    => 'callback.php'
	)
);

$FunctionList = array(
	'settings' => array()
);
?>
