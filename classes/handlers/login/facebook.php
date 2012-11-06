<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksLoginHanlderFacebook
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    16 Sep 2012
 **/

class nxcSocialNetworksLoginHanlderFacebook extends nxcSocialNetworksLoginHanlder
{
	protected function __construct() {
		parent::__construct();

		$this->OAunth2Connection = nxcSocialNetworksOAuth2::getInstanceByType( 'facebook' );
	}

	public function getScopes() {
		return array( 'email', 'user_about_me', 'user_photos' );
	}

	public function getCallbackURL() {
		return '/nxc_social_network_login/callback/facebook';
	}

	public function getLoginURL( array $scopes = null, $redirectURL = null ) {
		eZURI::transformURI( $redirectURL, false, 'full' );

		$connection = $this->getFacebookConnection();

		$params = array(
			'scope'        => implode( ', ', $scopes ),
			'redirect_uri' => $redirectURL
		);
		return $connection->getLoginUrl( $params );
	}

	public function getUserRemoteID() {
		$connection  = $this->getFacebookConnection();
		$uid         = (int) $connection->getUser();
		if( $uid === 0 ) {
			throw new Exception( 'Could not get user ID. Refresh the page or try again later.' );
		}

		return 'facebook_user_' . $uid;
	}

	public function getUserData() {
		$connection  = $this->getFacebookConnection();
		$uid         = $connection->getUser();
		if( $uid === 0 ) {
			throw new Exception( 'Could not get user ID. Refresh the page or try again later.' );
		}

		$picture = 'var/cache/fb_profile_' .  $uid . '.jpg';
		$fp = fopen( $picture, 'w' );
		$ch = curl_init( BaseFacebook::$DOMAIN_MAP['graph'] . '/' . $uid . '/picture?type=large' );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
		curl_setopt( $ch, CURLOPT_FILE, $fp );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_exec( $ch );
		curl_close( $ch );
		fclose( $fp );

		$data  = $connection->api( '/' . $uid );
		$login = $data['username'];
		$email = $data['email'];
		if(
			empty( $login )
			|| eZUser::fetchByName( $login ) instanceof eZUser
		) {
			$login = 'FacebookUser_' . $uid;
		}
		if( empty( $email ) ) {
			$email = $uid . '@nospam.facebook.com';
		}

		return array(
			'image'        => $picture,
			'user_account' => self::getUserAccountString( $login, $email ),
			'first_name'   => $data['first_name'],
			'last_name'    => $data['last_name']
		);
	}

	private function getFacebookConnection() {
		return new Facebook(
			array(
				'appId'  => $this->OAunth2Connection->appSettings['key'],
				'secret' => $this->OAunth2Connection->appSettings['secret']
			)
		);
	}
}
?>
