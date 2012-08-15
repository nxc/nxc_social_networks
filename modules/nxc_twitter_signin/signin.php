<?php
/**
 * @package nxcSocialNetworks
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    25 Nov 2010
 **/

$http = eZHTTPTool::instance();
$ini  = eZINI::instance( 'nxctwitter.ini' );

$connection = new TwitterOAuth(
	$ini->variable( 'TwitterAPI', 'Key' ),
	$ini->variable( 'TwitterAPI', 'Secret' )
);

$requestToken = $connection->getRequestToken(
	eZSys::serverURL() . eZSys::indexDir() . '/nxc_twitter_signin/callback'
);
$http->setSessionVariable( 'twitter_request_token', $requestToken['oauth_token'] );
$http->setSessionVariable( 'twitter_request_token_secret', $requestToken['oauth_token_secret'] );

switch( $connection->http_code ) {
	case 200:
		header( 'Location: ' . $connection->getAuthorizeURL( $requestToken['oauth_token'] ) );
		eZExecution::cleanExit();
	default:
		eZDebug::writeError( 'Could not connect to Twitter. Refresh the page or try again later.', 'NXC Twitter Signin' );
}
?>
