<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksLoginHanlderLinkedIn
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    16 Sep 2012
 **/

class nxcSocialNetworksLoginHanlderLinkedIn extends nxcSocialNetworksLoginHanlder
{
	protected function __construct() {
		parent::__construct();

		$this->OAunth2Connection = nxcSocialNetworksOAuth2::getInstanceByType( 'linkedin' );
	}

	public function getScopes() {
		return array( 'r_emailaddress' );
	}

	public function getCallbackURL() {
		return '/nxc_social_network_login/callback/linkedin';
	}

	public function getUserRemoteID() {
		$this->OAunth2Connection->getAccessToken();

		$profile = $this->OAunth2Connection->connection->profile( '~:(id,first-name,last-name,picture-url,email-address)' );
		$profile = (array) new SimpleXMLElement( $profile['linkedin'] );
		if( isset( $profile['id'] ) === false ) {
			throw new Exception( 'Could not get user ID. Refresh the page or try again later.' );
		}

		return 'linkedin_user_' . $profile['id'];
	}

	public function getUserData() {
		$profile = $this->OAunth2Connection->connection->profile( '~:(id,first-name,last-name,picture-url,email-address)' );
		$profile = (array) new SimpleXMLElement( $profile['linkedin'] );
		if( isset( $profile['id'] ) === false ) {
			throw new Exception( 'Could not get user ID. Refresh the page or try again later.' );
		}

		$login = 'LinkedInUser_' . $profile['id'];
		$email = $profile['email-address'];
		if(
			empty( $email )
			|| eZUser::fetchByEmail( $email ) instanceof eZUser
		) {
			$email = $profile['id'] . '@nospam.linkedin.com';
		}

		$attributes = array(
			'user_account' => self::getUserAccountString( $login, $email ),
			'first_name'   => $profile['first-name'],
			'last_name'    => $profile['last-name']
		);

		// Downloading user profile image
		if( $profile['picture-url'] ) {
			$picture = 'var/cache/linkdedin_profile_' .  $profile['id'] . '.jpg';
			$fp = fopen( $picture, 'w' );
			$ch = curl_init( $profile['picture-url'] );
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
