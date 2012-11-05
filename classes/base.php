<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksBase
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    16 Sep 2012
 **/

abstract class nxcSocialNetworksBase
{
	private static $instances         = array();
	private static $typeSettingsGroup = null;

	public static function getInstance() {
		$class = get_called_class();
		if( isset( self::$instances[ $class ] ) === false ) {
			self::$instances[ $class ] = new $class;
		}
		return self::$instances[ $class ];
	}

	public static function getInstanceByType( $type ) {
		$ini   = eZINI::instance( 'nxcsocialnetworks.ini' );
		$types = (array) $ini->variable( 'General', static::$typeSettingsGroup );

		if( isset( $types[ $type ] ) === false ) {
			throw new Exception( '"' . $type . '" is not supported type. Please check nxcsocialnetworks.ini' );
		}

		$callback = array( $types[ $type ], 'getInstance' );
		if( is_callable( $callback ) === false ) {
			throw new Exception( $callback[0] . '::' . $callback[1] . '() is not callable' );
		}

		return call_user_func( $callback );
	}
}
?>
