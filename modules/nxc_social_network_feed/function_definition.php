<?php
/**
 * @package nxcSocialNetworks
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    20 Sep 2012
 **/


$FunctionList = array();

/**
 * @see
 * - https://dev.twitter.com/docs/api/1.1/get/statuses/mentions_timeline
 * - https://dev.twitter.com/docs/api/1.1/get/statuses/user_timeline
 * - https://dev.twitter.com/docs/api/1.1/get/statuses/home_timeline
 **/
$FunctionList['twitter_timeline'] = array(
	'name'           => 'twitter_timeline',
	'call_method'    => array(
		'class'  => 'nxcSocialNetworksFeedTwitter',
		'method' => 'getTimeline'
	),
	'parameter_type' => 'standard',
	'parameters'     => array(
		array(
			'name'     => 'type',
			'type'     => 'string',
			'required' => true,
			'default'  => 'user'
		),
		array(
			'name'     => 'parameters',
			'type'     => 'array',
			'required' => false,
			'default'  => array()
		)
	)
);

/**
 * @see https://dev.twitter.com/docs/api/1.1/get/users/show
 **/
$FunctionList['twitter_user_info'] = array(
	'name'           => 'twitter_user_info',
	'call_method'    => array(
		'class'  => 'nxcSocialNetworksFeedTwitter',
		'method' => 'getUserInfo'
	),
	'parameter_type' => 'standard',
	'parameters'     => array()
);

/**
 * @see https://developers.facebook.com/docs/reference/api/user/#feed
 **/
$FunctionList['facebook_timeline'] = array(
	'name'           => 'facebook_timeline',
	'call_method'    => array(
		'class'  => 'nxcSocialNetworksFeedFacebook',
		'method' => 'getTimeline'
	),
	'parameter_type' => 'standard',
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
