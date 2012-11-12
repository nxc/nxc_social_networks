<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksPublishHanlderLinkedIn
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    21 Sep 2012
 **/

class nxcSocialNetworksPublishHanlderLinkedIn extends nxcSocialNetworksPublishHanlder
{
	protected $name = 'LinkedIn';

	public function publish( eZContentObject $object, $message ) {
		if( class_exists( 'Normalizer' ) ) {
			$message = Normalizer::normalize( $message, Normalizer::FORM_C );
		}
		$message = mb_substr( $message, 0, 400 );

		$share = array(
			'title'       => $object->attribute( 'name' ),
			'description' => $message
		);

		$options = $this->getOptions();
		if(
			isset( $options['include_url'] )
			&& (bool) $options['include_url'] === true
		) {
			$url = $object->attribute( 'main_node' )->attribute( 'url_alias' );
			eZURI::transformURI( $url, true, 'full' );
			$share['submitted-url'] = $url;
		}

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
