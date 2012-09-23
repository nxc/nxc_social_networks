<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksPublishHanlderFacebook
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    21 Sep 2012
 **/

class nxcSocialNetworksPublishHanlderFacebook extends nxcSocialNetworksPublishHanlder
{
	protected $name = 'Facebook';
	private $acessToken = null;

	public function hasExtraOptions() {
		return true;
	}

	public function getExtraOptionNames() {
		return array( 'target_id' );
	}

	public function publish( eZContentObject $object, $message ) {
		$options = $this->getOptions();

		$targetID = 'me';
		if(
			isset( $options['target_id'] )
			&& strlen( $options['target_id'] ) > 0
		) {
			$targetID = $options['target_id'];
		}

		if(
			isset( $options['include_url'] )
			&& (bool) $options['include_url'] === true
		) {
			$url = $object->attribute( 'main_node' )->attribute( 'url_alias' );
			eZURI::transformURI( $url, true, 'full' );
			$message .= ' ' . $url;
		}

		$this->getAPI()->api(
			'/' . $targetID . '/feed',
			'post',
			array(
				'access_token' => $this->acessToken,
				'message'      => $message
			)
		);
	}

	protected function getAPI() {
		$OAuth2 = nxcSocialNetworksOAuth2::getInstanceByType( 'facebook' );
		$API    = new Facebook(
			$OAuth2->appSettings['key'],
			$OAuth2->appSettings['secret']
		);

		$OAuth2Token = $OAuth2->getToken();
		$this->acessToken = $OAuth2Token->attribute( 'token' );

		return $API;
	}
}
?>
