<?php

class nxcSocialNetworksFeedInstagram extends nxcSocialNetworksFeed
{
	protected static $cacheDirectory     = 'nxc-instagram';
	protected static $debugMessagesGroup = 'NXC Social Networks Instagram feed';

	private $acessToken = null;

	public function __construct() {
		parent::__construct();

		$OAuth2      = nxcSocialNetworksOAuth2::getInstanceByType( 'instagram' );
		$OAuth2Token = $OAuth2->getToken()->Token;

		$this->API = array( 
			'key'    => $OAuth2->appSettings[ 'key' ],
			'secret' => $OAuth2->appSettings[ 'secret' ],
			'token'  => $OAuth2Token
		);
	}

	public function getMediaRecent( $pageID = false, $limit = 20 ) {
		$result = array( 'result' => array() );

		$accumulator = $this->debugAccumulatorGroup . '_instagram_media_recent';
		eZDebug::accumulatorStart(
			$accumulator,
			$this->debugAccumulatorGroup,
			'media_recent'
		);

		$cacheFileHandler = $this->getCacheFileHandler( '_media_recent', array( $pageID, $limit ) );
		try{
			if( $this->isCacheExpired( $cacheFileHandler ) ) {
				eZDebug::writeDebug(
					array( 'page_id' => $pageID, 'limit' => $limit ),
					self::$debugMessagesGroup
				);

				$items = array();

				$userData = eZHTTPTool::getDataByURL(
					'https://api.instagram.com/v1/users/search?' .
					'q=' . $pageID . '&' .
					'client_id=' . $this->API['key']
				);

				$userID = false;

				if( $userData !== false ) {
					$userDataArray = json_decode( $userData, true );
					if( isset( $userDataArray[ 'data' ][ 0 ] ) && $userDataArray[ 'data' ][ 0 ][ 'username' ] == $pageID ) {
						$userID = $userDataArray[ 'data' ][ 0 ][ 'id' ];
					}
				}

				if( $userID !== false ) {
					$leftLimit = $limit;
					$feedData = eZHTTPTool::getDataByURL(
						'https://api.instagram.com/v1/users/' .
						$userID . '/media/recent/?' .
						'access_token=' . $this->API[ 'token' ]
					);
					if( $feedData !== false ) {
						$feedDataArray = json_decode( $feedData, true );
						if( isset( $feedDataArray[ 'data' ] ) ) {
							$items = array_merge( $items, array_slice( $feedDataArray[ 'data' ], 0, $leftLimit ) );
							$leftLimit = $leftLimit - count( $feedDataArray[ 'data' ] );
						}
						$endlessLoopBreaker = 0;
						while( $endlessLoopBreaker < 50 && $leftLimit > 0 && isset( $feedDataArray[ 'pagination' ][ 'next_url' ] ) ) {
								$endlessLoopBreaker++;
								$feedData = eZHTTPTool::getDataByURL( $feedDataArray[ 'pagination' ][ 'next_url' ] );
								if( $feedData !== false ) {
								  $feedDataArray = json_decode( $feedData, true );
								  $items = array_merge( $items, array_slice( $feedDataArray[ 'data' ], 0, $leftLimit ) );
								}
								$leftLimit = $leftLimit - count( $feedDataArray[ 'data' ] );

						}
					}
				}
				$cacheFileHandler->fileStoreContents( $cacheFileHandler->filePath, serialize( $items ) );
			} else {
				$items = unserialize( $cacheFileHandler->fetchContents() );
			}

			eZDebug::accumulatorStop( $accumulator );
			$result['result'] = $items;
			return $result;
		} catch( Exception $e ) {
			eZDebug::accumulatorStop( $accumulator );
			eZDebug::writeError( $e->getMessage(), self::$debugMessagesGroup );
			return $result;
		}
	}

}
?>