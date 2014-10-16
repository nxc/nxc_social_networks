<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksLinkShortenHandlerGoogl
 * @author  Brookins Consulting <info@brookinsconsulting.com>
 * @date    01 Sep 2014
 **/

class nxcSocialNetworksLinkShortenHandlerGoogl extends nxcSocialNetworksLinkShortenHandler
{
	public $name = "Goo.gl";
	public $serviceCallUrl = "https://www.googleapis.com/urlshortener/v1/url";

	public function shorten( $url ) {
		$apiKey = eZINI::instance( 'nxcsocialnetworks.ini' )->variable( 'LinkShortenHandlerGoogl', 'ApiKey' );

		if( $apiKey == false ) {
			$postData = array( 'longUrl' => $url );
		} else {
			$postData = array( 'longUrl' => $url, 'key' => $apiKey );
		}

		$jsonPostData = json_encode( $postData );
		$response = $this->shortenUrl( $this->serviceCallUrl, 'post', $jsonPostData );
		$json = json_decode( $response );
		$shortUrl = $json->id;

		eZDebug::writeDebug( "Shortened {$url} to {$shortUrl}", __METHOD__ );

		return $shortUrl;
	}
}
?>
