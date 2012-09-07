<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcTwitterFeed
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    17 Aug 2012
 **/

class nxcTwitterFeed
{
	private $twitterAPI;
	private $cacheSettings;

	public function __construct() {
		$iniAPI = eZINI::instance( 'nxctwitter.ini' );
		$iniOAuthToken = eZINI::instance( 'nxctwitteraccesstoken.ini' );
		$this->twitterAPI = new TwitterOAuth(
			$iniAPI->variable( 'TwitterAPI', 'Key' ),
			$iniAPI->variable( 'TwitterAPI', 'Secret' ),
			$iniOAuthToken->variable( 'AccessToken', 'Token' ),
			$iniOAuthToken->variable( 'AccessToken', 'Secret' )
		);

		$this->cacheSettings = array(
			'path' => eZSys::cacheDirectory() . '/nxc-twitter/',
			'ttl'  => 60
		);
	}

	public function getTimeline( $type = 'home', array $parameters = array() ) {
		$possibleTypes = array( 'public', 'home', 'friends', 'user' );
		if( in_array( $type, $possibleTypes ) === false ) {
			eZDebug::writeError( 'Type "' . $type . '" ins`t allowed', 'NXC Twitter feed' );
			return false;
		}

		$parameters['type'] = $type;
		$cacheFileHandler = eZClusterFileHandler::instance(
			$this->cacheSettings['path'] . md5( serialize( $parameters ) ) . '_timeline.php'
		);

		try{
			if(
				$cacheFileHandler->fileExists( $cacheFileHandler->filePath ) === false ||
				time() > ( $cacheFileHandler->mtime() + $this->cacheSettings['ttl'] )
			) {
				eZDebug::writeDebug( '"' . $type . '" timeline', 'NXC Twitter feed' );
				eZDebug::writeDebug( $parameters, 'NXC Twitter feed' );

				$response = $this->twitterAPI->get( 'statuses/' . $type . '_timeline', $parameters );

				$errorKeys = array( 'error', 'errors' );
				foreach( $errorKeys as $errorKey ) {
					if( isset( $response->{$errorKey} ) ) {
						eZDebug::writeError( $response->{$errorKey}, 'NXC Twitter feed' );
						return false;
					}
				}

				$statuses     = array();
				$current_time = time();
				foreach( $response as $status ) {
					$created_at   = strtotime( $status->created_at );
					$created_diff = $current_time - $created_at;
					if( $created_diff < 60 ) {
						$created_ago = ezpI18n::tr(
							'extension/nxc_social_networks', '%secons seconds ago', null, array( '%secons' => ceil( $created_diff ) )
						);
					} elseif( $created_diff < 60 * 60 ) {
						$created_ago = ezpI18n::tr(
							'extension/nxc_social_networks', '%minutes minutes ago', null, array( '%minutes' => floor( $created_diff / 60 ) )
						);
					} elseif( $created_diff < 60 * 60 * 24 ) {
						$created_ago = ezpI18n::tr(
							'extension/nxc_social_networks', 'About %hours hours ago', null, array( '%hours' => floor( $created_diff / ( 60 * 60 ) ) )
						);
					} elseif( $created_diff < 60 * 60 * 24 * 7 ) {
						$created_ago = ezpI18n::tr(
							'extension/nxc_social_networks', 'About %days days ago', null, array( '%days' => floor( $created_diff / ( 60 * 60 * 24 ) ) )
						);
					} else {
						$created_ago = ezpI18n::tr(
							'extension/nxc_social_networks', 'About %weeks weeks ago', null, array( '%weeks' => floor( $created_diff / ( 60 * 60 * 24 * 7 ) ) )
						);
					}

					$status = self::objectToArray( $status );
					$status['created_timestamp'] = $created_at;
					$status['created_ago']       = $created_ago;

					$message = $status['text'];
					$message = preg_replace( '[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]', '<a href="\\0">\\0</a>', $message );
					$message = preg_replace( '/@([\w]*)/', '<a href="http://twitter.com/\\1">\\0</a>', $message );
					$message = preg_replace( '/#([\w]*)/', '<a href="https://twitter.com/search?q=%23\\1">\\0</a>', $message );
					$status['text'] = $message;

					$statuses[] = $status;
				}

				$cacheFileHandler->fileStoreContents( $cacheFileHandler->filePath, serialize( $statuses ) );
			} else {
				$statuses = unserialize( $cacheFileHandler->fetchContents() );
			}

			return array( 'result' => $statuses );
		} catch( Exception $e ) {
			eZDebug::writeError( $e, 'NXC Twitter feed' );
			return false;
		}
	}

	public function getUserInfo() {
		$cacheFileHandler = eZClusterFileHandler::instance( $this->cacheSettings['path'] . 'user_info.php' );

		try{
			if(
				$cacheFileHandler->fileExists( $cacheFileHandler->filePath ) === false ||
				time() > ( $cacheFileHandler->mtime() + $this->cacheSettings['ttl'] )
			) {
				$iniOAuthToken = eZINI::instance( 'nxctwitteraccesstoken.ini' );

				$response = $this->twitterAPI->get(
					'users/show',
					array( 'id' => $iniOAuthToken->variable( 'AccessToken', 'UserID' ) )
				);

				if( isset( $response->error ) ) {
					eZDebug::writeError( $response->error, 'NXC Twitter feed' );
					return false;
				}

				$info = self::objectToArray( $response );
				$cacheFileHandler->fileStoreContents( $cacheFileHandler->filePath, serialize( $info ) );
			} else {
				$info = unserialize( $cacheFileHandler->fetchContents() );
			}

			return array( 'result' => $info );
		} catch( Exception $e ) {
			eZDebug::writeError( $e, 'NXC Twitter feed' );
			return false;
		}
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
