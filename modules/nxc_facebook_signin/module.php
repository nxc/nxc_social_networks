<?php
/**
 * @package nxcSocialNetworks
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    10 Jan 2012
 **/

$Module = array(
	'name'            => 'NXC Facebook Signin',
 	'variable_params' => true
);

$ViewList = array(
	'signin'   => array(
		'functions' => array( 'facebook_signin' ),
		'script'    => 'signin.php'
	),
	'callback' => array(
		'functions' => array( 'facebook_signin' ),
		'script'    => 'callback.php'
	)
);

$FunctionList = array(
	'facebook_signin' => array()
);
?>
