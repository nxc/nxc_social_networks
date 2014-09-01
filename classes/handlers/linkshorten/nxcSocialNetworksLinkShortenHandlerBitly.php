<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksLinkShortenHandlerBitly
 * @author  Brookins Consulting <info@brookinsconsulting.com>
 * @date    01 Sep 2014
 **/

class nxcSocialNetworksLinkShortenHandlerBitly extends nxcSocialNetworksLinkShortenHandler
{
	public function __construct() {}

	public function shorten( $url ) {
		$bitly = new Bitly( null, null, eZINI::instance( 'nxcsocialnetworks.ini' )->variable( 'LinkShortenHandlerBitly', 'GenericAccessToken' ) );

		try {
			$url = $bitly->shorten( $url );
		} catch(Exception $e) {
			// Catch any API errors here
		}

		return $url['url'];
	}
}
?>
