<?php
/**
 * @package nxcSocialNetworks
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    16 Sep 2012
 **/

$error = false;
try{
	$OAth2 = nxcSocialNetworksOAuth2::getInstanceByType( $Params['type'] );
} catch( Exception $e ) {
	$error = $e->getMessage();
}

if( $error === false ) {
	try{
		$token = $OAth2->getAccessToken();
	} catch( Exception $e ) {
		$error = $e->getMessage();
	}
}

if( $error === false ) {
	$OAth2->storeToken( $token['token'], $token['secret'] );
}

$tpl = eZTemplate::factory();
$tpl->setVariable( 'error', $error );
$tpl->setVariable( 'connected', $error === false );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:social_networks/settings.tpl' );
$Result['path']    = array(
	array(
		'text' => ezpI18n::tr( 'extension/nxc_social_networks', 'Social Network Tokens Management' ),
		'url'  => false
	)
);
?>
