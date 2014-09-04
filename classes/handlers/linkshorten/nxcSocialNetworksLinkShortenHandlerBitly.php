<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksLinkShortenHandlerBitly
 * @author  Brookins Consulting <info@brookinsconsulting.com>
 * @date    01 Sep 2014
 **/

class nxcSocialNetworksLinkShortenHandlerBitly extends nxcSocialNetworksLinkShortenHandler
{
	public function shorten( $url ) {
		$shortUrl = false;
		$bitly = new Bitly( null, null, eZINI::instance( 'nxcsocialnetworks.ini' )->variable( 'LinkShortenHandlerBitly', 'GenericAccessToken' ) );

		try {
			$responce = $bitly->shorten( $url );
			$responseUrl = $responce['url'];

			if( $responseUrl != '' ) {
				$shortUrl = $responseUrl;
			}
		} catch(Exception $e) {
			// Catch any API errors here
		}

		eZDebug::writeDebug( "Shortened {$url} to {$shortUrl}", __METHOD__ );

		return $shortUrl;
	}
}
?>
