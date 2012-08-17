<?php
/**
 * @package nxcSocialNetworks
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    15 Aug 2012
 **/

$FunctionList = array();

$FunctionList['home_timeline'] = array(
	'name'             => 'home_timeline',
	'call_method'      => array(
		'class'  => 'nxcFacebookFeedOperations',
		'method' => 'getHomeTimeline'
	),
	'parameter_type'   => 'standard',
	'parameters'       => array(
		array(
			'name'     => 'page_id',
			'type'     => 'string',
			'required' => false,
			'default'  => false
		),
		array(
			'name'     => 'limit',
			'type'     => 'int',
			'required' => false,
			'default'  => 20
		)
	)
);
?>
