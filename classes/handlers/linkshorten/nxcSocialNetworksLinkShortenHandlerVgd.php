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

	public function __construct() {}

	public function shorten( $url ) {
		$call = $this->serviceCallUrl . urlencode( $url );

		return $this->shortenUrl( $call );
	}
}
?>
