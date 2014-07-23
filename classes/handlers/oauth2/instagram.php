<?php

class nxcSocialNetworksOAuth2Instagram extends nxcSocialNetworksOAuth2
{
	public static $tokenType = nxcSocialNetworksOAuth2Token::TYPE_INSTAGRAM;

	public function getAuthorizeURL( array $scopes = null, $redirectURL = null ) {
		if( $redirectURL === null ) {
			$redirectURL = '/nxc_social_network_token/get_access_token/instagram';
		}
		eZURI::transformURI( $redirectURL, false, 'full' );

		return 'https://api.instagram.com/oauth/authorize/?' .
			'client_id=' . $this->appSettings['key'] . '&' .
			'redirect_uri=' . $redirectURL . '&' . 
			'response_type=code';
	}

	public function getAccessToken( $redirectURL = null ) {
		$http = eZHTTPTool::instance();

		if( $redirectURL === null ) {
			$redirectURL = '/nxc_social_network_token/get_access_token/instagram';
		}
		eZURI::transformURI( $redirectURL, false, 'full' );

		$postdata = http_build_query(
			array(
				'client_id' => $this->appSettings['key'],
				'grant_type' => 'authorization_code',
				'client_secret' => $this->appSettings['secret'],
				'code' => $http->getVariable( 'code' ),
				'redirect_uri' => $redirectURL
			)
		);
		$opts = array('http' =>
			array(
				'method'  => 'POST',
				'content' => $postdata
			)
		);
		$context = stream_context_create( $opts );
		$data = file_get_contents( 'https://api.instagram.com/oauth/access_token', false, $context );
		$dataArray = json_decode( $data, true );

		if( isset( $adataArray[ 'access_token' ] ) ) {
			return array(
				'token'  => $adataArray[ 'access_token' ],
				'secret' => null
			);
		}

		if( isset( $dataArray[ 'access_token' ] ) ) {
			return array(
				'token'  => $dataArray[ 'access_token' ],
				'secret' => null
			);
		}

		if( isset( $dataArray[ 'error_message' ] ) && isset( $dataArray[ 'code' ] ) && isset( $dataArray[ 'error_type' ] ) ) {
		  throw new Exception( $dataArray[ 'error_type' ] . '(' . $dataArray[ 'code' ] . '): ' . $dataArray[ 'error_message' ] );
		}

		throw new Exception( 'Could not get access token. Refresh the page or try again later.' );
	}
}

?>