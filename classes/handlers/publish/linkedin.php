<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksPublishHandlerLinkedIn
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    21 Sep 2012
 **/

class nxcSocialNetworksPublishHandlerLinkedIn extends nxcSocialNetworksPublishHandler
{
	protected $name = 'LinkedIn';

	public function publish( eZContentObject $object, $message ) {
		$options = $this->getOptions();
		$messageLength = 400;

		$share = $this->message( $this, $object, $message, $messageLength, $options, $options['message_handler'] );
		$response = $this->getAPI()->share( 'new', $share, false );

		if( (bool) $response['success'] === false ) {
			throw new Exception( $response['error'] );
		}
	}

	protected function getAPI() {
		$OAuth2      = nxcSocialNetworksOAuth2::getInstanceByType( 'linkedin' );
		$OAuth2Token = $OAuth2->getToken();

		$API = new LinkedIn(
			array(
				'appKey'      => $OAuth2->appSettings['key'],
				'appSecret'   => $OAuth2->appSettings['secret'],
				'callbackUrl' => null
			)
		);
		$API->setTokenAccess(
			array(
				'oauth_token'        => $OAuth2Token->attribute( 'token' ),
				'oauth_token_secret' => $OAuth2Token->attribute( 'secret' )
			)
		);

		return $API;
	}
}
?>
