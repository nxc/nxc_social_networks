<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksLoginHanlderGoogle
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    24 Sep 2012
 **/

class nxcSocialNetworksLoginHanlderGoogle extends nxcSocialNetworksLoginHanlder
{
	private $profile = null;

	protected function __construct() {
		parent::__construct();

		$this->OAunth2Connection = nxcSocialNetworksOAuth2::getInstanceByType( 'google' );
	}

	public function getScopes() {
		return array(
			'https://www.googleapis.com/auth/userinfo.profile',
			'https://www.googleapis.com/auth/userinfo.email'
		);
	}

	public function getCallbackURL() {
		return '/nxc_social_network_login/callback/google';
	}

	public function getLoginURL( array $scopes = null, $redirectURL = null ) {
		$this->OAunth2Connection->connection->setAccessType( 'online' );
		$this->OAunth2Connection->connection->setApprovalPrompt( 'auto' );
		return $this->OAunth2Connection->getAuthorizeURL( $scopes, $redirectURL );
	}

	public function getUserRemoteID() {
		$service = new apiOauth2Service( $this->OAunth2Connection->connection );

		$token = $this->OAunth2Connection->getAccessToken( $this->getCallbackURL() );
		$this->OAunth2Connection->connection->setAccessToken( $token['token'] );

		$this->profile = $service->userinfo->get();
		if( isset( $this->profile['id'] ) === false ) {
			throw new Exception( 'Could not get user ID. Refresh the page or try again later.' );
		}
		return 'google_user_' . $this->profile['id'];
	}

	public function getUserData() {
		$login = 'GoogleUser_' . $this->profile['id'];
		$email = $this->profile['email'];
		if( empty( $email ) ) {
			$email = $this->profile['id'] . '@nospam.google.com';
		}

		$attributes = array(
			'user_account' => self::getUserAccountString( $login, $email ),
			'first_name'   => $this->profile['given_name'],
			'last_name'    => $this->profile['family_name']
		);

		// Downloading user profile image
		if( $this->profile['picture'] ) {
			$picture = 'var/cache/google_profile_' .  $this->profile['id'] . '.jpg';
			$fp = fopen( $picture, 'w' );
			$ch = curl_init( $this->profile['picture'] );
			curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
			curl_setopt( $ch, CURLOPT_FILE, $fp );
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
			curl_exec( $ch );
			curl_close( $ch );
			fclose( $fp );
			$attributes['image'] = $picture;
		}

		return $attributes;
	}
}
?>
