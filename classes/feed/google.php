<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksFeedGoogle
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    25 Sep 2012
 **/

class nxcSocialNetworksFeedGoogle extends nxcSocialNetworksFeed
{
	protected static $cacheDirectory     = 'nxc-google';
	protected static $debugMessagesGroup = 'NXC Social Networks Googile Plus feed';

	private $acessToken = null;

	public function __construct() {
		parent::__construct();

		$OAuth2 = nxcSocialNetworksOAuth2::getInstanceByType( 'google' );
		$token  = $OAuth2->getToken();
		$tmp    = json_decode( $token->attribute( 'token' ), true );
		try{
			$OAuth2->connection->refreshToken( $tmp['refresh_token'] );
			$this->API = new apiPlusService( $OAuth2->connection );
		} catch( Exception $e ) {
			eZDebug::writeError(
				$e->getMessage(),
				self::$debugMessagesGroup
			);
		}
	}

	public function getActivitiesList( $userID = 'me', $limit = 20 ) {
		$result = array( 'result' => array() );

		$accumulator = $this->debugAccumulatorGroup . '_google_activities_list';
		eZDebug::accumulatorStart(
			$accumulator,
			$this->debugAccumulatorGroup,
			'activities/list'
		);

		$cacheFileHandler = $this->getCacheFileHandler( '_activities_list', array( $userID, $limit ) );
		try{
			if( $this->isCacheExpired( $cacheFileHandler ) ) {
				eZDebug::writeDebug(
					array( 'user_id' => $userID, 'limit' => $limit ),
					self::$debugMessagesGroup
				);

				$response = $this->API->activities->listActivities(
					$userID, 'public', array( 'maxResults' => $limit )
				);

				$activities  = array();
				$currentTime = time();
				foreach( $response['items'] as $activity ) {
					$createdAt = strtotime( $activity['published'] );

					$activity['created_ago']       = self::getCreatedAgoString( $createdAt, $currentTime );
					$activity['created_timestamp'] = $createdAt;

					$activities[] = $activity;
				}

				$cacheFileHandler->fileStoreContents( $cacheFileHandler->filePath, serialize( $activities ) );
			} else {
				$activities = unserialize( $cacheFileHandler->fetchContents() );
			}

			eZDebug::accumulatorStop( $accumulator );
			$result['result'] = $activities;
			return $result;
		} catch( Exception $e ) {
			eZDebug::accumulatorStop( $accumulator );
			eZDebug::writeError( $e, self::$debugMessagesGroup );
			return $result;
		}
	}

	public function searchActivities( $query, $limit = 20, $sorting = 'best' ) {
		$result = array( 'result' => array() );

		$accumulator = $this->debugAccumulatorGroup . '_google_activities_search';
		eZDebug::accumulatorStart(
			$accumulator,
			$this->debugAccumulatorGroup,
			'activities/search'
		);

		$cacheFileHandler = $this->getCacheFileHandler( '_activities_search', array( $query, $limit, $sorting ) );
		try{
			if( $this->isCacheExpired( $cacheFileHandler ) ) {
				eZDebug::writeDebug(
					array( 'query' => $query, 'limit' => $limit, 'sorting' => $sorting ),
					self::$debugMessagesGroup
				);

				$response = $this->API->activities->search(
					$query,  array( 'maxResults' => $limit, 'orderBy' => $sorting )
				);

				$activities  = array();
				$currentTime = time();
				foreach( $response['items'] as $activity ) {
					$createdAt = strtotime( $activity['published'] );

					$activity['created_ago']       = self::getCreatedAgoString( $createdAt, $currentTime );
					$activity['created_timestamp'] = $createdAt;

					$activities[] = $activity;
				}

				$cacheFileHandler->fileStoreContents( $cacheFileHandler->filePath, serialize( $activities ) );
			} else {
				$activities = unserialize( $cacheFileHandler->fetchContents() );
			}

			eZDebug::accumulatorStop( $accumulator );
			$result['result'] = $activities;
			return $result;
		} catch( Exception $e ) {
			eZDebug::accumulatorStop( $accumulator );
			eZDebug::writeError( $e, self::$debugMessagesGroup );
			return $result;
		}
	}
}
?>
