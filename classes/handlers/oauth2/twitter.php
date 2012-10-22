<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksOAuth2Twitter
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    16 Sep 2012
 **/

class nxcSocialNetworksOAuth2Twitter extends nxcSocialNetworksOAuth2
{
	public static $tokenType = nxcSocialNetworksOAuth2Token::TYPE_TWITTER;

	public function getAuthorizeURL( array $scopes = null, $redirectURL = null ) {
		$connection = new TwitterOAuth(
			$this->appSettings['key'],
			$this->appSettings['secret']
		);

		if( $redirectURL === null ) {
			$redirectURL = '/nxc_social_network_token/get_access_token/twitter';
		}
		eZURI::transformURI( $redirectURL, false, 'full' );
		$requestToken = $connection->getRequestToken( $redirectURL );

		$http = eZHTTPTool::instance();
		$http->setSessionVariable( 'twitter_request_token', $requestToken['oauth_token'] );
		$http->setSessionVariable( 'twitter_request_token_secret', $requestToken['oauth_token_secret'] );

		switch( $connection->http_code ) {
			case 200:
				return $connection->getAuthorizeURL( $requestToken['oauth_token'], true );
			default:
				throw new Exception( 'Could not connect to Twitter. Refresh the page or try again later.' );
		}
	}

	public function getAccessToken( $redirectURL = null ) {
		$http = eZHTTPTool::instance();

		if(
			$http->hasGetVariable( 'twitter_request_token' ) &&
			( $http->getVariable( 'twitter_request_token' ) !== $http->sessionVariable( 'twitter_request_token' ) )
		) {
			throw new Exception( 'Wrong request token. Refresh the page or try again later.' );
		}

		$connection = new TwitterOAuth(
			$this->appSettings['key'],
			$this->appSettings['secret'],
			$http->sessionVariable( 'twitter_request_token' ),
			$http->sessionVariable( 'twitter_request_token_secret' )
		);
		$accessToken = $connection->getAccessToken( $http->getVariable( 'oauth_verifier' ) );
		$http->removeSessionVariable( 'twitter_request_token' );
		$http->removeSessionVariable( 'twitter_request_token_secret' );

		if( (int) $connection->http_code === 200 ) {
			return array(
				'token'   => $accessToken['oauth_token'],
				'secret'  => $accessToken['oauth_token_secret'],
				'user_id' => $accessToken['user_id']
			);
		}

		throw new Exception( 'Could not get access token. Refresh the page or try again later.' );
	}
}
?>
