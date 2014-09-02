<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksLinkShortenHandlerVgd
 * @author  Brookins Consulting <info@brookinsconsulting.com>
 * @date    01 Sep 2014
 **/

class nxcSocialNetworksLinkShortenHandlerVgd extends nxcSocialNetworksLinkShortenHandler
{
	public $serviceCallUrl = "http://v.gd/create.php?format=simple&url=";

	public function shorten( $url ) {
		$call = $this->serviceCallUrl . urlencode( $url );
		$shortUrl = $this->shortenUrl( $call );

		eZDebug::writeDebug( "Shortened {$url} to {$shortUrl}", __METHOD__ );

		return $shortUrl;
	}
}
?>
