<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksLinkShortenHandlerTinyurl
 * @author  Brookins Consulting <info@brookinsconsulting.com>
 * @date    01 Sep 2014
 **/

class nxcSocialNetworksLinkShortenHandlerTinyurl extends nxcSocialNetworksLinkShortenHandler
{
	public $serviceCallUrl = "http://tinyurl.com/api-create.php?url=";

	public function shorten( $url ) {
		$call = $this->serviceCallUrl . urlencode( $url );
		$shortUrl = $this->shortenUrl( $call );

		eZDebug::writeDebug( "Shortened {$url} to {$shortUrl}", __METHOD__ );

		return $shortUrl;
	}
}

?>
