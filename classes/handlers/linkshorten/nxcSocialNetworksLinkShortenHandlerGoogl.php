<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksLinkShortenHandlerGoogl
 * @author  Brookins Consulting <info@brookinsconsulting.com>
 * @date    01 Sep 2014
 **/

class nxcSocialNetworksLinkShortenHandlerGoogl extends nxcSocialNetworksLinkShortenHandler
{
	public $serviceCallUrl = "https://www.googleapis.com/urlshortener/v1/url";

	public function __construct() {}

	public function shorten( $url ) {
		$postData = array( 'longUrl' => $url, 'key' => eZINI::instance( 'nxcsocialnetworks.ini' )->variable( 'LinkShortenHandlerGoogl', 'ApiKey' ) );
		$jsonData = json_encode( $postData );

		$curlObj = curl_init();

		curl_setopt( $curlObj, CURLOPT_URL, $this->serviceCallUrl );
		curl_setopt( $curlObj, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $curlObj, CURLOPT_SSL_VERIFYPEER, 0 );
		curl_setopt( $curlObj, CURLOPT_HEADER, 0 );
		curl_setopt( $curlObj, CURLOPT_HTTPHEADER, array( 'Content-type:application/json' ) );
		curl_setopt( $curlObj, CURLOPT_POST, 1 );
		curl_setopt( $curlObj, CURLOPT_POSTFIELDS, $jsonData );

		$response = curl_exec( $curlObj );
		$json = json_decode( $response );

		curl_close( $curlObj );

		return $json->id;
	}
}
?>
