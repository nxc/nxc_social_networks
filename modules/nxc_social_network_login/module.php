<?php
/**
 * @package nxcSocialNetworks
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    16 Sep 2012
 **/

$Module = array(
	'name'            => 'NXC Social Network Logn',
 	'variable_params' => true
);

$ViewList = array();
$ViewList['redirect'] = array(
	'functions' => array( 'redirect' ),
	'script'    => 'redirect.php',
	'params'    => array( 'type' )
);
$ViewList['callback'] = array(
	'functions' => array( 'callback' ),
	'script'    => 'callback.php',
	'params'    => array( 'type' )
);

$FunctionList = array();
$FunctionList['login'] = array();
?>
