<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksFeedTwitter
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    20 Sep 2012
 **/

class nxcSocialNetworksFeedTwitter extends nxcSocialNetworksFeed
{
	protected static $cacheDirectory     = 'nxc-twitter';
	protected static $debugMessagesGroup = 'NXC Social Networks Twitter feed';

	private static $timeLineTypes = array( 'user', 'home', 'mentions' );

	public function __construct() {
		parent::__construct();

		$OAuth2      = nxcSocialNetworksOAuth2::getInstanceByType( 'twitter' );
		$OAuth2Token = $OAuth2->getToken();
		$this->API = new TwitterOAuth(
			$OAuth2->appSettings['key'],
			$OAuth2->appSettings['secret'],
			$OAuth2Token->attribute( 'token' ),
			$OAuth2Token->attribute( 'secret' )
		);
	}

	public function getTimeline( $type = 'home', array $parameters = array() ) {
		$result = array( 'result' => array() );

		if( in_array( $type, self::$timeLineTypes ) === false ) {
			eZDebug::writeError( 'Type "' . $type . '" ins`t allowed', self::$debugMessagesGroup );
			return $result;
		}

		$parameters['type'] = $type;
		$cacheFileHandler   = $this->getCacheFileHandler( 'timeline', $parameters );

		try{
			if( $this->isCacheExpired( $cacheFileHandler ) ) {
				eZDebug::writeDebug( '"' . $type . '" timeline', self::$debugMessagesGroup );
				eZDebug::writeDebug( $parameters, self::$debugMessagesGroup );

				$response = $this->API->get( 'statuses/' . $type . '_timeline', $parameters );

				$errorKeys = array( 'error', 'errors' );
				foreach( $errorKeys as $errorKey ) {
					if( isset( $response->{$errorKey} ) ) {
						eZDebug::writeError( $response->{$errorKey}, self::$debugMessagesGroup );
						return $result;
					}
				}

				$statuses    = array();
				$currentTime = time();
				foreach( $response as $status ) {
					$createdAt = strtotime( $status->created_at );

					$status = self::objectToArray( $status );
					$status['created_timestamp'] = $createdAt;
					$status['created_ago']       = self::getCreatedAgoString( $createdAt, $currentTime );
					$status['text']              = self::fixStatusLinks( $status['text'] );

					$statuses[] = $status;
				}

				$cacheFileHandler->fileStoreContents( $cacheFileHandler->filePath, serialize( $statuses ) );
			} else {
				$statuses = unserialize( $cacheFileHandler->fetchContents() );
			}

			$result['result'] = $statuses;
			return $result;
		} catch( Exception $e ) {
			eZDebug::writeError( $e, self::$debugMessagesGroup );
			return $result;
		}
	}

	public function getUserInfo() {
		$result = array( 'result' => array() );

		try{
			$response = $this->API->get( 'account/verify_credentials' );
			$userID   = $response->id;
		} catch( Exception $e ) {
			eZDebug::writeError( $e, self::$debugMessagesGroup );
			return $result;
		}

		$cacheFileHandler = $this->getCacheFileHandler( 'user_info', array( 'userID' => $userID ) );
		try{
			if( $this->isCacheExpired( $cacheFileHandler ) ) {
				$response = $this->API->get(
					'users/show',
					array( 'id' => $userID )
				);

				$errorKeys = array( 'error', 'errors' );
				foreach( $errorKeys as $errorKey ) {
					if( isset( $response->{$errorKey} ) ) {
						eZDebug::writeError( $response->{$errorKey}, self::$debugMessagesGroup );
						return $result;
					}
				}

				$info = self::objectToArray( $response );
				$cacheFileHandler->fileStoreContents( $cacheFileHandler->filePath, serialize( $info ) );
			} else {
				$info = unserialize( $cacheFileHandler->fetchContents() );
			}

			$result['result'] = $info;
			return $result;
		} catch( Exception $e ) {
			eZDebug::writeError( $e, self::$debugMessagesGroup );
			return $result;
		}
	}

	private static function fixStatusLinks( $message ) {
		$message = preg_replace( '|[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]|', '<a href="\\0">\\0</a>', $message );
		$message = preg_replace( '/@([\w]*)/', '<a href="http://twitter.com/\\1">\\0</a>', $message );
		$message = preg_replace( '/#([\w]*)/', '<a href="https://twitter.com/search?q=%23\\1">\\0</a>', $message );

		return $message;
	}
}
?>
