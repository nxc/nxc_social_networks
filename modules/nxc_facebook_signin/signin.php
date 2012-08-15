<?php
/**
 * @package nxcSocialNetworks
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    10 Jan 2012
 **/

$http = eZHTTPTool::instance();
$ini  = eZINI::instance( 'nxcfacebook.ini' );

$connection = new Facebook(
	array(
		'appId'  => $ini->variable( 'FacebookAPI', 'AppID' ),
		'secret' => $ini->variable( 'FacebookAPI', 'Secret' )
	)
);

$params = array(
	'scope'        => 'email, user_about_me, user_photos',
	'redirect_uri' => eZSys::serverURL() . eZSys::indexDir() . '/nxc_facebook_signin/callback'
);
header( 'Location: ' . $connection->getLoginUrl( $params ) );
eZExecution::cleanExit();
?>
