<?php
/**
 * @package nxcSocialNetworks
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    20 Oct 2010
 **/

$ini = eZINI::instance( 'nxcfacebook.ini' );

$redirectURL = '/nxc_facebook_api/access_token';
eZURI::transformURI( $redirectURL, false, 'full' );

$Params['Module']->redirectTo(
	'https://graph.facebook.com/oauth/authorize?' .
	'client_id=' . $ini->variable( 'FacebookAPI', 'AppID' ) . '&' .
	'redirect_uri=' . $redirectURL . '&' .
	'scope=offline_access,publish_stream,read_stream'
);
?>
