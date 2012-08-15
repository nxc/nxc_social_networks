<?php
/**
 * @package nxcSocialNetworks
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    12 Jan 2011
 **/

$Module = array(
	'name'            => 'NXC LinkedIn Signin',
 	'variable_params' => true
);

$ViewList = array(
	'signin'   => array(
		'functions' => array( 'linkedin_signin' ),
		'script'    => 'signin.php'
	),
	'callback' => array(
		'functions' => array( 'linkedin_signin' ),
		'script'    => 'callback.php'
	)
);

$FunctionList = array(
	'linkedin_signin' => array()
);
?>
