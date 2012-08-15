<?php
/**
 * @package nxcSocialNetworks
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    25 Nov 2010
 **/

$Module = array(
	'name'            => 'NXC Twitter Signin',
 	'variable_params' => true
);

$ViewList = array();
$ViewList['signin'] = array(
	'functions' => array( 'twitter_signin' ),
	'script'    => 'signin.php'
);
$ViewList['callback'] = array(
	'functions' => array( 'twitter_signin' ),
	'script'    => 'callback.php'
);
$ViewList['signout'] = array(
	'functions' => array( 'twitter_signin' ),
	'script'    => 'signout.php'
);

$FunctionList = array();
$FunctionList['twitter_signin'] = array();
?>
