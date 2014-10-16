<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksPublishHandlerTwitter
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    21 Sep 2012
 **/

class nxcSocialNetworksPublishHandlerTwitter extends nxcSocialNetworksPublishHandler
{
	protected $name = 'Twitter';

	public function publish( eZContentObject $object, $message ) {
		$options = $this->getOptions();

		$messageLength = 140;

		$message = $this->message( $this, $object, $message, $messageLength, $options, $options['message_handler'] );

		$response = $this->getAPI()->post(
			'statuses/update',
			array( 'status' => $message )
		);

		if( isset( $response->error ) ) {
			throw new Exception( $response->error );
		}
	}

	protected function getAPI() {
		$OAuth2      = nxcSocialNetworksOAuth2::getInstanceByType( 'twitter' );
		$OAuth2Token = $OAuth2->getToken();

		return new TwitterOAuth(
			$OAuth2->appSettings['key'],
			$OAuth2->appSettings['secret'],
			$OAuth2Token->attribute( 'token' ),
			$OAuth2Token->attribute( 'secret' )
		);
	}
}
?>
