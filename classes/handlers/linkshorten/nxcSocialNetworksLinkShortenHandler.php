<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksLinkShortenHandler
 * @author  Brookins Consulting <info@brookinsconsulting.com>
 * @date    01 Sep 2014
 **/

class nxcSocialNetworksLinkShortenHandler
{
	static protected $handlers = null;
	static protected $handlerKey = null;
	static protected $handlerClassName = null;

	public static function instance( $argHandlerKey = null ) {
		self::$handlers = eZINI::instance( 'nxcsocialnetworks.ini' )->variable( 'General', 'LinkShortenHandlers' );

		if( $argHandlerKey != null ) {
			self::$handlerKey = $argHandlerKey;
		} else {
			self::$handlerKey = eZINI::instance( 'nxcsocialnetworks.ini' )->variable( 'General', 'LinkShortenHandlerDefault' );
		}

		self::$handlerClassName = self::$handlers[ self::$handlerKey ];

		if ( class_exists( self::$handlerClassName ) ) {
			return new self::$handlerClassName();
		}
	}

	public static function getHandlers() {
		$ini = eZINI::instance( 'nxcsocialnetworks.ini' );
		return (array) $ini->variable( 'General', 'LinkShortenHandlers' );
	}

	public function shortenUrl( $serviceApiCallUrl, $type = 'get', $postData = null ) {
		$shortUrl = false;

		if( !function_exists('curl_init') ) {
			throw new Exception('Link Shortener ' . self::$handlerKey . ' needs the CURL PHP extension enabled.');
		} else {
			$curlObj = curl_init();
			curl_setopt( $curlObj, CURLOPT_URL, $serviceApiCallUrl );
			curl_setopt( $curlObj, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $curlObj, CURLOPT_HEADER, 0 );

			if( $type == 'get' ) {
				$shortUrlReturn = curl_exec( $curlObj );
			} elseif ( $type == 'post' ) {
				curl_setopt( $curlObj, CURLOPT_SSL_VERIFYPEER, 0 );
				curl_setopt( $curlObj, CURLOPT_HTTPHEADER, array( 'Content-type:application/json' ) );
				curl_setopt( $curlObj, CURLOPT_POST, 1 );
				curl_setopt( $curlObj, CURLOPT_POSTFIELDS, $postData );

				$shortUrlReturn = curl_exec( $curlObj );
			}

			curl_close( $curlObj );

			if( is_string( $shortUrlReturn ) && strlen( $shortUrlReturn ) <= 350 ) {
				$shortUrl = $shortUrlReturn;
			}
		}

		return $shortUrl;
	}
}
?>
