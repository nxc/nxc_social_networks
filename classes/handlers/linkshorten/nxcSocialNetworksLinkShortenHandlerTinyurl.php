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

	public function __construct() {}

	public function shorten( $url ) {
		$call = $this->serviceCallUrl . urlencode( $url );

		return $this->shortenUrl( $call );
	}
}

?>
