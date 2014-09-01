<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksLinkShortenHandlerIsgd
 * @author  Brookins Consulting <info@brookinsconsulting.com>
 * @date    01 Sep 2014
 **/

class nxcSocialNetworksLinkShortenHandlerIsgd extends nxcSocialNetworksLinkShortenHandler
{
	public $serviceCallUrl = "http://is.gd/create.php?format=simple&url=";

	public function __construct() {}

	public function shorten( $url ) {
		$call = $this->serviceCallUrl . urlencode( $url );

		return $this->shortenUrl( $call );
	}
}

?>