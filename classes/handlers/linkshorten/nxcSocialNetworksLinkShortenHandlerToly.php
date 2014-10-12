<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksLinkShortenHandlerToly
 * @author  Brookins Consulting <info@brookinsconsulting.com>
 * @date    01 Sep 2014
 **/

class nxcSocialNetworksLinkShortenHandlerToly extends nxcSocialNetworksLinkShortenHandler
{
	public $name = "to.ly";
	public $serviceCallUrl = "http://to.ly/api.php?longurl=";

	public function shorten( $url ) {
		$call = $this->serviceCallUrl . urlencode( $url );

		$shortUrl = $this->shortenUrl( $call );

		eZDebug::writeDebug( "Shortened {$url} to {$shortUrl}", __METHOD__ );

		return $shortUrl;
	}
}
?>
