<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksOAuth2LinkedIn
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    16 Sep 2012
 **/

class nxcSocialNetworksOAuth2LinkedIn extends nxcSocialNetworksOAuth2
{
	public static $tokenType = nxcSocialNetworksOAuth2Token::TYPE_LINKEDIN;

	protected function __construct() {
		parent::__construct();

		$redirectURL = '/nxc_social_network_token/get_access_token/linkedin';
		eZURI::transformURI( $redirectURL, false, 'full' );

		$this->connection = new LinkedIn(
			array(
				'appKey'      => $this->appSettings['key'],
				'appSecret'   => $this->appSettings['secret'],
				'callbackUrl' => $redirectURL
			)
		);
	}

	public function getPersistenceTokenScopes() {
		return array( 'rw_nus' );
	}

	public function getAuthorizeURL( array $scopes = null, $redirectURL = null ) {
		$http = eZHTTPTool::instance();

		if( $redirectURL !== null ) {
			eZURI::transformURI( $redirectURL, false, 'full' );
			$this->connection->setCallbackUrl( $redirectURL );
		}

		$response = $this->connection->retrieveTokenRequest( $scopes );
		if( $response['success'] === true ) {
			$http->setSessionVariable( 'linkedin_request_token', $response['linkedin'] );
			return LinkedIn::_URL_AUTH . $response['linkedin']['oauth_token'];
		} else {
			throw new Exception( 'Request token retrieval failed. Refresh the page or try again later.' );
		}
	}

	public function getAccessToken( $redirectURL = null ) {
		$http = eZHTTPTool::instance();

		$request = $http->hasSessionVariable( 'linkedin_request_token' )
			? $http->sessionVariable( 'linkedin_request_token' )
			: array( 'oauth_token' => null );
		if(
			$http->hasGetVariable( 'oauth_token' ) &&
			( $http->getVariable( 'oauth_token' ) !== $request['oauth_token'] )
		) {
			throw new Exception( 'Wrong request token. Refresh the page or try again later.' );
		}

		$response = $this->connection->retrieveTokenAccess(
			$request['oauth_token'],
			$request['oauth_token_secret'],
			$http->getVariable( 'oauth_verifier' )
		);
		if( $response['success'] === true ) {
			$http->removeSessionVariable( 'linkedin_request_token' );

			return array(
				'token'  => $response['linkedin']['oauth_token'],
				'secret' => $response['linkedin']['oauth_token_secret']
			);
		}

		throw new Exception( 'Could not get access token. Refresh the page or try again later.' );
	}
}
?>
