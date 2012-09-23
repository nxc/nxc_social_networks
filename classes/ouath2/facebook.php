<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksOAuth2Facebook
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    16 Sep 2012
 **/

class nxcSocialNetworksOAuth2Facebook extends nxcSocialNetworksOAuth2
{
	public static $tokenType = nxcSocialNetworksOAuth2Token::TYPE_FACEBOOK;

	public function getPersistenceTokenScopes() {
		return array( 'offline_access', 'publish_stream', 'read_stream' );
	}

	public function getAuthorizeURL( array $scopes = null, $redirectURL = null ) {
		if( $redirectURL === null ) {
			$redirectURL = '/nxc_social_network_token/get_access_token/facebook';
		}
		eZURI::transformURI( $redirectURL, false, 'full' );

		return 'https://graph.facebook.com/oauth/authorize?' .
			'client_id=' . $this->appSettings['key'] . '&' .
			'redirect_uri=' . $redirectURL . '&' .
			'scope=' . implode( ',', $scopes );
	}

	public function getAccessToken( $redirectURL = null ) {
		$http = eZHTTPTool::instance();

		if( $redirectURL === null ) {
			$redirectURL = '/nxc_social_network_token/get_access_token/facebook';
		}
		eZURI::transformURI( $redirectURL, false, 'full' );

		$data = file_get_contents(
			'https://graph.facebook.com/oauth/access_token?' .
			'client_id=' . $this->appSettings['key'] . '&' .
			'client_secret=' . $this->appSettings['secret'] . '&' .
			'code=' . $http->getVariable( 'code' ) . '&' .
			'redirect_uri=' . $redirectURL
		);

		if( strpos( $data, 'access_token=' ) !== false ) {
			preg_match( '/access_token=([^&]*)/i', $data, $matches );
			if( isset( $matches[1] ) ) {
				return array(
					'token'  => $matches[1],
					'secret' => null
				);
			}
		}

		throw new Exception( 'Could not get access token. Refresh the page or try again later.' );
	}
}
?>
