<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksLinkShortenHandlerOwly
 * @author  Brookins Consulting <info@brookinsconsulting.com>
 * @date    01 Sep 2014
 **/

class nxcSocialNetworksLinkShortenHandlerOwly extends nxcSocialNetworksLinkShortenHandler
{
	public function __construct() {}

	public function shorten( $url ) {
		$owly = OwlyApi::factory( array( 'key' => eZINI::instance( 'nxcsocialnetworks.ini' )->variable( 'LinkShortenHandlerOwly', 'ApiKey' ) ) );

		try {
			$url = $owly->shorten( $url );
		} catch(Exception $e) {
			// Catch any API errors here
		}

		return $url;
	}
}
?>
