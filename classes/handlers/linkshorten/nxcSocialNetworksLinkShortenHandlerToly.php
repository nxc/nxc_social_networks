<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksLinkShortenHandlerToly
 * @author  Brookins Consulting <info@brookinsconsulting.com>
 * @date    01 Sep 2014
 **/

class nxcSocialNetworksLinkShortenHandlerToly extends nxcSocialNetworksLinkShortenHandler
{
	public $serviceCallUrl = "http://to.ly/api.php?longurl=";

	public function __construct() {}

	public function shorten( $url ) {
		$call = $this->serviceCallUrl . urlencode( $url );

		return $this->shortenUrl( $call );
	}
}
?>
