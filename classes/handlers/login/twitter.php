<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksLoginHandlerTwitter
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    16 Sep 2012
 **/

class nxcSocialNetworksLoginHandlerTwitter extends nxcSocialNetworksLoginHandler
{
	protected function __construct() {
		parent::__construct();

		$this->OAunth2Connection = nxcSocialNetworksOAuth2::getInstanceByType( 'twitter' );
	}

	public function getCallbackURL() {
		return '/nxc_social_network_login/callback/twitter';
	}

	public function getUserRemoteID() {
		$this->token = $this->OAunth2Connection->getAccessToken();

		return 'twitter_user_' . $this->token['user_id'];
	}

	public function getUserData() {
		$this->twitterAPI = new TwitterOAuth(
			$this->OAunth2Connection->appSettings['key'],
			$this->OAunth2Connection->appSettings['secret'],
			$this->token['token'],
			$this->token['secret']
		);

		$userInfo = $this->twitterAPI->get( 'users/show', array( 'user_id' => $this->token['user_id'] ) );
		$nameArr  = explode( ' ', $userInfo->name );

		$login = $userInfo->screen_name;
		if(
			empty( $login )
			|| eZUser::fetchByName( $login ) instanceof eZUser
		) {
			$login = 'TwitterUser_' . $this->token['user_id'];
		}
		$email = $login . '@nospam.twitter.com';

		$attributes = array(
			'first_name'   => $nameArr[0],
			'last_name'    => isset( $nameArr[1] ) ? $nameArr[1] : '',
			'user_account' => self::getUserAccountString( $login, $email ),
			'signature'    => $userInfo->description
		);

		$filename = 'var/cache/'. substr( strrchr( $userInfo->profile_image_url, '/' ), 1 );
		if( copy( $userInfo->profile_image_url, $filename ) ) {
			$attributes['image'] = $filename;
		};

		return $attributes;
	}
}
?>
