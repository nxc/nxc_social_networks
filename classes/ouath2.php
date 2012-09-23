<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksOAuth2
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    16 Sep 2012
 **/

abstract class nxcSocialNetworksOAuth2 extends nxcSocialNetworksBase
{
	private static $instance = null;
	private static $types    = array();

	protected static $tokenType         = null;
	protected static $typeSettingsGroup = 'OAuth2';

	public $appSettings = array();
	public $connection  = null;

	private $token = null;

	protected function __construct() {
		$this->token = nxcSocialNetworksOAuth2Token::fetch( static::$tokenType );

		$ini = eZINI::instance( 'nxcsocailnetworks.ini' );
		$appSettingsGroup  = str_replace( __CLASS__, '', get_called_class() ) . 'Application';
		$this->appSettings = array(
			'key'      => $ini->variable( $appSettingsGroup, 'Key' ),
			'secret'   => $ini->variable( $appSettingsGroup, 'Secret' )
		);
		unset( $ini );
	}

	public function getPersistenceTokenScopes() {
		return array();
	}

	public function getAuthorizeURL( array $scopes = null, $redirectURL = null ) {}

	public function getAccessToken( $redirectURL = null ) {}

	public function storeToken( $token, $secret ) {
		if( $this->token instanceof nxcSocialNetworksOAuth2Token ) {
			$this->token->setAttribute( 'token', $token );
			$this->token->setAttribute( 'secret', $secret );
		} else {
			$this->token = new nxcSocialNetworksOAuth2Token(
				array(
					'type'   => static::$tokenType,
					'token'  => $token,
					'secret' => $secret
				)
			);
		}

		$this->token->store();
	}

	public function getToken() {
		return $this->token;
	}
}
?>
