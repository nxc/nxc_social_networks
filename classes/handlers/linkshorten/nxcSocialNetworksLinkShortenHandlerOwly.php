<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksLinkShortenHandlerOwly
 * @author  Brookins Consulting <info@brookinsconsulting.com>
 * @date    01 Sep 2014
 **/

class nxcSocialNetworksLinkShortenHandlerOwly extends nxcSocialNetworksLinkShortenHandler
{
	public function shorten( $url ) {
		$shortUrl = false;
		$owly = OwlyApi::factory( array( 'key' => eZINI::instance( 'nxcsocialnetworks.ini' )->variable( 'LinkShortenHandlerOwly', 'ApiKey' ) ) );

		try {
			$responseUrl = $owly->shorten( $url );

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
