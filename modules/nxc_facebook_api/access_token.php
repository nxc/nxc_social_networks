<?php
/**
 * @package nxcSocialNetworks
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    20 Oct 2010
 **/

$ini = eZINI::instance( 'nxcfacebook.ini' );

$redirectURL = '/nxc_facebook_api/access_token';
eZURI::transformURI( $redirectURL, false, 'full' );

$data = file_get_contents(
	'https://graph.facebook.com/oauth/access_token?' .
	'client_id=' . $ini->variable( 'FacebookAPI', 'AppID' ) . '&' .
	'client_secret=' . $ini->variable( 'FacebookAPI', 'Secret' ) . '&' .
	'code=' . $_GET['code'] . '&' .
	'redirect_uri=' . $redirectURL
);
if( strpos( $data, 'access_token=' ) !== false ) {
	$accessToken = false;
	preg_match( '/access_token=([^&]*)/i', $data, $matches );
	if( isset( $matches[1] ) ) {
		$accessToken = $matches[1];
	}

	$facebook = new Facebook(
		array(
			'appId'  => $ini->variable( 'FacebookAPI', 'AppID' ),
			'secret' => $ini->variable( 'FacebookAPI', 'Secret' ),
			'cookie' => true
		)
	);
	$info = $facebook->api( '/me', array( 'access_token' => $accessToken ) );

	$ini = eZINI::instance( 'nxcfacebookaccesstoken.ini' );
	$ini->setVariable( 'AccessToken', 'UserID', $info['id'] );
	$ini->setVariable( 'AccessToken', 'Token', $accessToken );
	$result = $ini->save(
		'nxcfacebookaccesstoken.ini.append.php',
		false,
		false,
		false,
		'settings/override',
		false,
		true
	);

	if( $result === false ) {
		eZDebug::writeError( 'Cannot update persistent token info at settings file', 'NXC Facebook API' );
	} else {
		$Params['Module']->redirectTo( '/nxc_facebook_api/settings/1' );
	}
} else {
	eZDebug::writeError( 'Could not get access token.', 'NXC Facebook API' );
}
?>
