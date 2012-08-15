<?php
/**
 * @package nxcSocialNetworks
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    17 Sep 2010
 **/

$http = eZHTTPTool::instance();
$ini  = eZINI::instance( 'nxctwitter.ini' );

if(
	$http->hasGetVariable( 'twitter_request_token' ) &&
	( $http->getVariable( 'twitter_request_token' ) !== $http->sessionVariable( 'twitter_request_token' ) )
) {
	$Params['Module']->redirectTo( '/nxc_twitter_api/settings' );
}

$connection = new TwitterOAuth(
	$ini->variable( 'TwitterAPI', 'Key' ),
	$ini->variable( 'TwitterAPI', 'Secret' ),
	$http->sessionVariable( 'twitter_request_token' ),
	$http->sessionVariable( 'twitter_request_token_secret' )
);
$accessToken = $connection->getAccessToken( $http->getVariable( 'oauth_verifier' ) );
$http->removeSessionVariable( 'twitter_request_token' );
$http->removeSessionVariable( 'twitter_request_token_secret' );

if( (int) $connection->http_code === 200 ) {
	$ini = eZINI::instance( 'nxctwitteraccesstoken.ini' );
	$ini->setVariable( 'AccessToken', 'UserID', $accessToken['user_id'] );
	$ini->setVariable( 'AccessToken', 'Token', $accessToken['oauth_token'] );
	$ini->setVariable( 'AccessToken', 'Secret', $accessToken['oauth_token_secret'] );
	$result = $ini->save(
		'nxctwitteraccesstoken.ini.append.php',
		false,
		false,
		false,
		'settings/override',
		false,
		true
	);

	if( $result === false ) {
		eZDebug::writeError( 'Cannot update persistent token info at settings file', 'NXC Twitter API' );
	}
}

$Params['Module']->redirectTo( '/nxc_twitter_api/settings/1' );
?>
