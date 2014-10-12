<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksMessageHandler
 * @author  Brookins Consulting <info@brookinsconsulting.com>
 * @date    12 Oct 2014
 **/

class nxcSocialNetworksMessageHandler
{
	static protected $handlers = null;
	static protected $handlerKey = null;
	static protected $handlerClassName = null;

	public static function instance( $argHandlerKey = null ) {
		self::$handlers = eZINI::instance( 'nxcsocialnetworks.ini' )->variable( 'General', 'MessageHandlers' );

		if( $argHandlerKey != null ) {
			self::$handlerKey = $argHandlerKey;
		} else {
			self::$handlerKey = 'default';
		}

		self::$handlerClassName = self::$handlers[ self::$handlerKey ];

		if ( class_exists( self::$handlerClassName ) ) {
			return new self::$handlerClassName();
		}
	}

	public static function getHandlers() {
		$ini = eZINI::instance( 'nxcsocialnetworks.ini' );
		return (array) $ini->variable( 'General', 'MessageHandlers' );
	}
}
?>