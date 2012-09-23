<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksLoginHanlder
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    16 Sep 2012
 **/

abstract class nxcSocialNetworksLoginHanlder extends nxcSocialNetworksBase
{
	protected static $typeSettingsGroup = 'LoginHandlers';

	protected $OAunth2Connection = null;
	protected $token = null;

	protected function __construct() {}

	public function getScopes() {
		return array();
	}

	public function getCallbackURL() {
		return null;
	}

	public function getLoginURL( array $scopes = null, $redirectURL = null ) {
		return $this->OAunth2Connection->getAuthorizeURL( $scopes, $redirectURL );
	}

	public function getUserData() {
		return array();
	}

	public function getUserRemoteID() {
		return null;
	}

	protected function getUserAccountString( $login, $email ) {
		$password     = eZUser::createPassword( 8 );
		$passwordHash = eZUser::createHash( $login, $password, eZUser::site(), eZUser::hashType() );
		return $login . '|' . $email . '|' . $passwordHash . '|' . eZUser::passwordHashTypeName( eZUser::hashType() );
	}
}
?>
