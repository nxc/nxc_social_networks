<?php
/**
 * @package nxcSocialNetworks
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    30 Nov 2010
 **/

$tpl = eZTemplate::factory();
$tpl->setVariable( 'connected', (int) $Params['connected'] === 1 );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:linkedin/settings.tpl' );
$Result['path']    = array(
	array(
		'text' => ezpI18n::tr( 'extension/nxc_social_networks', 'LinkedIn Settings' ),
		'url'  => false
	)
);
?>
