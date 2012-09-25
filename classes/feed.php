<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksFeed
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    20 Sep 2012
 **/

abstract class nxcSocialNetworksFeed
{
	protected $API = null;
	protected $cacheSettings = null;
	protected $debugAccumulatorGroup = null;

	protected static $cacheDirectory = null;
	protected static $debugMessagesGroup = null;

	public function __construct() {
		$this->cacheSettings = array(
			'path' => eZSys::cacheDirectory() . '/' . static::$cacheDirectory . '/',
			'ttl'  => 60
		);

		$this->debugAccumulatorGroup = 'nxc_social_networks_feed_';
		$this->debugAccumulatorGroup .= strtolower( str_replace( __CLASS__, '', get_called_class() ) );
		eZDebug::createAccumulatorGroup( $this->debugAccumulatorGroup, static::$debugMessagesGroup );
	}

	protected function getCacheFileHandler( $key, $params ) {
		return eZClusterFileHandler::instance(
			$this->cacheSettings['path'] . md5( serialize( $params ) ) . '_' . $key . '.php'
		);
	}

	protected function isCacheExpired( $cacheFileHandler ) {
		return
			$cacheFileHandler->fileExists( $cacheFileHandler->filePath ) === false
			|| time() > ( $cacheFileHandler->mtime() + $this->cacheSettings['ttl'] );
	}

	protected static function getCreatedAgoString( $createdAt, $currentTime = null ) {
		if( $currentTime === null ) {
			$currentTime = time();
		}

		$createdDiff = $currentTime - $createdAt;
		if( $createdDiff < 60 ) {
			$createdAgo = ezpI18n::tr(
				'extension/nxc_social_networks', '%secons seconds ago', null, array( '%secons' => ceil( $createdDiff ) )
			);
		} elseif( $createdDiff < 60 * 60 ) {
			$createdAgo = ezpI18n::tr(
				'extension/nxc_social_networks', '%minutes minutes ago', null, array( '%minutes' => floor( $createdDiff / 60 ) )
			);
		} elseif( $createdDiff < 60 * 60 * 24 ) {
			$createdAgo = ezpI18n::tr(
				'extension/nxc_social_networks', 'About %hours hours ago', null, array( '%hours' => floor( $createdDiff / ( 60 * 60 ) ) )
			);
		} elseif( $createdDiff < 60 * 60 * 24 * 7 ) {
			$createdAgo = ezpI18n::tr(
				'extension/nxc_social_networks', 'About %days days ago', null, array( '%days' => floor( $createdDiff / ( 60 * 60 * 24 ) ) )
			);
		} else {
			$createdAgo = ezpI18n::tr(
				'extension/nxc_social_networks', 'About %weeks weeks ago', null, array( '%weeks' => floor( $createdDiff / ( 60 * 60 * 24 * 7 ) ) )
			);
		}

		return $createdAgo;
	}

	public static function objectToArray( $obj ) {
		$arr = is_object( $obj ) ? get_object_vars( $obj ) : $obj;
		foreach ( $arr as $key => $val ) {
			$val = ( is_array( $val ) || is_object( $val ) ) ? self::objectToArray( $val ) : $val;
			$arr[ $key ] = $val;
		}
		return $arr;
	}
}
?>
