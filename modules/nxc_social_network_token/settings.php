<?php
/**
 * @package nxcSocialNetworks
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    16 Sep 2012
 **/

$tpl    = eZTemplate::factory();
$Result = array();
$Result['content'] = $tpl->fetch( 'design:social_networks/settings.tpl' );
$Result['path']    = array(
	array(
		'text' => ezpI18n::tr( 'extension/nxc_social_networks', 'Social Network Tokens Management' ),
		'url'  => false
	)
);
?>
