<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksMessageHandlerLinkedIn
 * @author  Brookins Consulting <info@brookinsconsulting.com>
 * @date    12 Oct 2014
 **/

class nxcSocialNetworksMessageHandlerLinkedIn extends nxcSocialNetworksMessageHandler
{
	public $name = 'LinkedIn';

	public static function message( $publishHandler, eZContentObject $object, $message, $messageLength = 400, $options ) {
		$url = false;
		$share = array( 'title' => $object->attribute( 'name' ) );

		if(
			isset( $options['include_url'] )
			&& (bool) $options['include_url'] === true
		) {
			$url = $object->attribute( 'main_node' )->attribute( 'url_alias' );
			eZURI::transformURI( $url, true, 'full' );

			if(
				isset( $options['shorten_url'] )
				&& (bool) $options['shorten_url'] === true
			) {
				$urlReturned = $publishHandler->shorten( $url, $options['shorten_handler'] );
				if( is_string( $urlReturned ) ) {
					$url = $urlReturned;
				}
			}

			$messageLength = $messageLength - strlen( $url ) - 1;

			$share['submitted-url'] = $url;
		}

		if( class_exists( 'Normalizer' ) ) {
			$message = Normalizer::normalize( $message, Normalizer::FORM_C );
		}
		$message = mb_substr( $message, 0, $messageLength );

		if( $url ) {
			$message .= ' ' . $url;
		}

		$share['description'] = $message;

		return $share;
	}
}
?>