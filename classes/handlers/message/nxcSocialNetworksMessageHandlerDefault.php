<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksMessageHandlerDefault
 * @author  Brookins Consulting <info@brookinsconsulting.com>
 * @date    12 Oct 2014
 **/

class nxcSocialNetworksMessageHandlerDefault extends nxcSocialNetworksMessageHandler
{
	public $name = 'Default';

	public static function message( $publishHandler, eZContentObject $object, $message, $messageLength = null, $options ) {
		$url = false;
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

			if( $messageLength != null ) {
				$messageLength = $messageLength - strlen($url) - 1;
			}
		}

		if( class_exists( 'Normalizer' ) ) {
			$message = Normalizer::normalize( $message, Normalizer::FORM_C );
		}

		if( $messageLength != null ) {
			$message = mb_substr( $message, 0, $messageLength );
		}

		if( $url ) {
			$message .= ' ' . $url;
		}

		return $message;
	}
}
?>