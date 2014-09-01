<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksLinkShortenHandler
 * @author  Brookins Consulting <info@brookinsconsulting.com>
 * @date    01 Sep 2014
 **/

class nxcSocialNetworksLinkShortenHandler
{
	public function __construct() {
		$this->handlers = eZINI::instance( 'nxcsocialnetworks.ini' )->variable( 'General', 'LinkShortenHandlers' );
		$this->handlerKey = eZINI::instance( 'nxcsocialnetworks.ini' )->variable( 'General', 'LinkShortenHandlerToUse' );
		$this->handlerClassName = $this->handlers[ $this->handlerKey ];

		if ( class_exists( $this->handlerClassName ) ) {
			$this->service = new $this->handlerClassName();
		}
	}

	public function shorten( $url ) {
		if ( $this->service instanceof nxcSocialNetworksLinkShortenHandler ) {
			$shortUrl = $this->service->shorten( $url );

			eZDebug::writeDebug( "Shortened {$url} to {$shortUrl}", __METHOD__ );

			return $shortUrl;
		}
	}

	public function shortenUrl( $url ) {
		$shortUrl = false;

		if( !function_exists('curl_init') ) {
			throw new Exception('Link Shortener ' . $this->handlerKey . ' needs the CURL PHP extension enabled.');
		} else {
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $url );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch, CURLOPT_HEADER, 0 );

			$shortUrlReturn = curl_exec( $ch );
			curl_close( $ch );

			if( strlen( $shortUrlReturn ) <= 350 ) {
				$shortUrl = $shortUrlReturn;
			}
		}

		return $shortUrl;
	}
}
?>
