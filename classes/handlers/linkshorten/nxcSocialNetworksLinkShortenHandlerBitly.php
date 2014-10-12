<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksLinkShortenHandlerBitly
 * @author  Brookins Consulting <info@brookinsconsulting.com>
 * @date    01 Sep 2014
 **/

class nxcSocialNetworksLinkShortenHandlerBitly extends nxcSocialNetworksLinkShortenHandler
{
	public $name = "Bit.ly";

	public function shorten( $url ) {
		$shortUrl = false;
		$bitly = new Bitly( null, null, eZINI::instance( 'nxcsocialnetworks.ini' )->variable( 'LinkShortenHandlerBitly', 'GenericAccessToken' ) );

		try {
			$response = $bitly->shorten( $url );
			$responseUrl = $response['url'];

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
